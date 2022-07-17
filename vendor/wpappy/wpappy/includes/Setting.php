<?php

namespace Wpappy_1_0_6;

defined( 'ABSPATH' ) || exit;

/**
 * Manage settings.
 *
 * Here's a complete toolset for creating settings pages and fields, getting their values, etc.\
 * Context is a little magic that makes it easy to add the setting pages, tabs, sub-tabs, boxes and settings.\
 * You just need to add the specific setting pages, tabs, sub-tabs, boxes and settings to the context. And then all the following `::get()` setting methods will use it to find out who their parents are.\
 * When adding settings, the same thing happens. But you don't need to explicitly update the context, as adding a page, tab, sub-tab, or box will automatically add it to the shadow context.\
 * You don't have direct access to the shadow context, and it does NOT overlap with the regular context either.
 */
class Setting extends Feature {

	protected $context;
	protected $shadow_context;
	protected $storage;
	protected $handler;
	protected $submit_btn     = 'Save changes';
	protected $error_notice   = 'Something went wrong.';
	protected $success_notice = 'Changes saved.';
	protected $required_label = 'required';

	/**
	 * Add a page or sub-page to the admin menu.
	 */
	public function add_page(
		string $page,
		?string $parent,
		string $nav_title,
		?string $title = null,
		?string $icon_url = null,
		?string $description = null,
		?int $position = null,
		string $capability = 'delete_posts'
	): App {
		if ( $this->validate_setup() ) {
			return $this->app;
		}

		if ( $this->storage()->is_page( $page ) ) {
			$this->core->debug()->die( "The <code>'$page'</code> page already exists." );

			return $this->app;
		}

		$this->shadow_context()->remove();
		$this->shadow_context()->add_page( $page );
		$this->storage()->add_page(
			$page,
			$parent,
			$nav_title,
			$title,
			$icon_url,
			$description,
			$position,
			$capability,
			$this->submit_btn,
			$this->handler()->get_url()
		);

		return $this->app;
	}

	/**
	 * Add a tab to the page.
	 */
	public function add_tab( string $tab, string $nav_title, ?string $title = null, ?string $description = null ): App {
		if ( $this->validate_setup() ) {
			return $this->app;
		}

		$page = $this->shadow_context()->get_page();

		if ( empty( $page ) ) {
			$this->core->debug()->die( "Need to add a page before adding the <code>'$tab'</code> tab." );

			return $this->app;
		}

		if ( $this->storage()->is_tab( $tab, $page ) ) {
			$this->core->debug()->die( "The <code>$tab</code> tab already exists." );

			return $this->app;
		}

		$this->shadow_context()->add_tab( $tab );
		$this->shadow_context()->add_sub_tab( null );
		$this->shadow_context()->add_box( null );
		$this->storage()->add_tab( $tab, $page, $nav_title, $title, $description );

		return $this->app;
	}

	/**
	 * Add a sub-tab to the tab.
	 */
	public function add_sub_tab( string $sub_tab, string $nav_title, ?string $title = null, ?string $description = null ): App {
		if ( $this->validate_setup() ) {
			return $this->app;
		}

		$page         = $this->shadow_context()->get_page();
		$tab          = $this->shadow_context()->get_tab();
		$prev_sub_tab = $this->shadow_context()->get_sub_tab();
		$box          = $this->shadow_context()->get_box();

		if ( $box && empty( $prev_sub_tab ) ) {
			$this->core->debug()->die(
				"Wrong position of the <code>'$sub_tab'</code> sub-tab in the structure. The parent <code>'$tab'</code> tab already has the direct child boxes."
			);

			return $this->app;
		}

		if ( empty( $tab ) ) {
			$this->core->debug()->die( "Need to add a tab before adding the <code>'$sub_tab'</code> sub-tab." );

			return $this->app;
		}

		if ( $this->storage()->is_sub_tab( $sub_tab, $tab, $page ) ) {
			$this->core->debug()->die( "The <code>'$sub_tab'</code> sub-tab already exists." );

			return $this->app;
		}

		$this->shadow_context()->add_sub_tab( $sub_tab );
		$this->shadow_context()->add_box( null );
		$this->storage()->add_sub_tab( $sub_tab, $tab, $page, $nav_title, $title, $description );

		return $this->app;
	}

	/**
	 * Add a box to the tab or sub-tab.
	 */
	public function add_box( string $box, ?string $title = null, ?string $description = null ): App {
		if ( $this->validate_setup() ) {
			return $this->app;
		}

		$page    = $this->shadow_context()->get_page();
		$tab     = $this->shadow_context()->get_tab();
		$sub_tab = $this->shadow_context()->get_sub_tab();

		if ( empty( $tab ) && empty( $sub_tab ) ) {
			$this->core->debug()->die( "Need to add a tab or sub-tab before adding the <code>'$box'</code> box." );

			return $this->app;
		}

		if ( $this->storage()->is_box( $box, $sub_tab, $tab, $page ) ) {
			$this->core->debug()->die( "The <code>'$box'</code> box already exists." );

			return $this->app;
		}

		$this->shadow_context()->add_box( $box );
		$this->storage()->add_box( $box, $sub_tab, $tab, $page, $title, $description );

		return $this->app;
	}

