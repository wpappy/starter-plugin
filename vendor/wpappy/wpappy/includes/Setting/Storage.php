<?php

namespace Wpappy_1_0_4\Setting;

use Wpappy_1_0_4\Feature;

defined( 'ABSPATH' ) || exit;

class Storage extends Feature {

	protected $instances = array();

	public function add_page(
		string $page,
		?string $parent,
		string $nav_title,
		?string $title,
		?string $icon_url,
		?string $description,
		?int $position,
		string $capability,
		?string $submit_btn,
		?string $handler_url
	): void {
		$this->instances[ $page ] = array(
			'object'   => new Page(
				$this->app,
				$this->core,
				$this,
				$page,
				$parent,
				$nav_title,
				$title,
				$icon_url,
				$description,
				$position,
				$capability,
				$submit_btn,
				$handler_url
			),
			'base_url' => $this->is_page( $parent ) ? 'admin' : str_replace( '.php', '', $parent ),
			'children' => array(),
		);
	}

	public function add_tab( string $tab, string $page, string $nav_title, ?string $title, ?string $description ): void {
		$this->instances[ $page ]['children'][ $tab ] = array(
			'object'   => new Tab(
				$this->app,
				$this->core,
				$this,
				$tab,
				$page,
				$nav_title,
				$title,
				$description
			),
			'children' => array(),
		);
	}

	public function add_sub_tab( string $sub_tab, string $tab, string $page, string $nav_title, ?string $title, ?string $description ): void {
		$this->instances[ $page ]['children'][ $tab ]['children'][ $sub_tab ] = array(
			'object'   => new Sub_Tab(
				$this->app,
				$this->core,
				$this,
				$sub_tab,
				$tab,
				$page,
				$nav_title,
				$title,
				$description
			),
			'children' => array(),
		);
	}

	public function add_box( string $box, ?string $sub_tab, string $tab, string $page, ?string $title, ?string $description ): void {
		$data = array(
			'object'   => new Box(
				$this->app,
				$this->core,
				$this,
				$box,
				$sub_tab,
				$tab,
				$page,
				$title,
				$description
			),
			'children' => array(),
		);

		if ( $sub_tab ) {
			$this->instances[ $page ]['children'][ $tab ]['children'][ $sub_tab ]['children'][ $box ] = $data;

		} else {
			$this->instances[ $page ]['children'][ $tab ]['children'][ $box ] = $data;
		}
	}

	public function add_content(
		string $content,
		?string $box,
		?string $sub_tab,
		?string $tab,
		string $page,
		callable $render_content,
		?callable $custom_handler
	): void {
		$data = array(
			'object'   => new Content(
				$this->app,
				$this->core,
				$this,
				$content,
				$box,
				$sub_tab,
				$tab,
				$page,
				$render_content,
				$custom_handler
			),
			'children' => array(),
		);

		if ( $box ) {
			$this->add_to_box(
				$data,
				$content,
				$box,
				$sub_tab,
				$tab,
				$page
			);

		} elseif ( $sub_tab ) {
			$this->instances[ $page ]['children'][ $tab ]['children'][ $sub_tab ]['children'][ $content ] = $data;

		} elseif ( $tab ) {
			$this->instances[ $page ]['children'][ $tab ]['children'][ $content ] = $data;

		} else {
			$this->instances[ $page ]['children'][ $content ] = $data;
		}
	}

	public function add_setting(
		string $setting,
		string $type,
		string $box,
		?string $sub_tab,
		string $tab,
		string $page,
		?string $title,
		array $args,
		string $required_label
	): void {
		$this->add_to_box(
			array(
				'object' => new Setting(
					$this->app,
					$this->core,
					$this,
					$setting,
					$type,
					$box,
					$sub_tab,
					$tab,
					$page,
					$title,
					$args,
					$required_label
				),
			),
			$setting,
			$box,
			$sub_tab,
			$tab,
			$page
		);
	}

