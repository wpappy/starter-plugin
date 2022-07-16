<?php

namespace Wpappy_1_0_5;

defined( 'ABSPATH' ) || exit;

/**
 * Helpers for working with third-party WordPress plugins.
 */
class Integration extends Feature {

	protected $acf;
	protected $polylang;
	protected $yoast;
	protected $wc;

	/**
	 * Advanced Custom Fields / ACF Pro.
	 */
	public function acf(): Integration\ACF {
		return $this->get_feature( $this->app, $this->core, 'acf', Integration\ACF::class );
	}

	/**
	 * Polylang.
	 */
	public function polylang(): Integration\Polylang {
		return $this->get_feature( $this->app, $this->core, 'polylang', Integration\Polylang::class );
	}

	/**
	 * Yoast SEO.
	 */
	public function yoast(): Integration\Yoast {
		return $this->get_feature( $this->app, $this->core, 'yoast', Integration\Yoast::class );
	}

	/**
	 * WooCommerce.
	 */
	public function wc(): Integration\WC {
		return $this->get_feature( $this->app, $this->core, 'wc', Integration\WC::class );
	}

	/**
	 * @param string $reference A namespace containing the `Setup` or `Setting` classes, or the fully qualified classname.
	 * @param string|null $active_method_name Optional. The name of a static method of the reference that returns statement of plugin activation. This piece of logic will be skipped if this method don't exist.
	 */
	public function is_active( string $reference, ?string $active_method_name = 'is_active' ): bool {
		if ( class_exists( "$reference\Setting" ) ) {
			$reference = "$reference\Setting";

		} elseif ( class_exists( "$reference\Setup" ) ) {
			$reference = "$reference\Setup";

		} elseif ( ! class_exists( $reference ) ) {
			return false;
		}

		if ( $active_method_name && method_exists( $reference, $active_method_name ) ) {
			return call_user_func( array( $reference, $active_method_name ) );
		}

		return true;
	}
}
