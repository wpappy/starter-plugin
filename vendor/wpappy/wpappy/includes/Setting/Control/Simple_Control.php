<?php

namespace Wpappy_1_0_5\Setting\Control;

defined( 'ABSPATH' ) || exit;

abstract class Simple_Control {

	abstract public static function render( string $type, string $name, /* mixed */ $value, ?string $title, array $args ): void;

	protected static function render_description( array $args ): void {
		if ( ! empty( $args['description'] ) ) {
			?>
			<div class="wpappy-description">
				<?php echo wp_kses_post( $args['description'] ); ?>
			</div>
			<?php
		}
	}

	protected static function width( array $args, ?string $type = null ): void {
		echo 'width:' . esc_attr( $args['width'] ?? 300 ) . 'px;';
	}

	protected static function placeholder( array $args ): void {
		if ( isset( $args['placeholder'] ) ) {
			echo 'placeholder="' . esc_attr( $args['placeholder'] ) . '"';
		}
	}

	protected static function is_required( array $args ): bool {
		return isset( $args['required'] ) && true === $args['required'];
	}

	protected static function required( array $args ): void {
		if ( static::is_required( $args ) ) {
			echo 'required';
		}
	}
}
