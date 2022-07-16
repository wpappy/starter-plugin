<?php
/**
 * Plugin Name:       My Plugin
 * Plugin URI:        https://github.com/wpappy/starter-plugin
 * Description:       Plugin based on the Wpappy library.
 * Version:           1.0.0
 * Text Domain:       my_plugin
 * Author:            Dmitry Pripa
 * Author URI:        https://dpripa.com
 * Requires PHP:      7.2.0
 * Requires at least: 5.0.0
 * License:           GPL
 * License URI:       https://github.com/wpappy/starter-plugin/blob/main/LICENSE
 */

namespace My_Plugin;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/wpappy/wpappy/index.php';
require_once __DIR__ . '/vendor/autoload.php';

use Wpappy_1_0_5\App as App;

function app(): App {
	return App::get( __NAMESPACE__, __FILE__ );
}

new Setup();
