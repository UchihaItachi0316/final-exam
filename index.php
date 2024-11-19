<?php
  session_start();
  require_once('functions.php');

  if (isset($_POST['login'])) {
      $con = openCon();

      $email = sanitizeEmail($_POST['email']);
      $password = sanitizeInput($_POST['password']);

      if ($stmt = $con->prepare("SELECT id, password FROM users WHERE email = ? AND password = ?")) {
          $stmt->bind_param('ss', $email, $password);

          $stmt->execute();
          $stmt->store_result();

          if ($stmt->num_rows > 0) {
              $stmt->bind_result($id, $hashed_password);
              $stmt->fetch();

              // password_verify($password, $hashed_password) lalagay pag nag register na na naka hashed
              if ($password === $hashed_password) {
                  $_SESSION['email'] = $email;
                  guard();
                  header('Location: admin/dashboard.php');
                  exit();
              } else {
                  $error = "Invalid email or password.";
              }
          } else {
              $error = "Invalid email or password.";
          }

          $stmt->close();
      } else {
          $error = "Error in database. Please try again later.";
      }

      closeCon($con);
    }
?>

<!DOCTYPE html>
<html lang="en">

  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
      <title></title>
  </head>

  <body class="bg-secondary-subtle">
      <div class="d-flex align-items-center justify-content-center vh-100">
          <div class="col-3">
              <?php 
                  if(isset($error)){

                      echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <strong>'.$error.'!</strong> You should check in on some of those fields below.
                      <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                    </div>';
                      
                  }
              
              ?>
              <div class="card">
                  <div class="card-body">
                      <h1 class="h3 mb-4 fw-normal">Login</h1>
                      <form method="post" action="">
                          <div class="form-floating mb-3">
                              <input type="text" class="form-control" id="email" name="email" placeholder="user1@example.com">
                              <label for="email">Email address</label>
                          </div>
                          <div class="form-floating mb-3">
                              <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                              <label for="password">Password</label>
                          </div>
                          <div class="form-floating mb-3">
                              <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                          </div>
                      </form>
                  </div>
              </div>
          </div>
      </div>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>

</html>