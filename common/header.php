<div class="container">
  <img src="/phpmotors/images/site/logo.png" alt="logo php motors">
  
  

    <?php 
      if(isset($cookieFirstname)){
        echo "<span>Welcome $cookieFirstname</span>";
      } 
      else if (isset($_SESSION['clientData']) && $_SESSION['clientData']['clientFirstname']) {
        echo "<span class='welcome-message'>Welcome " . $_SESSION['clientData']['clientFirstname'] . "</span>";
      }
    ?>   

<div id="containerIntocontainer">
    <p>    
    <?php
    
    if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']){
      echo "<a href='/phpmotors/accounts/index.php?action=Logout' title='Logout of your account'> Logout </a>";
    } 
    else{
      echo "<a href='/phpmotors/accounts/index.php?action=login.php' title='Login into your account'> My acount</a>";
    }?>    
    
    </p>
  </div>
</div>