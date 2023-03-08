<?php
	
	// Start the session
	session_start();
	$print_message = '';

	// Access only if has been initialized the role and selected to delete a movie
	if( (!isset($_SESSION['role'])) or strcmp($_SESSION['role'],'CINEMAOWNER') !== 0 or (!isset($_REQUEST['id_op'])) ){
		header('Location: owner.php?error=invalid_access');
	}

	// Delete cinemaowner movie
	// Create and initialize a curl session
	$curl = curl_init();

	// Set the options of curl session
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://172.18.1.10:1027/api/movies/'.$_REQUEST['id_op'],
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

	if (strcmp($httpcode,'200') == 0) { $print_message = "Record deleted successfully"; }
	else { $print_message = "Error status code ".$httpcode. "<br>";  }


	// Delete the movie from favorites table if exists
	// Create and initialize a curl session
	$curl = curl_init();

	// Set the options of curl session
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://172.18.1.10:1027/api/favorites/'.$_REQUEST['id_op'].'?movieid='.$_REQUEST['id_op'],
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

	if (strcmp($httpcode,'200') !== 0) {  $print_message = "Error status code ".$httpcode. "<br>"; }

	// Retrun the response to client
	echo $print_message;
?>


