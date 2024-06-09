<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

class ExportBlueprintSchema {
	protected array $default_exporters = array(
//      ExportPluginList::class,
//		ExportThemeList::class,
		ExportCoreProfilerSettings::class,
		ExportTaxRates::class,
		ExportShipping::class,
		ExportSettings::class,
		ExportPaymentGateways::class,
	);

	protected array $additional_exporters = array();

	public function add_exporter(ExportsBlueprintStep $exporter) {
	    $this->additional_exporters[] = $exporter;
	}

	public function export() {
		$schema = array(
			'steps' => array(),
		);
		$exporters = array_map(function($exporter_class) {
			return new $exporter_class;
		}, $this->default_exporters);

		$exporters = array_merge($exporters, $this->additional_exporters);

		foreach ($exporters as $exporter) {
			$schema['steps'][] = $exporter->export_as_step_configuration();
		}
		return $schema;
	}
}
