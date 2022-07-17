<?php

namespace Wpappy_1_0_6\Setting\Control;

defined( 'ABSPATH' ) || exit;

/**
 * Type: 'text', 'number', 'email', 'tel'.
 *
 * ### Arguments
 * - `'placeholder'` - field placeholder.
 * - `'width'` - width of the field in pixels. Default: `300`, `90` for `'number'`.
 */
class Text extends Control {

	/** @ignore */
	public static function render( string $type, string $name, /* mixed */ $value, ?string $title, array $args ): void {
		?>
		<div class="wpappy-control-text">
			<input
				type="<?php echo esc_attr( $type ); ?>"
				id="<?php echo esc_attr( $name ); ?>"
				name="<?php echo esc_attr( $name ); ?>"
				value="<?php echo esc_attr( $value ); ?>"
				<?php
				static::placeholder( $args );
				static::required( $args );
				?>
				style="<?php static::width( $args, $type ); ?>"
			>
			<?php static::render_description( $args ); ?>
		</div>
		<?php
	}

	protected static function width( array $args, ?string $type = null ): void {
		$default = 'number' === $type ? 90 : 300;

		echo 'width:' . esc_attr( $args['width'] ?? $default ) . 'px;';
	}
}
