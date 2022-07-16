<?php

namespace Wpappy_1_0_5;

use ReflectionClass;

defined( 'ABSPATH' ) || exit;

/**
 * Manage application classes that used the simpleton pattern.
 *
 * Simpleton is designed to improve the declarative approach to developing WordPress applications. This pattern ensures that a functionality of the current class (part of logic) has be called only once.\
 * In most cases we call simpleton classes inside the setup action, when the WordPress environment is fully loaded. But they can also be called earlier if necessary for the application logic. For example, it could be the application configuration or the database initialization, other logic based on the activation and deactivation hooks, etc.\
 * Also, in most cases, simpleton classes are called without assignment to a variable. But you can do it if you have the need.\
 * The simpleton `::__constructor()` is mainly used to call WordPress hooks and other simpleton classes.\
 * Methods that calling by the WordPress hooks needs to be public. Because of this, we consider that all non-static methods of the simpleton class are reserved for a hook-based logic. Use the private non-static methods to destructure it.\
 * Simpleton may also contain public constants, static methods and properties as the public APIs that associated to the logic of the current class.\
 */
class Simpleton extends Feature {

	protected $instances = array();

	/**
	 * Validate if current simpleton class was called.
	 *
	 * To declare current class as simpleton, just paste the following code at the beginning of the class `::__constructor()`:
	 * ``` php
	 * if ( app()->simpleton()->validate( self::class ) ) {
	 *   return;
	 * }
	 * ```
	 *
	 * @param bool $is_extendable Is current class extendable. Default is `false` because in most cases' simpleton class isn't extended-friendly and should be `final`.
	 */
	public function validate( string $classname, bool $is_extendable = false ): bool {
		$has_instance = in_array( $classname, array_keys( $this->instances ), true );

		$this->instances[ $classname ] = array(
			'classname'     => $classname,
			'is_extendable' => $is_extendable,
		);

		if ( $this->app->debug()->is_enabled() ) {
			if ( ! $is_extendable ) {
				$reflection = new ReflectionClass( $classname );

				if ( ! $reflection->isFinal() ) {
					$this->core->debug()->die( "Simpleton class <code>$classname</code> must be final." );
				}
			}

			if ( $has_instance ) {
				$this->core->debug()->die( "Simpleton class <code>$classname</code> must have just one instance call." );
			}
		}

		return $has_instance;
	}

	/**
	 * Get a list of simpleton classes that was called.
	 */
	public function get_instance_list(): array {
		return $this->instances;
	}
}
