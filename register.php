<?php

require_once('connection.php');
$registerError = 0;
$nameError = 0;

if (isset($_POST['Register'])) {
  $username = $_POST['Name'];
  $email = $_POST['Email'];
  $password = $_POST['Password'];
  $rpassword = $_POST['Repeatpassword'];
  $usertype = "user";

  $saltWord = "sentiment_analyzer";
  $composedPassword = $password.$saltWord;
  $finalHash = md5($composedPassword);

  $sql_check = "SELECT * FROM user WHERE username = '" . $_POST['Name'] . "'";
  $run_check = mysqli_query($conn, $sql_check);
  $check = mysqli_fetch_assoc($run_check);

  if ($password === $rpassword) {
    $query = "INSERT INTO user (username,password,email,usertype) VALUES ('$username','$finalHash','$email','$usertype')";
    if (strcmp($username, $check['username']) == 0) 
    {
      $nameError = 1;
    } 
    else 
    {
      $result = mysqli_query($conn, $query);
      header("location:index.php");
      $_SESSION['username'] = $_POST['Name'];
      $_SESSION['success'] = "You are now logged in";
    }
  } 
  else 
  {
    $registerError = 1;
  }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Register</title>

  <!-- Custom fonts for this template-->
  <link href="vendor_sb2/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css_sb2/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">
  <div class="container">

    <div class="row justify-content-center">

      <div class="col-xl-6 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">

              <div class="col-lg-12">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Create an Account</h1>
                  </div>
                  <form class="user" method="POST" action="register.php">
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="exampleFirstName" placeholder="User Name" required name="Name">
                    </div>
                    <div class="form-group">
                      <input type="email" class="form-control form-control-user" id="exampleInputEmail" placeholder="Email Address" required name="Email">
                    </div>
                    <div class="form-group row">
                      <div class="col-sm-6 mb-3 mb-sm-0">
                        <input type="password" class="form-control form-control-user" id="exampleInputPassword" placeholder="Password" required name="Password">
                      </div>
                      <div class="col-sm-6">
                        <input type="password" class="form-control form-control-user" id="exampleRepeatPassword" placeholder="Repeat Password" required name="Repeatpassword">
                      </div>
                    </div>
                    <button class="btn btn-primary btn-user btn-block" name="Register">
                      Register Account
                    </button>
                    <br>

                    <?php
                    if ($registerError == 1) {
                    ?>
                      <div class="alert alert-danger" role="alert">
                        <center>The passwords do not match!</center>
                      </div>
                    <?php
                    }
                    ?>

                    <?php
                    if ($nameError == 1) {
                    ?>
                      <div class="alert alert-danger" role="alert">
                        <center>The username is already taken!</center>
                      </div>
                    <?php
                    }
                    ?>


                    <hr>
                  </form>

                  <div class="text-center">
                    <a class="small" href="index.php">Already have an account? Login!</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor_sb2/jquery/jquery.min.js"></script>
  <script src="vendor_sb2/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor_sb2/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js_sb2/sb-admin-2.min.js"></script>

</body>

</html>