<?php
	
	// Start the session
	session_start();
	$print_message = '';

	// Access only if has been initialized the role and the role is USER
	if( (!isset($_SESSION['role'])) or strcmp($_SESSION['role'],'USER') !== 0 ){
		header('Location: ../index.php?error=invalid_access');
	}

?>

<!DOCTYPE html>
<html lang="en">
	
	<!-- Header -->
	<?php include('../templates/header.php'); ?>
		<link href="../css/movies.css" rel="stylesheet" type="text/css" >
	</head>

	<body class="grey lighten-4" onload="displayMovies()">

		<!-- Nav bar -->
		<nav class="transparent grey lighten-4">
    		<div class="nav-wrapper">
      			<a href="#" class="brand-logo flow-text black-text center">Movies</a>

      			<!-- Favorite -->
      			<ul class="right">
		        	<li>
		        		<a onclick="showFavorite()" data-target="modal1" class="btn waves-effect waves-light modal-trigger tooltipped" data-position="bottom" data-tooltip="Move to Favorite">Favorite<i class="material-icons right">star</i></a>
		        	</li>
	        	</ul>

	        	<!-- Go back -->
	      		<ul class="left">
			        <li><a href="../welcome/welcome.php" class="btn waves-effect waves-light tooltipped" data-position="bottom" data-tooltip="Go back to Menu">
			        	<i class="material-icons center">arrow_back</i></a>
			        </li>
		        </ul>
      		</div>
      	</nav>

      	<!-- Search -->
      	<div class="card">
		    <div class="card-content">
		      <p class="center">Select one of the following categories for seach</p>
		    </div>
		    <div class="card-tabs">
		    	<ul class="tabs tabs-fixed-width">
		        	<li class="tab"><a href="#cinema">Cinema</a></li>
		        	<li class="tab"><a href="#category" class="active black-text">Category</a></li>
		        	<li class="tab"><a href="#startdate">Starting date</a></li>
		        	<li class="tab"><a href="#title">Title</a></li>
		      	</ul>
		    </div>
		    <div class="card-content grey lighten-4">
		    	<!-- Searby by Cinema name -->
			    <div id="cinema">
			    	<div class="nav-wrapper">
					    <div class="input-field">
					        <input onsearch="showResults('CINEMANAME')" type="search" id="search_cinemaneme" data-target="modal2" class="active modal-trigger" placeholder = "Please enter the name of Cinema">
					        <label class="label-icon" for="search"><i class="material-icons">search</i></label>			          
					        <i class="material-icons">close</i>
					    </div>
      				</div>
				</div>
				<!-- Searby by Category of movie -->
			    <div id="category">
			    	<div class="nav-wrapper">
					    <div class="input-field">
					        <input onsearch="showResults('CATEGORY')" type="search" id="search_category" data-target="modal2" class="active modal-trigger" placeholder = "Please enter the category of movie">
					        <label class="label-icon" for="search"><i class="material-icons">search</i></label>			          
					        <i class="material-icons">close</i>
					    </div>
      				</div>
				</div>
				<!-- Searby by Starting date of movie -->
      			<div id="startdate">
      				<div class="nav-wrapper">
					    <div class="input-field">
					        <input onsearch="showResults('STARTDATE')" type="search" id="search_startdate" data-target="modal2" class="active modal-trigger" placeholder = "Please enter the starting date of movie(YYYY-MM-DD)">
					        <label class="label-icon" for="search"><i class="material-icons">search</i></label>			          
					        <i class="material-icons">close</i>
					    </div>
      				</div>
      			</div>
      			<!-- Searby by title of movie -->
      			<div id="title">
      				<div class="nav-wrapper">
					    <div class="input-field">
					        <input onsearch="showResults('TITLE')" type="search" id="search_title" data-target="modal2" class="active modal-trigger" placeholder = "Please enter the title of movie">
					        <label class="label-icon" for="search"><i class="material-icons">search</i></label>			          
					        <i class="material-icons">close</i>
					    </div>
      				</div>
      			</div>
			</div>
		</div>
			
      	<!-- Print the available movies using collection-->
		<div class="container">
			<!-- Collection with header -->
			<ul class = "collection with-header">
		        <li class = "collection-header"><h4 class="center">Available Movies</h4></li>
		        <span id="movies"></span>
		    </ul>
		</div>        

      	<!-- Modal using as favorite page -->
      	<div id="modal1" class="modal modal-fixed-footer">
		   	<div class="modal-content">
				<!-- Nav bar -->
				<nav class="transparent grey lighten-4">
		    		<div class="nav-wrapper">
		      			<a href="#" class="brand-logo flow-text black-text center"><?php echo $_SESSION['username']."'s "."Favorites Movies"; ?></a>
		      			<!-- Go back -->
		      			<ul class="right">
				        	<li><a href="#" class="modal-close btn waves-effect waves-light">
				        		<i class="material-icons center">close</i></a>
				        	</li>
			        	</ul>
		      		</div>
		      	</nav>

		      	<!-- Print favorite movies using collection-->
	      		<div class="container">
					<!-- Collection with header -->
					<ul class = "collection with-header">
				        <li class = "collection-header"><h4 class="center">Movies</h4></li>
			   		    <span id="results"></span>
		   	   		</ul>
				</div>
			</div>
		</div>


		<!-- Modal using as search movies page -->
      	<div id="modal2" class="modal modal-fixed-footer">
		   	<div class="modal-content">
				<!-- Nav bar -->
				<nav class="transparent grey lighten-4">
		    		<div class="nav-wrapper">
		      			<a href="#" class="brand-logo flow-text black-text center">Search Result</a>
		      			<!-- Go back to Movies-->
			      		<ul class="right">
					        <li>
					        	<a href="#" class="modal-close btn waves-effect waves-light">
					        	<i class="material-icons center">close</i></a>
					        </li>
				        </ul>
		      		</div>
		      	</nav>

		      	<!-- Print the result of search using collection-->
				<div class="container">
					<!-- Collection with header -->
					<ul class = "collection with-header">
		       			<li class = "collection-header"><h4 class="center">Movies</h4></li>
			   		    <span id="search_results"></span>
		   	   		</ul>
				</div>
			</div>
		</div>

		<!-- Compiled and minified JavaScript -->
		<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  		<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-beta/js/materialize.min.js"></script>
  		<script>
    		$(document).ready(function(){
    			$('.tabs').tabs();
    			$('.tooltipped').tooltip();

    			// Enable modal for favorite page
    			$('#modal1').modal({
    				preventScrolling : false,
    			});

    			// Enable Toasts
    			var message = "<?php echo"$print_message"?>";
    			if (message) { M.toast({html: message,displayLength:2000,classes: 'rounded'}); }
    		});
    	</script>

    	
    	<script>

    		// Display favorites
    		function showFavorite() {

    			// Create an XMLHttpRequest object
				var xmlhttp = new XMLHttpRequest();

				// Function executed when the server response is ready
			  	xmlhttp.onreadystatechange = function() {
			    	if (this.readyState==4 && this.status==200){

						if( this.responseText == "access_token_expired" ){ access_token_expired(); }
			      		document.getElementById("results").innerHTML=this.responseText;
			    	}
			  	}

			  	// Send the request off to a file on the server
			  	xmlhttp.open("GET","display_favorites.php?favorites=true",true);
			  	xmlhttp.send();
			}

			// Delete movie from favorites
    		function deleteFavorite(str) {

    			// Create an XMLHttpRequest object
				var xmlhttp = new XMLHttpRequest();

				// Function executed when the server response is ready
			  	xmlhttp.onreadystatechange = function() {
			    	if (this.readyState==4 && this.status==200) {

						if( this.responseText == "access_token_expired" ){ access_token_expired(); }
			      		showFavorite();
			    	}
			  	}

			  	// Send the request off to a file on the server
			    xmlhttp.open("GET","delete_movie.php?favid="+str,true);
			  	xmlhttp.send();
			}

			// Display all the availble movies
    		function displayMovies() {

    			// Create an XMLHttpRequest object
				var xmlhttp = new XMLHttpRequest();

				// Function executed when the server response is ready
			  	xmlhttp.onreadystatechange = function() {
			    	if (this.readyState==4 && this.status==200){

						if( this.responseText == "access_token_expired" ){ access_token_expired(); }
			      		document.getElementById("movies").innerHTML=this.responseText;
			    	}
			  	}

			  	// Send the request off to a file on the server
			  	xmlhttp.open("GET","display_movies.php",true);
			  	xmlhttp.send();
			}

			// Add movie to favorites
			function addToFavorite(str) {

    			// Create an XMLHttpRequest object
				var xmlhttp = new XMLHttpRequest();

				// Function executed when the server response is ready
			  	xmlhttp.onreadystatechange = function() {
			    	if (this.readyState==4 && this.status==200){

						if( this.responseText == "access_token_expired" ){ access_token_expired(); }
			    		M.toast({html: this.responseText,displayLength:1000,classes: 'rounded'});
			    	}
			  	}

				// Send the request off to a file on the server
			  	xmlhttp.open("GET","add_movie_to_favorites.php?movieid="+str,true);
			  	xmlhttp.send();
			}

			// Display the results of search
			function showResults(search_category){		

				// Create an XMLHttpRequest object
				var xmlhttp = new XMLHttpRequest();

				// Function executed when the server response is ready
			  	xmlhttp.onreadystatechange = function() {
			    	if (this.readyState==4 && this.status==200){

						if( this.responseText == "access_token_expired" ){ access_token_expired(); }
			      		document.getElementById("search_results").innerHTML=this.responseText;

						// Intialize the search value to empty string
			      		document.getElementById(getIdForSearch(search_category)).value = "";

			      		// Initialize modal
						intialize_modal(); 
			    	}
			  	}

			  	// Send the request off to a file on the server
			  	xmlhttp.open("GET","search_movies.php?search_category="+search_category+"&search="+document.getElementById(getIdForSearch(search_category)).value,true);
			  	xmlhttp.send();
			}

			// Initialize modal for search results
			function intialize_modal(){

				// Initialize modal for results
				$("#modal2").modal({

					preventScrolling : false,

					// Callback for Modal close
    				onCloseEnd: function() { 
				        $('#modal2').modal('destroy');  
				    }
				});

				// Open modal
				$("#modal2").modal('open');
			}

			// Get the id for search
			function getIdForSearch(str){

				if( str == 'CINEMANAME' ){ return "search_cinemaneme"; }
				else if( str == 'CATEGORY' ){ return "search_category"; }
				else if( str == 'STARTDATE' ){ return "search_startdate"; }
				else if( str == 'TITLE' ){ return "search_title"; }
			}

			// Access token expired
			function access_token_expired(){
				window.location.href = "http://localhost/config/access_token_expired.php";
			}

    	</script>

	<?php include('../templates/footer.php'); ?>

</html>
