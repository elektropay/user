<?php

require_once 'app/init.php';

if (isset($_GET['logout'])) {
	Auth::logout();
	redirect_to( App::url('admin.php') );
}

if (Auth::guest()) { 
	echo View::make('admin.login')->render();
	exit; 
}

if (!Auth::userCan('dashboard')) {
	echo View::make('admin.restricted')->render();
	exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

if (View::exists("admin.{$page}")) {
	echo View::make('admin.'.$page)->render();
} else {
	echo View::make('admin.404')->render();
}