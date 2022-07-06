<?php

namespace Wpappy_1_0_4\Integration;

use Wpappy_1_0_4\App;

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce.
 */
class WC extends Plugin {

	/**
	 * Is WooCommerce active.
	 */
	public function is_active(): bool {
		return class_exists( 'woocommerce', false );
	}

	public function set_theme_support(): App {
		$this->validate_setter();

		if ( $this->validate_single_call( __FUNCTION__ ) ) {
			return $this->app;
		}

		add_action(
			'after_setup_theme',
			function (): void {
				add_theme_support( 'woocommerce' );
				add_theme_support( 'wc-product-gallery-zoom' );
				add_theme_support( 'wc-product-gallery-lightbox' );
				add_theme_support( 'wc-product-gallery-slider' );
			},
			1
		);

		return $this->app;
	}

	public function set_block_support(): App {
		$this->validate_setter();

		$this->add_setup_action(
			__FUNCTION__,
			function (): void {
				if ( ! $this->is_active() ) {
					return;
				}

				$filter = function ( array $args ): array {
					$args['show_in_rest'] = true;

					return $args;
				};

				add_filter( 'woocommerce_taxonomy_args_product_cat', $filter );
				add_filter( 'woocommerce_taxonomy_args_product_tag', $filter );
				add_filter( 'woocommerce_register_post_type_product', $filter );

				add_filter(
					'use_block_editor_for_post_type',
					function ( bool $can_edit, string $post_type ): bool {
						if ( 'product' === $post_type ) {
							$can_edit = true;
						}

						return $can_edit;
					},
					10,
					2
				);
			}
		);

		return $this->app;
	}
}
