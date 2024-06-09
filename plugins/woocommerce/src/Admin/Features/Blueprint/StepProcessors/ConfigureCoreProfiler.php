<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessors;

use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessor;
use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessorResult;
use Automattic\WooCommerce\Admin\Features\Blueprint\Util;
use WC_Tax;

class ConfigureCoreProfiler implements StepProcessor {
	public function process($schema): StepProcessorResult {
		// Update standalone options.
		foreach (array('blogname', 'woocommerce_allow_tracking') as $standalone_option) {
			if (isset($schema->values->{$standalone_option})) {
				update_option($standalone_option, $schema->values->{$standalone_option});
			}
		}

		// Update onboarding profile values.
		$onboarding_profile = get_option('woocommerce_onboarding_profile', array());
		foreach (array('industry', 'business_choice', 'store_email') as $profile_item_key) {
			if (isset($schema->values->{$profile_item_key})) {
				$onboarding_profile[$profile_item_key] = $schema->values->{$profile_item_key};
			}
		}

		update_option('woocommerce_onboarding_profile', $onboarding_profile);

		return StepProcessorResult::success('ConfigureOnboardingProfile');
	}
}
