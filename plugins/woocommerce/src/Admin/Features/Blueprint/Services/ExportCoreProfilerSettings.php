<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

class ExportCoreProfilerSettings implements ExportsBlueprintStep {
	public function export() {
	    return array();
	}
	public function export_as_step_configuration() {
		return array(
			'step' => 'configureCoreProfiler',
			'plugins' => $this->export()
		);
	}
}
