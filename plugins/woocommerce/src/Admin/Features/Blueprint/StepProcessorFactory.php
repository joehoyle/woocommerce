<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

use Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators\LocalPluginDownloader;
use Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators\OrgPluginDownloader;
use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessors\InstallPlugins;

/**
 * Simple factory to create step processors.
 */
class StepProcessorFactory {
	private Schema $schema;
	public function __construct(Schema $schema)
	{
	    $this->schema = $schema;
	}
	public function create_from_name($name) {
		$stepProcessor = __NAMESPACE__ . '\\StepProcessors\\' . Util::snake_to_camel($name);
		if (!class_exists($stepProcessor)) {
			// throw error
			return null;
		}

		switch ($name) {
			case 'installPlugins':
				return $this->create_install_plugins_processor();
			default:
				return new $stepProcessor;
		}
	}

	private function create_install_plugins_processor() {
		$storage = new PluginsStorage();
		$storage->add_downloader(new OrgPluginDownloader());

		if ($this->schema instanceof ZipSchema) {
			$storage->add_downloader( new LocalPluginDownloader($this->schema->get_unzip_path()) );
		}

		return new InstallPlugins($storage);
	}
}
