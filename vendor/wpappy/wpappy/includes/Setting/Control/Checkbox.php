<?php

namespace Wpappy_1_0_4\Setting\Control;

defined( 'ABSPATH' ) || exit;

/**
 * Type: 'checkbox'.
 *
 * ### Arguments
 * - `'options'` - associative array of the checkbox options.
 */
class Checkbox extends Control {

	/** @ignore */
	public static function render( string $type, string $name, /* array|string|null */ $value, ?string $title, array $args ): void { // phpcs:ignore
		?>
		<div class="wpappy-control-checkbox">
			<fieldset id="<?php echo esc_attr( $name ); ?>" required>
				<?php
				if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
					foreach ( $args['options'] as $option_key => $option_title ) {
						?>
						<div class="wpappy-control-checkbox__option">
							<label>
								<input
									type="checkbox"
									name="<?php echo esc_attr( "{$name}[$option_key]" ); ?>"
									<?php static::checked( $option_key, $value ); ?>
								>
								<?php echo wp_kses_post( is_array( $option_title ) ? ( $option_title[0] ?? '' ) : $option_title ); ?>
							</label>
							<?php if ( is_array( $option_title ) && isset( $option_title[1] ) ) { ?>
								<div class="wpappy-description">
									<?php echo wp_kses_post( $option_title[1] ); ?>
								</div>
							<?php } ?>
						</div>
						<?php
					}
				}
				?>
			</fieldset>
			<?php static::render_description( $args ); ?>
		</div>
		<?php
	}

	protected static function checked( string $option_key, /* array|string|null */ $value ): void { // phpcs:ignore
		if ( is_array( $value ) && in_array( $option_key, array_keys( $value ), true ) ) {
			echo 'checked';
		}
	}
}
