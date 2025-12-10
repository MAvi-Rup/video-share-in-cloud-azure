<?php
  // Log the user out using Azure EasyAuth
  session_start();
  session_destroy();

  // Redirect the user to the Azure logout endpoint
  header("Location: /.auth/logout");
  exit;
?>
<?php
  // Log the user out using Azure EasyAuth
  session_start();
  session_destroy();

  // Redirect the user to the Azure logout endpoint
  header("Location: /.auth/logout");
  exit;
?>
