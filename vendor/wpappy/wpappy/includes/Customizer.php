<?php

namespace Wpappy_1_0_4;

use Wpappy_1_0_4\Customizer\Section;
use Wpappy_1_0_4\Customizer\Control;

defined( 'ABSPATH' ) || exit;

/**
 * Manage customizer settings.
 *
 * These are the helpers for creating settings, sections and panels for the customizer.\
 * It uses the context similar to the settings context.\
 * <a href="https://wpappy.dpripa.com/classes/Wpappy-1-0-4-Setting.html">Read about</a> the setting context.
 */
class Customizer extends Feature {

	protected $context;
	protected $shadow_context;
	protected $instances = array();

	protected $panel_classes = array();

	protected $section_classes = array(
		'link' => Section\Link::class,
	);

	protected $control_classes = array(
		'media'       => \WP_Customize_Media_Control::class,
		'code_editor' => \WP_Customize_Code_Editor_Control::class,
		'alpha_color' => Control\Alpha_Color::class,
		'line'        => Control\Line::class,
		'notice'      => Control\Notice::class,
		'radio_image' => Control\Radio_Image::class,
	);

	protected $default_setting_args = array(
		'type'       => 'option',
		'capability' => 'edit_theme_options',
	);

	protected $default_control_args = array();

	protected function get_key( string $slug ): string {
		return $this->app->get_key( "customizer_$slug" );
	}

	public function add_panel( string $panel, array $args ): App {
		if ( $this->validate_setup() ) {
			return $this->app;
		}

		$this->shadow_context()->remove();
		$this->shadow_context()->add_panel( $panel );

		add_action(
			'customize_register',
			function ( \WP_Customize_Manager $wp_customize ) use ( $panel, $args ): void {
				$key  = $this->get_key( $panel );
				$type = $args['type'] ?? '';

				if ( isset( $args['type'] ) && in_array( $type, array_keys( $this->panel_classes ), true ) ) {
					unset( $args['type'] );

					$wp_customize->add_panel( new $this->panel_classes[ $type ]( $wp_customize, $key, $args ) );

				} elseif ( isset( $args['type'] ) && class_exists( $type ) ) {
					unset( $args['type'] );

					$wp_customize->add_panel( new $type( $wp_customize, $key, $args ) );

				} else {
					$wp_customize->add_panel( $key, $args );
				}
			}
		);

		return $this->app;
	}

	public function add_section( string $section, array $args, ?string $panel = null ): App {
		if ( $this->validate_setup() ) {
			return $this->app;
		}

		$context_panel = $this->context()->get_panel();

		if ( $panel ) {
			$args['panel'] = $this->get_key( $panel );

		} elseif ( $context_panel ) {
			$args['panel'] = $this->get_key( $context_panel );
		}

		$this->shadow_context()->add_section( $section );

		add_action(
			'customize_register',
			function ( \WP_Customize_Manager $wp_customize ) use ( $section, $args ): void {
				$key  = $this->get_key( $section );
				$type = $args['type'] ?? '';

				if ( isset( $args['type'] ) && in_array( $type, array_keys( $this->section_classes ), true ) ) {
					unset( $args['type'] );

					$wp_customize->add_section( new $this->section_classes[ $type ]( $wp_customize, $key, $args ) );

				} elseif ( isset( $args['type'] ) && class_exists( $type ) ) {
					unset( $args['type'] );

					$wp_customize->add_section( new $type( $wp_customize, $key, $args ) );

				} else {
					$wp_customize->add_section( $key, $args );
				}
			}
		);

		return $this->app;
	}

	public function add_setting( string $setting, array $args, array $control_args ): App {
		if ( $this->validate_setup() ) {
			return $this->app;
		}

		$section = $this->shadow_context()->get_section();
		$key     = $this->get_key( "{$section}_$setting" );

		if ( empty( $section ) ) {
			$this->core->debug()->die( "Need to add a section before adding the <code>'$setting'</code> setting." );
		}

		$control_args['section'] = $this->get_key( $section );

		if ( isset( $args['default'] ) ) {
			$this->instances[ $section ][ $setting ]['default'] = $args['default'];
		}

		if ( isset( $args['type'] ) ) {
			$this->instances[ $section ][ $setting ]['type'] = $args['type'];
		}

		if ( $this->validate_setup() ) {
			return $this->app;
		}

		$args         = wp_parse_args( $args, $this->default_setting_args );
		$control_args = wp_parse_args( $control_args, $this->default_control_args );

		add_action(
			'customize_register',
			function ( \WP_Customize_Manager $wp_customize ) use ( $key, $args, $control_args ): void {
				$type = $control_args['type'] ?? '';

				$wp_customize->add_setting( $key, $args );

				if ( isset( $control_args['type'] ) && in_array( $type, array_keys( $this->control_classes ), true ) ) {
					unset( $control_args['type'] );

					$wp_customize->add_control( new $this->control_classes[ $type ]( $wp_customize, $key, $control_args ) );

				} elseif ( isset( $control_args['type'] ) && class_exists( $type ) ) {
					unset( $control_args['type'] );

					$wp_customize->add_control( new $type( $wp_customize, $key, $args ) );

				} else {
					$wp_customize->add_control( $key, $control_args );
				}
			}
		);

		return $this->app;
	}

	public function get_setting( string $setting, ?string $section ) /* mixed */ {
		$this->validate_setup();

		if ( empty( $section ) ) {
			$section = $this->context()->get_section();
		}

		$key = $this->get_key( "{$section}_$setting" );

		return get_option( $key, $this->instances[ $section ][ $setting ]['default'] ?? null );
	}

	/**
	 * Manage customizer context.
	 */
	public function context(): Customizer\Context {
		$this->validate_setup();

		return $this->get_feature( $this->app, $this->core, 'context', Customizer\Context::class );
	}

	protected function shadow_context(): Customizer\Context {
		return $this->get_feature( $this->app, $this->core, 'shadow_context', Customizer\Context::class );
	}

	/**
	 * Required.
	 */
	public function setup(): App {
		$this->add_setup_action(
			__FUNCTION__,
			function (): void {
				$this->enqueue_assets();
			}
		);

		return $this->app;
	}

	protected function enqueue_assets(): void {
		add_action(
			'customize_controls_enqueue_scripts',
			function (): void {
				$this->core->asset()->enqueue_script(
					'customizer',
					array(
						'jquery',
						'customize-controls',
						'wp-color-picker',
					)
				);
				$this->core->asset()->enqueue_style(
					'customizer',
					array(
						'wp-color-picker',
					)
				);
			}
		);
	}
}
