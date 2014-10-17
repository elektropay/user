<?php
require_once 'app/init.php';

if (Auth::check()) redirect_to(App::url());
?>			
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>EasyLogin Pro</title>

	<meta name="csrf-token" content="<?php echo csrf_token() ?>">
	<script src="<?php echo asset_url('js/vendor/jquery-1.11.1.min.js') ?>"></script>
	<script src="<?php echo asset_url('js/vendor/bootstrap.min.js') ?>"></script>
	<script src="<?php echo asset_url('js/easylogin.js') ?>"></script>
	<script src="<?php echo asset_url('js/main.js') ?>"></script>
	<script>
		EasyLogin.options = {
			ajaxUrl: '<?php echo App::url("ajax.php") ?>',
			lang: <?php echo json_encode(trans('main.js')) ?>,
			debug: <?php echo Config::get('app.debug')?1:0; ?>,
		};
	</script>
</head>
<body>
	<?php if (Session::has('reminder_sent')): Session::deleteFlash(); ?>
		<h3><?php _e('main.check_email') ?></h3>
		<?php _e('main.reminder_check_email') ?>
	<?php else: ?>
		<h3><?php echo _e('main.recover_pass') ?></h3>
		<form action="reminder" class="ajax-form">
			<p>
		        <label for="email"><?php _e('main.enter_email') ?></label>
		        <input type="text" name="email" id="email">
		    </p>
			
			<?php if (Config::get('auth.captcha')): ?>
			    <p class="recaptcha">
			    	<label for="recaptcha_response_field"><?php _e('main.enter_captcha') ?></label>
					<div id="recaptcha_widget" class="recaptcha-outer" style="display:none">
						<div id="recaptcha_image" class="recaptcha-image"></div>
					    <div class="recaptcha-controls">
							<div><a href="javascript:Recaptcha.reload()" tabindex="-1"><?php _e('main.captcha_reload') ?></a> |</div>
							<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')" tabindex="-1"><?php _e('main.captcha_listen') ?></a> |</div>
							<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')" tabindex="-1"><?php _e('main.captcha_image') ?></a> |</div>
							<div><a href="javascript:Recaptcha.showhelp()" tabindex="-1"><?php _e('main.captcha_help') ?></a></div>
						</div>
						<input type="text" name="captcha" id="recaptcha_response_field">
					</div>
					<script type="text/javascript">
						var RecaptchaOptions = {
						    theme : 'custom',
						    custom_theme_widget: 'recaptcha_widget'
						};
					 </script>
					<script src="http://www.google.com/recaptcha/api/challenge?k=<?php echo Config::get('services.recaptcha.public_key') ?>"></script>
			    </p>
			<?php endif ?>

		    <p>
		    	<button type="submit" name="submit"><?php _e('main.continue') ?></button>
		    </p>
		</form>
	<?php endif ?>
</body>
</html>