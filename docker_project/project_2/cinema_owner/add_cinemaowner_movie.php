<?php
	
	// Start the session
	session_start();
	$print_message = '';

	// Access only if has been initialized the role and only if has been selected the submit button
	if( (!isset($_SESSION['role'])) or strcmp($_SESSION['role'],'CINEMAOWNER') !== 0 or (!isset($_POST['submit'])) ){
		header('Location: owner.php?error=invalid_access');
	}

	// Initialize variable for errors
	$errors = array('title' => '', 'startdate' => '' , 'enddate' => '' , 'category' => '');

	// Get the movie id and cinemaname
	$cinemaname = $_POST['cinemaname'];

	// Update the values after the check
	if(isset($_POST['submit'])){
		
		// Check Title
		$title = $_POST['title'];
		if(!preg_match('/^[\w\-\s]+$/', $title)){
			$errors['title'] = 'Title must be letters and numbers only';
		}

		// Start Date
		$startdate = ( strcmp($_POST['startdate'],'') !== 0 ) ? $_POST['startdate'] : $errors['startdate'] = 'Please insert the starting date';

		// End Date
		$enddate = ( strcmp($_POST['enddate'],'') !== 0 ) ? $_POST['enddate'] : $errors['enddate'] = 'Please insert the ending date';

		// Check Category
		$category = $_POST['category'];
		if(!preg_match('/^[a-zA-Z]+$/', $category)){
			$errors['category'] = 'Category must be letters only';
		}

		// If not exist erros then add the movie
		if(!array_filter($errors)){

			// Insert cinemaowner movie to database
			$curl = curl_init();

			// Preparation of data that will be sended wtih POST request
			$data = array(
				"TITLE" => $title,
				"STARTDATE" => $startdate,
				"ENDDATE" => $enddate,
				"CINEMANAME" => $cinemaname,
				"CATEGORY" => $category,
				"CINEMAOWNERID" => $_SESSION['id']
			);

			// Set the options of curl session
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'http://172.18.1.10:1027/api/movies',
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

			if (strcmp($httpcode,'200') == 0) { $print_message = "Record inserted successfully"; }
			else { $print_message = "Error status code ".$httpcode. "<br>";   }
		}

	} // end POST check and Submit

	// Return response to client
	$str = array('print_message'=>$print_message,'error'=>$errors);
	echo http_build_query($str,'','?');
?>