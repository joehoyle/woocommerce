<?php

use Automattic\WooCommerce\Admin\Features\Blueprint\Cli\Export;
use Automattic\WooCommerce\Admin\Features\Blueprint\Cli\ExportBlueprint;
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
		WP_CLI::add_command( 'wc blueprint import', function($args, $assoc_args) {
			$import = new Import($args[0]);
			$import->run($assoc_args);
		}, array(
			'synopsis' => [
				[
					'type' => 'positional',
					'name' => 'schema-path',
					'optional' => false,
				],
				[
					'type' => 'assoc',
					'name' => 'message',
					'optional' => true,
					'options' => ['all', 'error', 'info', 'debug'],
				],
			],
			'when' => 'after_wp_load',
		));

		WP_CLI::add_command( 'wc blueprint export', function($args, $assoc_args) {
			$import = new Export($args[0]);
			$import->run($assoc_args);
		}, array(
			'synopsis' => [
				[
					'type' => 'positional',
					'name' => 'save-to',
					'optional' => false,
				],
			],
			'when' => 'after_wp_load',
		));
	}
}
