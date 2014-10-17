<?php
require_once 'app/init.php';

if (Auth::guest()) exit;

$options = array(
	'upload_dir' => app('path.base') . '/uploads/',
	'upload_url' => App::url('uploads/'),
	
	'max_file_size' => 5000000, // 5 mb
    'max_width'  => 2000,
    'max_height' => 2000,

    'versions' => array(
    	'' => array(
    		'crop' => true,
    		'max_width' => 300,
    		'max_height' => 300
    	),
    ),
	
	'upload_start' => function($image, $instance) {
		$image->name = '~'.Auth::user()->id.'.' . $image->type;
	},

	'crop_start' => function($image, $instance) {
		$image->name = Auth::user()->id.'.' . $image->type;
	},

	'crop_complete' => function($image, $instance) {
		Usermeta::update(Auth::user()->id, 'avatar_image', $image->name);
	}
);

new ImagePicker\ImgPicker($options, trans('imgpicker'));