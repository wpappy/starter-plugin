<?php

namespace Wpappy_1_0_5\Admin;

use Wpappy_1_0_5\App;
use Wpappy_1_0_5\Core;
use Wpappy_1_0_5\Feature;

defined( 'ABSPATH' ) || exit;

/**
 * Manage admin notices.
 */
class Notice extends Feature {

	protected $transient_key;

	/** @ignore */
	public function __construct( App $app, Core $core ) {
		parent::__construct( $app, $core );

		$this->transient_key = $app->get_key( 'transient_admin_notices' );
	}

	protected function get_transients(): array {
		return get_option( $this->transient_key, array() );
	}

	protected function update_transients( array $notices ): App {
		update_option( $this->transient_key, $notices );

		return $this->app;
	}

	/**
	 * Add a notice to render it in the transient queue.
	 *
	 * @param string $level `'info'`, `'success'`, `'warning'` or `'error'`.
	 */
	public function add_transient( string $message, string $level = 'warning' ): App {
		if ( $this->validate_setup() ) {
			return $this->app;
		}

		$notices = $this->get_transients();

		$notices[ $level ][] = $message;

		$this->update_transients( $notices );

		return $this->app;
	}

	/**
	 * Render a notice.
	 *
	 * @param string $level `'info", `'success'`, `'warning'` or `'error'`.
	 */
	public function render( string $message, string $level = 'warning' ): App {
		if ( $this->validate_setup() ) {
			return $this->app;
		}

		$this->core->admin()->notice()->render( $message, $level );

		return $this->app;
	}

	/**
	 * Required.
	 */
	public function setup(): App {
		$this->add_setup_action(
			__FUNCTION__,
			function (): void {
				$this->render_transients();
			},
			1,
			true
		);

		return $this->app;
	}

	protected function render_transients(): void {
		$notices = $this->get_transients();

		if ( empty( $notices ) ) {
			return;
		}

		add_action(
			'admin_init',
			function () use ( $notices ): void {
				foreach ( $notices as $level => $messages ) {
					foreach ( $messages as $message ) {
						$this->render( $message, $level );
					}
				}

				$this->update_transients( array() );
			}
		);
	}
}
