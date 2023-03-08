<?php
	
	// Start the session
	session_start();

	// Initialize the variables
	$application_id = 'ddb26225-3cbe-4d84-b079-767105da13b9';
	$role = $username = $password = $email = $cinemaname ;
	$errors = array('password' => '', 'username' => '', 'email' => '' , 'cinemaname' => '');

	// Access only if has been selected to move to the sign up page,that is to press the button signup
	if(!isset($_SESSION['signup'])){
		header('Location: index.php?error=invalid_access');
	}

	// Submit button
	if(isset($_POST['submit'])){
		
		// Get Role
		$role = $_POST['role'];

		// Validation check of cinemaname
		if( strcmp($role,"CINEMAOWNER") == 0){

			//Check cinema name
			if(empty($_POST['cinemaname'])){
				$errors['cinemaname'] = 'This field is required';
			}
			else{
				$cinemaname = $_POST['cinemaname'];
				if(!preg_match('/^[a-zA-Z0-9 ]+$/', $cinemaname)){
					$errors['cinemaname'] = 'Cinema name must be letters,numbers and spaces only';
				}
			}
		}

		// Validation check of Username
		$username = $_POST['username'];
		if(!preg_match('/^[a-zA-Z0-9_]+$/', $username)){
			$errors['username'] = 'Username must be letters,numbers and underscores only';
		}

		// Check Password
		$password = $_POST['password'];
		if (strlen($password) < 3) {
			$errors['password'] = 'Password must be greater than three characters';
		}

		// Check Email
		$email = $_POST['email'];
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$errors['email'] = 'Email must be a valid email address';
		}

		// If all fields are correct then insert the user to database
		if(!array_filter($errors)){

			// Create the user via admin of application

			// Get subject-token 
		    // Create and initialize a curl session
			$curl = curl_init();

			// Set the options of curl session
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'http://keyrock:3005/v1/auth/tokens',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HEADER => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS =>' {
					"name": "admin@test.com",
					"password": "1234"
				}',
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
				),
			));

			// Execute the request
			$response = curl_exec($curl);

			// Get header of response
			$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $header_size);
			$header = explode("\r\n", $header); 

			// Get X-Subject-token
			$subject_token = trim(explode(":",$header[2])[1]);
			
			// Close curl resource to free up system resources
			curl_close($curl);	

			// Create the user
			// Create and initialize a curl session
			$curl = curl_init();

			// Preparation of data that will be sended wtih POST request
			// Check if user is ADMIN
			if(strcmp($role,"ADMIN") == 0){ $admin = true; }
			else{ $admin = false; }

			$data = array(
				"user" => array (
					"username" => $username,
					"email" => $email,
					"password" => $password,
					"admin" =>  $admin,
					"description" => $cinemaname
				)
			);

			// Set the options of curl session
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'http://keyrock:3005/v1/users',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HEADER => false,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => json_encode($data),
				CURLOPT_HTTPHEADER => array(
					'Content-Type: application/json',
					'X-Auth-token: '.$subject_token,
				),
			));
			
			// Execute the request
			$response = curl_exec($curl);

			// Close curl resource to free up system resources
			curl_close($curl);

			// Decode json to a associative array.
			$response = json_decode($response,TRUE);

			// If username does not exist then insert
			if( !isset($response['error']) ){

				// Get the user id 
				$user_id = $response['user']['id'];

				// Get the role id
				if( strcmp($role,"ADMIN") == 0 ){ $role_id = '939c7841-2136-4e01-bd76-07aec638608a'; }
				else if( strcmp($role,"USER") == 0 ){ $role_id = '533b37bc-a088-4ce9-bfec-5d4d0de8e2b9'; }
				else { $role_id = 'f8855336-0314-4d57-ad74-7b282c7ce24a'; }

				// Assign role to user on application
				// Create and initialize a curl session
				$curl = curl_init();

				// Set the options of curl session
				curl_setopt_array($curl, array(
					CURLOPT_URL => 'http://keyrock:3005/v1/applications/'.$application_id.'/users/'.$user_id.'/roles/'.$role_id,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HEADER => false,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_HTTPHEADER => array(
						'Content-Type: application/json',
						'X-Auth-token: '.$subject_token,
					),
				));
				
				// Execute the request
				$response = curl_exec($curl);

				// Close curl resource to free up system resources
				curl_close($curl);

				// Unset the variable signup so that user does not have access if not use the button sign up on index page
				unset($_SESSION['signup']);

				// Redirect on index page after sign up
				header('Location: index.php?signup=valid');
			}
			else{  $errors['email'] = $response['error']['message']; }
		}
	}

	// Cancel button. Clear session variable for sign up page and redirect
	if(isset($_POST['cancel'])){

		unset($_SESSION['signup']);

		// Redirect to index page
		header('Location: index.php?cancel_signup=true');
	}


?>

