<?php

session_start();
require_once('connection.php');

if(isset($_POST['Logout']))
{
    session_destroy();
    unset($_SESSION['username']);
}

$error = 0;

if (isset($_POST['Login'])) {
  if (empty($_POST['Name']) || empty($_POST['Password'])) {
    header("location:index.php");
  } else {
    $saltWord = "sentiment_analyzer";
    $enteredPassword = $_POST['Password'];
    $composedPassword = $enteredPassword.$saltWord;
    $finalHash = md5($composedPassword);
    $query = "SELECT * FROM user WHERE username = '" . $_POST['Name'] . "' AND password = '$finalHash' ";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
      $_SESSION['username'] = $_POST['Name'];
      $_SESSION['password'] = $_POST['Password'];
      $_SESSION['email'] = $row['email'];
      $_SESSION['userID'] = $row['userID'];
      $_SESSION['usertype'] = $row['usertype'];
      if ($row['usertype'] == 'admin') header("location:admin_statistics.php");
      else header("location:user_comments.php");
    } else {
      $error = 1;
    }
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

  <title>Login</title>

  <!-- Custom fonts for this template-->
  <link href="vendor_sb2/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css_sb2/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

  <div class="container">
    <br>
    <br>

    <!-- <h1 class="h2 text-gray-100 mb-4" align="center">Welcome to Sentiment Analyzer</h1> -->
    <a class="h2 text-gray-100 mb-4" align="center">
      <div class="sidebar-brand-icon">
        <i class="fas fa-laugh-wink"></i>
        <i class="fas fa-sad-cry"></i>
        <i class="fas fa-smile-beam"></i>
        <i class="fas fa-angry"></i>
        <i class="fas fa-tired"></i>
        <i class="fas fa-laugh-squint"></i>
        <i class="fas fa-grin-hearts"></i>
      </div>
      <div>
        <h1 class="h2 text-gray-100 mb-4">Welcome to Sentiment Analyzer</h1>
        <h6>An opinion mining tool for web platforms</h6>
      </div>
    </a>
    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-6 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="row">

              <div class="col-lg-12">
                <div class="p-5">
                  <!-- <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Welcome to Sentiment Analyzer</h1>
                  </div> -->
                  <form class="user" method="POST" action="index.php">
                    <div class="form-group">
                      <input type="text" name="Name" class="form-control form-control-user" required id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Username">
                    </div>
                    <div class="form-group">
                      <input type="password" name="Password" class="form-control form-control-user" required id="exampleInputPassword" placeholder="Password">
                    </div>

                    <button class="btn btn-primary btn-user btn-block" name="Login">
                      Login
                    </button>
                    
                    

                    <?php
                    if ($error == 1) {
                    ?>
                      <p>
                      <div class="alert alert-danger" role="alert">
                        <center>The username or password are not correct!</center>
                      </div>
                    <?php
                    }
                    ?>

                    <hr>

                  </form>

                  <div class="text-center">
                    <a class="small" href="register.php">Create an Account</a>
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