<?php

	if(!isset($_SESSION[$BXAF_CONFIG['BXAF_LOGIN_KEY']]) || ! $_SESSION[$BXAF_CONFIG['BXAF_LOGIN_KEY']]){

		$_SESSION['BXAF_USER_LOGIN_FAILED'] = $_SERVER['REQUEST_URI'];

		header("Location: " . $BXAF_CONFIG['BXAF_LOGIN_PAGE']);
	}
	else {
		if (isset($_SESSION['BXAF_USER_LOGIN_FAILED'])) unset($_SESSION['BXAF_USER_LOGIN_FAILED']);
	}

?>