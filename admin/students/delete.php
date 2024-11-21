<?php
  session_start();
  require('../../functions.php');
  guard();

  $arrStudentsRec = [];
  $error = [];

  if (isset($_GET['delete_id'])) {
      $deleteId = sanitizeInput($_GET['delete_id']);
      $con = openCon();
      
      $sql = "SELECT student_id, first_name, last_name FROM students WHERE student_id = ?";
      if ($stmt = mysqli_prepare($con, $sql)) {
          mysqli_stmt_bind_param($stmt, "s", $deleteId);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_bind_result($stmt, $studentId, $firstName, $lastName);
          if (mysqli_stmt_fetch($stmt)) {
              $arrStudentsRec = [
                  'id' => $studentId,
                  'first_name' => $firstName,
                  'last_name' => $lastName
              ];
          } else {
              $error[] = "Student not found.";
          }
          mysqli_stmt_close($stmt);
      } else {
          $error[] = "Error fetching student data: " . mysqli_error($con);
      }

      if (isset($_POST['btnDelete'])) {
          $sqlDelete = "DELETE FROM students WHERE student_id = ?";
          if ($deleteStmt = mysqli_prepare($con, $sqlDelete)) {
              mysqli_stmt_bind_param($deleteStmt, "s", $deleteId);
              if (mysqli_stmt_execute($deleteStmt)) {
                  $_SESSION['student_success'] = "Student record deleted successfully!";
              } else {
                  $_SESSION['error'] = "Error deleting student: " . mysqli_error($con);
              }
              mysqli_stmt_close($deleteStmt);
          } else {
              $_SESSION['error'] = "Error query: " . mysqli_error($con);
          }
          closeCon($con);
          header("Location: register.php");
          exit();
      }

      closeCon($con);
  } else {
      $_SESSION['error'] = "No student ID.";
      header("Location: register.php");
      exit();
  }

  include('../partials/header.php');
  include('../partials/side-bar.php');
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
  <h1 class="h2">Edit Student Details</h1>
  <hr>
  <p><a href="/admin/dashboard.php">Dashboard</a><a href="register.php">/ Register</a> / Delete Student</p>

  <div class="border p-3 rounded">
    <h3>Are you sure you want to delete the record of the following student?</h3>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger">
        <?php foreach ($error as $errMsg): ?>
          <p><?php echo htmlspecialchars($errMsg); ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($arrStudentsRec): ?>
      <ul>
        <li><strong>Student ID:</strong> <?php echo htmlspecialchars($arrStudentsRec['id']); ?></li>
        <li><strong>Student First Name:</strong> <?php echo htmlspecialchars($arrStudentsRec['first_name']); ?></li>
        <li><strong>Student Last Name:</strong> <?php echo htmlspecialchars($arrStudentsRec['last_name']); ?></li>
      </ul>

      <form method="POST" action="">
        <button type="submit" name="btnCancel" class="btn btn-secondary" href='register.php'>Cancel</button>
        <button type="submit" name="btnDelete" class="btn btn-danger">Delete Student Record</button>
      </form>
    <?php else: ?>
      <p>No student found with this ID.</p>
    <?php endif; ?>
  </div>
</main>

<?php include('../partials/footer.php'); ?>