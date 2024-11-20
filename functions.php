<?php 

function openCon(){
  $con = mysqli_connect("localhost", "root", "", "php-exam");

  if($con === false)
    die("Error Database couldn't connect" . mysqli_connect_error());

    return $con;
}

function closeCon($con){
    mysqli_close($con);
}
function sanitizeInput($input){
    return stripslashes(htmlspecialchars(trim($input)));
}

function sanitizeEmail($email) {
   
    $email = sanitizeInput($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return $email;
}

function guard() {
    if (!isset($_SESSION['email'])) {
        header("Location: /index.php");
        exit();
    }
}



?>