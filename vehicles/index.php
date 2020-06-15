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
    // Get the accounts model
    require_once '../model/vehicles-model.php';
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

         case 'addVehicle':
            // Filter and store the data
            $classificationId = filter_input(INPUT_POST, 'classificationId', FILTER_SANITIZE_NUMBER_INT);
            $invMake = filter_input(INPUT_POST, 'invMake', FILTER_SANITIZE_STRING);
            $invModel = filter_input(INPUT_POST, 'invModel', FILTER_SANITIZE_STRING);
            $invDescription = filter_input(INPUT_POST, 'invDescription', FILTER_SANITIZE_STRING);
            $invImage = filter_input(INPUT_POST, 'invImage', FILTER_SANITIZE_STRING);
            $invThumbnail = filter_input(INPUT_POST, 'invThumbnail', FILTER_SANITIZE_STRING);
            $invPrice = filter_input(INPUT_POST, 'invPrice', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $invStock = filter_input(INPUT_POST, 'invStock', FILTER_SANITIZE_NUMBER_INT);
            $invColor = filter_input(INPUT_POST, 'invColor', FILTER_SANITIZE_STRING);

            
            // Check for missing data
            if(empty($classificationId) || empty($invMake) || empty($invModel) || empty($invDescription) || empty($invImage) || empty($invThumbnail) || empty($invPrice) || empty($invStock) || empty($invColor)){
               
               $message = '<p>Please provide information for all empty form fields to add a new car.</p>';
               include '../view/add-vehicle.php';
               exit; 
            }

            // Send the data to the model
            $regOutcome1 = regVehicle($classificationId, $invMake, $invModel, $invDescription, $invImage, $invThumbnail, $invPrice, $invStock, $invColor);
      
            // Check and report the result
            if($regOutcome1 === 1){
               $message = "<p>The $invMake has been registered.</p>";
               include '../view/add-vehicle.php';
               exit;
            } else {
               $message = "<p>Sorry, but the registration failed for $invMake. Please try again.</p>";
               include '../view/add-vehicle.php';
               exit;
            }  
         break;

         case 'addCarclassification':
            // Filter and store the data
            $classificationName = filter_input(INPUT_POST, 'classificationName');

            // Check for missing data
            if(empty($classificationName)){
               $message = '<p>Please provide information for all empty form fields.</p>';
               include '../view/add-classification.php';
               exit; 
            }  

            // Send the data to the model
            $regOutcome2 = regcarClassification($classificationName);
        
            // Check and report the result
            if($regOutcome2 === 1){
               $message = "";
               include '../view/add-classification.php';
               exit;
            } else {
               $message = "<p>Sorry $classificationName, but the registration failed. Please try again.</p>";
               include '../view/add-classification.php';
               exit;
            }  

            break;  

        case 'vehicle-man.php':
            include '../view/vehicle-man.php';  
         break;

         case 'add-classification.php':
            include '../view/add-classification.php';  
         break;

         case 'add-vehicle.php':
            include '../view/add-vehicle.php';  
         break;
        
         /* * ********************************** 
         * Get vehicles by classificationId 
         * Used for starting Update & Delete process 
         * ********************************** */ 
         case 'getInventoryItems': 
            // Get the classificationId 
            $classificationId = filter_input(INPUT_GET, 'classificationId', FILTER_SANITIZE_NUMBER_INT); 
            // Fetch the vehicles by classificationId from the DB 
            $inventoryArray = getInventoryByClassification($classificationId); 
            // Convert the array to a JSON object and send it back 
            echo json_encode($inventoryArray); 
         break;

        case 'mod':
            $invId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $invInfo = getInvItemInfo($invId);
            if(count($invInfo)<1){
               $message = 'Sorry, no vehicle information could be found.';
            }
            include '../view/vehicle-update.php';
            exit;
        break;

        case 'updateVehicle':
            // Filter and store the data
            $classificationId = filter_input(INPUT_POST, 'classificationId', FILTER_SANITIZE_NUMBER_INT);
            $invMake = filter_input(INPUT_POST, 'invMake', FILTER_SANITIZE_STRING);
            $invModel = filter_input(INPUT_POST, 'invModel', FILTER_SANITIZE_STRING);
            $invDescription = filter_input(INPUT_POST, 'invDescription', FILTER_SANITIZE_STRING);
            $invImage = filter_input(INPUT_POST, 'invImage', FILTER_SANITIZE_STRING);
            $invThumbnail = filter_input(INPUT_POST, 'invThumbnail', FILTER_SANITIZE_STRING);
            $invPrice = filter_input(INPUT_POST, 'invPrice', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $invStock = filter_input(INPUT_POST, 'invStock', FILTER_SANITIZE_NUMBER_INT);
            $invColor = filter_input(INPUT_POST, 'invColor', FILTER_SANITIZE_STRING);
            $invId = filter_input(INPUT_POST, 'invId', FILTER_SANITIZE_NUMBER_INT);

            // Check for missing data
            if(empty($classificationId) || empty($invMake) || empty($invModel) || empty($invDescription) || empty($invImage) || empty($invThumbnail) || empty($invPrice) || empty($invStock) || empty($invColor)){
              
               $message = '<p>Please provide information for all empty form fields to update the car.</p>';
               include '../view/vehicle-update.php';
               exit; 
            }
               //echo "$classificationId, $invMake, $invModel, $invDescription, $invImage, $invThumbnail, $invPrice, $invStock, $invColor, $invId"; exit;
            // Send the data to the model
            $updateResult = updateVehicle($invMake, $invModel, $invDescription, $invImage, $invThumbnail, $invPrice, $invStock, $invColor, $classificationId, $invId);
      
            // Check and report the result
            if($updateResult ===1){
               $message = "<p>The $invMake has been update.</p>";
               $_SESSION['message'] = $message;
	            header('location: /phpmotors/vehicles/');
               exit;
            } else {
               $message = "<p>Sorry, but the uptdate failed for $invMake. Please try again.</p>";
               include '../view/vehicle-update.php';
               exit;
            }  
        break;

        case 'del':
            $invId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $invInfo = getInvItemInfo($invId);
            if (count($invInfo) < 1) {
               $message = 'Sorry, no vehicle information could be found.';
            }
            include '../view/vehicle-delete.php';
            exit; 
        break;

        case 'deleteVehicle':   
            $invMake = filter_input(INPUT_POST, 'invMake', FILTER_SANITIZE_STRING);
            $invModel = filter_input(INPUT_POST, 'invModel', FILTER_SANITIZE_STRING);
            $invId = filter_input(INPUT_POST, 'invId', FILTER_SANITIZE_NUMBER_INT);
            
            $deleteResult = deleteVehicle($invId);
            if ($deleteResult) {
               $message = "<p class='notice'>Congratulations the, $invMake $invModel was	successfully and permanetly deleted.</p>";
               $_SESSION['message'] = $message;
               header('location: /phpmotors/vehicles/');
               exit;
            } else {
               $message = "<p class='notice'>Error: $invMake $invModel was not
            deleted.</p>";
               $_SESSION['message'] = $message;
               header('location: /phpmotors/vehicles/');
               exit;
            }
        break;

        case 'admin':
         $classificationList = buildClassificationList($classifications);
        include '../view/vehicle-man.php'; 
       break;
       
        default:  
            $classificationList = buildClassificationList($classifications);
         include '../view/vehicle-man.php';
        break;
      }

        


?>
