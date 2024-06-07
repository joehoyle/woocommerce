<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

use WC_Admin_Settings;
use WC_Settings_Page;
use WC_Tax;

/**
 * Returns the following array structure based off the settings configuration.
 * Page Name
 *  Section A
 *      setting_id
 *          value
 *          description
 *
 * array(
 *     'general' => array(
 *          'store_address' => array(
 *              'woocommerce_address_1' => array(
 *                  'value' => '1234 Main St',
 *                  'description' => 'Address line 1'
 *              )
 *          )
 *      ),
 *      'products' => array(
 *          'general' => array(...),
 *          'inventory' => array(...),
 *      )
 * )
 */
class ExportSettings implements ExportsBlueprintStep {
	/**
	 * @var WC_Settings_Page[]
	 */
	private array $setting_pages;
	public function __construct(array $setting_pages = array()) {
		if (empty($setting_pages)){
			$setting_pages = WC_Admin_Settings::get_settings_pages();
		}
		$this->setting_pages = $setting_pages;

		// @todo -- need a better approach
		$this->add_filter('wooblueprint_export_settings_tax', array($this, 'export_tax_rates'));
		$this->add_filter('wooblueprint_export_settings_shipping', array($this, 'export_shipping'));

	}

	public function add_filter($name, $callback) {
		return \add_filter($name, $callback);
	}

	public function apply_filters($name, $args) {
		return \apply_filters($name, $args);
	}

	public function export() {
		$settings = array();
	    foreach ($this->setting_pages as $page) {
			$id = $page->get_id();
			if ($id !='shipping') {
				continue;
			}
			$page_settings = $this->get_page_settings($page);
			// If we have only on section, it's a setting page without any sub-sections
		    // just get the values so we avoid nested array
			if (count($page_settings) === 1) {
				$page_settings = current($page_settings);
			}

			$page_settings = $this->apply_filters('wooblueprint_export_settings_'.$id, $page_settings);

			$settings[$id] = $page_settings;
	    }
		return $settings;
	}

	public function export_as_step_configuration() {
	    return array(
			'step' => 'configureSettings',
		    'pages' => $this->export()
		);
	}

	private function get_page_settings( WC_Settings_page $page ) {
		$settings = array();
		foreach ($page->get_sections() as $section_id => $section) {
			$label = Util::camel_to_snake(strtolower($section));
			$section_settings = $this->get_page_section_settings($page->get_settings($section_id));
			if (count($section_settings) === 1) {
				$section_settings = current($section_settings);
			}
			$settings[$label] = $section_settings;
		}

		return $settings;
	}

	private function get_page_section_settings($settings) {
		$current_title = '';
		$data = array();
		foreach ($settings as $setting) {
			if ($setting['type'] === 'sectionend' || !isset($setting['id'])) {
				continue;
			}

			if ($setting['type'] == 'title') {
				$current_title = Util::camel_to_snake(strtolower($setting['title']));
				if (!isset($newArray[$current_title])) {
					$newArray[$current_title] = array();
				}
			} else {
				if ($current_title) {
					$data[$current_title][] = array(
						'id' => $setting['id'],
						'value' => get_option($setting['id'], $setting['default'] ?? null),
						'title' => $setting['title'] ?? $setting['desc'],
					);
				}
			}
		}
		return $data;
	}

	public function export_tax_rates($settings) {
		global $wpdb;
		// @todo check to see if we already have a DAO for taxes.
		$rates = $wpdb->get_results("
			SELECT *
			FROM {$wpdb->prefix}woocommerce_tax_rates as tax_rates
		");

		unset($settings['standard_rates']);
		unset($settings['reduced_rate_rates']);
		unset($settings['zero_rate_rates']);

		$settings['rates'] = array();

		foreach ($rates as $rate) {
			$settings['rates'][] = (array)$rate;
		}
		return $settings;
	}

	public function export_shipping($settings) {
		global $wpdb;
		$classes = $wpdb->get_results("
			SELECT *
			FROM {$wpdb->prefix}term_taxonomy
			where taxonomy = 'product_shipping_class'
		");

		$settings['classes'] = $classes;
		$settings['local_pickup'] = array(
			'general' => get_option('woocommerce_pickup_location_settings', array()),
			'locations' => get_option('pickup_location_pickup_locations', array())
		);

		$settings['shipping_zones'] = array();

		$zones = $wpdb->get_results("
			SELECT *
			FROM {$wpdb->prefix}woocommerce_shipping_zones
		");

		$methods = $wpdb->get_results("
			SELECT *
			FROM {$wpdb->prefix}woocommerce_shipping_zone_methods
		");


		$methods_by_zone_id = array();
		foreach ($methods as $method) {
			if (!isset($methods_by_zone_id[$method->zone_id])) {
				$methods_by_zone_id[$method->zone_id] = array();
			}
			$methods_by_zone_id[$method->zone_id][] = $method->method_id;
		}

		$locations = $wpdb->get_results("
			SELECT *
			FROM {$wpdb->prefix}woocommerce_shipping_zone_locations
		");

		$locations_by_zone_id = array();
		foreach ($locations as $location) {
			if (!isset($locations_by_zone_id[$location->zone_id])) {
				$locations_by_zone_id[$location->zone_id] = array();
			}
			$locations_by_zone_id[$location->zone_id][] = $location->location_id;
		}

		$settings['shipping_methods'] = $methods;
		$settings['shipping_locations'] = $locations;


		foreach ($zones as $zone) {
			$zone->methods = $methods_by_zone_id[$zone->zone_id];
			$zone->locations = $locations_by_zone_id[$zone->zone_id];
			$settings['shipping_zones'][] = $zone;
		}

		return $settings;
	}
}
