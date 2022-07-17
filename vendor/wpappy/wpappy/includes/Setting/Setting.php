<?php

namespace Wpappy_1_0_6\Setting;

use Wpappy_1_0_6\App;
use Wpappy_1_0_6\Core;

defined( 'ABSPATH' ) || exit;

class Setting extends Base {

	protected $type;
	protected $setting;
	protected $box;
	protected $sub_tab;
	protected $tab;
	protected $page;
	protected $key;
	protected $title;
	protected $args;
	protected $required_label;
	protected $sanitize_callback;

	protected $default_args = array();

	public function __construct(
		App $app,
		Core $core,
		Storage $storage,
		string $setting,
		string $type,
		string $box,
		?string $sub_tab,
		string $tab,
		string $page,
		?string $title,
		array $args,
		string $required_label
	) {
		parent::__construct( $app, $core, $storage, $page );

		$this->setting        = $setting;
		$this->type           = $type;
		$this->box            = $box;
		$this->sub_tab        = $sub_tab;
		$this->tab            = $tab;
		$this->key            = $storage->get_page_key( $page ) . '_' . $tab . ( $sub_tab ? ( '_' . $sub_tab ) : '' ) . '_' . $box . '_' . $setting;
		$this->title          = $title;
		$this->args           = wp_parse_args( $args, $this->default_args );
		$this->required_label = $required_label;

		$this->set_sanitize_callback( $args['sanitize_callback'] ?? null );

		add_action(
			'admin_menu',
			function (): void {
				$this->core->hook()->add_action( 'setting_box', array( $this, 'render' ), 10, 4 );
			},
			5
		);
	}

	public function get_key(): string {
		return $this->key;
	}

	protected function set_sanitize_callback( /* array|string|null */ $sanitize_callback ): void { // phpcs:ignore
		if (
			$sanitize_callback && (
			is_array( $sanitize_callback ) ? (
				2 === count( $sanitize_callback ) &&
				method_exists( $sanitize_callback[0], $sanitize_callback[1] ) ) :
				function_exists( $sanitize_callback ) )
		) {
			$this->sanitize_callback = $sanitize_callback;

		} else {
			$this->sanitize_callback = 'sanitize_text_field';
		}
	}

	public function get_sanitize_callback() /* array|string|null */ { // phpcs:ignore
		return $this->sanitize_callback;
	}

	public function get() /* mixed */ {
		return get_option( $this->key, $this->args['default'] ?? null );
	}

	public function render( string $box, ?string $sub_tab, string $tab, string $page ): void {
		if (
			$this->box !== $box ||
			( $sub_tab && $this->sub_tab !== $sub_tab ) ||
			$this->tab !== $tab ||
			$this->page !== $page
		) {
			return;
		}

		Control::render(
			$this->core,
			$this->type,
			$this->key,
			$this->get(),
			$this->title,
			$this->args,
			$this->required_label,
			$box,
			$sub_tab,
			$tab,
			$page
		);
	}
}
