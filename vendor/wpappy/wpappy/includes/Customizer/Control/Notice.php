<?php

namespace Wpappy_1_0_5\Customizer\Control;

defined( 'ABSPATH' ) || exit;

/**
 * Type: 'notice'.
 */
class Notice extends \WP_Customize_Control {

	/** @ignore */
	public $type = 'wpappy_notice';

	protected function render_content() {
		$allowed_html = array(
			'a'      => array(
				'href'   => array(),
				'title'  => array(),
				'class'  => array(),
				'target' => array(),
			),
			'b'      => array(),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'i'      => array(
				'class' => array(),
			),
			'span'   => array(
				'class' => array(),
			),
			'code'   => array(),
		);

		?>
		<div class="wpappy-notice">
			<?php if ( ! empty( $this->label ) ) { ?>
				<span class="wpappy-notice__title"><?php echo esc_html( $this->label ); ?></span>
			<?php } ?>
			<?php if ( ! empty( $this->description ) ) { ?>
				<span class="wpappy-notice__description customize-control-description">
							<?php
							echo wp_kses(
								$this->description,
								$allowed_html
							);
							?>
						</span>
			<?php } ?>
		</div>
		<?php
	}
}
