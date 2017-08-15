<?php

namespace Sloth\Core;

use Illuminate\Container\Container;

class Application extends Container {
	/**
	 * Project paths.
	 * Same as $GLOBALS['themosis.paths'].
	 *
	 * @var array
	 */
	protected $paths = [];

	/**
	 * The loaded service providers.
	 *
	 * @var array
	 */
	protected $loadedProviders = [];

	public function __construct() {
		$this->registerApplication();
	}

	/**
	 * Register the Application class into the container,
	 * so we can access it from the container itself.
	 */
	public function registerApplication() {
		// Normally, only one instance is shared into the container.
		static::setInstance( $this );
		$this->instance( 'app', $this );
	}

	/**
	 * Register a service provider with the application.
	 *
	 * @param \Sloth\Core\ServiceProvider|string $provider
	 * @param array $options
	 * @param bool $force
	 *
	 * @return \Sloth\Core\ServiceProvider
	 */
	public function register( $provider, array $options = [], $force = false ) {
		if ( ! $provider instanceof ServiceProvider ) {
			$provider = new $provider( $this );
		}
		if ( array_key_exists( $providerName = get_class( $provider ), $this->loadedProviders ) ) {
			return;
		}

		$this->loadedProviders[ $providerName ] = true;
		$provider->register();

		if ( method_exists( $provider, 'boot' ) ) {
			$provider->boot();
		}
	}

	/**
	 * adds a filepath to our container
	 *
	 * @param $key
	 * @param $path
	 */
	public function addPath( $key, $path ) {
		$this->instance( 'path.' . $key, $path );
	}
}