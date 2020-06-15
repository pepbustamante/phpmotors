<?php
require_once 'connections.php';

/*Proxy connection to the PHP Motors database*/
function phpmotorsConnect()
{
$server = 'localhost';
$dbname= 'phpmotors';
$username = 'iClient';
$password = 'rtR8IrHzUFf3F6xb';
$dsn = "mysql:host=$server;dbname=$dbname";
$options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);

 // Create the actual connection object and assign it to a variable
 try {
    $link = new PDO($dsn,$username,$password,$options);
    //if(is_object($link)){
    //  echo 'is working';
    //}
    return $link;
   } catch(PDOException $e) {
    //echo"it did not work, error:" . $e->getMessage();
    header('Location: http://localhost/phpmotors/view/500.php');
    exit;
   }
}
phpmotorsConnect()
?>