<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

use WC_Admin_Settings;
use WC_Settings_Page;

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
	}

	public function export() {
		$settings = array();
	    foreach ($this->setting_pages as $page) {
			$id = $page->get_id();
			$page_settings = $this->get_page_settings($page);
			// If we have only on section, it's a setting page without any sub-sections
		    // just get the values so we avoid nested array
			if (count($page_settings) === 1) {
				$page_settings = current($page_settings);
			}

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
			$settings[$label] = $this->get_page_section_settings($page->get_settings($section_id));
		}

		return $settings;
	}

	private function get_page_section_settings($settings) {
		$current_title = '';
		$data = array();
		foreach ($settings as $setting) {
			if ($setting['type'] === 'sectioned' || !isset($setting['id'])) {
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
						'title' => $setting['title'] ?? '',
					);
				}
			}
		}
		return $data;
	}
}
