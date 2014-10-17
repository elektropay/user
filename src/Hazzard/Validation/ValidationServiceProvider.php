<?php namespace Hazzard\Validation;

use Hazzard\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerPresenceVerifier();

		$this->app->bindShared('validator', function($app) {
			$validator = new Factory($app['translator'], $app);

			if (isset($app['validation.presence'])) {
				$validator->setPresenceVerifier($app['validation.presence']);
			}

			if ($app['config']['auth.captcha']) {
				$this->registerCaptcha($validator);
			}

			return $validator;
		});
	}

	/**
	 * Register the database presence verifier.
	 *
	 * @return void
	 */
	protected function registerPresenceVerifier()
	{
		$this->app->bindShared('validation.presence', function($app) {
			return new DatabasePresenceVerifier($app['db']);
		});
	}

	/**
	 * Add custom validation for captcha.
	 *
	 * @return void
	 */
	protected function registerCaptcha($validator)
	{
		$app = $this->app;

		$validator->extend('captcha', function($attributes, $value, $parameters) use ($app) {
			return \ReCaptcha::check(
				$app['config']['services.recaptcha.private_key'],
				$_SERVER['REMOTE_ADDR'], $parameters[0], $value
			);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('validator', 'validation.presence');
	}
}