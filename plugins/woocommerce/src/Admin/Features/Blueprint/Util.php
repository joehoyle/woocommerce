<?php

namespace Automattic\WooCommerce\Admin\Features\Blueprint;

class Util {
	public static function snake_to_camel($string) {
		// Split the string by underscores
		$words = explode('_', $string);

		// Capitalize the first letter of each word
		$words = array_map('ucfirst', $words);

		// Join the words back together
		return implode('', $words);
	}

	public static function camel_to_snake($input) {
		// Replace all uppercase letters with an underscore followed by the lowercase version of the letter
		$pattern = '/([a-z])([A-Z])/';
		$replacement = '$1_$2';
		$snake = preg_replace($pattern, $replacement, $input);

		// Replace spaces with underscores
		$snake = str_replace(' ', '_', $snake);

		// Convert the entire string to lowercase
		return strtolower($snake);
	}
}
