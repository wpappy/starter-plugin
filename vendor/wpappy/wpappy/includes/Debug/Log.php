<?php

namespace Wpappy_1_0_6\Debug;

use Wpappy_1_0_6\App;
use Wpappy_1_0_6\Feature;

defined( 'ABSPATH' ) || exit;

/**
 * Manage logs.
 */
class Log extends Feature {

	/**
	 * Get a path to the log file.
	 */
	public function get_path( string $group = 'app' ): string {
		return $this->core->debug()->log()->get_path( $group );
	}

	/**
	 * Add a message to the log file.
	 *
	 * @param string $level `'info", `'success'`, `'warning'` or `'error'`.
	 */
	public function add( string $message, string $level = 'warning', string $group = 'app' ): App {
		if ( 'core' === $group ) {
			$this->core->debug()->die( "The <code>'core'</code> group is reserved for writing Wpappy logs." );
		}

		$this->core->debug()->log()->add( $message, $level, $group );

		return $this->app;
	}

	/**
	 * Is the log exists.
	 */
	public function exists( string $group = 'app' ): bool {
		return $this->core->debug()->log()->exists( $group );
	}

	/**
	 * Get a content of the log.
	 */
	public function get( string $group = 'app' ): string {
		return $this->core->debug()->log()->get( $group );
	}

	/**
	 * Get the log file size.
	 */
	public function get_size( string $group = 'app' ): string {
		return $this->core->debug()->log()->get_size( $group );
	}

	/**
	 * Get the url to delete the log file.
	 */
	public function get_delete_url( string $group = 'app' ): string {
		return $this->core->debug()->log()->get_delete_url( $group );
	}

	/**
	 * Directly delete the log file.
	 */
	public function delete( string $group = 'app' ): App {
		$this->core->debug()->log()->delete( $group );

		return $this->app;
	}
}
