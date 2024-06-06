<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessors;

use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessor;
use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessorResult;
use Automattic\WooCommerce\Admin\Features\Blueprint\Util;
use WC_Tax;

class ConfigureOnboardingProfile implements StepProcessor {
	public function process($schema): StepProcessorResult {

		return StepProcessorResult::success(self::class);
	}

}
