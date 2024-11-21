<?php
  session_start();
  require('../../functions.php');
  guard();

  $error = [];
  $success = '';

  if (isset($_GET['id'])) {
      $student_id = sanitizeInput($_GET['id']);
      $con = openCon();

      $sql = "SELECT student_id, first_name, last_name FROM students WHERE student_id = ?";
      if ($stmt = mysqli_prepare($con, $sql)) {
          mysqli_stmt_bind_param($stmt, "s", $student_id);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_bind_result($stmt, $id, $firstname, $lastname);
          mysqli_stmt_fetch($stmt);
          mysqli_stmt_close($stmt);
      } else {
          $_SESSION['error'] = "Error fetching student data: " . mysqli_error($con);
      }

      if (isset($_POST['btnEdit'])) {
          $id = sanitizeInput($_POST['student_id']);
          $firstname = sanitizeInput($_POST['student_firstname']);
          $lastname = sanitizeInput($_POST['student_lastname']);

          if (empty($id)) {
              $error[] = "Empty Student ID";
          } elseif (!is_numeric($id)) {
              $error[] = "Invalid Student ID. It must be a number.";
          }
          
          if (empty($firstname)) {
              $error[] = "Empty First Name";
          }
          if (empty($lastname)) {
              $error[] = "Empty Last Name";
          }

          if (empty($error)) {
              $sqlUpdate = "UPDATE students SET first_name = ?, last_name = ? WHERE student_id = ?";
              if ($stmt = mysqli_prepare($con, $sqlUpdate)) {
                  mysqli_stmt_bind_param($stmt, "sss", $firstname, $lastname, $id);
                  if (mysqli_stmt_execute($stmt)) {
                      $success = "Student recors updated successfully!";
                  } else {
                      $error[] = "Error updating student: " . mysqli_error($con);
                  }
                  mysqli_stmt_close($stmt);
              } else {
                  $error[] = "Error preparing query: " . mysqli_error($con);
              }
          }
      }

      closeCon($con);
  } else {
      $_SESSION['error'] = "Student ID is not provided.";
      header("Location: register.php");
      exit();
  }

  include('../partials/header.php');
  include('../partials/side-bar.php');
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Edit Student Details</h1>
    <hr>
    <p><a href="/admin/dashboard.php">Dashboard</a><a href="register.php">/ Register</a> / Edit Student</p>

    <?php 
        if (!empty($error)) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
            foreach ($error as $errorMsg) {
                echo '<strong>' . $errorMsg . '</strong><br>';
            }
            echo '
                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
        } elseif (!empty($success)) {
            echo '
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>' . $success . '</strong>
                <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
        }
    ?>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="border p-4 rounded">
                <form method="POST" action="">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="student_id" placeholder="Student ID" value="<?php echo $id ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="studentName" class="form-label">First Name</label>
                        <input type="text" class="form-control" name="student_firstname" value="<?php echo $firstname; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="studentEmail" class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="student_lastname" value="<?php echo $lastname; ?>">
                    </div>

                    <button type="submit" name="btnEdit" class="btn btn-primary">Update Student</button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php 
  include('../partials/footer.php');
?>