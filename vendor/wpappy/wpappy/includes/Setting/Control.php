<?php

namespace Wpappy_1_0_5\Setting;

use Wpappy_1_0_5\Core;
use Wpappy_1_0_5\Feature;

defined( 'ABSPATH' ) || exit;

class Control extends Feature {

	protected static $control_classes = array(
		'checkbox' => Control\Checkbox::class,
		'radio'    => Control\Radio::class,
		'select'   => Control\Select::class,
		'text'     => Control\Text::class,
		'number'   => Control\Text::class,
		'email'    => Control\Text::class,
		'tel'      => Control\Text::class,
		'textarea' => Control\Textarea::class,
	);

	public static function render(
		Core $core,
		string $type,
		string $key,
		/* mixed */ $value,
		?string $title,
		array $args,
		string $required_label,
		?string $box = null,
		?string $sub_tab = null,
		?string $tab = null,
		?string $page = null
	): void {
		$sub_tab_label = $sub_tab ? "sub-tab: <code>'$sub_tab'</code>," : '';
		$parent_label  = " Parent box: <code>'$box'</code>, $sub_tab_label tab: <code>'$tab'</code>, page: <code>'$page'</code>.";

		if ( in_array( $type, array_keys( static::$control_classes ), true ) ) {
			$control_classname = static::$control_classes[ $type ];

		} elseif ( class_exists( $type ) ) {
			$control_classname = $type;

		} else {
			$core->debug()->die( "Undefined type <code>'$type'</code> of a setting control.$parent_label" );

			$control_classname = '';
		}

		if ( ! method_exists( $control_classname, 'render' ) ) {
			$core->debug()->die( "Not found the static method <code>render</code> in the control class $control_classname." );

			return;
		}

		$title_method_exists = method_exists( $control_classname, 'render_title' );
		?>
		<tr class="wpappy-control">
			<?php if ( $title_method_exists ) { ?>
				<th class="wpappy-control__title" scope="row">
					<?php call_user_func( array( $control_classname, 'render_title' ), $type, $key, $title, $args, $required_label ); ?>
				</th>
			<?php } ?>
			<td class="wpappy-control__body" <?php echo $title_method_exists ? '' : 'colspan="2"'; ?>>
				<?php call_user_func( array( $control_classname, 'render' ), $type, $key, $value, $title, $args ); ?>
			</td>
		</tr>
		<?php
	}

	public static function render_custom(
		Core $core,
		string $type,
		string $key,
		/* mixed */ $value,
		?string $title,
		array $args,
		string $required_label
	): void {
		?>
		<table class="form-table" role="presentation">
			<tbody>
				<?php static::render( $core, $type, $key, $value, $title, $args, $required_label ); ?>
			</tbody>
		</table>
		<?php
	}
}
