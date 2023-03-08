<?php
   
   // Start session
	session_start();
   
   // Remove all session variables
	session_unset();

	// Destroy the session
   session_destroy();

   // Unset all the cookies
   $past = time() - 3600;
   foreach ( $_COOKIE as $key => $value ){ setcookie($key, $value, $past); }

   // Refresh for 1 sec and redirect to index.php
   header('Refresh: 1; URL = ../index.php?logout=true');
?>