<?php

use Automattic\WooCommerce\Admin\Features\Blueprint\Cli\Import;

/**
 * Class BlueprintCLI.
 *
 * This class is included and execute from WC_CLI(class-wc-cli.php) to register
 * WP CLI commands.
 *
 */
class BlueprintCLI {
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
