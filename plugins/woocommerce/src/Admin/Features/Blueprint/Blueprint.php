<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

class Blueprint {
	private $schema_path;
	public function __construct( $schema_path ) {
		$this->schema_path = $schema_path;
	}

	protected function get_schema_content() {
		return json_decode(file_get_contents($this->schema_path));
	}

	public static function create_from_json($json_path) {
		return new self($json_path);
	}

	public static function crate_from_zip($zip_path) {
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		\WP_Filesystem();
		$unzip_path = wp_upload_dir()['path'];
		$unzip = \unzip_file($zip_path, $unzip_path);

		if (!$unzip) {
			throw new \Exception("Unable to unzip the file to {$unzip_path}. Please check directory permission.");
		}

		return new self($unzip_path.'/woo-blueprint.json');
	}

	/**
	 * @return StepProcessorResult[]
	 */
	public function process() {
		if ( ! $this->validate() ) {
			// @todo Implement JSON Schema validation here.
			return false;
		}

		$schema = $this->get_schema_content();
		/**
		 * @var StepProcessorResult[]
		 */
		$results = array();
		foreach ( $schema->steps as $stepSchema ) {
			$stepProcessor = __NAMESPACE__ . '\\StepProcessors\\' . ucfirst( $stepSchema->step );
			if ( class_exists( $stepProcessor ) ) {
				/**
				 * @var $stepProcessor StepProcessor
				 * @todo Use container.
				 */
				$stepProcessor = new $stepProcessor();
				$results[] = $stepProcessor->process($stepSchema);
			}
		}

		return $results;
	}

	private function validate() {
		return true;
	}
}
