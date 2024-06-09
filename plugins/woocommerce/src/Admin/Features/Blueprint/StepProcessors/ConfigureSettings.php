<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessors;

use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessor;
use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessorResult;
use Automattic\WooCommerce\Admin\Features\Blueprint\Util;
use Automattic\WooCommerce\Admin\Notes\Note;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use WC_Tax;

class ConfigureSettings implements StepProcessor {
	public function process($schema): StepProcessorResult {
		$result = StepProcessorResult::success('ConfigureSettings');

		foreach ($schema->values->options as $option) {
			if (is_object($option->value)) {
				$option->value = (array) $option->value;
			}
			$updated = update_option($option->id, $option->value);
			$updated && $result->add_debug("{$option->id} has been updated");

			if (!$updated) {
				$current_value = get_option($option->id);
				if ($current_value === $option->value) {
					$result->add_debug( "{$option->id} has not been updated because the current value is already up to date." );
				}
			}
		}
		return $result;
	}
}
