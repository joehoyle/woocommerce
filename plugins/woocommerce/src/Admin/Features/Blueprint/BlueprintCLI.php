<?php

use Automattic\WooCommerce\Admin\Features\Blueprint\CLICommands\Import;

class BlueprintCLI {
	public function __construct() {
		// @todo is this even allowed here? or does it have any sideeffect for other commands?
	}
	public static function register_commands() {
		WP_CLI::add_command( 'wc blueprint import', function($args) {
			$import = new Import($args[0]);
			$import->run();

		}, array(
			'synopsis' => [
				[
					'type' => 'positional',
					'name' => 'schema-path',
					'optional' => false,
				],
			],
			'when' => 'after_wp_load',
		));
	}

}
