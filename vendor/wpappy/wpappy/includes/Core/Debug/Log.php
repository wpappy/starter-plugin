<?php

namespace Wpappy_1_0_5\Core\Debug;

use Wpappy_1_0_5\Core;
use Wpappy_1_0_5\Core\Feature;

defined( 'ABSPATH' ) || exit;

class Log extends Feature {

	protected $key;
	protected $path;
	protected $delete_key;
	protected $capability = 'delete_posts';

	public function __construct( Core $core ) {
		parent::__construct( $core );

		$this->key        = $core->get_app_key( 'debug' );
		$this->path       = $core->media()->upload()->get_path( $this->key );
		$this->delete_key = "{$this->key}_delete";
	}

	protected function get_raw_path( string $path = '' ): string {
		return $this->path . ( $path ? DIRECTORY_SEPARATOR . $path : '' );
	}

	public function get_path( string $group = 'core' ): string {
		return $this->get_raw_path( "$group.log" );
	}

	public function add( string $message, string $level = 'warning', string $group = 'core' ): void {
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			include_once ABSPATH . 'wp-includes' . DIRECTORY_SEPARATOR . 'pluggable.php';
		}

		if ( ! current_user_can( $this->capability ) ) {
			return;
		}

		$this->create_dir();

		$group = fopen( $this->get_path( $group ), 'a' ); // phpcs:ignore

		$timestamp = gmdate( 'n/j/Y H:i:s' );
		$content   = "[$timestamp] $level: $message\n";

		fwrite( $group, $content ); // phpcs:ignore
		fclose( $group ); // phpcs:ignore
	}

	protected function create_dir(): void {
		if ( is_dir( $this->get_raw_path() ) ) {
			return;
		}

		mkdir( $this->get_raw_path(), 0755, true );

		$htaccess_path = $this->get_raw_path( '.htaccess' );

		if ( file_exists( $htaccess_path ) ) {
			return;
		}

		$htaccess = fopen( $htaccess_path, 'w' ); // phpcs:ignore

		fwrite( $htaccess, 'deny from all' ); // phpcs:ignore
		fclose( $htaccess ); // phpcs:ignore
	}

	public function exists( string $group = 'core' ): bool {
		return file_exists( $this->get_path( $group ) );
	}

	public function get( string $group = 'core' ): string {
		return $this->exists( $group ) ?
			nl2br( file_get_contents( $this->get_path( $group ) ) ) : // phpcs:ignore
			'';
	}

	public function get_size( string $group = 'core', bool $int = false ) /* string|int */ {
		$size = $this->exists( $group ) ?
			round( filesize( $this->get_path( $group ) ) / 1024, 3 ) :
			0;

		return $int ? $size : "{$size}KB";
	}

	public function get_delete_url( string $group = 'core' ): string {
		return add_query_arg(
			array( $this->delete_key => $group ),
			$this->core->http()->get_current_url()
		);
	}

	public function delete( string $group = 'core' ): void {
		if (
			current_user_can( $this->capability ) ||
			! $this->exists( $group )
		) {
			return;
		}

		unlink( $this->get_path( $group ) );

		if ( empty( $_GET[ $this->delete_key ] ) ) { // phpcs:ignore
			return;
		}

		$this->core->http()->redirect(
			remove_query_arg( $this->delete_key, $this->core->http()->get_current_url() )
		);
	}

	public function setup(): void {
		$this->add_setup_action(
			__FUNCTION__,
			function (): void {
				$this->delete_by_url();
			}
		);
	}

	protected function delete_by_url(): void {
		if ( empty( $_GET[ $this->delete_key ] ) ) { // phpcs:ignore
			return;
		}

		add_action(
			'admin_init',
			function (): void {
				$this->delete( $_GET[ $this->delete_key ] ); // phpcs:ignore
			}
		);
	}
}
