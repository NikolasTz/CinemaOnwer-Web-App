<?php  
	
	// Start the session
	session_start();

	// Access only if has been initialized the role and only if has been selected to add movie on favorites table
	if( (!isset($_SESSION['role'])) or strcmp($_SESSION['role'],'USER') !== 0 or (!isset($_REQUEST['movieid'])) ){
		header('Location: movies.php?error=invalid_access');
	}

	// Add movie to favorite
	// Check if movie id already exists on favorites
	// Create and initialize a curl session
	$curl = curl_init();

	// Set the options of curl session
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://172.18.1.10:1027/api/favorites?movieid='.$_REQUEST['movieid'].'&userid='.$_SESSION['id'],
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HEADER => false,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json',
			'X-Auth-Token: '.$_SESSION['access_token']
		),
	));
	
	// Execute the request
	$response = curl_exec($curl);

	// Decode json to a associative array.
	$result = json_decode($response,TRUE);

	// Get the http status code
	$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

	// Close curl resource to free up system resources
	curl_close($curl);

	// If the access_token is expired then redirect to access_token_expired
	if( strcmp($httpcode,'401') == 0 ){ echo 'access_token_expired'; exit(); }

	// If exist then print error message
	if (count($result) == 1){
		$print_message = "This movie is already exist in favorite.Please choose another";
	}
	else{

		// Insert favorite movie to database
		$curl = curl_init();

		// Preparation of data that will be sended wtih POST request
		$data = array(
			"USERID" => $_SESSION['id'],
			"MOVEID" => $_REQUEST['movieid']
		);

		// Set the options of curl session
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'http://172.18.1.10:1027/api/favorites',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($data),
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/json',
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

		if (strcmp($httpcode,'200') == 0) { $print_message = "Movie added successfully to Favorites"; }
		else { $print_message = "Error: status code ".$httpcode. "<br>";   }
		
	}

	// Retrun message to client
	echo $print_message;

?>