<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators;

class OrgPluginLocator implements PluginLocator {
	public function locate( $slug ) {
		return $slug;
	}

	public function get_supported_resource() {
		return 'wordpress.org/plugins';
	}
}
