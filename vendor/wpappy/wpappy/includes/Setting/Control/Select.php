<?php

namespace Wpappy_1_0_6\Setting\Control;

defined( 'ABSPATH' ) || exit;

/**
 * Type: 'select'.
 *
 * ### Arguments
 * - `'placeholder'` - field placeholder.
 * - `'options'` - array of the select options.
 * - `'multiple'` - pass `true` to enable multiple selection.
 * - `'width'` - width of the field in pixels. Default: `300`.
 */
class Select extends Control {

	/** @ignore */
	public static function render( string $type, string $name, /* array|string|null */ $value, ?string $title, array $args ): void { // phpcs:ignore
		?>
		<div class="wpappy-control-select">
			<select
				id="<?php echo esc_attr( $name ); ?>"
				name="<?php echo esc_attr( $name ) . ( static::is_multiple( $args ) ? '[]' : '' ); ?>"
				<?php
				echo static::is_multiple( $args ) ? 'multiple' : '';
				static::multiple_placeholder( $args );
				static::required( $args );
				?>
				style="<?php static::width( $args ); ?>"
			>
				<?php
				static::placeholder( $args );

				if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
					foreach ( $args['options'] as $option_key => $option_title ) {
						?>
						<option
							value="<?php echo esc_attr( $option_key ); ?>"
							<?php static::selected( $option_key, $value ); ?>
						>
							<?php echo esc_html( $option_title ); ?>
						</option>
						<?php
					}
				}
				?>
			</select>
			<?php static::render_description( $args ); ?>
		</div>
		<?php
	}

	protected static function is_multiple( array $args ): bool {
		return isset( $args['multiple'] ) && true === $args['multiple'];
	}

	protected static function selected( string $option_key, /* array|string|null */ $value ): void { // phpcs:ignore
		echo is_array( $value ) ?
			( in_array( $option_key, $value, true ) ? 'selected' : '' ) :
			( $option_key === $value ? 'selected' : '' );
	}

	protected static function multiple_placeholder( array $args ): void {
		if ( isset( $args['placeholder'] ) && static::is_multiple( $args ) ) {
			echo 'data-placeholder="' . esc_attr( $args['placeholder'] ) . '"';
		}
	}

	protected static function placeholder( array $args ): void {
		if ( isset( $args['placeholder'] ) && ! static::is_multiple( $args ) ) {
			?>
			<option value="" disabled selected hidden>
				<?php echo esc_html( $args['placeholder'] ); ?>
			</option>
			<?php
		}
	}
}
