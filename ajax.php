<?php

//header('Access-Control-Allow-Origin: yourwebsite.com');
//header('Access-Control-Allow-Origin: www.yourwebsite.com');

require_once 'app/init.php';

use Hazzard\Support\MessageBag;

// CSRF check
if (Config::get('app.csrf')) {
	if (is_ajax_request()) {
		$headers = array_change_key_case(getallheaders());
		$token = isset($headers['x-csrf-token']) ? $headers['x-csrf-token'] : '';
	} else {
		$token = @$_GET['_token'];
	}

	if (Session::token() != $token) {
		Session::regenerateToken();
		json_message('CSRF Token Mismatch. Reload the page.', false);
	}
}

if (isset($_POST['action'])) {

	switch ($_POST['action']) {
		
		// Login
		case 'login':
			if (Auth::check()) exit;
			
			Auth::login($_POST['email'], $_POST['password'], isset($_POST['remember']));

			if (Auth::passes()) {
				json_message(Config::get('auth.login_redirect'));
			} else {
				json_message(Auth::errors()->toArray(), false);
			}
		break;


		// Logout
		case 'logout':
			Auth::logout();
		break;


		// Register
		case 'signup':
			if (Auth::check()) exit;

			Register::signup($_POST);

			if (Register::passes()) {
				if (Config::get('auth.email_activation')) {
					Session::flash('signup_complete', true);
					json_message();
				} else {
					Auth::login($_POST['email'], $_POST['pass1']);
					json_message(array('redirect' => Config::get('auth.login_redirect')));
				}
			} else {
				json_message(Register::errors()->toArray(), false);
			}
		break;

		
		// Send activation reminder
		case 'activation':
			if (Auth::check()) exit;

			Register::reminder($_POST['email'], @$_POST['captcha'], @$_POST['recaptcha_challenge_field']);
			
			if (Register::passes()) {
				Session::flash('activation_sent', true);
				json_message();
			} else {
				json_message(Register::errors()->toArray(), false);
			}
		break;


		// Activate account
		case 'activate':
			if (Auth::check()) exit;

			Register::activate($_POST['reminder']);
			
			if (Register::passes()) {
				json_message();
			} else {
				json_message(Register::errors()->toArray(), false);
			}
		break;


		// Send password reminder
		case 'reminder':
			if (Auth::check()) exit;

			Password::reminder($_POST['email'], @$_POST['captcha'], @$_POST['recaptcha_challenge_field']);
			
			if (Password::passes()) {
				Session::flash('reminder_sent', true);
				json_message();
			} else {
				json_message(Password::errors()->toArray(), false);
			}
		break;


		// Password reset
		case 'reset':
			if (Auth::check()) exit;

			Password::reset($_POST['pass1'], $_POST['pass2'], $_POST['reminder']);
			
			if (Password::passes()) {
				Session::flash('password_updated', true);
				json_message();
			} else {
				json_message(Password::errors()->toArray(), false);
			}
		break;


		// Account settings - Account
		case 'settingsAccount':
			if (Auth::guest()) exit;

			$user = User::find(Auth::user()->id);
			
			$data = array('email' => $_POST['email']);
			$rules = array('email' => 'required|email|max:100|unique:users,email,'.$user->id);

		    if (Config::get('auth.require_username') && Config::get('auth.username_change')) {
		    	$data['username'] = $_POST['username'];
		    	$rules['username'] = 'required|min:3|max:50|alpha_dash|unique:users,username,'.$user->id;
		    }

		    $validator = Validator::make($data, $rules);

			if ($validator->passes()) {
				$user->email = $_POST['email'];

				if (Config::get('auth.require_username') && Config::get('auth.username_change')) {
					$user->username = $_POST['username'];
				}

				if ($user->save()) {
					if (isset($_POST['locale'])) {
						$locale = $_POST['locale'];
						$locales = Config::get('app.locales');

						if (array_key_exists($locale, $locales)) {
							Usermeta::update($user->id, 'locale', $locale);
						}
					}

					json_message();
				} else {
					json_message(with(new MessageBag(array('error' => trans('errors.dbsave'))))->toArray(), false);
				}
			}  else {
				json_message($validator->errors()->toArray(), false);
			}
		break;


		// Account settings - Profile
		case 'settingsProfile':
			if (Auth::guest()) exit;

			$user = User::find(Auth::user()->id);

			$data = array('avatar_type' => $_POST['avatar_type']);

			$types = implode(',', array_keys(Config::get('auth.providers', array())));

			$rules = array('avatar_type' => "in:image,gravatar,$types");

		    foreach (UserFields::all('user') as $key => $field) {
		    	if (!empty($field['validation'])) {
		    		$data[$key] = @$_POST[$key];
		    		$rules[$key] = $field['validation'];
		    	}
		    }

		    $validator = Validator::make($data, $rules);

			if ($validator->passes()) {
				
				$displayName = escape(@$_POST['display_name']);
				if (!empty($displayName)) {
					$user->display_name = $displayName;
				}

				if ($user->save()) {
					$fields = array_merge(UserFields::all('user'), array('avatar_type' => ''));

					foreach ($fields as $key => $field) {
						Usermeta::update($user->id, $key, escape(@$_POST[$key]), @$user->usermeta[$key]);
					}

					json_message();
				} else {
					json_message(with(new MessageBag(array('error' => trans('errors.dbsave'))))->toArray(), false);
				}
			}  else {
				json_message($validator->errors()->toArray(), false);
			}
		break;

		
		// Account settings - Password
		case 'settingsPassword':
			if (Auth::guest()) exit;

			$user = User::find(Auth::user()->id);

			$validator = Validator::make(
				array(
					'current_password' => $_POST['pass1'],
					'new_password' => $_POST['pass2'],
	    			'new_password_confirmation' => $_POST['pass3'],
				), 
				array(
					'new_password' => 'required|between:4,30|confirmed',
					'current_password' => strlen($user->password) ? 'required' : ''
				)
			);

			if ($validator->passes()) {
				if (!strlen($user->password) || (strlen($user->password) && Hash::check($_POST['pass1'], $user->password))) {
					$user->password = Hash::make($_POST['pass2']);
					
					if ($user->save()) {
						json_message();
					} else {
						json_message(with(new MessageBag(array('error' => trans('errors.dbsave'))))->toArray(), false);
					}
				} else {
					json_message(with(new MessageBag(array('error' => trans('errors.current_password'))))->toArray(), false);
				}
			} else {
				json_message($validator->errors()->toArray(), false);
			}
		break;


		// Account settings - Messages
		case 'settingsMessages':
			if (Auth::guest()) exit;

			if (isset($_POST['email_messages'])) {
				Usermeta::update(Auth::user()->id, 'email_messages', 1);
			} else {
				Usermeta::delete(Auth::user()->id, 'email_messages');
			}

			json_message(true);
		break;

		
		// Send Message
		case 'sendMessage':
			if (Auth::guest()) exit;

			if (!isset($_POST['to'], $_POST['message'])) exit;

			$isContact = Contact::check(Auth::user()->id, $_POST['to']);
			$isWebmaster = (int) $_POST['to'] == (int) Config::get('pms.webmaster');

			if (!$isContact && !$isWebmaster && !Auth::userCan('message_users')) {
				json_message(trans('errors.contact'), false);
			}

			$limit = Config::get('pms.limit');

			if (Message::limitExceed($limit, App::make('session')) && !Auth::userCan('message_users')) {
				json_message(trans('errors.message_limit'), false);
			}

			$maxlength = Config::get('pms.maxlength');

			$message = Message::send(Auth::user()->id, $_POST['to'], $_POST['message'], $maxlength);
			
			if (is_array($message)) {
				
				$sendEmail = Usermeta::get($_POST['to'], 'email_messages', true);
				if (!empty($sendEmail)) {
					$user = User::find($_POST['to']);
					if ($user) {
						Mail::send('emails.message', array('body' => $_POST['message']), function($message) use($user) {
							$message->to($user->email);
							$message->subject(trans('emails.new_message_subject', array('user' => Auth::user()->display_name)));
						});
					}
				}

				json_message($message);
			} else if(is_object($message)) {
				json_message($message->toArray(), false);
			} else {
				json_message(trans('errors.db'), false);
			}
		break;


		// Send message to the Webmaster
		case 'webmasterContact':
			if (Auth::guest()) exit;
			
			if (!isset($_POST['message'])) exit;

			$limit = Config::get('pms.limit');

			if (Message::limitExceed($limit, App::make('session'))) {
				json_message(trans('errors.message_limit'), false);
			}

			$webmaster = Config::get('pms.webmaster');
			$maxlength = Config::get('pms.maxlength');

			$message = Message::send(Auth::user()->id, $webmaster, $_POST['message'], $maxlength);
			
			if (is_array($message)) {
				json_message($message);
			} else if(is_object($message)) {
				json_message($message->toArray(), false);
			} else {
				json_message(trans('errors.db'), false);
			}
		break;

		
		// Delete Message(s) for the logged user.
		case 'deleteMessage':
			if (Auth::guest()) exit;
			
			if ( Message::delete(Auth::user()->id, @$_POST['id']) ) {
				json_message(true);
			} else {
				json_message(trans('errors.unexpected'), false);
			}
		break;


		// Mark all messages as read for the logged user.
		case 'markAllAsRead':
			if (Auth::guest()) exit;

			json_message( Message::markAllAsRead( Auth::user()->id ) );
		break;


		// Add contact
		case 'addContact':
			if (Auth::guest()) exit;
			if (!isset($_POST['id'])) exit;

			json_message( Contact::add(Auth::user()->id, $_POST['id']) );
		break;


		// Remove contact
		case 'removeContact':
			if (Auth::guest()) exit;
			if (!isset($_POST['id'])) exit;

			json_message( Contact::remove(Auth::user()->id, $_POST['id']) );
		break;


		// Confirm contact
		case 'confirmContact':
			if (Auth::guest()) exit;
			if (!isset($_POST['id'])) exit;

			json_message( Contact::confirm(Auth::user()->id, $_POST['id']) );
		break;


		//  Delete user (admin)
		case 'deleteUser':
			if (!Auth::userCan('delete_users')) {
				json_message(trans('errors.permission'), false);
			}

			if (!isset($_POST['user_id'])) exit;

			$id = $_POST['user_id'];

			if (!empty($id) && is_numeric($id)) {
				if (Auth::user()->id != $id) {
					User::where('id', $id)->limit(1)->delete();
					
					Usermeta::newQuery()->where('user_id', $id)->delete();

					Message::newQuery()->where('to_user', $id)
										->orWhere('from_user', $id)
										->delete();
					
					Contact::deleteAll($id);
				}
			}

			json_message();
		break;


		// Delete users (admin)
		case 'deleteUsers':
			if (!Auth::userCan('delete_users')) {
				json_message(trans('errors.permission'), false);
			}

			parse_str($_POST['users'], $data);

			if (isset($data['users'])) {
				$users = array();
				
				foreach ((array) $data['users'] as $key => $id) {
					if (is_numeric($id) && $id != Auth::user()->id) { 
						$users[] = $id;
					}
				}

				if (count($users)) {
					$values = array_values($users);
					
					User::whereIn('id', $values)->limit(count($users))->delete();
					
					Usermeta::newQuery()->whereIn('user_id', $values)->delete();
					
					Message::newQuery()->whereIn('to_user', $values)
										->orWhereIn('from_user', $values)
										->delete();

					Contact::newQuery()->whereIn('user1', $values)
										->orWhereIn('user2', $values)
										->delete();
				}
			}

			json_message();
		break;

		
		// Send Email (admin)
		case 'sendEmail':
			if (!Auth::userCan('message_users')) {
				json_message(trans('errors.permission'), false);
			}

			if (!isset($_POST['to'], $_POST['subject'], $_POST['message'])) exit;

			$validator = Validator::make(
				array(
					'to' => $_POST['to'],
					'subject' => $_POST['subject'],
	    			'message' => $_POST['message'],
				), 
				array(
					'to' => 'required',
					'subject' => 'required',
					'message' => 'required',
				)
			);

			if ($validator->passes()) {
				$to = explode(';', $_POST['to']);				
				
				$emails = array();
				foreach ($to as $email) {
					$email = trim($email);

					if (filter_var($email, FILTER_VALIDATE_EMAIL)) $emails[] = $email;
				}

				if (count($emails)) {
					$subject = $_POST['subject'];
					$message = $_POST['message'];

					foreach ($emails as $email) {
						Mail::send('emails.email', array('body' => $message), function($message) use($email, $subject) {
						    $message->to($email)->subject($subject);
						});
					}
				}

				json_message();
			} else {
				json_message($validator->errors()->toArray(), false);
			}
		break;

		// Delete conversation (admin)
		case 'deleteConversation':
			if (!Auth::userCan('message_users')) {
				json_message(trans('errors.permission'), false);
			}

			if (!isset($_POST['user_id'])) exit;

			$webmaster = Config::get('pms.webmaster');

			if (Message::deleteConversation($webmaster, $_POST['user_id'])) {
				json_message();
			} else {
				json_message(trans('errors.db'), false);
			}
		break;


		// Delete conversations (admin)
		case 'deleteConversations':
			if (!Auth::userCan('message_users')) {
				json_message(trans('errors.permission'), false);
			}

			if (!isset($_POST['conversations'])) exit;

			parse_str($_POST['conversations'], $data);

			if (isset($data['messages'])) {
				$webmaster = Config::get('pms.webmaster');

				foreach ((array) $data['messages'] as $userId) {
					Message::deleteConversation($webmaster, $userId);
				}
			}

			json_message();
		break;
	}
}