	protected function add_to_box( array $data, string $content_or_setting, string $box, ?string $sub_tab, string $tab, string $page ): void {
		if ( $sub_tab ) {
			$this->instances[ $page ]['children'][ $tab ]['children'][ $sub_tab ]['children'][ $box ]['children'][ $content_or_setting ] = $data;

		} else {
			$this->instances[ $page ]['children'][ $tab ]['children'][ $box ]['children'][ $content_or_setting ] = $data;
		}
	}

	public function get(): array {
		return $this->instances;
	}

	public function get_page( string $page ): ?array {
		return $this->instances[ $page ] ?? null;
	}

	public function get_tab( string $tab, string $page ): ?array {
		return $this->instances[ $page ]['children'][ $tab ] ?? null;
	}

	public function get_sub_tab( string $sub_tab, string $tab, string $page ): ?array {
		return $this->instances[ $page ]['children'][ $tab ]['children'][ $sub_tab ] ?? null;
	}

	public function get_box( string $box, ?string $sub_tab, string $tab, string $page ): ?array {
		if ( $sub_tab ) {
			return $this->instances[ $page ]['children'][ $tab ]['children'][ $sub_tab ]['children'][ $box ] ?? null;
		}

		return $this->instances[ $page ]['children'][ $tab ]['children'][ $box ] ?? null;
	}

	public function get_content(
		string $content,
		?string $box,
		?string $sub_tab,
		?string $tab,
		string $page
	): ?array {
		if ( $sub_tab ) {
			return $this->instances[ $page ]['children'][ $tab ]['children'][ $sub_tab ]['children'][ $content ] ?? null;

		} elseif ( $tab ) {
			return $this->instances[ $page ]['children'][ $tab ]['children'][ $content ] ?? null;

		} elseif ( $box ) {
			return $this->get_setting( $content, $box, $sub_tab, $tab, $page );
		}

		return $this->instances[ $page ]['children'][ $content ] ?? null;
	}

	public function get_setting(
		string $setting,
		string $box,
		?string $sub_tab,
		string $tab,
		string $page
	): ?array {
		if ( $sub_tab ) {
			return $this->instances[ $page ]['children'][ $tab ]['children'][ $sub_tab ]['children'][ $box ]['children'][ $setting ] ?? null;
		}

		return $this->instances[ $page ]['children'][ $tab ]['children'][ $box ]['children'][ $setting ] ?? null;
	}

	public function get_page_key( string $page ): string {
		return $this->app->get_key( $page );
	}

	public function is_page( ?string $page, $is_slug = true ): bool {
		if ( ! $is_slug ) {
			$app_key = $this->app->get_key();
			$page    = str_replace( "{$app_key}_", '', $page );
		}

		return in_array( $page, array_keys( $this->get() ), true );
	}

	public function is_tab( string $tab, string $page ): bool {
		$page_data = $this->get_page( $page );

		if ( empty( $page_data ) ) {
			return false;
		}

		return in_array( $tab, array_keys( $page_data['children'] ?? array() ), true );
	}

	public function is_sub_tab( string $sub_tab, string $tab, string $page ): bool {
		$tab_data = $this->get_tab( $tab, $page );

		if ( empty( $tab_data ) ) {
			return false;
		}

		return in_array( $sub_tab, array_keys( $tab_data['children'] ), true );
	}

	public function is_box( string $box, ?string $sub_tab, string $tab, string $page ): bool {
		$tab_or_sub_tab_data = $sub_tab ?
			$this->get_sub_tab( $sub_tab, $tab, $page ) :
			$this->get_tab( $tab, $page );

		if ( empty( $tab_or_sub_tab_data ) ) {
			return false;
		}

		return in_array( $box, array_keys( $tab_or_sub_tab_data['children'] ?? array() ), true );
	}

	public function is_content( string $content, ?string $box, ?string $sub_tab, ?string $tab, string $page ): bool {
		if ( $box ) {
			$parent_data = $this->get_box( $box, $sub_tab, $tab, $page );

		} elseif ( $sub_tab ) {
			$parent_data = $this->get_sub_tab( $sub_tab, $tab, $page );

		} elseif ( $tab ) {
			$parent_data = $this->get_tab( $tab, $page );

		} else {
			$parent_data = $this->get_page( $page );
		}

		if ( empty( $parent_data ) ) {
			return false;
		}

		return in_array( $content, array_keys( $parent_data['children'] ?? array() ), true );
	}

