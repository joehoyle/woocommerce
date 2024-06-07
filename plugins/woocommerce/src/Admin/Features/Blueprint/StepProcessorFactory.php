<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

use Automattic\WooCommerce\Admin\Features\Blueprint\ResourceDownloaders\LocalThemeResourceDownloader;
use Automattic\WooCommerce\Admin\Features\Blueprint\ResourceDownloaders\OrgThemeResourceDownloader;
use Automattic\WooCommerce\Admin\Features\Blueprint\ResourceDownloaders\LocalPluginResourceDownloader;
use Automattic\WooCommerce\Admin\Features\Blueprint\ResourceDownloaders\OrgPluginResourceDownloader;

use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessors\InstallPlugins;
use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessors\InstallThemes;

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
			case 'installThemes':
				return $this->create_install_themes_processor();
			default:
				return new $stepProcessor;
		}
	}

	private function create_install_plugins_processor() {
		$storage = new ResourceStorage();
		$storage->add_downloader(new OrgPluginResourceDownloader());

		if ($this->schema instanceof ZipSchema) {
			$storage->add_downloader( new LocalPluginResourceDownloader($this->schema->get_unzip_path()) );
		}

		return new InstallPlugins($storage);
	}

	private function create_install_themes_processor() {
		$storage = new ResourceStorage();
		$storage->add_downloader(new OrgThemeResourceDownloader());
		if ($this->schema instanceof ZipSchema) {
			$storage->add_downloader( new LocalThemeResourceDownloader($this->schema->get_unzip_path()) );
		}

		return new InstallThemes($storage);
	}
}
