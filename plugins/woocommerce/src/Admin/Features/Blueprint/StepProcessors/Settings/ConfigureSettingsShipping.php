<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessors\Settings;

use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessor;
use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessorResult;

class ConfigureSettingsShipping implements StepProcessor {
	public function process( $schema ): StepProcessorResult {
		return StepProcessorResult::success(self::class);
	}
}
