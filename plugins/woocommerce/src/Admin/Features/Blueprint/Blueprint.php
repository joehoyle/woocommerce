<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

use Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators\LocalPluginDownloader;
use Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators\OrgPluginDownloader;
use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessors\InstallPlugins;

class Blueprint {
	private Schema $schema;
	private StepProcessorFactory $step_factory;
	public function __construct( Schema $schema, StepProcessorFactory $step_factory = null) {
		$this->schema = $schema;
		if (!$schema->validate()) {
			// throw exception here;
		}
		if ($step_factory === null) {
			$step_factory = new StepProcessorFactory($schema);
		}

		$this->step_factory = $step_factory;
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

		foreach ( $this->schema->get_steps() as $stepSchema ) {
			$stepProcessor = $this->step_factory->create_from_name($stepSchema->step);
			// test code
			if (! $stepProcessor instanceof InstallPlugins) {
				continue;
			}

			if ( ! $stepProcessor instanceof StepProcessor ) {
				$result->add_error("Unable to create step processor for {$stepSchema->step}");
			}

			$result[] = $stepProcessor->process( $stepSchema );
		}

		return $results;
	}
}
