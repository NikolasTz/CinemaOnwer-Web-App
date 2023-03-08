<?php
	
	// Start the session
	session_start();

	// Access only if has been initialized the role and only if has been selected the edit button
	if( (!isset($_SESSION['role'])) or strcmp($_SESSION['role'],'CINEMAOWNER') !== 0 or (!isset($_REQUEST['edit_id'])) ){
		header('Location: owner.php?error=invalid_access');
	}

	// Initialize variables
	$id = $title = $startdate = $enddate = $cinemaname = $category = '';

	// Get cinemaonwer movie for editing
	// Create and initialize a curl session
	$curl = curl_init();

	// Set the options of curl session
	curl_setopt_array($curl, array(
		CURLOPT_URL => 'http://172.18.1.10:1027/api/movies?id='.$_REQUEST['edit_id'],
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

	// Initialize the variables
	if (strcmp($httpcode,'200') == 0) {
		$id = $result["_id"];
		$title = $result["TITLE"];
		$startdate = date_format(date_create($result["STARTDATE"]),"Y-m-d");
		$enddate = date_format(date_create($result["ENDDATE"]),"Y-m-d");
		$cinemaname = $result["CINEMANAME"];
		$category = $result["CATEGORY"];
	}

?>

<!-- Return to the client -->

<!-- Nav bar -->
<nav class="transparent grey lighten-4 z-depth-5">
	<div class="nav-wrapper">
		<a href="#" class="brand-logo flow-text black-text center">Edit Movie</a>
		<!-- Go back -->
  		<ul class="right">
	        <li><a href="#" class="modal-close btn waves-effect waves-light">
	        	<i class="material-icons center">close</i></a>
	        </li>
        </ul>
	</div>
</nav>

<!-- Edit Form -->
<section>
	<div class="container">
		<div class="row">
			<div class="col s10 offset-s1 m10 offset-m1 l10 offset-l1">
			 	<div class="card-panel grey lighten-4 z-depth-3 ">
			 			<!-- Edit Form Movie -->
						<div class = "row">
						   <!-- ID -->
			               <div class = "input-field col s6 m6 l6" hidden>
			               	 	<div class = "card-panel grey lighten-3">
				                  <label for = "edit_id" class="black-text">ID:</label>
				                  <input  type = "text" id = "edit_id" name="edit_id" class = "validate" value="<?php echo htmlspecialchars($id) ?>" disabled />
				                 </div>
			                </div>
			                <!-- Title -->
			                <div class = "input-field col s12 m12 l12">
			               	 	<div class = "card-panel grey lighten-3">
				                  <label for = "edit_title" class="black-text">Title:</label>
				                  <input  type = "text" id = "edit_title" name="edit_title" class = "validate" value="<?php echo htmlspecialchars($title) ?>" /> 
				                  <div id="edit_error_title" class="red-text"></div> 
				                 </div>
			                </div> 
			            </div>

			            <!-- Start Date -->
						<div class = "row">
			               <div class = "input-field col s12 m12 l12">
			               	 	<div class = "card-panel grey lighten-3">
				                  <label for = "edit_startdate" class="black-text">Start Date:</label>
				                  <input  type = "date" id = "edit_startdate" name="edit_startdate" class = "validate" value="<?php echo htmlspecialchars($startdate) ?>" /> 
				                  <div id="edit_error_startdate" class="red-text"></div>  
				                 </div>
			                </div>
			            </div> 

			            <!-- End Date -->
						<div class = "row">
			               <div class = "input-field col s12 m12 l12">
			               	 	<div class = "card-panel grey lighten-3">
				                  <label for = "edit_enddate" class="black-text">End Date:</label>
				                  <input  type = "date" id = "edit_enddate" name="edit_enddate" class = "validate" value="<?php echo htmlspecialchars($enddate) ?>" />
				                  <div id="edit_error_enddate" class="red-text"></div>
				                 </div>
			                </div>
			            </div> 
				 	 	
						<div class = "row">
						    <!-- Cinema Name -->
			                <div class = "input-field col s6 m6 l6">
			               	 	<div class = "card-panel grey lighten-3">
				                  <label for = "edit_cinemaname" class="black-text">Cinema Name:</label>
				                  <input  type = "text" id = "edit_cinemaname" name="edit_cinemaname" class = "validate" value="<?php echo htmlspecialchars($cinemaname) ?>" disabled/>  
				                  <div id="edit_error_cinemaname" class="red-text"></div> 
				                 </div>
			                </div> 
			                <!-- Category -->
			                <div class = "input-field col s6 m6 l6"> 
				               <div class = "card-panel grey lighten-3">     
				                  <label for = "category" class="black-text">Category:</label>
				                  <input type = "text" id = "edit_category" name="edit_category" class = "validate" value="<?php echo htmlspecialchars($category) ?>" /> 
				                  <div id="edit_error_category" class="red-text"></div>          
				               </div>
				            </div>  
			            </div>

			            <!-- Submit button -->
			            <div class = "row"> 
			            	<div class = "input-field col s12 m12 l12"> 
				            	<button onclick="editCinemaOwnerMovie()" data-target="modal1" class="teal black-text btn waves-effect waves-light col s12 m12 l12 modal-trigger">Submit</button>	
				            </div>	
			            </div>
			    </div>
			</div>
		</div>
	</div>
</section>

