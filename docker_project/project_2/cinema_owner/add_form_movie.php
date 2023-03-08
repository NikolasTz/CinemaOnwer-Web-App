<?php
	
	// Start the session
	session_start();
	$print_message = '';

	// Access only if has been initialized the role and only if has been selected the add-movie button
	if( (!isset($_SESSION['role'])) or strcmp($_SESSION['role'],'CINEMAOWNER') !== 0 or (!isset($_REQUEST['add_id'])) ){
		header('Location: owner.php?error=invalid_access');
	}

	// Initialize the variables
	$title = $startdate = $enddate = $cinemaname = $category = '';

	// Initialize cinemaname variable
	$cinemaname = $_SESSION['cinemaname'];

?>

<!-- Return to the client -->

<!-- Nav bar -->
<nav class="transparent grey lighten-4 z-depth-5">
	<div class="nav-wrapper">
		<a href="#" class="brand-logo flow-text black-text center">Add Movie</a>
		<!-- Go back -->
  		<ul class="right">
	        <li><a href="#" class="modal-close btn waves-effect waves-light">
	        	<i class="material-icons center">close</i></a>
	        </li>
        </ul>
	</div>
</nav>

<!-- Add Form -->
<section>
	<div class="container">
		<div class="row">
			<div class="col s10 offset-s1 m10 offset-m1 l10 offset-l1">
			 	<div class="card-panel grey lighten-4 z-depth-3 ">
			 			<!-- Add Movie Form -->
						<div class = "row">
			                <!-- Title -->
			                <div class = "input-field col s12 m12 l12">
			               	 	<div class = "card-panel grey lighten-3">
				                  <label for = "title" class="black-text">Title:</label>
				                  <input  type = "text" id = "title" name="title" class = "validate" value="<?php echo htmlspecialchars($title) ?>" /> 
				                  <div id="error_movie_title" class="red-text"></div> 
				                 </div>
			                </div> 
			            </div>

			            <!-- Start Date -->
						<div class = "row">
			               <div class = "input-field col s12 m12 l12">
			               	 	<div class = "card-panel grey lighten-3">
				                  <label for = "startdate" class="black-text">Start Date:</label>
				                  <input  type = "date" id = "startdate" name="startdate" class = "validate" value="<?php echo htmlspecialchars($startdate) ?>" /> 
				                  <div id="error_movie_startdate" class="red-text"></div>   
				                 </div>
			                </div>
			            </div> 

			            <!-- End Date -->
						<div class = "row">
			               <div class = "input-field col s12 m12 l12">
			               	 	<div class = "card-panel grey lighten-3">
				                  <label for = "enddate" class="black-text">End Date:</label>
				                  <input  type = "date" id = "enddate" name="enddate" class = "validate" value="<?php echo htmlspecialchars($enddate) ?>" />
				                  <div id="error_movie_enddate" class="red-text"></div>
				                 </div>
			                </div>
			            </div> 
				 	 	
						<div class = "row">
						    <!-- Cinema Name -->
			                <div class = "input-field col s6 m6 l6">
			               	 	<div class = "card-panel grey lighten-3">
				                  <label for = "cinemaname" class="black-text">Cinema Name:</label>
				                  <input  type = "text" id = "cinemaname" name="cinemaname" class = "validate" value="<?php echo htmlspecialchars($cinemaname) ?>" disabled />  
				                 </div>
			                </div> 
			                <!-- Category -->
			                <div class = "input-field col s6 m6 l6"> 
				               <div class = "card-panel grey lighten-3">     
				                  <label for = "category" class="black-text">Category:</label>
				                  <input type = "text" id = "category" name="category" class = "validate" value="<?php echo htmlspecialchars($category) ?>" /> 
				                   <div id="error_movie_category" class="red-text"></div>       
				               </div>
				            </div>  
			            </div>

			            <!-- Submit button -->
			            <div class = "row"> 
			            	<div class = "col s12 m12 l12"> 
				            	<button onclick="addCinemaOwnerMovie()" data-target="modal2" class="teal black-text btn waves-effect waves-light col s12 m12 l12 modal-trigger">Submit</button>
				            </div>	
			            </div>
			    </div>
			</div>
		</div>
	</div>
</section>