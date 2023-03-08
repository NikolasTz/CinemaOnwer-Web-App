<?php
	
	// Start the session
	session_start();
	$print_message = '';

	// Access only if has been initialized the role and the role is CINEMAOWNER
	if( (!isset($_SESSION['role'])) or strcmp($_SESSION['role'],'CINEMAOWNER') !== 0 ){
		header('Location: ../index.php?error=invalid_access');
	}

?>

<!DOCTYPE html>
<html lang="en">

	<!-- Header -->	
	<?php include('../templates/header.php'); ?>
	</head>

	<body class="grey lighten-4" onload="displayCinemaOwnerMovies()">

		<!-- Nav bar -->
		<nav class="transparent grey lighten-4">
    		<div class="nav-wrapper">
    			<!-- Title -->
      			<a href="#" class="brand-logo flow-text black-text center">Cinema Owner</a>
      			<!-- Go back -->
	      		<ul class="left">
			        <li><a href="../welcome/welcome.php" class="btn waves-effect waves-light tooltipped" data-position="bottom" data-tooltip="Go back to Menu">
			        	<i class="material-icons center">arrow_back</i></a>
			        </li>
		        </ul>
      		</div>
      	</nav>

      	<!-- Add Movie -->
		<div class="row">
			<div class = "col s3 offset-s10 m3 offset-m10 l3 offset-l10">
				<a onclick="displayAddFormMovie()" data-target="modal2" class="btn waves-effect waves-light modal-trigger tooltipped" data-position="bottom" data-tooltip="Add Movie">Add Movie<i class="material-icons right">add</i></a>
			</div>
		</div>

      	<!-- Print available movies for edit and delete -->
   		<div class="container">
			<table class="responsive-table centered striped">
		        <thead>
		        	<tr>
		              	<th>Title</th>
		              	<th>Start Date</th>
		              	<th>End Date</th>
		              	<th>Cinema Name</th>
		              	<th>Category</th>
		          	</tr>
		        </thead>
		        <tbody id="cinemaowner_movies">
		        </tbody>
		    </table>
		</div>

		<!-- Modal using as editing page -->
      	<div id="modal1" class="modal modal-fixed-footer">
		   	<div class="modal-content">
				<span id="edit_form"></span>
			</div>
		</div> 

		<!-- Modal using as add movie page -->
      	<div id="modal2" class="modal modal-fixed-footer">
		   	<div class="modal-content">
				<span id="add_form"></span>
			</div>
		</div>         


		<!-- Compiled and minified JavaScript -->
		<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  		<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
  		<script>
    		$(document).ready(function(){

    			$('.tooltipped').tooltip();

    			// Enable Modals

    			// Modal for editing movie
    			$('#modal1').modal({

    				preventScrolling : false,

				    // Callback for Modal close
    				onCloseStart: function() { 
				        displayCinemaOwnerMovies();  
				    }

    			});

    			// Modal for add movie
    			$('#modal2').modal({

    				preventScrolling : false,

				    // Callback for Modal close
    				onCloseStart: function() { 
				        displayCinemaOwnerMovies();  
				    }

    			});

    			// Enable Toasts
    			var message = "<?php echo"$print_message"?>";
    			if (message) { M.toast({html: message,displayLength:2000,classes: 'rounded'}); }
    		});
    	</script> 

    	<script type="text/javascript">
    		
    		// Display movies for cinemaowner
    		function displayCinemaOwnerMovies() {

    			// Create an XMLHttpRequest object
				var xmlhttp = new XMLHttpRequest();

				// Function executed when the server response is ready
			  	xmlhttp.onreadystatechange = function() {
			    	if (this.readyState==4 && this.status==200){

						if( this.responseText == "access_token_expired" ){ access_token_expired(); }
			      		document.getElementById("cinemaowner_movies").innerHTML=this.responseText;

						// Create and update entities and create subscriptions on Orion
						createEntitiesAndSubscriptions();

			      		// Enable tooltipped
			      		$('.tooltipped').tooltip();
			    	}
			  	}

			  	// Send the request off to a file on the server
			  	xmlhttp.open("GET","display_cinemaowner_movies.php",true);
			  	xmlhttp.send();
			}

			// Delete movie for cinemaowner
			function deleteCinemaOwnerMovie(movieId){

				// Create an XMLHttpRequest object
				var xmlhttp = new XMLHttpRequest();

				// Function executed when the server response is ready
			  	xmlhttp.onreadystatechange = function() {
			    	if (this.readyState==4 && this.status==200){

						if( this.responseText == "access_token_expired" ){ access_token_expired(); }
			      		M.toast({html: this.responseText,displayLength:1000,classes: 'rounded'});

			      		$('.tooltipped').tooltip('destroy');
			      		displayCinemaOwnerMovies();
			    	}
			  	}

			  	// Send the request off to a file on the server
			  	xmlhttp.open("GET","delete_cinemaowner_movie.php?id_op="+movieId,true);
			  	xmlhttp.send();
			}

			// Display editing movie form
			function displayEditFormMovie(movieId){

				// Create an XMLHttpRequest object
				var xmlhttp = new XMLHttpRequest();

				// Function executed when the server response is ready
			  	xmlhttp.onreadystatechange = function() {
			    	if (this.readyState==4 && this.status==200){

						if( this.responseText == "access_token_expired" ){ access_token_expired(); }
			    		document.getElementById("edit_form").innerHTML=this.responseText;
			    	}
			  	}

			  	// Send the request off to a file on the server
			  	xmlhttp.open("GET","edit_form_movie.php?edit_id="+movieId,true);
			  	xmlhttp.send();				
			}

			// Edit CinemaOwner movie
			function editCinemaOwnerMovie(){

				// Create an XMLHttpRequest object
				var xmlhttp = new XMLHttpRequest();

				// Function executed when the server response is ready
			  	xmlhttp.onreadystatechange = function() {
			    	if (this.readyState==4 && this.status==200){

						if( this.responseText == "access_token_expired" ){ access_token_expired(); }

			    		// Decode the response from server
			    		var decodedstr = decodeURIComponent(this.responseText);
			    		decodedstr = decodedstr.replaceAll("+", " ");
			    		decodedstr = decodedstr.replaceAll("=", ":");
			    		decodedstr = decodedstr.replaceAll(">", "");

			    		// Split the decoded string
						var result = decodedstr.split("?", 2);

			    		// Print message after edit movie
			    		if(result[0].split(":")[1] != "") { M.toast({html: result[0].split(":")[1],displayLength:1000,classes: 'rounded'}); }

			    		// Set errors to edit form if exist
			    		result = decodedstr.split("?");
			    		document.getElementById("edit_error_title").innerHTML = result[1].split(":")[1];
						document.getElementById("edit_error_startdate").innerHTML = result[2].split(":")[1];
						document.getElementById("edit_error_enddate").innerHTML = result[3].split(":")[1];
			    		document.getElementById("edit_error_cinemaname").innerHTML = result[4].split(":")[1];
						document.getElementById("edit_error_category").innerHTML = result[5].split(":")[1];
			    	}
			  	}

			  	// Send the request off to a file on the server
			  	xmlhttp.open("POST","edit_cinemaowner_movie.php",true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			  	xmlhttp.send(
			  		"id="+document.getElementById("edit_id").value+"&title="+document.getElementById("edit_title").value
			  		+"&startdate="+document.getElementById("edit_startdate").value+"&enddate="+document.getElementById("edit_enddate").value
			  		+"&cinemaname="+document.getElementById("edit_cinemaname").value+"&category="+document.getElementById("edit_category").value
			  		+"&submit=Submit"
			  	);
			}

			// Display add movie form
			function displayAddFormMovie(){

				// Create an XMLHttpRequest object
				var xmlhttp = new XMLHttpRequest();

				// Function executed when the server response is ready
			  	xmlhttp.onreadystatechange = function() {
			    	if (this.readyState==4 && this.status==200){
			    		document.getElementById("add_form").innerHTML=this.responseText;
			    	}
			  	}

			  	// Send the request off to a file on the server
			  	xmlhttp.open("GET","add_form_movie.php?add_id=true",true);
			  	xmlhttp.send();	

			}

			// Add CinemaOwner movie
			function addCinemaOwnerMovie(){

				// Create an XMLHttpRequest object
				var xmlhttp = new XMLHttpRequest();

				// Function executed when the server response is ready
			  	xmlhttp.onreadystatechange = function() {
			    	if (this.readyState==4 && this.status==200){

						if( this.responseText == "access_token_expired" ){ access_token_expired(); }
			    		
			    		// Decode the response from server
			    		var decodedstr = decodeURIComponent(this.responseText);
			    		decodedstr = decodedstr.replaceAll("+", " ");
			    		decodedstr = decodedstr.replaceAll("=", ":");
			    		decodedstr = decodedstr.replaceAll(">", "");

			    		// Split the decoded string
						var result = decodedstr.split("?", 2);

			    		// Print message after edit movie
			    		if(result[0].split(":")[1] != "") { M.toast({html: result[0].split(":")[1],displayLength:1000,classes: 'rounded'}); }


			    		// Set errors to edit form if exist
			    		result = decodedstr.split("?");
			    		document.getElementById("error_movie_title").innerHTML = result[1].split(":")[1];
						document.getElementById("error_movie_startdate").innerHTML = result[2].split(":")[1];
						document.getElementById("error_movie_enddate").innerHTML = result[3].split(":")[1];
						document.getElementById("error_movie_category").innerHTML = result[4].split(":")[1];
			    	}
			  	}

			  	// Send the request off to a file on the server			  	
			  	xmlhttp.open("POST","add_cinemaowner_movie.php",true);
			  	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			  	xmlhttp.send(
			  		"title="+document.getElementById("title").value
			  		+"&startdate="+document.getElementById("startdate").value+"&enddate="+document.getElementById("enddate").value
			  		+"&cinemaname="+document.getElementById("cinemaname").value+"&category="+document.getElementById("category").value
			  		+"&submit=Submit"
			  	);
			}

			// Create the entities and subscriptions
			function createEntitiesAndSubscriptions(){

				// Create an XMLHttpRequest object
				var xmlhttp = new XMLHttpRequest();

				// Function executed when the server response is ready
				xmlhttp.onreadystatechange = function() {
			    	if (this.readyState==4 && this.status==200){
						if( this.responseText == "access_token_expired" ){ access_token_expired(); }
					}
				}
				// Send the request off to a file on the server
				xmlhttp.open("GET","create_entities_subscriptions.php",true);
				xmlhttp.send();
			}

			// Access token expired
			function access_token_expired(){
				window.location.href = "http://localhost/config/access_token_expired.php";
			}

    	</script>       

	<?php include('../templates/footer.php'); ?>

</html>