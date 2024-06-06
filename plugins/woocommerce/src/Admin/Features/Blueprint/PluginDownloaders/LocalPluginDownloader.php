<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators;

class LocalPluginDownloader implements PluginDownloader {
	private array $paths = [];
	public function __construct($path) {
		$this->paths[] = $path;
	}

	/**
	 * Local plugins are already included (downloaded) in the zip file.
	 *
	 * @param $slug
	 *
	 * @return false|string
	 */
	public function download( $slug ) {
		foreach ($this->paths as $path) {
			$full_path = $path.'/plugins/'.$slug.'.zip';
			if (is_file($full_path)) {
				return $full_path;
			}
		}
		return false;
	}

	public function get_supported_resource(): string {
		return 'self/plugins';
	}
}
