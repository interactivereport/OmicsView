<?php

// Important: disable login requirement
$BXAF_CONFIG_CUSTOM['PAGE_LOGIN_REQUIRED']	= false;
$BXAF_CONFIG_CUSTOM['BXAF_PAGE_SPLIT']		= false;

include_once(dirname(__FILE__) . "/config.php");


if (isset($_SESSION[$BXAF_CONFIG['BXAF_LOGIN_KEY']])){
	header("Location: " . $BXAF_CONFIG['BXAF_LOGOUT_PAGE']);
	exit();
}

if ($BXAF_CONFIG['USER_SIGNUP_ENABLE'] && count($_POST) > 0){

	$_POST 			= array_map('trim', $_POST);
	$_POST['Email']	= strtolower($_POST['Email']);

	$_POST_addslashes 	= array_map('addslashes', $_POST);

	$found_error = false;
	if ($_POST['First_Name'] == ''){
		$found_error = true;
		$error_message = "The first name is required and it cannot be empty.";
	}

	if ($_POST['Last_Name'] == ''){
		$found_error = true;
		$error_message = "The last name is required and it cannot be empty.";
	}

	if ($_POST['Email'] == ''){
		$found_error = true;
		$error_message = "The email is required and it cannot be empty.";
	}
	else if (!bxaf_validate_email($_POST['Email'])){
		$found_error = true;
		$error_message = "The email you entered ({$_POST['Email']}) is invalid.";
	}
	else {

		$sql = "SELECT * FROM {$BXAF_CONFIG['TBL_BXAF_LOGIN']} WHERE Login_Name = '{$_POST_addslashes['Email']}'";

		if ($BXAF_CONFIG['BXAF_DB_DRIVER'] == 'sqlite'){
			$BXAF_CONN = bxaf_get_user_db_connection();
			$user_info 	= $BXAF_CONN->querySingle($sql, true);
			$BXAF_CONN->close();

		}
		else if($BXAF_CONFIG['BXAF_DB_DRIVER'] == 'mysql'){
			$BXAF_CONN = bxaf_get_user_db_connection();
			$user_info = $BXAF_CONN->get_row($sql);
		}

		$found_id = intval($user_info['ID']);

		if ($found_id > 0){
			$found_error = true;
			$error_message = "The email ({$_POST['Email']}) has been taken. Please try again with a different email address.";
		}
	}



	if (! $found_error){

		$dataArray = array();

		if(	isset($BXAF_CONFIG['BXAF_USER_DEFAULT_PASSWORD']) && $BXAF_CONFIG['BXAF_USER_DEFAULT_PASSWORD'] != '' &&
			isset($_POST['Use_Default_Password']) && $_POST['Use_Default_Password'] == 1){
			$_POST['Password'] = $BXAF_CONFIG['BXAF_USER_DEFAULT_PASSWORD'];
		}
		if($_POST['Password'] == ''){
			$_POST['Password'] = bxaf_random_password(8);
		}

		$dataArray['Name'] 			= "{$_POST_addslashes['First_Name']} {$_POST_addslashes['Last_Name']}";
		$dataArray['Login_Name'] 	= $_POST_addslashes['Email'];
		$dataArray['Password'] 		= md5($_POST['Password']);
		$dataArray['First_Name'] 	= $_POST_addslashes['First_Name'];
		$dataArray['Last_Name'] 	= $_POST_addslashes['Last_Name'];
		$dataArray['Email'] 		= $_POST_addslashes['Email'];

		$sql = "INSERT INTO {$BXAF_CONFIG['TBL_BXAF_LOGIN']} (Name, Login_Name, Password, First_Name, Last_Name, Email) VALUES ('" . implode("','", $dataArray) . "');";

		$result = false;
		if ($BXAF_CONFIG['BXAF_DB_DRIVER'] == 'sqlite'){
			$BXAF_CONN = bxaf_get_user_db_connection();
			$result = $BXAF_CONN->exec($sql);
			$BXAF_CONN->close();
		}
		else if($BXAF_CONFIG['BXAF_DB_DRIVER'] == 'mysql'){
			$result = $BXAF_CONN->Execute($sql);
		}

		if(! $result){
			$error_message = "Your account can not be created. <BR>Please contact system administrator.";
		}
		else {

			$sql = "SELECT * FROM {$BXAF_CONFIG['TBL_BXAF_LOGIN']} WHERE Login_Name = '{$_POST_addslashes['Email']}'";

			$found_id = 0;
			$user_info = array();

			if ($BXAF_CONFIG['BXAF_DB_DRIVER'] == 'sqlite'){
				$BXAF_CONN = bxaf_get_user_db_connection();
				$user_info 	= $BXAF_CONN->querySingle($sql, true);
				$BXAF_CONN->close();
			}
			else if($BXAF_CONFIG['BXAF_DB_DRIVER'] == 'mysql'){
				$user_info = $BXAF_CONN->get_row($sql);
			}

			if (is_array($user_info) && count($user_info) > 0){

				$found_id = intval($user_info['ID']);

				$_SESSION[$BXAF_CONFIG['BXAF_LOGIN_KEY']] 	= $found_id;
				$_SESSION['User_Info'] = $user_info;
				if($_SESSION['User_Info']['Category'] == 'Advanced') $_SESSION['BXAF_ADVANCED_USER'] = true;

				$body = "
					<p>Hello {$_POST['First_Name']},</p>
					<p>Here is your account information on <a href='{$BXAF_CONFIG['BXAF_WEB_URL']}' target='_blank'>{$BXAF_CONFIG['BXAF_PAGE_APP_NAME']}</a>:</p>
					<p>
						E-mail: <strong>{$_POST['Email']}</strong><BR />
						Password: <strong>(******)</strong>
					</p>
					<p>If you have any questions, please contact us at <a href='mailto:{$BXAF_CONFIG['BXAF_PAGE_EMAIL']}'>{$BXAF_CONFIG['BXAF_PAGE_EMAIL']}</a>.</p>
					<p>Thank you!</p>
					<p>Sincerely,<BR />{$BXAF_CONFIG['BXAF_PAGE_AUTHOR']}<BR />{$BXAF_CONFIG['BXAF_PAGE_APP_NAME']}<BR />{$BXAF_CONFIG['BXAF_WEB_URL']}</p>
				";

				$email_param = array(
					'From'     => $BXAF_CONFIG['BXAF_PAGE_EMAIL'],
					'FromName' => $BXAF_CONFIG['BXAF_PAGE_APP_NAME'],
					'Subject'  => "Welcome to " . $BXAF_CONFIG['BXAF_PAGE_APP_NAME'],
					'Body'     => $body,
					'To'       => array($_POST['Email'] => $dataArray['Name']),
				);
				$result = bxaf_send_email($email_param);

				// Send email to webmaster
				$body = "
					<p>Hello,</p>
					<p>New user has signed up on <a href='{$BXAF_CONFIG['BXAF_WEB_URL']}' target='_blank'>{$BXAF_CONFIG['BXAF_PAGE_APP_NAME']}</a>:</p>
					<p>
						E-mail: <strong>{$_POST['Email']}</strong><BR />
						Name: <strong>{$dataArray['Name']}</strong>
					</p>
					<p>Sincerely,<BR />{$BXAF_CONFIG['BXAF_PAGE_AUTHOR']}<BR />{$BXAF_CONFIG['BXAF_PAGE_APP_NAME']}<BR />{$BXAF_CONFIG['BXAF_WEB_URL']}</p>
				";

				$email_param = array(
					'From'     => $BXAF_CONFIG['BXAF_PAGE_EMAIL'],
					'FromName' => $BXAF_CONFIG['BXAF_PAGE_APP_NAME'],
					'Subject'  => "New user sign up on " . $BXAF_CONFIG['BXAF_PAGE_APP_NAME'],
					'Body'     => $body,
					'To'       => array($BXAF_CONFIG['BXAF_PAGE_EMAIL'] => $BXAF_CONFIG['BXAF_PAGE_APP_NAME']),
				);
				$result = bxaf_send_email($email_param);


				header("Location: " . $BXAF_CONFIG['BXAF_LOGIN_SUCCESS']);
				exit();

			}
			else {
				$error_message = "Your account can not be created. <BR>Please contact system administrator.";
			}

		}
	}

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<?php
		include_once('page_header.php');
	?>
</head>

<body>


<?php include_once('page_menu.php'); ?>


<div class="container-fluid">
	<div class="row mt-5 p-2">

        <div class="mx-auto" style="width: 350px;">

			<h3 class="w-100 py-2">Create an account now</h3>
			<p class="text-muted">*: all fields are required</p>

			<?php
				if (!$BXAF_CONFIG['USER_SIGNUP_ENABLE']){
					echo "<div class='p-4 alert alert-danger'>This tool is disabled. Please contact the system administrator for details.</div>";
				} else {

					if ($error_message != ''){
						echo "<div class='p-4 alert alert-danger'>{$error_message}</div>";
					}

			?>

					<form action='<?php echo $_POST['BXAF_SIGNUP_PAGE']; ?>' method='post' role='form'>
						<div class='form-group'>
							 <label class="font-weight-bold" for="First_Name">First Name:</label>
							<input type='text' class='form-control' name='First_Name' placeholder='* Your first name' value='<?php if(isset($_POST['First_Name'])) echo $_POST['First_Name']; else if(isset($_GET['First_Name'])) echo $_GET['First_Name']; ?>' autofocus required>
						</div>
						<div class='form-group'>
							<label class="font-weight-bold" for="Last_Name">Last Name:</label>
							<input type='text' class='form-control' name='Last_Name' placeholder='* Your last name' value='<?php if(isset($_POST['Last_Name'])) echo $_POST['Last_Name']; else if(isset($_GET['Last_Name'])) echo $_GET['Last_Name']; ?>' required>
						</div>
						<div class='form-group'>
							<label class="font-weight-bold" for="Email">E-mail:</label>
							<input type='email' class='form-control' name='Email' placeholder='* Your e-mail address' value='<?php if(isset($_POST['Email'])) echo $_POST['Email']; else if(isset($_GET['Email'])) echo $_GET['Email']; ?>' required>
						</div>

						<div class='form-group form-inline'>
							<label class="font-weight-bold" for="Password">Password:</label>
							<?php if(isset($BXAF_CONFIG['BXAF_USER_DEFAULT_PASSWORD']) && $BXAF_CONFIG['BXAF_USER_DEFAULT_PASSWORD'] != ''){ ?>
								<input type='checkbox' name='Use_Default_Password' id='Use_Default_Password' checked="checked" class="form-control ml-3 mr-1" value="1" onClick="if(! $(this).prop('checked') ) {  $('#section_password').removeClass('hidden'); } else { $('#section_password').addClass('hidden'); } "> <span>Use default password</span>
							<?php }  ?>
						</div>

						<div class='form-group<?php if(isset($BXAF_CONFIG['BXAF_USER_DEFAULT_PASSWORD']) && $BXAF_CONFIG['BXAF_USER_DEFAULT_PASSWORD'] != '') echo ' hidden'; ?>' id="section_password">
							<input type='password' class='form-control' name='Password' placeholder='Your own password' value=''>
						</div>

						<button type='submit' class='btn btn-primary'><i class='fas fa-user-plus'></i> Sign Up</button>
						<a class='btn btn-link' href='<?php echo $BXAF_CONFIG['BXAF_LOGIN_PAGE']; ?>'><i class='fas fa-sign-in-alt'></i> Sign In</a>

					</form>
			<?php } ?>

        </div>

	</div>

</div>

</BODY>
</HTML>