if (isset($_GET['action'])) {
	switch ($_GET['action']) {
	
		// Get the number of unread messages for the logged user.
		case 'countUnreadMessages':
			if (Auth::guest()) exit;

			json_message( Message::countUnread(Auth::user()->id) );
		break;


		// Get the conversations for the logged user.
		case 'getConversations':
			if (Auth::guest()) exit;

			json_message( Message::getConversations( Auth::user()->id ) );
		break;


		// Get the conversation messages for the logged user.
		case 'getConversation':
			if (Auth::guest()) exit;

			if (!isset($_GET['id'])) exit;

			$timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : null;

			json_message( Message::getConversation(Auth::user()->id, $_GET['id'], $timestamp) );
		break;


		// Get the user contacts
		case 'getContacts':
			if (Auth::guest()) exit;

			json_message( Contact::all(Auth::user()->id) );
		break;


		// Search Contact
		case 'searchContact':
			if (Auth::guest()) exit;
			
			if (!isset($_GET['user']) || strlen($_GET['user']) < 2) exit;

			$user = $_GET['user'];

			$usersTable = User::getTable();
			$contactsTable = Contact::getTable();

			$query = User::select("{$usersTable}.id as id", 'username', 'display_name', 'email');
			
			if (!isset($_GET['admin']) || !Auth::userCan('message_users')) {
				$query->join($contactsTable, function($join) use($usersTable) {
						$join->on("{$usersTable}.id", '=', 'user1')->orOn("{$usersTable}.id", '=', 'user2');
					})
					->where(function($q) {
						$q->where('user1', Auth::user()->id)->orWhere('user2', Auth::user()->id);
					})
					->where("{$usersTable}.id", '!=', Auth::user()->id)->where('accepted', 1);
			}

			$query->where('status', 1)
				->where(function($q) use($user) {
					$q->where('username', 'like', "{$user}%");
					$q->orWhere('display_name', 'like', "{$user}%");
					$q->orWhere('email', 'like', "{$user}%");
				})
				->limit(5);

			$contacts = array();

			foreach ($query->get() as $user) {
				$contacts[] = array(
					'id' => $user->id,
					'name' => $user->display_name,
					'avatar' => $user->avatar,
					'username' => $user->username
				);
			}

			json_message($contacts);
		break;


		// Get avatar preview
		case 'avatarPreview':
			if (Auth::guest()) exit;

			json_message( User::generateAvatar(Auth::user()->usermeta, Auth::user()->email, @$_GET['type']) );
		break;


		// Get users for DataTables (admin)
		case 'getUsers':
			if (!Auth::userCan('list_users')) exit;

			$usersTable = User::getTable();
			$rolesTable = Role::getTable();

			$columns = array(
				array('db' => "{$usersTable}.id",     'dt' => 0, 'as' => 'id'),
				array('db' => 'username',     'dt' => 1),
				array('db' => 'email',        'dt' => 2),
				array('db' => 'display_name', 'dt' => 3),
				array('db' => 'joined',       'dt' => 4, 
					'formatter' => function($data, $row) {
						$date = new DateTime($data);
						return '<span title="'.$date->format('Y-m-d H:i:s').'">'.$date->format('M j, Y').'</span>';
					}
				),
				array('db' => 'status', 'dt' => 5),
				array('db' => "{$rolesTable}.name", 'dt' => 6, 'as' => 'role'),
			);

			$query = User::join($rolesTable, "{$usersTable}.role_id", '=', "{$rolesTable}.id", 'left');

			$dt = new Hazzard\Support\DataTables($_GET, $columns, $query);

			echo json_encode($dt->get());
		break;


		// Get messages for DataTables (admin)
		case 'getMessages':
			if (!Auth::userCan('message_users')) exit;

			$userId = Config::get('pms.webmaster');

			$columns = array(
				array('db' => 'id',        'dt' => 0),
				array('db' => 'message',   'dt' => 1),
				array('db' => 'from_user', 'dt' => 2),
				array('db' => 'date',      'dt' => 3, 
					'formatter' => function($data, $row) {
						$date = new DateTime($data);
						return '<span title="'.$date->format('Y-m-d H:i:s').'">'.$date->format('M j, Y').'</span>';
					}
				),
				array('db' => 'read', 'dt' => 4),
				array('db' => 'to_user', 'dt' => 5)
			);

			$dt = new Hazzard\Support\DataTables($_GET, $columns, Message::newQuery()->orderBy('date', 'desc'));

			$result = $dt->get();

			$messages = array();
			foreach ($result['data'] as $message) {
				$id = ($message[2] == $userId) ? $message[5] : $message[2];
				$user = User::find($id);

				if (!$user || isset($messages[$user->id])) continue;

				$messages[$user->id] = array(
					$message[0],
					mb_strlen($message[1]) > 70 ? mb_substr($message[1], 0, 70).'...' : $message[1],
					empty($user->display_name) ? $user->email : $user->display_name,
					$message[3],
					(bool) $message[4],
					$user->id,
					$message[2] == $userId
				);
			}

			$result['data'] = array_values($messages);

			echo json_encode($result);
		break;
	}
}