<?php

namespace Wpappy_1_0_4\Integration;

use Wpappy_1_0_4\Feature;

defined( 'ABSPATH' ) || exit;

abstract class Plugin extends Feature {

	abstract public function is_active(): bool;
}
