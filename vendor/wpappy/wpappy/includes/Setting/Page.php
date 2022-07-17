<?php

namespace Wpappy_1_0_6\Setting;

use Wpappy_1_0_6\App;
use Wpappy_1_0_6\Core;

defined( 'ABSPATH' ) || exit;

class Page extends Base {

	protected $parent;
	protected $key;
	protected $nav_title;
	protected $title;
	protected $icon_url;
	protected $description;
	protected $position;
	protected $capability;
	protected $submit_btn;
	protected $handler_url;

	public function __construct(
		App $app,
		Core $core,
		Storage $storage,
		string $page,
		?string $parent,
		string $nav_title,
		?string $title,
		?string $icon_url,
		?string $description,
		?int $position,
		string $capability,
		?string $submit_btn,
		string $handler_url
	) {
		parent::__construct( $app, $core, $storage, $page );

		$this->parent      = $this->storage->is_page( $parent ) ?
			$this->storage->get_page_key( $parent ) :
			$parent;
		$this->key         = $this->storage->get_page_key( $page );
		$this->nav_title   = $nav_title;
		$this->title       = $title;
		$this->icon_url    = $icon_url;
		$this->description = $description;
		$this->position    = $position;
		$this->capability  = $capability;
		$this->submit_btn  = $submit_btn;
		$this->handler_url = $handler_url;

		add_action(
			'admin_menu',
			function (): void {
				if ( $this->parent ) {
					add_submenu_page(
						$this->parent,
						$this->title ?? $this->nav_title,
						$this->nav_title,
						$this->capability,
						$this->key,
						array( $this, 'render' ),
						$this->position
					);

				} else {
					add_menu_page(
						$this->title ?? $this->nav_title,
						$this->nav_title,
						$this->capability,
						$this->key,
						array( $this, 'render' ),
						$this->icon_url ?? '',
						$this->position
					);
				}
			},
			9
		);
	}

	public function get_url(): string {
		return add_query_arg(
			array(
				'page' => $this->storage->get_page_key( $this->page ),
			),
			$this->app->admin()->get_url(
				$this->storage->get_page( $this->page )['base_url']
			)
		);
	}

	public function render(): void {
		$active_sub_tab  = $this->storage->get_active_sub_tab();
		$active_tab      = $this->storage->get_active_tab();
		$active_page     = $this->storage->get_active_page( true );
		$layout_classes  = 'wpappy-page';
		$layout_classes .= ' ' . $this->app->get_key( 'page', '-' );
		$layout_classes .= ' ' . $this->app->get_key( 'page-' . $this->page, '-' );

		?>
		<form class="<?php echo esc_attr( $layout_classes ); ?>" action="<?php echo esc_attr( esc_url( $this->handler_url ) ); ?>" method="post">
			<?php
			wp_nonce_field( $this->key, "{$this->key}_nonce" );

			$this->core->hook()->do_action( 'setting_header', $active_sub_tab, $active_tab, $active_page );
			?>
			<div class="wpappy-notices"></div>
			<?php if ( $this->title ) { ?>
				<h1><?php echo esc_html( $this->title ); ?></h1>
				<?php
			}

			if ( $this->description ) {
				?>
				<div class="wpappy-description">
					<?php echo wp_kses_post( $this->description ); ?>
				</div>
			<?php } ?>
			<div class="wpappy-page__body">
				<?php
				$this->core->hook()->do_action( 'setting_page', $this->page );

				if ( $this->storage->has_setting( $this->page, $active_tab, $active_sub_tab ) ) {
					Submit_Btn::render( $this->submit_btn );
				}
				?>
			</div>
			<?php $this->core->hook()->do_action( 'setting_footer', $active_sub_tab, $active_tab, $active_page ); ?>
		</form>
		<?php
	}
}
