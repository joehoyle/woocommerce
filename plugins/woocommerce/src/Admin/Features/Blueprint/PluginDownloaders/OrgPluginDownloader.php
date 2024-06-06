<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators;

class OrgPluginDownloader implements PluginDownloader {
	public function download( $slug ) {
		$info = plugins_api(
			'plugin_information',
			array(
				'slug'   => $slug,
				'fields' => array(
					'sections' => false,
				),
			)
		);

		$path = download_url($info->download_link);
		return $path;
	}

	public function get_supported_resource() {
		return 'wordpress.org/plugins';
	}
}
