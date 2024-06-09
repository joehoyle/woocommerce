<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint\Exporters;

class CoreProfilerSettingsExporter implements Exporter {
	public function export() {
		$onboarding_profile = get_option('woocommerce_onboarding_profile', array());
	    return array(
			'blogname' => get_option('blogname'),
		    "woocommerce_allow_tracking"=> true,
			"industry"=> $onboarding_profile['industry'] ?? array(),
			"business_choice"=> $onboarding_profile['business_choice'] ?? '',
			"store_email"=> $onboarding_profile['store_email'] ?? ''
	    );
	}
	public function export_as_step_configuration() {
		return array(
			'step' => 'configureCoreProfiler',
			'values' => $this->export()
		);
	}
}
