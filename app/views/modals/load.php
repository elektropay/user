<?php 
if (Auth::check()) {

	if (@$_GET['p'] != 'profile') {
		echo View::make('modals.settings')->render();
	}

	echo View::make('modals.pms')->render();

} else {
	echo View::make('modals.login')->render();

	echo View::make('modals.signup')->render();

	echo View::make('modals.activation')->render();

	echo View::make('modals.reminder')->render();

	if (Config::get('auth.captcha')) { 
		?>
		<script type="text/template" id="recaptchaTemplate">
			<label for="recaptcha_response_field"><?php _e('main.enter_captcha') ?></label>
			<div id="recaptcha_widget" class="recaptcha-outer" style="display:none">
				<div id="recaptcha_image" class="recaptcha-image"></div>
			    <div class="recaptcha-controls">
					<div><a href="javascript:Recaptcha.reload()" tabindex="-1"><?php _e('main.captcha_reload') ?></a> |</div>
					<div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')" tabindex="-1"><?php _e('main.captcha_listen') ?></a> |</div>
					<div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')" tabindex="-1"><?php _e('main.captcha_image') ?></a> |</div>
					<div><a href="javascript:Recaptcha.showhelp()" tabindex="-1"><?php _e('main.captcha_help') ?></a></div>
				</div>
				<input type="text" name="captcha" id="recaptcha_response_field" class="form-control">
			</div>
		</script>
		<input type="hidden" id="recaptcha_public_key" value="<?php echo Config::get('services.recaptcha.public_key') ?>">
		<?php 
	}
}