<?php

namespace Wpappy_1_0_4\Setting;

use Wpappy_1_0_4\App;
use Wpappy_1_0_4\Core;

defined( 'ABSPATH' ) || exit;

class Sub_Tab extends Base {

	protected $sub_tab;
	protected $tab;
	protected $nav_title;
	protected $title;
	protected $description;

	public function __construct(
		App $app,
		Core $core,
		Storage $storage,
		string $sub_tab,
		string $tab,
		string $page,
		string $nav_title,
		?string $title,
		?string $description
	) {
		parent::__construct( $app, $core, $storage, $page );

		$this->sub_tab     = $sub_tab;
		$this->tab         = $tab;
		$this->nav_title   = $nav_title;
		$this->title       = $title;
		$this->description = $description;

		add_action(
			'admin_menu',
			function (): void {
				$this->core->hook()->add_action( 'setting_tab', array( $this, 'render' ), 10, 2 );
			},
			7
		);
	}

	public function get_nav_title(): string {
		return $this->nav_title;
	}

	public function get_url(): string {
		return add_query_arg(
			array(
				'page'    => $this->storage->get_page_key( $this->page ),
				'tab'     => $this->tab,
				'sub_tab' => $this->sub_tab,
			),
			$this->app->admin()->get_url(
				$this->storage->get_page( $this->page )['base_url']
			)
		);
	}

	public function render( string $tab, string $page ): void {
		if (
			$this->tab !== $tab ||
			$this->page !== $page ||
			! $this->storage->is_active_sub_tab( $this->sub_tab, $tab, $page )
		) {
			return;
		}
		?>
		<div class="wpappy-sub-tab">
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

			$this->core->hook()->do_action( 'setting_sub_tab', $this->sub_tab, $this->tab, $this->page );
			?>
		</div>
		<?php
	}

	protected function render_nav(): void {
		$sub_tabs = $this->storage->filter_children(
			$this->storage->get_tab( $this->tab, $this->page )['children'],
			static::class
		);

		if ( count( $sub_tabs ) < 2 ) {
			return;
		}
		?>
		<ul class="wpappy-sub-tab__nav subsubsub">
			<?php
			$is_first = true;

			foreach ( $sub_tabs as $sub_tab_key => $sub_tab_data ) {
				/**
				 * @var self $sub_tab
				 */
				$sub_tab      = $sub_tab_data['object'];
				$active_class = $this->storage->is_active_sub_tab( $sub_tab_key, $this->tab, $this->page ) ? 'current' : '';
				?>
				<li>
					<?php
					if ( ! $is_first ) {
						echo ' | ';
					}

					$is_first = false;
					?>
					<a class="<?php echo esc_attr( $active_class ); ?>" href="<?php echo esc_url( $sub_tab->get_url() ); ?>">
						<?php echo esc_html( $sub_tab->get_nav_title() ); ?>
					</a>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
	}
}
