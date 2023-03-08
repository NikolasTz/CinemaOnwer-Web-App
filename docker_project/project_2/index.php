<?php

	$username = $password = $cinemaname = '';
	$errors = '';
	$client_id = 'ddb26225-3cbe-4d84-b079-767105da13b9';
	$client_secret = '95de014e-c6fe-4bcd-b0b1-730f76162a6f';

	// Submit button
	if(isset($_POST['submit'])){
		
		$username = $_POST['username'];
		$password = $_POST['password'];

		// Create and initialize a curl session
		$curl = curl_init();

		// Set the options of curl session
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'http://keyrock:3005/oauth2/token',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HEADER => false,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_POSTFIELDS => 'grant_type=password&username='.urlencode($username).'&password='.urlencode($password),
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_HTTPHEADER => array(
				'Content-Type: application/x-www-form-urlencoded',
				'Authorization: Basic '.base64_encode($client_id.':'.$client_secret)
			),
		));
		
		// Execute the request
		$response = curl_exec($curl);

		// Decode json to a associative array.
		$response = json_decode($response,TRUE);

		// Close curl resource to free up system resources
		curl_close($curl);	

		if( !isset($response['error']) ){

			// Get access token
			$access_token = $response['access_token'];

			// Get user's information via acess token
			// Create and initialize a curl session
			$curl = curl_init();

			// Set the options of curl session
			curl_setopt_array($curl, array(
				CURLOPT_URL => 'http://keyrock:3005/user?access_token='.$access_token,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HEADER => false,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
			));

			// Execute the request
			$response = curl_exec($curl);
			
			// Close curl resource to free up system resources
			curl_close($curl);
				
			// Decode json to a associative array.
			$response = json_decode($response,TRUE);

			// If exist and is confirmed from admin then create a session for this username
			if ( isset($response['username']) and isset($response['id']) and isset($response['roles']) ){			

				// Only in case of CINEMAOWNER,get the cinemaname(description of user)
				if( strcmp($response['roles'][0]['name'],'CINEMAOWNER') == 0 ){

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
					$res = curl_exec($curl);

					// Get header of response
					$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
					$header = substr($res, 0, $header_size);
					$header = explode("\r\n", $header);

					// Get X-Subject-token
					$subject_token = trim(explode(":",$header[2])[1]);

					// Get the cinemaname from description
					// Create and initialize a curl session
					$curl = curl_init();

					// Set the options of curl session
					curl_setopt_array($curl, array(
						CURLOPT_URL => 'http://keyrock:3005/v1/users/'.$response['id'],
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_ENCODING => '',
						CURLOPT_MAXREDIRS => 10,
						CURLOPT_TIMEOUT => 0,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_HEADER => false,
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						CURLOPT_CUSTOMREQUEST => 'GET',
						CURLOPT_HTTPHEADER => array(
							'X-Auth-token: '.$subject_token
						),
					));

					// Execute the request
					$res = curl_exec($curl);
					
					// Close curl resource to free up system resources
					curl_close($curl);
						
					// Decode json to a associative array.
					$res = json_decode($res,TRUE);
					$cinemaname = $res['user']['description'];
				}

				// Start the session
				session_start();

				// Set up the session for this user
				$_SESSION['username'] = $response['username'];
				$_SESSION['role'] = $response['roles'][0]['name'];
				$_SESSION['id'] = $response['id'];
				if( strcmp($response['roles'][0]['name'],'CINEMAOWNER') == 0 ) { $_SESSION['cinemaname'] = $cinemaname; }
				$_SESSION['access_token'] = $access_token;

				// Redirect to welcome page
				header('Location: welcome/welcome.php');	

			}
			else{ $errors = 'Failed to retrive username and role.Please try again'; }
		
		}
		else{ $errors = 'Username or password is invalid'; }
			
	}

	// Sign up button
	// For signup page set the corresponding session variable so that user does not have access if not use the signup button
	if(isset($_POST['signup'])){

		// Start the session
		session_start();

		// Set up the session variable for sign up page
		$_SESSION['signup'] = 'true';

		// Redirect to signup page
		header('Location: signup.php');
	}

?>

<!DOCTYPE html>
<html lang="en">
	
	<!-- Header -->
	<?php include('templates/header.php'); ?>
	 	<link rel="stylesheet" href="css/index.css" type="text/css">
	</head>

	<body class="grey lighten-4">
		<!-- Nav bar -->
		<nav class="transparent">
		   <div class="container">
		     <a href="#" class="brand-logo brand-text center white-text">CINEMA</a>
		   </div>
		</nav>

		<section>
			<div class="container">
				<div class="row">
					<div class="col s6 offset-s8 m6 offset-m8 l6 offset-l8">
					 	<div class="card-panel grey lighten-1 z-depth-5">
					 		
					 		<!-- Form for login button -->
					        <form action="index.php" method="POST" >

						 	 	<!-- Username -->
								<div class = "row">
					               <div class = "input-field col s12 m12 l12">
					               	 	<div class = "card-panel grey lighten-3">
						                  <i class = "material-icons left">account_circle</i>
						                  <label for = "username" class="black-text">Email:</label>
						                  <input  type = "text" id = "username" name="username" class = "validate" value="<?php echo htmlspecialchars($username) ?>" required /> 
						                </div>
					                </div> 
					            </div>

					            <!-- Password -->
					            <div class = "row">  
					               <div class = "input-field col s12 m12 l12"> 
						               <div class = "card-panel grey lighten-3"> 
						               	  <i class = "material-icons left">lock</i>    
						                  <label for = "password" class="black-text">Password:</label>
						                  <input type = "password" id = "password" name="password" class = "validate" value="<?php echo htmlspecialchars($password) ?>" required />
						               </div>
						            </div>   
					            </div>

					            <!-- Print error if exist on username,password-->
					             <div class = "row"> 
					            	<div class = "col s12 m12 l12">    
						            	<div class="red-text center-align"><?php echo $errors; ?></div>  
						            </div>	   
						        </div>
					            
					            <!-- Submit button -->
					            <div class = "row"> 
					            	<div class = "col s12 m12 l12">
						            	<input type="submit" name="submit" value="Login" class="grey lighten-3 black-text btn waves-effect waves-light col s12 m12 l12">	
						            </div>	
					            </div>

					            <hr>  
			 			    </form>

			 			    <!-- Form for sign up button -->
					        <form action="index.php" method="POST" >
			 			   		 <!-- Sign up button -->
					            <div class = "row"> 
					            	<div class = "col s12 m12 l12">
					            		<input type="submit" name="signup" value="Sign up" class="grey lighten-3 black-text btn waves-effect waves-light col s6 offset-s3 m6 offset-m3 l6 offset-l3">
						            </div>	
					            </div>
					        </form>    

					    </div>
					</div>
				</div>
			</div>
		</section>

	<?php include('templates/footer.php'); ?>
</html>