<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

use Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators\LocalThemeDownloader;
use Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators\OrgThemeDownloader;
use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessors\InstallPlugins;
use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessors\InstallThemes;

class SchemaProcessor {
	private Schema $schema;
	private StepProcessorFactory $step_factory;
	public function __construct( Schema $schema, StepProcessorFactory $step_factory = null) {
		$this->schema = $schema;
		if ($step_factory === null) {
			$step_factory = new StepProcessorFactory($schema);
		}

		$this->step_factory = $step_factory;
	}

	public static function crate_from_file($file) {
		// @todo check for mime type
		// @todo check for allowed types -- json or zip
		$path_info = pathinfo($file);
		$is_zip = $path_info['extension'] === 'zip';

		return $is_zip ? SchemaProcessor::crate_from_zip($file) : SchemaProcessor::create_from_json($file);
	}

	public static function create_from_json($json_path) {
		return new self(new JsonSchema($json_path));
	}

	public static function crate_from_zip($zip_path) {
		return new self(new ZipSchema($zip_path));
	}

	/**
	 * @return StepProcessorResult[]
	 */
	public function process() {

		$results = array();
		$result = StepProcessorResult::success(self::class);
		$results[] = $result;

		$settings = $this->testSettings();
		return $results;

		foreach ( $this->schema->get_steps() as $stepSchema ) {
			$stepProcessor = $this->step_factory->create_from_name($stepSchema->step);
			if (!$stepProcessor instanceof InstallThemes) {
				continue;
			}
			if ( ! $stepProcessor instanceof StepProcessor ) {
				$result->add_error("Unable to create a step processor for {$stepSchema->step}");
				continue;
			}

			$results[] = $stepProcessor->process( $stepSchema );
		}

		return $results;
	}

	private function testSettings() {

		$exporter = new ExportSettings(\WC_Admin_Settings::get_settings_pages());
		$settings = $exporter->get_settings();

		var_dump($settings);

		exit;

		var_dump('hi');
		foreach ( \WC_Admin_Settings::get_settings_pages() as $settings_page ) {

			var_dump(get_class($settings_page));
			exit;
			$page_sections = $settings_page->get_sections();
			$page_id = $settings_page->get_id();

			$settings_data[$page_id] = array();

			foreach ( $page_sections as $section_id => $section_title ) {
				$section_settings = $settings_page->get_settings( $section_id );

				foreach ($section_settings as $section_setting) {
					if ( ! $section_setting['id'] || 'sectionend' === $section_setting['type'] ) {
						continue;
					}

				}
				var_dump($section_settings);
exit;

//
//				foreach ( $section_settings as $setting ) {
//					if ( ! $setting['id'] || 'sectionend' === $setting['type'] || 'title' === $setting['type'] ) {
//						continue;
//					}
//					$settings_data[$page_id][$section_key] = get_option($setting['id']);
//				}
			}

			exit;


		}

		return $settings_data;
	}

}