<!DOCTYPE html>
<html lang="en">

	<!-- Header -->	
	<?php include('templates/header.php'); ?>
		<link rel="stylesheet" href="css/signup.css" type="text/css">
	</head>

	<body class="teal lighten-2">
		<!-- Nav bar -->
		<nav class="transparent z-depth-5">
			<div class="container">
      			<a href="#" class="brand-logo brand-text center white-text">Registration</a>
    		</div>
		</nav>

		<section>
			<div class="container">
				<div class="row">
					<div class="col s8 offset-s2 m8 offset-m2 l8 offset-l2">
					 	<div class="card-panel z-depth-3" style="background-color : rgb(255,254,206);">

					 		<!-- Form for submit button -->
					        <form action="signup.php" method="POST" autocomplete="on">

								<div class = "row">
					                <!-- Role -->
					                <div class = "input-field col s12 m12 l12">
					               	 	<div class = "card-panel white">
					               	 		<label class="black-text">Role:</label>
						                	<select required="required" id="role" name="role">
											    <option value="" disabled selected>Choose your role</option>
											    <option value="ADMIN" <?php echo (isset($_POST['role']) && $_POST['role'] == 'ADMIN') ? 'selected' : ''; ?>>ADMIN</option>
											    <option value="USER" <?php echo (isset($_POST['role']) && $_POST['role'] == 'USER') ? 'selected' : ''; ?>>USER</option>
											    <option value="CINEMAOWNER" <?php echo (isset($_POST['role']) && $_POST['role'] == 'CINEMAOWNER') ? 'selected' : ''; ?>>CINEMAOWNER</option>
									    	</select>
						                </div>
					                </div>  
					            </div>
						 	 	
								<div class = "row">
								    <!-- Username -->
					                <div class = "input-field col s6 m6 l6">
					               	 	<div class = "card-panel white">
						                  <label for = "username" class="black-text">Username:</label>
						                  <input  type = "text" id = "username" name="username" class = "validate" value="<?php echo htmlspecialchars($username) ?>" required />  
						                  <div class="red-text"><?php echo $errors['username']; ?></div> 
						                </div>
					                </div> 
					                <!-- Password -->
					                <div class = "input-field col s6 m6 l6"> 
						               <div class = "card-panel white">     
						                  <label for = "password" class="black-text">Password:</label>
						                  <input type = "password" id = "password" name="password" class = "validate" value="<?php echo htmlspecialchars($password) ?>" required /> 
						                  <div class="red-text"><?php echo $errors['password']; ?></div>          
						                </div>
						            </div>  
					            </div>

					            <!-- Email -->
								<div class = "row">
					               <div class = "input-field col s12 m12 l12">
					               	 	<div class = "card-panel white">
						                  <label for = "email" class="black-text">Email:</label>
						                  <input  type = "text" id = "email" name="email" class = "validate" value="<?php echo htmlspecialchars($email) ?>" required />
						                  <div class="red-text"><?php echo $errors['email']; ?></div>  
						                </div>
					                </div> 
					            </div>

								<!-- Cinema Name -->
								<div id="enableCinemaName" class = "row" hidden>
					               <div class = "input-field col s12 m12 l12">
					               	 	<div class = "card-panel white">
						                  <label for = "cinemaname" class="black-text">Cinena Name:</label>
						                  <input type = "text" id = "cinemaname" name="cinemaname" class = "validate" value="<?php echo htmlspecialchars($cinemaname) ?>"/>
						                  <div class="red-text"><?php echo $errors['cinemaname']; ?></div>  
						                 </div>
					                </div> 
					            </div>
	 							
					            <!-- Submit button -->
					            <div class = "row"> 
					            	<div class = "col s12 m12 l12">
						            	<input type="submit" name="submit" value="Submit" class="red black-text btn waves-effect waves-light col s12 m12 l12">
						            </div>	
					            </div> 
			 			    </form>

			 			    <!-- Form for cancel button -->
					        <form action="signup.php" method="POST" autocomplete="on">
			 			    	<!-- Cancel button -->
					            <div class = "row"> 
					            	<div class = "col s6 offset-s3 m6 offset-m3 l6 offset-l3">
					            	 <input type="submit" name="cancel" value="Cancel" class="red black-text btn waves-effect waves-light col s6 offset-s3 m6 offset-m3 l6 offset-l3">	
						            </div>	
					            </div> 
					        </form>
					    </div>
					</div>
				</div>
			</div>
		</section>

		<!-- Compiled and minified JavaScript -->
		<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  		<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
  		<script>
    		$(document).ready(function(){
    			$('select').formSelect();
				$('select[required]').css({display: 'inline', height: 0, padding: 0, width: 0, position: 'absolute'});
				
				// Enable/Disable input field based on selected option when someone change is not happened
    			// It is enabled only when the selected option is CINEMAOWNER
    			if( $('select option').filter(':selected').text() == "CINEMAOWNER"){
 					$('#enableCinemaName').prop( "hidden", false );
 				}
 				else{ $('#enableCinemaName').prop( "hidden", true ); }

 				// Enable/Disable input field based on selected option when change is happened
 				// It is enabled only when the selected option is CINEMAOWNER
 				$('select').on('change', function () {
 					if( $(this).val() == "CINEMAOWNER") { $('#enableCinemaName').prop( "hidden", false ); }
				    else { $('#enableCinemaName').prop( "hidden", true ); }
 				});
    		});
    	</script>

	<?php include('templates/footer.php'); ?>

</html>