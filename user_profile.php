<?php

session_start();
require_once('connection.php');

if(!isset($_SESSION['username']))
{
  header("location:index.php");
}

$resetError = 0;
$nameError = 0;

if (isset($_POST['Update'])) {
    $u_id = $_POST['editID'];
    $u_name = $_POST['Name'];
    $u_email = $_POST['Email'];
    $u_password = $_POST['Password'];
    $u_rpassword = $_POST['Repeatpassword'];

    $sql_check = "SELECT * FROM user WHERE username = '" . $_POST['Name'] . "'";
    $run_check = mysqli_query($conn, $sql_check);
    $check = mysqli_fetch_assoc($run_check);

    $saltWord = "sentiment_analyzer";
    $composedPassword = $u_password.$saltWord;
    $finalHash = md5($composedPassword);

    if ($u_password === $u_rpassword) 
    {
        $query = "UPDATE user SET username = '$u_name', email = '$u_email', password = '$finalHash' WHERE userID = '$u_id'";
        if(strcmp($u_name, $_SESSION['username']) == 0)
        {
            $result = mysqli_query($conn, $query);
            if ($result) 
            {
                $_SESSION['success'] = "Your data is updated";
                header("location:index.php");
                echo '<script>alert("Your data is updated")</script>';
            } 
            else 
            {
                $_SESSION['success'] = "Your data is NOT updated";
                echo '<script>alert("Your data is NOT updated")</script>';
            }
        }
        else
        {
            if(strcmp($u_name, $check['username']) == 0)
            {
                $nameError = 1;
            }
            else
            {
                $result = mysqli_query($conn, $query);
                if ($result) 
                {
                    $_SESSION['success'] = "Your data is updated";
                    header("location:index.php");
                    echo '<script>alert("Your data is updated")</script>';
                } 
                else 
                {
                $_SESSION['success'] = "Your data is NOT updated";
                echo '<script>alert("Your data is NOT updated")</script>';
            }
            }
        }
    } 
    else 
    {
        $resetError = 1;
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

    <title>User profile</title>

    <!-- Custom fonts for this template-->
    <link href="vendor_sb2/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css_sb2/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include "user_sidebar.php"; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include "topbar.php"; ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Earnings (Monthly) Card Example -->


                        <!-- Earnings (Monthly) Card Example -->


                        <!-- Earnings (Monthly) Card Example -->

                        <!-- Pending Requests Card Example -->


                        <!-- Content Row -->



                        <!-- Area Chart -->

                        <!-- Card Body -->


                        <!-- Pie Chart -->

                        <!-- Card Body -->

                        <!-- Content Row -->

                        <!-- Content Column -->
                        <div class="col-lg-12 mb-4">

                            <!-- Project Card Example -->
                            <div class="card shadow mb-4">


                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Edit user profile</h6>
                                </div>
                                <div class="card-body">

                                    <form class="user" method="POST" action="user_profile.php">
                                        <input type="hidden" name="editID" value="<?php echo $_SESSION['userID'] ?>">
                                        <div class="form-group">
                                            <label><strong>User name</strong></label>
                                            <input type="text" class="form-control form-control-user" id="exampleFirstName" placeholder="User Name" required name="Name" value="<?php echo $_SESSION['username']; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Email</strong></label>
                                            <input type="email" class="form-control form-control-user" id="exampleInputEmail" placeholder="Email Address" required name="Email" value="<?php echo $_SESSION['email']; ?>">
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <label><strong>Password</strong></label>
                                                <input type="password" class="form-control form-control-user" id="exampleInputPassword" placeholder="Password" required name="Password" value="<?php echo $_SESSION['password']; ?>">
                                            </div>
                                            <div class="col-sm-6">
                                                <label><strong>Confirm password</strong></label>
                                                <input type="password" class="form-control form-control-user" id="exampleRepeatPassword" placeholder="Repeat Password" required name="Repeatpassword" value="<?php echo $_SESSION['password']; ?>">
                                            </div>
                                        </div>

                                        <?php
                                        if ($resetError == 1) {
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
                                                <center>The username is already taken</center>
                                            </div>
                                        <?php
                                        }
                                        ?>

                                        <button class="btn btn-primary btn-user btn-block" name="Update">
                                            Update user details
                                        </button>
                                        <hr>
                                    </form>



                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Color System -->

                </div>




            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->

        <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>


    <!-- Bootstrap core JavaScript-->
    <script src="vendor_sb2/jquery/jquery.min.js"></script>
    <script src="vendor_sb2/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor_sb2/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js_sb2/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor_sb2/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js_sb2/demo/chart-area-demo.js"></script>
    <script src="js_sb2/demo/chart-pie-demo.js"></script>

</body>

</html>