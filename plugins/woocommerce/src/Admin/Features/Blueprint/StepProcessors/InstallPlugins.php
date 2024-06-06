<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessors;

use Automattic\WooCommerce\Admin\Features\Blueprint\PluginsStorage;
use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessor;
use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessorResult;
use Plugin_Upgrader;

class InstallPlugins implements StepProcessor {
	private PluginsStorage $storage;
	public function __construct(PluginsStorage $storage) {
		$this->storage = $storage;
	}
	public function process($schema): StepProcessorResult {
		$result = StepProcessorResult::success();
		foreach ($schema->plugins as $plugin) {
			switch ($plugin->resource) {
				case "wordpress.org/plugins":
					$this->install_from_org($plugin);
					break;
				case "self/plugins":
					$this->install_from_self($plugin);
					break;
				default:
					$result->add_error("Invalid resource type for {$plugin->slug}");
					break;
			}

		}

		return $result;
	}

	private function install_from_org( $plugin ) {
		$slug = $this->storage->locate($plugin->slug, 'wordpress.org/plugins');
//		PluginsHelper::install_plugins( array( $slug ) );
//		if ($plugin->activate === true) {
//			PluginsHelper::activate_plugins( array( $slug ) );
//		}
	}
	private function install_from_self( $plugin ) {

		include_once ABSPATH . '/wp-admin/includes/class-wp-upgrader.php';
		include_once ABSPATH . '/wp-admin/includes/class-plugin-upgrader.php';

		$path = $this->storage->locate($plugin->slug, 'self/plugins');
		$upgrader = new \Plugin_Upgrader( new \Automatic_Upgrader_Skin() );
		$result   = $upgrader->install( $path );

	}
}
