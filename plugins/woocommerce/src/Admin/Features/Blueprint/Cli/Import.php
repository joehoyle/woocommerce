<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint\Cli;

use Automattic\WooCommerce\Admin\Features\Blueprint\CliResultFormatter;
use Automattic\WooCommerce\Admin\Features\Blueprint\SchemaProcessor;

class Import {
	private $schema_path;
	public function __construct($schema_path) {
		$this->schema_path = $schema_path;
	}

	public function run()
	{
	    $blueprint = SchemaProcessor::crate_from_file($this->schema_path);
		$results = $blueprint->process();

		$result_formatter = new CliResultFormatter($results);
		$result_formatter->format('all');
	}
}
