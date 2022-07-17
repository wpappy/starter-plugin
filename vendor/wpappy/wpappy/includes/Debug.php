<?php

namespace Wpappy_1_0_6;

defined( 'ABSPATH' ) || exit;

/**
 * Manage debugging.
 */
class Debug extends Feature {

	protected $log;
	protected $die_footer_text = '';

	/**
	 * Set a text to the "die" message footer.
	 *
	 * @param string $text Default empty.
	 */
	public function set_die_footer_text( string $text ): App {
		$this->set_property( 'die_footer_text', $text );

		return $this->app;
	}

	/**
	 * Set debug mode enabled.
	 *
	 * @param bool $is_enabled Default: `true`.
	 */
	public function set_enabled( bool $is_enabled ): App {
		$this->core->debug()->set_raw_enabled( $is_enabled );

		return $this->app;
	}

	public function is_enabled(): bool {
		return $this->core->debug()->is_enabled();
	}

	public function die( string $message, ?string $title = null, bool $enable_backtrace = true ): void {
		Core\Debug::raw_die(
			$message,
			$title,
			$this->app->get_key(),
			$enable_backtrace,
			false,
			$this->die_footer_text,
			$this->is_enabled()
		);
	}

	/**
	 * Manage logs.
	 */
	public function log(): Debug\Log {
		return $this->get_feature( $this->app, $this->core, 'log', Debug\Log::class );
	}
}
