<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

use Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators\LocalPluginDownloader;
use Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators\OrgPluginDownloader;
use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessors\InstallPlugins;

class Blueprint {
	private Schema $schema;
	public function __construct( Schema $schema ) {
		$this->schema = $schema;
	}

	public static function create_from_json($json_path) {
		return new self(new JsonSchema($json_path));
	}

	public static function crate_from_zip($zip_path) {
		return new self(new ZipSchema($zip_path));
	}

	/**
	 * @return StepProcessorResult
	 */
	public function process() {
		if ( ! $this->validate() ) {
			// @todo Implement JSON Schema validation here.
			return false;
		}

		$results = StepProcessorResult::success(self::class);
		foreach ( $this->schema->get_steps() as $stepSchema ) {
			$stepProcessor = $this->create_step_processor($stepSchema->step);
			// test code
			if (! $stepProcessor instanceof InstallPlugins) {
				continue;
			}

			if ( ! $stepProcessor instanceof StepProcessor) {
				$results->add_error("Unable to create step processor for {$stepSchema->step}");
			}

			$results->merge( $stepProcessor->process( $stepSchema ) );

		}

		return $results;
	}

	private function validate() {
		return true;
	}

	private function create_step_processor( $step_name ) {
		$stepProcessor = __NAMESPACE__ . '\\StepProcessors\\' . Util::snake_to_camel($step_name);
		if (!class_exists($stepProcessor)) {
			// throw error
			return false;
		}

		switch ($step_name) {
			case 'installPlugins':
				return $this->create_install_plugins_processor();
			default:
				return new $stepProcessor;
		}
	}

	private function create_install_plugins_processor() {
		$storage = new PluginsStorage();
		if ($this->schema instanceof ZipSchema) {
			$storage->add_downloader( new LocalPluginDownloader($this->schema->get_unzip_path()) );
		}

		$storage->add_locator(new OrgPluginDownloader());
		return new InstallPlugins($storage);
	}
}
