<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

use Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators\PluginLocator;

class PluginsStorage {
	/**
	 * @var PluginLocator[]
	 */
	protected array $locators = array();
	public function add_locator(PluginLocator $locator) {
		$supported_resource = $locator->get_supported_resource();
		if (!isset($this->locators[$supported_resource])) {
			$this->locators[$supported_resource] = array();
		}
		$this->locators[$supported_resource][] = $locator;
	}

	public function locate($slug, $resource) {
		if (!isset($this->locators[$resource])) {
			return false;
		}
		$locators = $this->locators[$resource];
	    foreach ($locators as $locator) {
			if ($found = $locator->locate($slug)) {
				return $found;
			}
	    }

		return false;
	}
}
