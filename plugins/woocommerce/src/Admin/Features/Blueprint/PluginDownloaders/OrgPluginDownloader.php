<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint\PluginLocators;

class OrgPluginDownloader implements PluginDownloader {
	public function download( $slug ) {
		return $this->download_url($this->get_download_link($slug));
	}

	protected function download_url($url) {
		return download_url($url);
	}

	protected function get_download_link($slug) {
		$info = plugins_api(
			'plugin_information',
			array(
				'slug'   => $slug,
				'fields' => array(
					'sections' => false,
				),
			)
		);

		return $info->download_link;
	}

	public function get_supported_resource(): string {
		return 'wordpress.org/plugins';
	}
}
