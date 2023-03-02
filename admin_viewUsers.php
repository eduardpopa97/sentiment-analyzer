<?php

session_start();
require_once('connection.php');

if (!isset($_SESSION['username'])) {
    header("location:index.php");
}

if ($_SESSION['usertype'] != "admin") {
    header("location:user_page_error.php");
    exit;
}

if(isset($_POST['upgrade_btn']))
{
    $user_id = $_POST['upgrade_id'];
    $sql = "UPDATE user SET usertype = 'admin' WHERE userID = '$user_id'";
    $sql_run = mysqli_query($conn, $sql);
}

if(isset($_POST['downgrade_btn']))
{
    $user_id = $_POST['downgrade_id'];
    $sql = "UPDATE user SET usertype = 'user' WHERE userID = '$user_id'";
    $sql_run = mysqli_query($conn, $sql);
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

    <title>View users</title>

    <!-- Custom fonts for this template-->
    <link href="vendor_sb2/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css_sb2/sb-admin-2.min.css" rel="stylesheet">

    <link href="vendor_sb2/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css_sb2/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor_sb2/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php include "admin_sidebar.php"; ?>
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
                                    <h6 class="m-0 font-weight-bold text-primary">View users</h6>
                                </div>
                                <div class="card-body">



                                    <div class="container-fluid">

                                        <!-- Page Heading -->

                                        <!-- DataTales Example -->


                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <?php
                                                $query = "SELECT * FROM user";
                                                $result = mysqli_query($conn, $query);
                                                ?>
                                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">

                                                    <thead>
                                                        <tr>
                                                            <th>User ID</th>
                                                            <th>User Name</th>
                                                            <th>Email</th>
                                                            <th>User Type</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php
                                                        if (mysqli_num_rows($result) > 0) {
                                                            while ($row = mysqli_fetch_assoc($result)) {
                                                        ?>
                                                                <tr>
                                                                    <td><?php echo $row['userID']; ?></td>
                                                                    <td><?php echo $row['username']; ?></td>
                                                                    <td><?php echo $row['email']; ?></td>
                                                                    <td><?php echo $row['usertype']; ?></td>
                                                                    <td>
                                                                        <?php
                                                                        if ($row['usertype'] == "user") {
                                                                        ?>
                                                                        <center>
                                                                            <a href="#adminModal<?php echo $row['userID']; ?>" data-toggle="modal" style="text-decoration: none;">
                                                                                <button class="btn btn-primary btn-icon-split mx-2">
                                                                                    <span class="icon text-white-50">
                                                                                        <i class="fas fa-user-plus"></i>
                                                                                    </span>
                                                                                    <span class="text">Upgrade to Admin</span>
                                                                                </button>
                                                                            </a>
                                                                        </center>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                        <?php
                                                                        if ($row['usertype'] == "admin" && $row['username'] != $_SESSION['username']) {
                                                                        ?>
                                                                        <center>
                                                                            <a href="#userModal<?php echo $row['userID']; ?>" data-toggle="modal" style="text-decoration: none;">
                                                                                <button class="btn btn-warning btn-icon-split mx-2">
                                                                                    <span class="icon text-white-50">
                                                                                        <i class="fas fa-user-times"></i>
                                                                                    </span>
                                                                                    <span class="text">Downgrade to User</span>
                                                                                </button>
                                                                            </a>
                                                                        </center>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                        <?php
                                                                        if ($row['usertype'] == "admin" && $row['username'] == $_SESSION['username']) {
                                                                        ?>

                                                                            <center><strong><span class="text">No action available</span></strong></center>

                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </td>

                                                                     <!-- Upgrade to Admin Modal-->
                                                                    <div class="modal fade" id="adminModal<?php echo $row['userID']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="exampleModalLabel">Upgrade user to admin</h5>
                                                                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">Select "Upgrade to admin" below if you are sure to make the user <strong><?php echo $row['username'] ?></strong> an admin.</div>
                                                                                <div class="modal-footer">
                                                                                    <form action="admin_viewUsers.php" method="POST">
                                                                                        <input type="hidden" name="upgrade_id" value="<?php echo $row['userID'] ?>">
                                                                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                                                                        <button class="btn btn-primary" type="submit" name="upgrade_btn">Upgrade to admin</button>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- End of Upgrade to Admin Modal -->

                                                                     <!-- Downgrade to User Modal-->
                                                                     <div class="modal fade" id="userModal<?php echo $row['userID']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="exampleModalLabel">Downgrade user</h5>
                                                                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">Select "Downgrade to user" below if you are sure to downgrade the user <strong><?php echo $row['username'] ?></strong>.</div>
                                                                                <div class="modal-footer">
                                                                                    <form action="admin_viewUsers.php" method="POST">
                                                                                        <input type="hidden" name="downgrade_id" value="<?php echo $row['userID'] ?>">
                                                                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                                                                        <button class="btn btn-danger" type="submit" name="downgrade_btn">Downgrade to user</button>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- End of Downgrade to User Modal -->

                                                                </tr>



                                                        <?php


                                                            }
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>




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



    <!-- Core plugin JavaScript-->


    <!-- Custom scripts for all pages-->


    <!-- Bootstrap core JavaScript-->

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


    <script src="vendor_sb2/jquery/jquery.min.js"></script>
    <script src="vendor_sb2/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor_sb2/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js_sb2/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor_sb2/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor_sb2/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js_sb2/demo/datatables-demo.js"></script>


</body>

</html>