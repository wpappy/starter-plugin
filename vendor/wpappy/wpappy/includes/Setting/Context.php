<?php

namespace Wpappy_1_0_6\Setting;

use Wpappy_1_0_6\App;
use Wpappy_1_0_6\Feature;

defined( 'ABSPATH' ) || exit;

/**
 * Manage setting context.
 */
class Context extends Feature {

	protected $page;
	protected $tab;
	protected $sub_tab;
	protected $box;

	public function add_page( ?string $page_or_sub_page ): App {
		$this->page = $page_or_sub_page;

		return $this->app;
	}

	public function add_tab( ?string $tab ): App {
		$this->tab = $tab;

		return $this->app;
	}

	public function add_sub_tab( ?string $sub_tab ): App {
		$this->sub_tab = $sub_tab;

		return $this->app;
	}

	public function add_box( ?string $box ): App {
		$this->box = $box;

		return $this->app;
	}

	public function add( string $page, ?string $tab = null, ?string $sub_tab = null, ?string $box = null ): App {
		$this->add_page( $page );
		$this->add_tab( $tab );
		$this->add_sub_tab( $sub_tab );
		$this->add_box( $box );

		return $this->app;
	}

	public function remove(): App {
		$this->page    = null;
		$this->tab     = null;
		$this->sub_tab = null;
		$this->box     = null;

		return $this->app;
	}

	public function get_page(): ?string {
		return $this->page;
	}

	public function get_tab(): ?string {
		return $this->tab;
	}

	public function get_sub_tab(): ?string {
		return $this->sub_tab;
	}

	public function get_box(): ?string {
		return $this->box;
	}
}
