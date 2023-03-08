<?php
	
    // Start the session
    session_start();

    // Access only if has been initialized the role and only if has been selected to move to the subscrption page
    if( (!isset($_SESSION['role'])) or strcmp($_SESSION['role'],'USER') !== 0 or (!isset($_REQUEST['sub'])) ){
        header('Location: welcome.php?error=invalid_access');
    }
    
    // Return the result as string
    $str = "";

    // Get the favorites movies of user
    // Create and initialize a curl session
    $curl = curl_init();

    // Set the options of curl session
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://172.18.1.10:1027/api/favorites?userid='.$_SESSION['id'],
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

    // Get the http status code
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    // Decode json to a associative array.
    $result = json_decode($response,TRUE);

    // Close curl resource to free up system resources
    curl_close($curl);

    // If the access_token is expired then redirect to access_token_expired
    if( strcmp($httpcode,'401') == 0 ){ echo 'access_token_expired'; exit(); }

    // Print the notifications as collection item
    $total_count = 0;
    if ( count($result) > 0 and !(isset($result['error'])) ){
      foreach($result as $rows){

        // Get the notifications about movie using movieid
        // Create and initialize a curl session
        $curl = curl_init();

        // Set the options of curl session
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://172.18.1.10:1027/api/subscriptions?movieid='.$rows['MOVEID'],
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

        // Get the http status code
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        // Decode json to a associative array.
        $result_notification = json_decode($response,TRUE);

        // Close curl resource to free up system resources
        curl_close($curl);

        // If the access_token is expired then redirect to access_token_expired
        if( strcmp($httpcode,'401') == 0 ){ echo 'access_token_expired'; exit(); }

        // Parse the notifications and print the messages
        $count = 0;
        if ( count($result_notification) > 0 and !(isset($result_notification['error'])) ){
            foreach($result_notification as $row){
              
              // Check the availabilty of movie with comparing the last enddate of movie to current date
              if( ($count == 0) and (date(DATE_ISO8601) > $row["data"][0]["ENDDATE"]["value"]) ){
          
                    $msg = "The movie ".$row["data"][0]["TITLE"]["value"]
                            ." is not availale on ".$row["data"][0]["CINEMANAME"]["value"];

                    $str = $str."<li class=\"collection-item avatar\">
                    <i class=\"material-icons circle teal \">movie</i>".
                    "<span class=\"title\">".$msg."</span>

                    <!--Delete notification from subscription -->
                    <div class=\"secondary-content\">
                        <button onclick=\"deleteNotification('$row[_id]')\" class=\"btn waves-effect waves-light\">
                            <i class=\"material-icons center\">remove_circle</i>
                        </button>
                    </div>	
                  </li>";
                  
                  $total_count = $total_count + 1;
              }
              
              $msg = "The movie ".$row["data"][0]["TITLE"]["value"]
                      ." is availale from ".date_format(date_create($row["data"][0]["STARTDATE"]["value"]),"Y-m-d")
                      ." to ".date_format(date_create($row["data"][0]["ENDDATE"]["value"]),"Y-m-d")." on ".$row["data"][0]["CINEMANAME"]["value"];

              $str = $str."<li class=\"collection-item avatar\">
                            <i class=\"material-icons circle teal \">movie</i>".
                            "<span class=\"title\">".$msg."</span>

                            <!--Delete notification from subscription -->
                            <div class=\"secondary-content\">
                                <button onclick=\"deleteNotification('$row[_id]')\" class=\"btn waves-effect waves-light\">
                                    <i class=\"material-icons center\">remove_circle</i>
                                </button>
                            </div>	
                          </li>";

              $total_count = $total_count + 1;
              $count = $count + 1;
            }
        }

      }
    }

    // Retrun the response to client
    echo $total_count.'?'.$str;
?>