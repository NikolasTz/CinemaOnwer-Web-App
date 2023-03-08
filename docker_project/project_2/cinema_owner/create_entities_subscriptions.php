<?php 
	
	// Start the session
	session_start();

	// Access only if has been initialized the role and the role is CINEMAOWNER
	if( (!isset($_SESSION['role'])) or strcmp($_SESSION['role'],'CINEMAOWNER') !== 0 ){
		header('Location: owner.php?error=invalid_access');
	}
	
	// Return the result as string
	$str = "";

	// Get the movies for CINEMAOWNER
	// Create and initialize a curl session
	$curl = curl_init();

	// Set the options of curl session
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://172.18.1.10:1027/api/movies?cinemaownerid='.$_SESSION['id'],
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

    // Create entities and subsciptions on Orion
	if ( count($result) > 0 and !(isset($result['error'])) ){
		foreach($result as $rows){

			// Create as many entities as movies on Orion
			// Create and initialize a curl session
			$curl = curl_init();

			// Preparation of data that will be sended wtih POST request
			$data = array(
				"id" => $rows['_id'],
				"type" => "Movie",
				"TITLE" => array(
					"type" => "String",
					"value" => $rows["TITLE"]
				),
				"STARTDATE" => array(
					"type" => "DateTime",
					"value" => $rows["STARTDATE"]
				),
				"ENDDATE" => array(
					"type" => "DateTime",
					"value" => $rows["ENDDATE"]
				),
				"CINEMANAME" => array(
					"type" => "String",
					"value" => $rows["CINEMANAME"]
				),
				"CATEGORY" => array(
					"type" => "String",
					"value" => $rows["CATEGORY"]
				),
				"CINEMAOWNERID" => array(
					"type" => "String",
					"value"=> $_SESSION['id']
				)
			);

			// Set the options of curl session
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'http://172.18.1.11:1028/v2/entities',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HEADER => false,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_POSTFIELDS => json_encode($data),
				CURLOPT_CUSTOMREQUEST => 'POST',
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

			// If movie exists then updated
			if( isset($result['description']) and strcmp($result['description'],'Already Exists') == 0 ){
				
				// Create and initialize a curl session
				$curl = curl_init();

				// Preparation of data that will be sended wtih POST request
				$data = array(
					"TITLE" => array(
						"type" => "String",
						"value" => $rows["TITLE"]
					),
					"STARTDATE" => array(
						"type" => "DateTime",
						"value" => $rows["STARTDATE"]
					),
					"ENDDATE" => array(
						"type" => "DateTime",
						"value" => $rows["ENDDATE"]
					),
					"CINEMANAME" => array(
						"type" => "String",
						"value" => $rows["CINEMANAME"]
					),
					"CATEGORY" => array(
						"type" => "String",
						"value" => $rows["CATEGORY"]
					),
					"CINEMAOWNERID" => array(
						"type" => "String",
						"value"=> $_SESSION['id']
					)
				);
			
				// Set the options of curl session
				curl_setopt_array($curl, array(
					CURLOPT_URL => 'http://172.18.1.11:1028/v2/entities/'.$rows['_id'].'/attrs',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HEADER => false,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_POSTFIELDS => json_encode($data),
					CURLOPT_CUSTOMREQUEST => 'PATCH',
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
			}
			// Create the subscription for movies
			else{

				// Subscription for the movie
			    // Create and initialize a curl session
				$curl = curl_init();

				// Preparation of data that will be sended wtih POST request	 
				$data = array(
					"description" => "A subscription to get info about ".$rows['TITLE'],
					"subject" => array(
						"entities" => [ array(
							"id" => $rows['_id'],
							"type" => "Movie"
						)],
						"condition" => array(
							"attrs" => ["STARTDATE" , "ENDDATE"],		
						)			
					),
					"notification" => array(
						"http" => array(
							"url" =>  "http://nodejs:27018/api/subscriptions"
						),
						"attrs" => []
					),
					"expires" => "2040-01-01T14:00:00.00Z",
					"throttling" => 5
				);

				// Set the options of curl session
				curl_setopt_array($curl, array(
					CURLOPT_URL => 'http://172.18.1.11:1028/v2/subscriptions',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HEADER => false,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_POSTFIELDS => json_encode($data),
					CURLOPT_CUSTOMREQUEST => 'POST',
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

			}
		}		
	}

?>