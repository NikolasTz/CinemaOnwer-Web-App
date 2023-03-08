<?php
	
	// Start the session
	session_start();

	// Access only if has been initialized the role and only if has been selected the wrong page
	if( (!isset($_SESSION['role'])) or (!isset($_SESSION['wrong_access'])) ){
		header('Location: welcome.php?error=invalid_access');
	}
	else{
		$page = '';
		if( strcmp($_SESSION['role'],"USER") == 0 ){ $page = 'User'; }
		else if( strcmp($_SESSION['role'],"CINEMAOWNER") == 0 ){ $page = 'Cinema Owner'; }
		else if( strcmp($_SESSION['role'],"ADMIN") == 0 ){ $page = 'Admin'; } 
	}

	// On return clean the session variable for wrong access page and redirect
	if(isset($_POST['return'])){

		unset($_SESSION['wrong_access']);

		// Redirect to welcome page
		header('Location: welcome.php?return=true');
	}
	
?>


<!DOCTYPE html>
<html lang="en">
	
	<!-- Header -->
	<?php include('../templates/header.php'); ?>
	</head>

	<body class="grey lighten-4">

		<!-- Nav bar -->
		<nav class="transparent">
			<div class = "row"> 
				<div class = "col s12 m12 l12">
					<a class="brand-logo brand-text center black-text">Access denied</a>
				</div>
			</div>	
		</nav>

		<section>
			<div class = "card-panel grey lighten-4 z-depth-5">	
				<div class = "row"> 
					<div class = "col s12 m12 l12">
						<div class="red-text center"><?php echo 'Your role is '.$_SESSION['role']; ?></div>
					</div>
				</div>	
				
				<div class = "row"> 
					<div class = "col s12 m12 l12">
						<div class="container red-text center"><?php echo 'You have access only on '.$page.' page' ?></div>
					</div>
				</div>

				<div class = "row"> 
					<div class = "col s12 m12 l12">
						<div class="container red-text center">Please press button to return</div>
					</div>
				</div>

				<!-- Return button -->
				<div class = "row"> 
					<div class = "col s4 offset-s4 m4 offset-m4 l4 offset-l4">
						<form action="wrong_access.php" method="POST" >
							<input type="submit" name="return" value="Return" class="black-text btn waves-effect waves-light col s4 offset-s4 m4 offset-m4 l4 offset-l4" style="background-color : rgb(22,175,193);">
						</form>		            	
					</div>	
				</div>

			</div>
		</section>

	<?php include('../templates/footer.php'); ?>

</html>