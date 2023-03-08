<?php
	
	// Start the session
	session_start();
	$username = $role = $print_message = '';
	
	// Access after submit your username and password ,on index page have been initialized the session variables username,role,id 
	if( (!isset($_SESSION['username'])) or (!isset($_SESSION['role'])) or (!isset($_SESSION['id'])) ){
		header('Location: ../index.php?error=invalid_access');
	}
	else{
		$username = $_SESSION['username'];
		$role = $_SESSION['role'];
	}

	// Wrong access page
	if(isset($_REQUEST["role"])){
		$role = $_REQUEST["role"];

		// Redirect to wrong access page
		// Set the appropriate session variable so that the user have access on wrong_access page if and only if he selected page for which does not have access
		if(strcmp($role,$_SESSION['role']) !== 0){
			header('Location: wrong_access.php');
			$_SESSION['wrong_access'] = 'true';
		}
		// Move to role's page respectively 
		else{
			if(strcmp($role,'USER') == 0){ header('Location: ../users/movies.php'); }
			if(strcmp($role,'CINEMAOWNER') == 0){ header('Location: ../cinema_owner/owner.php'); }
		}
	}

?>

<!DOCTYPE html>
<html lang="en">

	<!-- Header -->
	<?php include('../templates/header.php'); ?>
		<link href="../css/welcome.css" rel="stylesheet" type="text/css"> 
	</head>

	<body>

		<!-- Dropdown Structure - Menu -->
        <ul id="dropdown" class="dropdown-content collection">	
    		<li class="collection-item avatar">
    			<i class="material-icons circle" style="background-color : rgb(22,175,193);">movie</i>
    			<a href="welcome.php?role=CINEMAOWNER" class="title" style="color : rgb(22,175,193);">Cinema Owners</a>
    		</li> 
    		<li class="collection-item avatar">
    			<i class="material-icons circle" style="background-color : rgb(22,175,193);">person_outline</i>
    			<a href="welcome.php?role=USER" class="title" style="color : rgb(22,175,193);">Users</a>
    		</li>
    		<li class="collection-item avatar">
    			<i class="material-icons circle" style="background-color : rgb(22,175,193);">supervisor_account</i>
    			<a href="welcome.php?role=ADMIN" class="title"  style="color : rgb(22,175,193);">Admins</a>		
    		</li>    
        </ul>

		<!-- Modal using as notification page -->
		<div id="modal1" class="modal modal-fixed-footer">
		   	<div class="modal-content">
				<!-- Nav bar -->
				<nav class="transparent grey lighten-4">
		    		<div class="nav-wrapper">
		      			<a href="#" class="brand-logo flow-text black-text center"><?php echo $_SESSION['username']."'s "." Notifications"; ?></a>
		      			<!-- Go back -->
		      			<ul class="right">
				        	<li><a href="#" class="modal-close btn waves-effect waves-light">
				        		<i class="material-icons center">close</i></a>
				        	</li>
			        	</ul>
		      		</div>
		      	</nav>

		      	<!-- Print notifications using collection-->
	      		<div class="container">
					<!-- Collection with header -->
					<ul class = "collection with-header">
				        <li class = "collection-header"><h4 class="center">Available Notifications</h4></li>
			   		    <span id="results"></span>
		   	   		</ul>
				</div>
			</div>
		</div>

		<!-- Nav bar -->
		<nav class="transparent">
			<div class="nav-wrapper">
				<!-- Title -->
      			<a href="#" class="brand-logo flow-text center black-text">Welcome</a>
      			<!-- Name,Surname,Role -->
      			<ul class="right">
       				<li><a href="#" class="right flow-text black-text"><?php echo $username.",".$role; ?></a></li>
      			</ul>
      		</div>
		</nav>

		<!-- Menu and Notification Button -->
		<div class="row transparent">

			<!-- Menu -->
			<div class = "col s10 m10 l11">
				<a id="menu" class="dropdown-button black-text btn waves-effect waves-light" style="background-color : rgb(22,175,193);" href="#" data-target="dropdown">MENU</a>
			</div>

			<!-- Notification button -->
			<div id="enable_notification" class = "col s2 offset-s10 m2 offset-m10 l1 offset-l11" disabled >
				<input type = "text" id = "enable_role" value="<?php echo htmlspecialchars($role) ?>" hidden />
				<a onclick="showNotifications()" data-target="modal1" class="btn waves-effect waves-light modal-trigger" style="background-color : rgb(22,175,193);" >
				<i class="material-icons">notifications</i> <span id="number_of_notifications" class="new badge transparent"></span></a>
			</div>

		</div>

		<!-- Logout button -->
		<div class="row transparent">
			<div class = "col s2 offset-s10 m2 offset-m10 l1 offset-l11">
				<a href="../config/logout.php" class="black-text btn waves-effect waves-light col s12 m12 l12" style="background-color : rgb(22,175,193);">Logout</a>
			</div>
		</div>

		<!-- Compiled and minified JavaScript -->
		<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  		<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
  		<script>
    		$(document).ready(function(){

				// Enable menu
    			$('#menu').dropdown({
		            inDuration: 300,
		            outDuration: 225,
		            hover: true, // Activate on hover
		            belowOrigin: true, // Displays dropdown below the button
		            alignment: 'right' // Displays dropdown with edge aligned to the left of button
				});

				// Enable and disable the button for notifications
				if( $('#enable_role').val() == "USER" ){ $('#enable_notification').prop( "hidden", false ); }
				else{ $('#enable_notification').prop( "hidden", true ); }

				// Enable modal for notifications
				$('#modal1').modal();

			});				
    	</script>

		<script type="text/javascript">

			// Will run the function 'showNotifications' every 10 seconds only for users
			if( $('#enable_role').val() == "USER" ) {  setInterval(showNotifications, 10000); } 

			// Display user notifications
      		function showNotifications() {

				// Create an XMLHttpRequest object
				var xmlhttp = new XMLHttpRequest();

				// Function executed when the server response is ready
				xmlhttp.onreadystatechange = function() {
					if (this.readyState==4 && this.status==200){

						if( this.responseText == "access_token_expired" ){ access_token_expired(); }

						var res = this.responseText.split("?");
						document.getElementById("results").innerHTML= res[1];
						
						// Set the number of notification
						document.getElementById("number_of_notifications").innerHTML=res[0];
					}
				}

				// Send the request off to a file on the server
				xmlhttp.open("GET","subscrpitions.php?sub=true",true);
				xmlhttp.send();
			}

			// Delete user notification
			function deleteNotification(str){

				// Create an XMLHttpRequest object
				var xmlhttp = new XMLHttpRequest();

				// Function executed when the server response is ready
				xmlhttp.onreadystatechange = function() {
					if (this.readyState==4 && this.status==200) {
						
						if( this.responseText == "access_token_expired" ){ access_token_expired(); }
						showNotifications();
					}
				}

				// Send the request off to a file on the server
				xmlhttp.open("GET","delete_notification.php?subid="+str,true);
  				xmlhttp.send();
			}

			// Access token expired
			function access_token_expired(){
				window.location.href = "http://localhost/config/access_token_expired.php";
			}

		</script>

	<?php include('../templates/footer.php'); ?>

</html>
