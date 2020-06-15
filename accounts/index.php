<?php
//this is the acounts controler ACOUNTS, I repeat ACOUNTS, A-CO-UN-TS

    // Create or access a Session
    session_start();

    // Get the database connection file
    require_once '../library/connections.php';
    // Get the PHP Motors model for use as needed
    require_once '../model/main-model.php';
    // Get the accounts model
    require_once '../model/accounts-model.php';
    // Get the functions library
    require_once '../library/functions.php';

    // Get the array of classifications
    $classifications = getClassifications();
    
    //Build a navigation bar using the $classifications array
    $navList = buildNavigation($classifications);

    //var_dump($classifications);
    //exit;


    $action = filter_input(INPUT_GET, 'action');
     if ($action == NULL){
    $action = filter_input(INPUT_POST, 'action');
    }

    switch ($action){

      case 'login.php':
        include '../view/login.php';  
     break;

      case 'registration.php':
         include '../view/registration.php';  
      break;
      
      case 'admin':
       include '../view/admin.php';  
      break;

      case 'register':
        // Filter and store the data
        $clientFirstname = filter_input(INPUT_POST, 'clientFirstname', FILTER_SANITIZE_STRING);
        $clientLastname = filter_input(INPUT_POST, 'clientLastname', FILTER_SANITIZE_STRING);
        $clientEmail = filter_input(INPUT_POST, 'clientEmail', FILTER_SANITIZE_EMAIL);
        $clientPassword = filter_input(INPUT_POST, 'clientPassword', FILTER_SANITIZE_STRING);
       
        $clientEmail = checkEmail($clientEmail);
        $checkPassword = checkPassword($clientPassword);

        // Check for missing data
        if(empty($clientFirstname) || empty($clientLastname) || empty($clientEmail) || empty($checkPassword)){
          $message = '<p>Please provide information for all empty form fields.</p>';
          include '../view/registration.php';
          exit; 
        }

        $existingEmail = checkExistingEmail($clientEmail);
        //checking for an existing email address
        // Check for existing email address in the table
        if($existingEmail){
          $message = '<p>That email address already exists. Do you want to login instead?</p>';
          include '../view/login.php';
          exit;
        } 
        
        // Hash the checked password
        $hashedPassword = password_hash($clientPassword, PASSWORD_DEFAULT);
        // Send the data to the model
        $regOutcome = regClient($clientFirstname, $clientLastname, $clientEmail, $hashedPassword);
        

        // Check and report the result
        if($regOutcome === 1){
          //setcookie('firstname', $clientFirstname, strtotime('+1 year'), '/');

          $_SESSION['message'] = "Thanks for registering $clientFirstname. Please use your email and password to login.";
          header('Location: /phpmotors/accounts/?action=login.php');
          exit;
        } else {
          $message = "<p>Sorry $clientFirstname, but the registration failed. Please try again.</p>";
          include '../view/registration.php';
          exit;
        }  

      break;
      
      case 'Logout': 
        session_destroy();
        header('Location: /phpmotors/index.php');
      break;

      case 'Login':  
         
          $clientEmail = filter_input(INPUT_POST, 'clientEmail', FILTER_SANITIZE_EMAIL);
          $clientPassword = filter_input(INPUT_POST, 'clientPassword', FILTER_SANITIZE_STRING);
          
          $clientEmail = checkEmail($clientEmail);
          $checkPassword = checkPassword($clientPassword);
        
          // Run basic checks, return if errors
          if (empty($clientEmail) || empty($checkPassword)) {
          $message = '<p class="notice">Please provide a valid email address and password.</p>';
          include '../view/login.php';
          exit;
          }
            
          // A valid password exists, proceed with the login process
          // Query the client data based on the email address
          $clientData = getClient($clientEmail);
          // Compare the password just submitted against
          // the hashed password for the matching client
          $hashCheck = password_verify($clientPassword, $clientData['clientPassword']);
          // If the hashes don't match create an error
          // and return to the login view
          if(!$hashCheck) {
            $_SESSION['message'] = '<p class="notice">Please check your password and try again.</p>';
            include '../view/login.php';
            exit;
          }
          // A valid user exists, log them in
          $_SESSION['loggedin'] = TRUE;
          // Remove the password from the array
          // the array_pop function removes the last
          // element from an array
          array_pop($clientData);
          // Store the array into the session
          $_SESSION['clientData'] = $clientData;
          // Send them to the admin view
          include '../view/admin.php';
          exit;
                  
      break;
      
      case 'clientUpdate':
        include '../view/client-update.php';
      break;

      case 'updateClient':
           // Filter and store the data
        $clientFirstname = filter_input(INPUT_POST, 'clientFirstname', FILTER_SANITIZE_STRING);
        $clientLastname = filter_input(INPUT_POST, 'clientLastname', FILTER_SANITIZE_STRING);
        $clientEmail = filter_input(INPUT_POST, 'clientEmail', FILTER_SANITIZE_EMAIL);
        $clientId = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_NUMBER_INT);

       // echo "$clientFirstname, $clientLastname, $clientEmail, $clientId"; exit;

        // Check for missing data
        if(empty($clientFirstname) || empty($clientLastname) || empty($clientEmail) ){
          $message = '<p>Please provide information for all empty form fields.</p>';
          include '../view/client-update.php';
          exit; 
        }

        //$existingEmail = checkExistingEmail($clientEmail);
        //checking for an existing email address
        // Check for existing email address in the table
        //if($existingEmail){
         // $message = '<p>That email address already exists.</p>';
         // include '../view/client-update.php';
         // exit;
        //} 
      
        $updateClient = updateClient($clientFirstname, $clientLastname, $clientEmail,  $clientId);
          if ($updateClient===1) {
          $message = "<p class='notice'>Congratulations, the $clientFirstname $clientLastname information was successfully updated.</p>";
            $_SESSION['message'] = $message;
            header('location: /phpmotors/accounts?action=admin');
            exit;
          } else {
            $message = "<p class='notice'>Error. the $clientFirstname $clientLastname information was not updated.</p>";
            include '../view/client-update.php';
            exit;
          }
          // A valid user exists, log them in
          $_SESSION['loggedin'] = TRUE;
          // Remove the password from the array
          // the array_pop function removes the last
          // element from an array
          array_pop($clientData);
          // Store the array into the session
          $_SESSION['clientData'] = $clientData;
          // Send them to the admin view
          include '../view/admin.php';
        break;

    case 'mod':
      //getClient to getInfoClient
      $clientId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
      //$clientId = getClient($clientId);
      $clientInfo = getInfoClient($clientId); //prove
      //if(count($clientId)<1){
      if(count($clientInfo)<1){ //prove
       $message = 'Sorry, no client information could be found.';
      }
      //include '../view/client-update.php';
      include '../view/client-update.php, ../view/admin.php';
      //include '../view/admin.php';
      exit;
    break;

    case 'updatePassword':
      // Filter and store the data
      $clientPassword = filter_input(INPUT_POST, 'clientPassword', FILTER_SANITIZE_STRING);
      $clientId = filter_input(INPUT_POST, 'clientId', FILTER_SANITIZE_NUMBER_INT);
      
      $checkPassword = checkPassword($clientPassword);
      
      // Check for missing data
      if(empty($checkPassword)){
        $message = '<p>Please provide a new password.</p>';
        include '../view/client-update.php';
        exit; 
      }
      // Hash the checked password
      $hashedPassword = password_hash($clientPassword, PASSWORD_DEFAULT);

      $updatePassword = updatePassword($hashedPassword, $clientId);
           if ($updatePassword === 1) {
           $message = "<p class='notice'> your Password has been successfully updated.</p>";
             $_SESSION['message'] = $message;
             header('location: /phpmotors/accounts?action=admin');
             include '../view/admin.php';
             exit;
           } else {
             $message = "<p class='notice'>Error, your password has not been changed.</p>";
             include '../view/client-update.php';
             exit;
           }
    break;
    
    default:
    break;

    //********************PROVE************** */
    case 'getClientsItems': 
      // Get the classificationId 
      $clientId = filter_input(INPUT_GET, 'clientId', FILTER_SANITIZE_NUMBER_INT); 
      // Fetch the vehicles by classificationId from the DB 
      $ClientsArray = getInfoClient($clientId); 
      // Convert the array to a JSON object and send it back 
      echo json_encode($ClientsArray); 
      break;

       }
        
?>
