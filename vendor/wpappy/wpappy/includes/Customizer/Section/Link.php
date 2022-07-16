<?php

namespace Wpappy_1_0_5\Customizer\Section;

defined( 'ABSPATH' ) || exit;

/**
 * Type: 'link'.
 */
class Link extends \WP_Customize_Section {

	/** @ignore */
	public $type = 'wpappy_link';

	/** @ignore */
	public $link = '';

	/** @ignore */
	public $target = '';

	protected function render() {
		?>
		<li id="accordion-section-<?php echo esc_attr( $this->id ); ?>" class="wpappy-section-link accordion-section control-section control-section-<?php echo esc_attr( $this->type ); ?>">
			<a href="<?php echo esc_url( $this->link ); ?>" <?php echo esc_html( $this->target ? ( 'target="' . $this->target . '"' ) : '' ); ?>>
				<h3 class="accordion-section-title" tabindex="0">
					<?php echo esc_html( $this->title ); ?>
				</h3>
			</a>
		</li>
		<?php
	}
}
