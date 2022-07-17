<?php

namespace My_Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * @var array $args {
 *  @type string 'label'
 * }
 */
?>
<div class="my-plugin-label">
	<div class="my-plugin-label__content">
		<?php echo esc_html( $args['label'] ); ?>
	</div>
</div>
