<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

class ZipSchema extends Schema {
	protected $unzip_path;
	public function __construct($zip_path, $unzip_path = null) {
		$this->init_filesystem();
		$this->unzip_path = $unzip_path ?? wp_upload_dir()['path'];
		$unzip = $this->unzip($zip_path, $this->unzip_path);
		if (!$unzip) {
			throw new \Exception("Unable to unzip the file to {$zip_path}. Please check directory permission.");
		}

		if (!$this->validate_unzipped_files()) {
			//invalid zipfile provided.
			// @todo needs better message.
			throw new \Exception('Invalid zipfile provided.');
		}
		$this->schema = json_decode(file_get_contents($this->unzip_path.'/woo-blueprint.json'));
	}

	public function get_unzip_path() {
		return $this->unzip_path;
	}

	protected function unzip($zip_path, $to) {
		$unzip =  \unzip_file($zip_path, $to);
		if (!$unzip) {
			throw new \Exception("Unable to unzip the file to {$zip_path}. Please check directory permission.");
		}

		return $unzip;
	}

	protected function init_filesystem() {
		if ( !function_exists('WP_Filesystem')) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		\WP_Filesystem();
	}

	private function validate_unzipped_files() {
		return true;
	}
}
