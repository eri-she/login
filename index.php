<?php
// start a session calling this function.
session_start();

if (isset($_SESSION['user'])) {
    header("Location: home.php");
    exit;
}

if (isset($_SESSION['adm'])) {
    header("Location: dashboard.php");
    exit;
}
require_once 'components/db_connect.php';

$error = false;
// you can declare many variables at once and set them empty.
$email = $pass = $emailError = $passError = "";
// checks if the user has clicked on the submit button.
if (isset($_POST['btn-login'])) {
    // prevent sql injections/ clear user invalid inputs
    $email = trim($_POST['email']);
    // trim eliminates spaces before and after email.
    $email = strip_tags($email);
    // strip_tags removes HTML and PHP tags from the string
    $email = htmlspecialchars($email);

    $pass = trim($_POST['pass']);
    $pass = strip_tags($pass);
    $pass = htmlspecialchars($pass);
// empty checks if the field is empty
    if (empty($email)) {
        $error = true;
        $emailError = "Please enter your email address.";
        // filter validation checks if the email provided is in the right format
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = true;
        $emailError = "Please enter valid email address.";
    }
    if (empty($pass)) {
        $error = true;
        $passError = "Please enter your password.";
    }
// if error remains in false, that means that the format of all the fields are correct, then we proceed fetching the information from the database.
// ! error means if $error is not true.
    if (!$error) {
        // hash () returns a hash value for the given data ($pass) based on the algorithm sha256
        $password = hash('sha256', $pass);
        $sql = "SELECT * FROM users WHERE email= '$email' AND password = '$password'";
        $result = mysqli_query($connect, $sql);
        $row = mysqli_fetch_assoc($result);
       
// checks the number of records matching email and password
        $count = mysqli_num_rows($result);
// if there is at least one record.
        if ($count == 1) {
            if ($row['status'] == "adm") {
                $_SESSION['adm'] = $row['id'];
                header("Location: dashboard.php");
                exit;
            } else {
                $_SESSION['user'] = $row['id'];
                header("Location: home.php");
                exit;
            }
        } else {
            $errMSG = "Incorrect Credentials, Try again...";
        }
    }
}
// mysqli_close($connect);


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login & Registration System</title>
  <?php require_once 'components/boot.php' ?>
</head>

<body>
  <div class="container border mt-5">
    <!-- htmpspecialchars replaces special characters  -->
      <form class="w-75" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">
          <h2>Login</h2>
          <hr />
          <!-- error message displayed if you are not registered -->
          <?php
          if (isset($errMSG)) {
              echo $errMSG;
          }
          ?>

          <input type="email" autocomplete="off" name="email" class="form-control mb-2" placeholder="Your Email"  maxlength="40" />
          <span class="text-danger"><?php echo $emailError; ?></span>

          <input type="password" name="pass" class="form-control" placeholder="Your Password" maxlength="15" />
          <span class="text-danger"><?php echo $passError; ?></span>
          <hr />
          <button class="btn btn-block btn-primary" type="submit" name="btn-login">Sign In</button>
          <hr />
          <a href="register.php">Not registered yet? Click here</a>
      </form>
  </div>
</body>
</html>