	public function is_setting( string $setting, string $box, ?string $sub_tab, string $tab, string $page ): bool {
		$box_data = $this->get_box( $box, $sub_tab, $tab, $page );

		if ( empty( $box_data ) ) {
			return false;
		}

		return in_array( $setting, array_keys( $box_data['children'] ?? array() ), true );
	}

	public function is_active_page( string $page ): bool {
		return $this->get_page_key( $page ) === $this->get_active_page(); // phpcs:ignore
	}

	public function is_active_tab( string $tab, string $page ): bool {
		$tabs = $this->filter_children(
			$this->get_page( $page )['children'],
			Tab::class
		);

		return $this->is_active_page( $page ) && (
			isset( $_GET['tab'] ) ? // phpcs:ignore
				$tab === $_GET['tab'] : // phpcs:ignore
				( $tabs && array_keys( $tabs )[0] === $tab )
			);
	}

	public function is_active_sub_tab( string $sub_tab, string $tab, string $page ): bool {
		$sub_tabs = $this->filter_children(
			$this->get_tab( $tab, $page )['children'],
			Sub_Tab::class
		);

		return $this->is_active_tab( $tab, $page ) && (
			isset( $_GET['sub_tab'] ) ? // phpcs:ignore
				$sub_tab === $_GET['sub_tab'] : // phpcs:ignore
				( $sub_tabs && array_keys( $sub_tabs )[0] === $sub_tab ) );
	}

	public function get_active_page( bool $rel = false ): ?string {
		return isset( $_GET['page'] ) ? ( // phpcs:ignore
		$rel ?
				str_replace(
					$this->app->get_key() . '_',
					'',
					$_GET['page'] // phpcs:ignore
				) :
				$_GET['page'] ) // phpcs:ignore
			: null;
	}

	public function get_active_tab(): ?string {
		$page = $this->get_active_page( true );
		$tabs = $this->filter_children(
			$this->get_page( $page )['children'],
			Tab::class
		);

		return $_GET['tab'] ?? // phpcs:ignore
			( $tabs ? array_keys( $tabs )[0] : null );
	}

	public function get_active_sub_tab(): ?string {
		$page     = $this->get_active_page( true );
		$tab      = $this->get_active_tab();
		$sub_tabs = $this->filter_children(
			$this->get_tab( $tab, $page )['children'],
			Sub_Tab::class
		);

		return $_GET['sub_tab'] ?? // phpcs:ignore
			( $sub_tabs ? array_keys( $sub_tabs )[0] : null );
	}

	public function filter_children( ?array $children, string $classname ): array {
		return $children ? array_filter(
			$children,
			function ( $child ) use ( $classname ): bool {
				return isset( $child['object'] ) &&
					is_a( $child['object'], $classname );
			}
		) : array();
	}

	public function has_setting( string $page, ?string $tab = null, ?string $sub_tab = null ): bool {
		$page_data = $this->get_page( $page );

		if ( empty( $page_data['children'] ) ) {
			return false;
		}

		foreach ( $page_data['children'] as $tab_key => $tab_data ) {
			if ( $tab && $tab_key !== $tab ) {
				continue;
			}

			if ( empty( $tab_data['children'] ) ) {
				return false;
			}

			foreach ( $tab_data['children'] as $sub_tab_key => $sub_tab_data ) {
				if ( $sub_tab && $sub_tab_key !== $sub_tab ) {
					continue;
				}

				if ( empty( $sub_tab_data['children'] ) ) {
					return false;
				}

				foreach ( $sub_tab_data['children'] as $box_data ) {
					if (
						isset( $box_data['object'] ) &&
						is_a( $box_data['object'], Setting::class )
					) {
						return true;
					}

					if ( empty( $box_data['children'] ) ) {
						return false;
					}

					foreach ( $box_data['children'] as $setting_data ) {
						if (
							isset( $setting_data['object'] ) &&
							is_a( $setting_data['object'], Setting::class )
						) {
							return true;
						}
					}
				}
			}
		}

		return false;
	}
}