	/**
	 * Add a setting.
	 *
	 * @param string $type Type of the setting control. <a href="https://wpappy.dpripa.com/namespaces/wpappy-1-0-6-setting-control.html">View the list</a> of available controls. You can also pass the classname of the custom control.
	 * @param array $args Configuration of the setting. A control can have its own additional arguments, you can find them on each control's documentation page. General arguments:
	 *  - `'default'` - default value.
	 *  - `'description'` - setting description.
	 *  - `'sanitize_callback'` - name of a helper function to sanitize a setting value. Default: `'sanitize_text_field'`.
	 */
	public function add( string $setting, string $type, ?string $title, array $args = array() ): App {
		if ( $this->validate_setup() ) {
			return $this->app;
		}

		$page    = $this->shadow_context()->get_page();
		$tab     = $this->shadow_context()->get_tab();
		$sub_tab = $this->shadow_context()->get_sub_tab();
		$box     = $this->shadow_context()->get_box();

		if ( empty( $box ) ) {
			$this->core->debug()->die( "Need to add a box before adding the <code>'$setting'</code> setting." );

			return $this->app;
		}

		if ( $this->storage()->is_setting( $setting, $box, $sub_tab, $tab, $page ) ) {
			$this->core->debug()->die( "The <code>'$setting'</code> setting already exists." );

			return $this->app;
		}

		$this->storage()->add_setting(
			$setting,
			$type,
			$box,
			$sub_tab,
			$tab,
			$page,
			$title,
			$args,
			$this->required_label
		);

		return $this->app;
	}

	/**
	 * Add a custom content to the page, tab, sub-tab or box.
	 *
	 * @param callable $render_content Parameters:
	 *  - string|null $sub_tab
	 *  - string|null $tab
	 *  - string|null $page
	 *
	 * @param callable|null $custom_handler Handler for data from a custom source. Parameters:
	 *  - string|null $sub_tab
	 *  - string|null $tab
	 *  - string|null $page
	 */
	public function add_content( string $content, callable $render_content, ?callable $custom_handler = null ): App {
		if ( $this->validate_setup() ) {
			return $this->app;
		}

		$page    = $this->shadow_context()->get_page();
		$tab     = $this->shadow_context()->get_tab();
		$sub_tab = $this->shadow_context()->get_sub_tab();
		$box     = $this->shadow_context()->get_box();

		if ( empty( $page ) ) {
			$this->core->debug()->die( "Need to add at least a page before adding the <code>'$content'</code> content." );

			return $this->app;
		}

		if ( $this->storage()->is_content( $content, $box, $sub_tab, $tab, $page ) ) {
			$this->core->debug()->die( "The <code>'$content'</code> content already exists." );

			return $this->app;
		}

		$this->storage()->add_content(
			$content,
			$box,
			$sub_tab,
			$tab,
			$page,
			$render_content,
			$custom_handler
		);

		return $this->app;
	}

	/**
	 * Add an item to the tab navigation.
	 *
	 * @param callable $render_item Parameters:
	 *  - string|null $tab
	 *  - string|null $page
	 */
	public function add_tab_nav_item( callable $render_item ): App {
		if ( $this->validate_setup() ) {
			return $this->app;
		}

		$this->core->hook()->add_action( 'setting_tab_nav', $render_item, 10, 2 );

		return $this->app;
	}

	/**
	 * Get a setting value.
	 */
	public function get(
		string $setting,
		?string $box = null,
		?string $sub_tab = null,
		?string $tab = null,
		?string $page = null
	) /* mixed */ {
		if ( $this->validate_setup() ) {
			return null;
		}

		if ( empty( $box ) ) {
			$box = $this->context()->get_box();
		}

		if ( empty( $sub_tab ) ) {
			$sub_tab = $this->context()->get_sub_tab();
		}

		if ( empty( $tab ) ) {
			$tab = $this->context()->get_tab();
		}

		if ( empty( $page ) ) {
			$page = $this->context()->get_page();
		}

		if ( ! $this->storage()->is_setting( $setting, $box, $sub_tab, $tab, $page ) ) {
			$sub_tab_label = $sub_tab ? "sub-tab: <code>'$sub_tab'</code>," : '';

			$this->core->debug()->die(
				"The <code>'$setting'</code> setting doesn't exists. Parent box: <code>'$box'</code>, $sub_tab_label tab: <code>'$tab'</code>, page: <code>'$page'</code>."
			);

			return null;
		}

		$setting_data = $this->storage()->get_setting(
			$setting,
			$box,
			$sub_tab,
			$tab,
			$page
		);

		return $setting_data['object']->get();
	}

	/**
	 * Get the full page key.
	 */
	public function get_page_key( string $page ): string {
		return $this->storage()->get_page_key( $page );
	}

