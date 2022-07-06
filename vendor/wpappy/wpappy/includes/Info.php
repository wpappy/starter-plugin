<?php

namespace Wpappy_1_0_4;

defined( 'ABSPATH' ) || exit;

/**
 * Information from the application metadata.
 */
class Info extends Feature {

	public function get_name(): string {
		return $this->core->info()->get_name();
	}

	public function get_url(): string {
		return $this->core->info()->get_url();
	}

	public function get_version(): string {
		return $this->core->info()->get_version();
	}

	public function get_description(): string {
		return $this->core->info()->get_description();
	}

	public function get_author(): string {
		return $this->core->info()->get_author();
	}

	public function get_author_url(): string {
		return $this->core->info()->get_author_url();
	}

	public function get_textdomain(): string {
		return $this->core->info()->get_textdomain();
	}

	public function get_domain_path(): string {
		return $this->core->info()->get_domain_path();
	}

	public function get_requires_php(): string {
		return $this->core->info()->get_requires_php();
	}

	public function get_requires_wp(): string {
		return $this->core->info()->get_requires_wp();
	}
}
