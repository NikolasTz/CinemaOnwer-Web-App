<?php
	
	// Start the session
	session_start();

	// Access only if has been initialized the role and only if has been selected to delete notification from subscription table
	if( (!isset($_SESSION['role'])) or strcmp($_SESSION['role'],'USER') !== 0 or (!isset($_REQUEST['subid'])) ){
		header('Location: welcome.php?error=invalid_access');
	}

	// Delete notification from subscription
	// Create and initialize a curl session
	$curl = curl_init();

	// Set the options of curl session
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://172.18.1.10:1027/api/subscriptions/'.$_REQUEST['subid'],
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HEADER => false,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_HTTPHEADER => array(
            'X-Auth-Token: '.$_SESSION['access_token']
        ),
	));
	
	// Execute the request
	$response = curl_exec($curl);

	// Get the http status code
	$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

	// Close curl resource to free up system resources
	curl_close($curl);

	// If the access_token is expired then redirect to access_token_expired
	if( strcmp($httpcode,'401') == 0 ){ echo 'access_token_expired'; exit(); }

	if (strcmp($httpcode,'200') == 0) { $print_message = "Notification deleted successfully"; }
	else { $print_message = "Error: status code ".$httpcode. "<br>";  }

?>