<?php
	
	// Start the session
	session_start();

	// Access only if has been initialized the role and only if has been selected the search "button"
	if( (!isset($_SESSION['role'])) or (strcmp($_SESSION['role'],'USER') !== 0) or (!isset($_REQUEST['search_category'])) ){
		header('Location: movies.php?error=invalid_access');
	}

	// Return the result as string
	$str = "";

	// Trim the search_value variable for both sides
	$trimmedSearchValue = ltrim($_REQUEST['search']);
	$trimmedSearchValue = rtrim($trimmedSearchValue);

	// Get the search results
	// Create and initialize a curl session
	$curl = curl_init();

	// Set the options of curl session
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://172.18.1.10:1027/api/movies?'.strtolower($_REQUEST['search_category']).'='.urlencode($trimmedSearchValue),
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
	
	// Print the result
	if (count($result) > 0 and !(isset($result['error'])) ){
		foreach($result as $rows){
			$str = $str."<li class=\"collection-item avatar\">
					<i class=\"material-icons circle teal \">movie</i>".
					"<span class=\"title\">Title: ".$rows["TITLE"]."</span>
					<p>	Starting Date: ".date_format(date_create($rows["STARTDATE"]),"Y-m-d")."<br>
						Ending Date: ".date_format(date_create($rows["ENDDATE"]),"Y-m-d")."<br>
						Cinema Name: ".$rows["CINEMANAME"]."<br>
						Category: ".$rows["CATEGORY"] ."<br>
					</p>							      	     		
			</li>";
		}
	}

	// Retrun the response to client
	echo $str;
?>