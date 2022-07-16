<?php

namespace Wpappy_1_0_5\Setting;

use Wpappy_1_0_5\App;
use Wpappy_1_0_5\Core;

defined( 'ABSPATH' ) || exit;

class Tab extends Base {

	protected $tab;
	protected $nav_title;
	protected $title;
	protected $description;

	public function __construct(
		App $app,
		Core $core,
		Storage $storage,
		string $tab,
		string $page,
		string $nav_title,
		?string $title,
		?string $description
	) {
		parent::__construct( $app, $core, $storage, $page );

		$this->tab         = $tab;
		$this->nav_title   = $nav_title;
		$this->title       = $title;
		$this->description = $description;

		add_action(
			'admin_menu',
			function (): void {
				$this->core->hook()->add_action( 'setting_page', array( $this, 'render' ) );
			},
			8
		);
	}

	public function get_nav_title(): string {
		return $this->nav_title;
	}

	public function get_url(): string {
		return add_query_arg(
			array(
				'page' => $this->storage->get_page_key( $this->page ),
				'tab'  => $this->tab,
			),
			$this->app->admin()->get_url(
				$this->storage->get_page( $this->page )['base_url']
			)
		);
	}

	public function render( string $page ): void {
		if (
			$this->page !== $page ||
			! $this->storage->is_active_tab( $this->tab, $page )
		) {
			return;
		}
		?>
		<div class="wpappy-tab">
			<?php
			$this->render_nav();

			if ( $this->title ) {
				?>
				<h2><?php echo esc_html( $this->title ); ?></h2>
				<?php
			}

			if ( $this->description ) {
				?>
				<div class="wpappy-description">
					<?php echo wp_kses_post( $this->description ); ?>
				</div>
				<?php
			}

			$this->core->hook()->do_action( 'setting_tab', $this->tab, $page );
			?>
		</div>
		<?php
	}

	protected function render_nav(): void {
		$tabs = $this->storage->filter_children(
			$this->storage->get_page( $this->page )['children'],
			static::class
		);

		if ( count( $tabs ) < 2 ) {
			return;
		}
		?>
		<div class="wpappy-tab__nav nav-tab-wrapper">
			<?php
			foreach ( $tabs as $tab_key => $tab_data ) {
				/**
				 * @var self $tab
				 */
				$tab          = $tab_data['object'];
				$active_class = $this->storage->is_active_tab( $tab_key, $this->page ) ? 'nav-tab-active' : '';
				?>
				<a class="nav-tab <?php echo esc_attr( $active_class ); ?>" href="<?php echo esc_url( $tab->get_url() ); ?>">
					<?php echo esc_html( $tab->get_nav_title() ); ?>
				</a>
				<?php
			}

			$this->core->hook()->do_action( 'setting_tab_nav', $this->tab, $this->page );
			?>
		</div>
		<?php
	}
}
