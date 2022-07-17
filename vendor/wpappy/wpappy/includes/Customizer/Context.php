<?php

namespace Wpappy_1_0_6\Customizer;

use Wpappy_1_0_6\App;
use Wpappy_1_0_6\Feature;

defined( 'ABSPATH' ) || exit;

/**
 * Manage customizer context.
 */
class Context extends Feature {

	protected $panel;
	protected $section;

	public function add_panel( ?string $panel ): App {
		$this->panel = $panel;

		return $this->app;
	}

	public function add_section( ?string $section ): App {
		$this->section = $section;

		return $this->app;
	}

	public function add( ?string $section, ?string $panel ): App {
		$this->add_section( $section );
		$this->add_panel( $panel );

		return $this->app;
	}

	public function remove(): App {
		$this->panel   = null;
		$this->section = null;

		return $this->app;
	}

	public function get_panel(): ?string {
		return $this->panel;
	}

	public function get_section(): ?string {
		return $this->section;
	}
}
