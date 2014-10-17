<?php 

return array(
	'app' => array(
		'debug' => 'Debug Mode',
		'debug_help' => 'When enabled the actual PHP errors will be shown. If disabled, a simple generic error message is shown.',

		'url' => 'Website URL',
		'url_help' => ' This URL is used in emails, page redirects and assets. You must set this to the root of the script.',

		'name' => 'Website Name',
		'name_help' => 'The name is used in emails or page titles.',

		'color_scheme' => 'Website Color Scheme',
		'color_scheme_help' => 'If you use the script with its design you can choose from multiple color schemes.',

		'locale' => 'Default Locale',
		'locale_help' => ' The default locale that will be used by the translation. ',

		'locales' => 'Locales Names',
		'locales_help' => 'The available locales for translation. <br> Format: <code>key:value</code> separated with comma (,). Eg: <code>en:English, de:Deutsch</code>.',

		'timezone' => 'Timezone',
		'timezone_help' => 'The default timezone for your website. (<a href="http://www.php.net/manual/en/timezones.php" target="_blank">?</a>)',

		'csrf' => 'CSRF Prevention',
		'csrf_help' => 'Prevents the website from CSRF attacks.',
	),

	'mail' => array(
		'driver' => 'Mail Driver',
		'driver_help' => 'For "mailgun" and "mandrill" you`l have set the api keys on the <a href=":link">Services</a> page or in <span class="text-info">app/config/services.php</span>',

		'host' => 'SMTP Host Address',
		'host_help' => '',
		
		'port' => 'SMTP Host Port',
		'port_help' => '',

		'from_address' => 'Global "From" Address',
		'from_address_help' => 'Allows you to send the emails from the same e-mail address.',

		'from_name' => 'Global "From" Name',
		'from_name_help' => 'Allows you to send the emails from with the same name.',

		'encryption' => 'E-Mail Encryption Protocol',
		'encryption_help' => '',

		'username' => 'SMTP Server Username',
		'username_help' => '',

		'password' => 'SMTP Server Password',
		'password_help' => '',

		'sendmail' => 'Sendmail System Path',
		'sendmail_help' => ''
	),

	'auth' => array(
		'require_username' => 'Auth Username',
		'require_username_help' => 'Indicates whether the usernames will be used. For a basic login system you can disable this.',

		'username_change' => 'Allow Username Changing',
		'username_change_help' => 'Indicates whether if the users can change their the usernames.',

		'delete_account' => 'Allow Account Deletion',
		'delete_account_help' => 'Indicates whether if the users can delete their accounts.',

		'email_activation' => 'Send Activation Email',
		'email_activation_help' => 'If enabled when a user creates an accout an email with the activation link will be sent. If disabled the account will be activated by default.',

		'default_role' => 'Default Role',
		'default_role_help' => 'The default role to be used when someone signs up.',

		'captcha' => 'CAPTCHA',
		'captcha_help' => 'Indicates whether CAPTCHA should be used. If enabled you have to set the api keys on the <a href=":link">Services</a> page or in :file.',

		'login_redirect' => 'Login Redirect URL',
		'login_redirect_help' => 'The url to where the users should be redirected after login. If not set the page will reload.',

		'providers' => 'OAuth Service Providers',
		'providers_help' => 'The available services that users can use to log in or sign up. For each one enabled you have to define the api keys on the <a href=":link">Services</a> page or in :file. <br> Format: <code>key:value</code> separated with comma(,). Eg: <code>facebook:Facebook, twitter:Twitter</code>.',
	),
	
	'pms' => array(
		'realtime' => 'Real Time',
		'realtime_help' => 'Whether the message should appear in the inbox in real time. Enabling this may slow down your website.',

		'delay' => 'Request Delay',
		'delay_help' => 'The delay in secods between the requests for checking for new messages.',

		'maxlength' => 'Message Maxlength',
		'maxlength_help' => 'The message max length allowed.',

		'limit' => 'Messages Limit',
		'limit_help' => 'The number of allowd messages an user can send per hour.',

		'webmaster' => 'Webmaster User ID',
		'webmaster_help' => 'The User ID of the Webmaster. Used when user want to contact the site administrator.',
	),
);