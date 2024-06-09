<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

class ExportPaymentGateways implements ExportsBlueprintStep {

	public function export() {
		$payment_gateways = array();
		foreach (WC()->payment_gateways->payment_gateways() as $id => $payment_gateway) {
			$payment_gateways[$id] = array(
				'title'	=> $payment_gateway->get_title(),
				'description' => $payment_gateway->get_description(),
				'enabled' => $payment_gateway->is_available(),
			);
		}

		return $payment_gateways;
	}

	public function export_as_step_configuration() {
		return array(
			'step' => 'configurePaymentMethods',
			'payment_gateways' => $this->export()
		);
	}
}
