<?php 

/**
 * Fires after initialization.
 *	
 * @return void
 */
Event::listen('app.init', function() {
	app('auth')->check();

	$locales = app('config')->get('app.locales');

	if (isset($_GET['lang'])) {
		if (array_key_exists($_GET['lang'], $locales)) {
			app('cookie')->set('easylogin_locale', $_GET['lang'], 60*24*30*10);
		}
		
		redirect_to( isset($_GET['r']) ? $_GET['r'] : app()->url() );
	}

	if (app('auth')->check()) $locale = app('auth')->user()->locale;

	if (empty($locale)) $locale = app('cookie')->get('easylogin_locale');

	if (empty($locale)) $locale = GeoPlugin::countryCode();

	if (array_key_exists($locale, $locales)) {
		app('cookie')->set('easylogin_locale', $locale, 60*24*30*10);
	} else {
		$locale = 'en';
	}

	if (empty($locale)) $locale = 'en';

	app('translator')->setLocale($locale);
});


/**
 * Fires before user log in.
 *
 * @param  User 	$user
 * @param  bool 	$remember
 * @return void
 */
Event::listen('auth.login', function($user, $remember) {

	Usermeta::update($user->id, 'last_login', with(new DateTime)->format('Y-m-d H:i:s'));

	Usermeta::update($user->id, 'last_login_ip', $_SERVER['REMOTE_ADDR']);
});

/**
 * Fires before user log out.
 *
 * @param  User 	$user
 * @return void
 */
Event::listen('auth.logout', function($user) {

});

/**
 * Fires after user signup.
 *
 * @param  User 	$user	
 * @return void
 */
Event::listen('auth.signup', function($user) {

});