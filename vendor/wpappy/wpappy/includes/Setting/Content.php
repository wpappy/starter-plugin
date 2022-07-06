<?php

namespace Wpappy_1_0_4\Setting;

use Wpappy_1_0_4\App;
use Wpappy_1_0_4\Core;

defined( 'ABSPATH' ) || exit;

class Content extends Base {

	protected $content;
	protected $box;
	protected $sub_tab;
	protected $tab;
	protected $render_content;
	protected $custom_handler = 'sati';

	public function __construct(
		App $app,
		Core $core,
		Storage $storage,
		string $content,
		?string $box,
		?string $sub_tab,
		?string $tab,
		string $page,
		callable $render_content,
		?callable $custom_handler
	) {
		parent::__construct( $app, $core, $storage, $page );

		$this->content        = $content;
		$this->box            = $box;
		$this->sub_tab        = $sub_tab;
		$this->tab            = $tab;
		$this->render_content = $render_content;
		$this->custom_handler = $custom_handler;

		add_action(
			'admin_menu',
			function (): void {
				if ( $this->box ) {
					$this->core->hook()->add_action( 'setting_box', array( $this, 'render_in_box' ), 10, 4 );

				} elseif ( $this->sub_tab ) {
					$this->core->hook()->add_action( 'setting_sub_tab', array( $this, 'render_in_sub_tab' ), 10, 3 );

				} elseif ( $this->tab ) {
					$this->core->hook()->add_action( 'setting_tab', array( $this, 'render_in_tab' ), 10, 2 );

				} else {
					$this->core->hook()->add_action( 'setting_page', array( $this, 'render_in_page' ) );
				}

				if ( is_callable( $this->custom_handler ) ) {
					$this->core->hook()->add_action( 'setting_custom_handler', array( $this, 'add_custom_handler' ), 10, 3 );
				}
			},
			5
		);
	}

	public function render_in_box( string $box, string $sub_tab, string $tab, string $page ): void {
		if (
			$this->box !== $box ||
			$this->sub_tab !== $sub_tab ||
			$this->tab !== $tab ||
			$this->page !== $page
		) {
			return;
		}

		$this->render( $box, $sub_tab, $tab, $page );
	}

	public function render_in_sub_tab( string $sub_tab, string $tab, string $page ): void {
		if (
			$this->sub_tab !== $sub_tab ||
			$this->tab !== $tab ||
			$this->page !== $page
		) {
			return;
		}

		$this->render( null, $sub_tab, $tab, $page );
	}

	public function render_in_tab( string $tab, string $page ): void {
		if (
			$this->tab !== $tab ||
			$this->page !== $page
		) {
			return;
		}

		$this->render( null, null, $tab, $page );
	}

	public function render_in_page( string $page ): void {
		if ( $this->page !== $page ) {
			return;
		}

		$this->render( null, null, null, $page );
	}

	public function render( ?string $box, ?string $sub_tab, ?string $tab, string $page ): void {
		$active_sub_tab = $this->storage->get_active_sub_tab();
		$active_tab     = $this->storage->get_active_tab();
		$active_page    = $this->storage->get_active_page();

		call_user_func( $this->render_content, $active_sub_tab, $active_tab, $active_page );
	}

	public function add_custom_handler( ?string $sub_tab, ?string $tab, string $page ): void {
		if (
			$this->sub_tab !== $sub_tab ||
			$this->tab !== $tab ||
			$this->page !== $page
		) {
			return;
		}

		call_user_func( $this->custom_handler );
	}
}
