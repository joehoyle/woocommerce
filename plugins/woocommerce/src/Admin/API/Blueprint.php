<?php



namespace Automattic\WooCommerce\Admin\API;

use Automattic\WooCommerce\Admin\Features\Blueprint\Blueprint as BlueprintService;
use Automattic\WooCommerce\Admin\Features\QuickConfig\QuickConfigService;

class Blueprint {
	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'blueprint';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'process';

	/**
	 * Register routes.
	 *
	 * @since 3.5.0
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'process' ),
					'permission_callback' => function () {
						return true;
					},
				),
			)
		);
	}

	public function process() {
		if ( !empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK ) {
			$uploaded_file = $_FILES['file']['tmp_name'];
			$is_zip = $_FILES['file']['type'] === 'application/zip';

//			if ( json_last_error() !== JSON_ERROR_NONE ) {
//				return new \WP_REST_Response(array(
//					'status' => 'error',
//					'message' => 'Invalid JSON data',
//				), 400);
//			}

			// @todo check for alloweed types -- json or zip only.
			if ($is_zip) {
				$blueprint = BlueprintService::crate_from_zip($uploaded_file);
			} else {
				$blueprint = BluePrintService::create_from_json($uploaded_file);
			}

			$paul = $blueprint->process();

			return new \WP_HTTP_Response( array(
				'status' => 'success',
				'message' => 'Data processed successfully',
				'data' => $paul,
			), 200 );
		}

		return new \WP_REST_Response(array(
			'status' => 'error',
			'message' => 'No file uploaded',
		), 400);
	}
}
