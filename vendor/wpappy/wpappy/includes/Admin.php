<?php

namespace Wpappy_1_0_5;

defined( 'ABSPATH' ) || exit;

/**
 * Features used only for the admin panel.
 */
class Admin extends Feature {

	protected $notice;

	/**
	 * Manage admin notices.
	 */
	public function notice(): Admin\Notice {
		return $this->get_feature( $this->app, $this->core, 'notice', Admin\Notice::class );
	}

	public function get_url( string $slug = '', ?int $blog_id = null ): string {
		return get_admin_url( $blog_id, $slug ? "$slug.php" : '' );
	}
}