	public function get_url( string $page, ?string $tab = null, ?string $sub_tab = null ): string {
		if ( ! $this->storage()->is_page( $page ) ) {
			$this->core->debug()->die( "The <code>'$page'</code> page not exists." );
		}

		if ( $sub_tab && $this->storage()->is_sub_tab( $sub_tab, $tab, $page ) ) {
			return $this->storage()->get_sub_tab( $sub_tab, $tab, $page )['object']->get_url();
		}

		if ( $tab && $this->storage()->is_tab( $tab, $page ) ) {
			return $this->storage()->get_tab( $tab, $page )['object']->get_url();
		}

		return $this->storage()->get_page( $page )['object']->get_url();
	}

	/**
	 * Set a header for the setting pages.
	 *
	 * @param callable $render_header Parameters:
	 *  - string|null $sub_tab
	 *  - string|null $tab
	 *  - string|null $page
	 */
	public function set_header( callable $render_header ): App {
		if ( $this->validate_setter() ) {
			return $this->app;
		}

		add_action(
			'admin_menu',
			function () use ( $render_header ): void {
				$this->core->hook()->add_action( 'setting_header', $render_header, 10, 3 );
			},
			5
		);

		return $this->app;
	}

	/**
	 * Set a footer for the setting pages.
	 *
	 * @param callable $render_footer Parameters:
	 *  - string|null $sub_tab
	 *  - string|null $tab
	 *  - string|null $page
	 */
	public function set_footer( callable $render_footer ): App {
		if ( $this->validate_setter() ) {
			return $this->app;
		}

		add_action(
			'admin_menu',
			function () use ( $render_footer ): void {
				$this->core->hook()->add_action( 'setting_footer', $render_footer, 10, 3 );
			},
			5
		);

		return $this->app;
	}

	/**
	 * Set a title for the submit button.
	 *
	 * Default: `'Save changes'`.
	 *
	 * The submit button will only be displaying when at least one setting is displaying.
	 *
	 * @param string|null $btn_title Pass `null` to hide the button anyway.
	 */
	public function set_submit_btn( ?string $btn_title ): App {
		$this->set_property( 'submit_btn', $btn_title );

		return $this->app;
	}

	/**
	 * Set a message for the error notice of the save settings process.
	 *
	 * Default: `'Something went wrong.'`.
	 */
	public function set_error_notice( string $message ): App {
		$this->set_property( 'error_notice', $message );

		return $this->app;
	}

	/**
	 * Set a message for the success notice of the save settings process.
	 *
	 * Default: `'Changes saved.'`.
	 */
	public function set_success_notice( string $message ): App {
		$this->set_property( 'success_notice', $message );

		return $this->app;
	}

	/**
	 * Set the "required" label of a setting control.
	 *
	 * Default: `'required'`.
	 */
	public function set_required_label( string $text ): App {
		$this->set_property( 'required_label', $text );

		return $this->app;
	}

	/**
	 * Render a control for data from a custom source.
	 *
	 * Designed for use in the `$custom_handler` parameter of the `::add_content()` method.
	 */
	public function render_control( string $type, string $key, /* mixed */ $value, ?string $title, array $args = array() ): App {
		if ( $this->validate_setup() ) {
			return $this->app;
		}

		Setting\Control::render_custom( $this->core, $type, $key, $value, $title, $args, $this->required_label );

		return $this->app;
	}

	/**
	 * Render submit button in a custom location.
	 *
	 * Using the `::render_control()` method on a page with no settings you may need to add the button manually, the `::render_submit_btn()` method is mostly for that.
	 *
	 * @param string|null $btn_title Pass title if you've hidden the default button, or if you want to change the title of that particular button.
	 */
	public function render_submit_btn( ?string $btn_title = null ): App {
		if ( $this->validate_setup() ) {
			return $this->app;
		}

		Setting\Submit_Btn::render( $btn_title ?? $this->submit_btn );

		return $this->app;
	}

	/**
	 * Manage setting context.
	 */
	public function context(): Setting\Context {
		$this->validate_setup();

		return $this->get_feature( $this->app, $this->core, 'context', Setting\Context::class );
	}

	protected function shadow_context(): Setting\Context {
		return $this->get_feature( $this->app, $this->core, 'shadow_context', Setting\Context::class );
	}

	protected function storage(): Setting\Storage {
		return $this->get_feature( $this->app, $this->core, 'storage', Setting\Storage::class );
	}

	protected function handler(): Setting\Handler {
		return $this->get_feature( $this->app, $this->core, 'handler', Setting\Handler::class );
	}

	/**
	 * Required.
	 */
	public function setup(): App {
		$this->add_setup_action(
			__FUNCTION__,
			function (): void {
				add_action(
					'admin_menu',
					function (): void {
						if ( ! $this->storage()->is_page( $this->storage()->get_active_page(), false ) ) {
							return;
						}

						$this->enqueue_assets();
						$this->handler()->setup(
							$this->storage(),
							$this->error_notice,
							$this->success_notice
						);
					},
					10
				);
			}
		);

		$this->app->admin()->notice()->setup();

		return $this->app;
	}

	protected function enqueue_assets(): void {
		$this->core->asset()->enqueue_script( 'setting', array( 'jquery' ) );
		$this->core->asset()->enqueue_style( 'setting' );
	}
}
