<?php

namespace Wpappy_1_0_4\Core\Type;

use Wpappy_1_0_4\Core\Feature;

defined( 'ABSPATH' ) || exit;

class Str extends Feature {

	public function to_camelcase( string $text, string $separator = '_' ): string {
		return array_reduce(
			explode( $separator, $text ),
			function ( string $result, string $item ): string {
				return empty( $result ) ? $item : $result . ucfirst( $item );
			},
			''
		);
	}

	public function generate_random_str( int $length = 16, string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' ): string {
		$pieces = array();
		$max    = mb_strlen( $keyspace, '8bit' ) - 1;

		for ( $i = 0; $i < $length; ++ $i ) {
			$pieces[] = $keyspace[ random_int( 0, $max ) ];
		}

		return implode( '', $pieces );
	}
}
