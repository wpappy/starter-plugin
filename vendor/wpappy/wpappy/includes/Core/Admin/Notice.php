<?php

namespace Wpappy_1_0_5\Core\Admin;

use Wpappy_1_0_5\Core\Feature;

defined( 'ABSPATH' ) || exit;

class Notice extends Feature {

	public function render( string $message, string $level = 'warning' ): void {
		add_action(
			'admin_notices',
			function () use ( $message, $level ): void {
				?>
				<div class="notice notice-<?php echo esc_attr( $level ); ?> is-dismissible" style="padding-top: 10px; padding-bottom: 10px;">
					<?php echo wp_kses_post( $message ); ?>
				</div>
				<?php
			}
		);
	}
}
