<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

class Blueprint {
	public function __construct() {
		add_filter('woocommerce_admin_rest_controllers', function(array $controllers) {
			$controllers[] = 'Automattic\WooCommerce\Admin\API\Blueprint';
			return $controllers;
		});
	}
}
