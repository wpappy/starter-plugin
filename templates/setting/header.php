<?php

namespace My_Plugin;

defined( 'ABSPATH' ) || exit;
?>
<div class="my-header">
	<div class="my-header__title">
		<?php echo esc_html( app()->info()->get_name() ); ?>
	</div>
</div>
