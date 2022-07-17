<?php

namespace My_Plugin;

defined( 'ABSPATH' ) || exit;
?>
<div class="my-plugin-header">
	<div class="my-plugin-header__title">
		<?php echo esc_html( app()->info()->get_name() ); ?>
	</div>
</div>
