<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

use Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators\PluginDownloader;

class PluginsStorage {
	/**
	 * @var PluginDownloader[]
	 */
	protected array $downloaders = array();
	public function add_locator(PluginDownloader $downloader) {
		$supported_resource = $downloader->get_supported_resource();
		if (!isset($this->downloaders[$supported_resource])) {
			$this->downloaders[$supported_resource] = array();
		}
		$this->downloaders[$supported_resource][] = $downloader;
	}

	public function is_supported_resource($resource) {
	    foreach ($this->downloaders as $downloader) {
			if ($downloader->get_supported_resource() === $resource) {
				return true;
			}
	    }
		return false;
	}

	public function download($slug, $resource) {
		if (!isset($this->downloaders[$resource])) {
			return false;
		}
		$downloaders = $this->downloaders[$resource];
	    foreach ($downloaders as $downloader) {
			if ($found = $downloader->download($slug)) {
				return $found;
			}
	    }

		return false;
	}
}
