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

	// Print the result as collection item
	if ( count($result) > 0 and !(isset($result['error'])) ){
		foreach($result as $rows){

			$str = $str."<tr><td>" . $rows["TITLE"] . "</td><td>" .date_format(date_create($rows["STARTDATE"]),"Y-m-d") . "</td><td>" . date_format(date_create($rows["ENDDATE"]),"Y-m-d") . "</td><td>" 
			.$rows["CINEMANAME"]."</td><td>".$rows["CATEGORY"]."</td>";

			$str = $str."<td>
						<div class=\"input-field\">
							<button onclick=\"displayEditFormMovie('$rows[_id]')\" data-target=\"modal1\" class=\"btn-small waves-effect waves-light tooltipped modal-trigger\" data-position=\"bottom\" data-tooltip=\"Edit movie\">
								<i class=\"material-icons center\">edit</i>
							</button>
							<button onclick=\"deleteCinemaOwnerMovie('$rows[_id]')\" class=\"btn-small waves-effect waves-light tooltipped\" data-position=\"bottom\" data-tooltip=\"Remove movie\">
								<i class=\"material-icons center\">remove_circle</i>
							</button>
						</div>
						</td>
					</tr>";
		}
	}

	// Retrun the response to client
	echo $str;	
?>