<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

abstract class Schema {
	protected object $schema;
	public function get_steps() {
		return $this->schema->steps;
	}
}
