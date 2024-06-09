<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint\Exporters;

class TaxRatesExporter implements Exporter {

	public function export() {
		global $wpdb;

		// @todo check to see if we already have a DAO for taxes.
		$rates = $wpdb->get_results("
			SELECT *
			FROM {$wpdb->prefix}woocommerce_tax_rates as tax_rates
		", ARRAY_A);

		return $rates;
	}

	public function export_as_step_configuration() {
		return array(
			'step' => 'configureTaxRates',
			'rates' => $this->export()
		);
	}
}
