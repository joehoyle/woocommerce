<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessors;

use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessor;
use Automattic\WooCommerce\Admin\Features\Blueprint\StepProcessorResult;
use Automattic\WooCommerce\Admin\Features\Blueprint\Util;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use WC_Tax;

class ConfigureSettings implements StepProcessor {
	public function process($schema): StepProcessorResult {
		$result = StepProcessorResult::success('ConfigureSettings');
		$pages = json_decode(json_encode($schema->pages), true);
		$settings = $this->filter_setting_items($pages);

		foreach ($settings as $setting) {
			$updated = update_option($setting['id'], $setting['value']);
			if ($updated) {
				$result->add_debug("{$setting['id']} has been updated");
			} else {
				$current_value = get_option($setting['id']);
				if ($current_value === $setting['value']) {
					$result->add_debug( "{$setting['id']} has not been updated because the current value is already up to date." );
				}
			}
		}

		return $result;
	}

	function filter_setting_items($array) {
		$result = [];
		foreach ($array as $item) {
			if (is_array($item)) {
				if (isset($item['id'])) {
					$result[] = $item;
				}
				// Recursively search in nested arrays
				$nestedResult = $this->filter_setting_items($item);
				if (!empty($nestedResult)) {
					$result = array_merge($result, $nestedResult);
				}
			}
		}
		return $result;
	}
}
