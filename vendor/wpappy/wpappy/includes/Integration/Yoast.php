<?php

namespace Wpappy_1_0_4\Integration;

use Wpappy_1_0_4\App;

defined( 'ABSPATH' ) || exit;

/**
 * Yoast SEO.
 */
class Yoast extends Plugin {

	/**
	 * Is Yoast SEO active.
	 */
	public function is_active(): bool {
		return function_exists( 'YoastSEO' );
	}

	/**
	 * Render breadcrumbs.
	 *
	 * Rendering nothing when plugin isn't activated.
	 */
	public function render_breadcrumbs( string $before, string $after ): App {
		if ( ! $this->is_active() || is_front_page() ) {
			return $this->app;
		}

		yoast_breadcrumb( $before, $after );

		return $this->app;
	}

	/**
	 * Get the primary term ID of the post that ID is being passed.
	 */
	public function get_primary_term_id( int $post_id = 0, string $taxonomy = 'category' ): int {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$terms = get_the_terms( $post_id, $taxonomy );

		if ( empty( $terms ) ) {
			return 0;
		}

		if ( ! $this->is_active() ) {
			return $terms[0]->term_id;
		}

		$primary_term = new \WPSEO_Primary_Term( $taxonomy, $post_id );
		$primary_term = $primary_term->get_primary_term();
		$term         = get_term( $primary_term );

		if ( is_wp_error( $term ) ) {
			return $terms[0]->term_id;
		}

		return $term->term_id;
	}
}
