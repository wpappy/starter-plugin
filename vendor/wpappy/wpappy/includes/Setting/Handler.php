<?php

namespace Wpappy_1_0_4\Setting;

use Wpappy_1_0_4\App;
use Wpappy_1_0_4\Core;
use Wpappy_1_0_4\Feature;

defined( 'ABSPATH' ) || exit;

class Handler extends Feature {

	protected $action_key;
	protected $storage;
	protected $error_notice;
	protected $success_notice;

	public function __construct( App $app, Core $core ) {
		parent::__construct( $app, $core );

		$this->action_key = $app->get_key( 'setting_handler' );
	}

	public function get_url(): string {
		return add_query_arg(
			array( $this->action_key => true ),
			$this->app->http()->get_current_url()
		);
	}

	public function setup( Storage $storage, string $error_notice, string $success_notice ) {
		if ( empty( $_GET[ $this->action_key ] ) ) { // phpcs:ignore
			return;
		}

		$this->storage        = $storage;
		$this->error_notice   = $error_notice;
		$this->success_notice = $success_notice;

		$this->handle();
	}

	protected function handle(): void {
		$page = $this->storage->get_active_page();

		if (
			empty( $page ) ||
			empty( $_POST[ "{$page}_nonce" ] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ "{$page}_nonce" ] ) ), $page )
		) {
			$this->finalize( $this->error_notice );

			return;
		}

		$settings_data = $this->get_settings_data();
		$tab           = $this->storage->get_active_tab();
		$sub_tab       = $this->storage->get_active_sub_tab();

		if ( $settings_data ) {
			$this->update_settings( $settings_data );
		}

		$this->core->hook()->do_action( 'setting_custom_handler', $sub_tab, $tab, $page );
		$this->finalize( $this->success_notice, 'success' );
	}

	protected function get_settings_data(): array {
		$settings  = array();
		$page_data = $this->storage->get_page(
			$this->storage->get_active_page( true )
		);

		if ( empty( $page_data['children'] ) ) {
			return $settings;
		}

		$tab     = $this->storage->get_active_tab();
		$sub_tab = $this->storage->get_active_sub_tab();

		foreach ( $page_data['children'] as $tab_key => $tab_data ) {
			if ( $tab && $tab_key !== $tab ) {
				continue;
			}

			if ( empty( $tab_data['children'] ) ) {
				return $settings;
			}

			foreach ( $tab_data['children'] as $sub_tab_key => $sub_tab_data ) {
				if ( $sub_tab && $sub_tab_key !== $sub_tab ) {
					continue;
				}

				if ( empty( $sub_tab_data['children'] ) ) {
					return $settings;
				}

				foreach ( $sub_tab_data['children'] as $box_data ) {
					if (
						isset( $box_data['object'] ) &&
						is_a( $box_data['object'], Setting::class )
					) {
						$settings[] = array(
							'key'               => $box_data['object']->get_key(),
							'sanitize_callback' => $box_data['object']->get_sanitize_callback(),
						);
					}

					if ( empty( $box_data['children'] ) ) {
						continue;
					}

					foreach ( $box_data['children'] as $setting_data ) {
						if (
							isset( $setting_data['object'] ) &&
							is_a( $setting_data['object'], Setting::class )
						) {
							$settings[] = array(
								'key'               => $setting_data['object']->get_key(),
								'sanitize_callback' => $setting_data['object']->get_sanitize_callback(),
							);
						}
					}
				}

				break;
			}

			break;
		}

		return $settings;
	}

	protected function update_settings( array $settings_data ): void {
		foreach ( $settings_data as $setting_data ) {
			if ( ! isset( $_POST[ $setting_data['key'] ] ) ) { // phpcs:ignore
				return;
			}

			$value      = null;
			$post_value = $_POST[ $setting_data['key'] ]; // phpcs:ignore

			if ( is_array( $post_value ) ) {
				foreach ( $post_value as $option_key => $option_value ) {
					$value[ $option_key ] = $this->sanitize_value( $setting_data['sanitize_callback'], $option_value );
				}
			} else {
				$value = $this->sanitize_value( $setting_data['sanitize_callback'], $post_value );
			}

			update_option( $setting_data['key'], $value );
		}
	}

	protected function sanitize_value( /* array|string|null */ $sanitize_callback, /* mixed */ $value ) /* mixed */ { // phpcs:ignore
		$value = wp_unslash( $value );

		if ( $sanitize_callback ) {
			return call_user_func( $sanitize_callback, $value );
		}

		return $value;
	}

	protected function finalize( string $message, string $level = 'error' ): bool {
		$this->app->http()->redirect(
			remove_query_arg(
				$this->action_key,
				$this->app->http()->get_current_url()
			),
			function () use ( $message, $level ): void {
				$this->app->admin()->notice()->add_transient( $message, $level );
			}
		);

		return true;
	}
}
