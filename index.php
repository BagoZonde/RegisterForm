<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link href="styles/style.css" media="screen" rel="stylesheet" type="text/css">
		<title>Register Form</title>
	</head>
	<body>
		<?php
		
		
			$RegisterForm = new Auth();
			$RegisterForm->display();
			
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

			
		?>
	</body>
</html>
