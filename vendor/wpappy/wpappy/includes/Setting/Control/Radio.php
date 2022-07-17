<?php

namespace Wpappy_1_0_6\Setting\Control;

defined( 'ABSPATH' ) || exit;

/**
 * Type: 'radio'.
 *
 * ### Arguments
 * - `'options'` - associative array of the radio options.
 */
class Radio extends Control {

	/** @ignore */
	public static function render( string $type, string $name, /* array|string|null */ $value, ?string $title, array $args ): void { // phpcs:ignore
		?>
		<div class="wpappy-control-radio">
			<fieldset id="<?php echo esc_attr( $name ); ?>">
				<?php
				if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
					foreach ( $args['options'] as $option_key => $option_title ) {
						?>
						<div class="wpappy-control-radio__option">
							<label>
								<input
									type="radio"
									name="<?php echo esc_attr( $name ); ?>"
									value="<?php echo esc_attr( $option_key ); ?>"
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

	protected static function checked( string $option_key, ?string $value ): void {
		if ( $option_key === $value ) {
			echo 'checked';
		}
	}
}
