// reCAPTCHA helper
EasyLogin.recaptcha = {
	html: function () {
    	var template = $('#recaptchaTemplate');
		
		return template.length ? template.html() : '';
	},

	get: function () {
		if (EasyLogin.recaptcha.html() == '') return;

		var protocol = window.location.protocol == 'https:' ? 'https' : 'http';
		var public_key = $('#recaptcha_public_key').val();

		$.getScript(protocol+'://www.google.com/recaptcha/api/js/recaptcha_ajax.js', function () {
			Recaptcha.create(public_key, 'recaptcha_widget', {
				theme : 'custom',
				custom_theme_widget: 'recaptcha_widget'
			});
		});
	}
};

jQuery(function ($) {
	// Render the reCAPTCHA input and image
	$('#reminderModal, #activationModal, #signupModal').on('show.bs.modal', function (e) {
		$('.modal .recaptcha').html('');	
		$(e.currentTarget).find('.recaptcha').html( EasyLogin.recaptcha.html() );
		EasyLogin.recaptcha.get();
	});

	// Clear the hash when the reset and activation modals are closing
	$('#resetModal, #activateModal').on('hide.bs.modal', function () {
		window.location.hash = '';
	});

	$('.avatar-container select').on('change', function () {
		$.get(EasyLogin.options.ajaxUrl, {action: 'avatarPreview', type: $(this).val()}, function (response) {
			if (response.success) 
				$('.avatar-image').attr('src', response.message);
		}, 'json');
	});

	$('#settingsModal a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		var modal = $('#settingsModal');
		var action = $(e.target).attr('href').replace('#', '');
		
		modal.find('form').attr('action', action != 'connectTab' ? action : '');

		modal.find('.alert').hide();

		if (action == 'settingsMessages') {
			$.get(EasyLogin.options.ajaxUrl, {action: 'getContacts'}, function(response) {
				if (response.success) {
					var list = modal.find('.contact-list');
					list.html('');
					
					for (var i = 0; i < response.message.length; i++) {
						list.append(tmpl('contactItemTemplate', response.message[i]));
					}

				}
			}, 'json');
		}
	});

	$('.ajax-form').on('click', '.social-connect a', function(e) {
		EasyLogin.alert(EasyLogin.trans('connecting') + $(this).text() + '...', 0, $(e.delegateTarget));
	});

	// Open password reset and activation modals if we
	// found a reminder in the hash. Eg: #reset-123456
	var hash = window.location.hash;
	switch ( hash.substr(1, hash.indexOf('-')-1) ) {
		case 'reset':
			var modal = $('#resetModal');
			modal.find('[name="reminder"]').val( hash.substr(hash.indexOf('-')+1, hash.length ) );
			modal.modal('show');
		break;

		case 'activate':
			var modal = $('#activateModal');
			modal.find('[name="reminder"]').val( hash.substr(hash.indexOf('-')+1, hash.length ) );
			modal.modal('show');
			modal.on('shown.bs.modal', function (){
				modal.find('form').trigger('submit');
			});
		break;

		case 'settings':
			var modal = $('#settingsModal');
			modal.modal('show');
			modal.find('a[href="#connectTab"]').tab('show');

			window.location.hash = '';
		break;
	}
});

// Register ajaxForm callbacks

EasyLogin.ajaxFormCb.login = function (message) {
	if (message.length)
		window.location.href = message;
	else 
		window.location.reload();
};

EasyLogin.ajaxFormCb.signup = function (message) {
	if ($('#signupModal').css('display') == 'block')
		$('#signupSuccessModal').modal('show');
	else if (message && message.redirect)
		window.location.href = message.redirect;
	else
		window.location.reload();
};

EasyLogin.ajaxFormCb.activation = function () {
	if ($('#activationModal').css('display') == 'block')
		$('#activationSuccessModal').modal('show');
	else
		window.location.reload();
};

EasyLogin.ajaxFormCb.activate = function () {
	$('#activateSuccessModal').modal('show');
};

EasyLogin.ajaxFormCb.reminder = function () {
	if ($('#reminderModal').css('display') == 'block')
		$('#reminderSuccessModal').modal('show');
	else
		window.location.reload();
};

EasyLogin.ajaxFormCb.reset = function () {
	if ($('#resetModal').css('display') == 'block')
		$('#resetSuccessModal').modal('show');
	else
		window.location.href = window.location.origin + window.location.pathname;
};

EasyLogin.ajaxFormCb.settingsAccount =
EasyLogin.ajaxFormCb.settingsProfile = 
EasyLogin.ajaxFormCb.settingsMessages = function (m, form) {
	EasyLogin.alert(EasyLogin.trans('changes_saved'), 1, form);
};

EasyLogin.ajaxFormCb.settingsPassword = function (m, form) {
	form.find('input').val('');
	EasyLogin.alert(EasyLogin.trans('pass_changed'), 1, form);
};

EasyLogin.ajaxFormCb.webmasterContact = function (m, form) {
	form.find('[name="message"]').val('');

	EasyLogin.alert(EasyLogin.trans('message_sent'), 1, form);
};