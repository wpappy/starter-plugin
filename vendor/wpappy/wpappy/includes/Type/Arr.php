<?php

namespace Wpappy_1_0_5\Type;

use Wpappy_1_0_5\Feature;

defined( 'ABSPATH' ) || exit;

/**
 * Helpers for working with arrays.
 */
class Arr extends Feature {

	public function map_associative( callable $callback, array $array ): array {
		$result = array();

		foreach ( $array as $key => $val ) {
			$result[ $key ] = $callback( $key, $val );
		}

		return $result;
	}

	public function insert_to_position( array $value, int $position, array $array ): array {
		if ( empty( $array ) ) {
			return $value;
		}

		return array_merge(
			array_slice( $array, 0, $position ),
			$value,
			array_slice( $array, $position )
		);
	}
}
