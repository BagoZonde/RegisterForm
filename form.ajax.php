<?php
	/**
	 * This file is requested by AJAX when bootstrap and POSTing.
	 */

	//Form configuration
	$form_1_config=array(
		'title'=>'Register Form',
		'method'=>'POST',
		'action'=>'',
		'form_name'=>'register_form',
		'form_id'=>'register_form_1',
		'target_id'=>'formWindow',
		'ajax_frame'=>'form.ajax.php',
		'fields'=>array(
			'name'=>array('label'=>'Name', 'placeholder'=>'e.g. James', 'type'=>'text', 'maxlength'=>'50', 'regex'=>'/^[\w\d\s\p{L}]+$/u'),
			'surname'=>array('label'=>'Surname', 'placeholder'=>'e.g. Town', 'type'=>'text', 'maxlength'=>50, 'regex'=>'/^[\w\d\s\p{L}]+$/u'),
			'email'=>array('label'=>'E-mail', 'placeholder'=>'e.g. james@jamestown.com', 'type'=>'text', 'maxlength'=>40, 'regex'=>'/^[a-zA-Z0-9._-]+@[A-Za-z0-9-]+\.[a-z]{2,4}$/'),
			'password'=>array('label'=>'Password (must contains at least capital letter, small letter and digit). Length: 4-10 characters', 'type'=>'password', 'maxlength'=>10, 'regex'=>'/^(?=.*\d+)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%]{6,10}$/'),
			'password_re_enter'=>array('label'=>'Re-enter password', 'type'=>'password', 'maxlength'=>10, 'regex'=>'/^(?=.*\d+)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%]{6,10}$/', 'compare'=>'password'),
			'sex'=>array('label'=>'Sex', 'type'=>'select', 'maxlength'=>2, 'options'=>array(''=>'- select -', 'M'=>'Man', 'W'=>'Woman'), 'regex'=>'/\b(M|W)\b/'),
		),
		'submit'=>'Register me',
		'done'=>'<div class="thankyou">Thank you!</div>',
	);

	//Form
	try{
		$RegisterForm = new Auth('form_1', $form_1_config);
	}catch(Exception $e){
		print '<span class="warning">' . $e->getMessage() . '</span>';
	}

	//Autoload classes
	function __autoload($class_name){
		try{
			if (@!include_once('classes/' . $class_name . '.class.php')){
				throw new Exception('Can\'t locate ' .$class_name. ' class file.');
			}
		}catch (Exception $e){
			print '<span class="warning">' . $e->getMessage() . '</span>';
			die();
		}
	}
