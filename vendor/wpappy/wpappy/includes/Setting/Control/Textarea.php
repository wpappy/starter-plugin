<?php

namespace Wpappy_1_0_6\Setting\Control;

defined( 'ABSPATH' ) || exit;

/**
 * Type: 'textarea'.
 *
 * ### Arguments
 * - `'placeholder'` - field placeholder.
 * - `'width'` - width of the field in pixels. Default: `300`.
 */
class Textarea extends Control {

	/** @ignore */
	public static function render( string $type, string $name, /* mixed */ $value, ?string $title, array $args ): void {
		?>
		<div class="wpappy-control-textarea">
			<textarea
				id="<?php echo esc_attr( $name ); ?>"
				name="<?php echo esc_attr( $name ); ?>"
				<?php static::placeholder( $args ); ?>
				style="<?php static::width( $args ); ?>"
			><?php echo esc_html( $value ); ?></textarea>
			<?php static::render_description( $args ); ?>
		</div>
		<?php
	}
}
