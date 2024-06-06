<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators;

interface PluginLocator {
	public function get_supported_resource();
	public function locate($slug);
}
