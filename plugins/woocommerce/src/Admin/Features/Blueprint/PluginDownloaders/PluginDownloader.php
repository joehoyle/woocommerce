<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators;

interface PluginDownloader {
	public function get_supported_resource();

	/**
	 * @param $slug
	 *
	 * @return string downloaded local path.
	 */
	public function download($slug);
}
