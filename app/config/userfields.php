<?php

return array(

	'first_name' => array(
		'type' => 'text',
		'attributes' => array(),
		'content_before' => '<p>',
		'content_after'  => '</p>',
		'assignment' => array('admin', 'user')
	),


	'last_name' => array(
		'type' => 'text',
		'attributes' => array(),
		'content_before' => '<p>',
		'content_after'  => '</p>',
		'assignment' => array('admin', 'user')
	),


	'gender' => array(
		'type' => 'select',
		'validation' => 'required|in:X,F,M',
		'attributes' => array(
			'options' => array(
				array('value' => 'X'),
				array('value' => 'F'),
				array('value' => 'M')
			),
		),
		'content_before' => '<p>',
		'content_after'  => '</p>',
		'assignment' => array('admin', 'user')
	),


	'birthday' => array(
		'type' => 'text',
		'validation' => 'date_format:Y-m-d',
		'attributes' => array(),
		'content_before' => '<p>',
		'content_after'  => '</p>',
		'assignment' => array('admin', 'user')
	),


	'url' => array(
		'type' => 'text',
		'attributes' => array(),
		'content_before' => '<p>',
		'content_after'  => '</p>',
		'validation' => 'url',
		'assignment' => array('admin', 'user')
	),


	'phone' => array(
		'type' => 'text',
		'attributes' => array(),
		'content_before' => '<p>',
		'content_after'  => '</p>',
		'assignment' => array('admin', 'user')
	),


	'location' => array(
		'type' => 'text',
		'attributes' => array(),
		'content_before' => '<p>',
		'content_after'  => '</p>',
		'assignment' => array('admin', 'user')
	),


	'about' => array(
		'type' => 'textarea',
		'attributes' => array(),
		'content_before' => '<p>',
		'content_after'  => '</p>',
		'assignment' => array('admin', 'user')
	),


	'verified' => array(
		'type' => 'checkbox',
		'validation' => 'in:1',
		'attributes' => array('value' => '1'),
		'content_before' => '<div class="checkbox">',
		'content_after'  => '</div>',
		'assignment' => array('admin')
	)
);