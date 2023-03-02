<?php

session_start();
require_once('connection.php');
include_once('lib/Opinion.php');

if (!isset($_SESSION['username'])) {
    header("location:index.php");
}

$op = new Opinion();
$op->train('datasets/negative_tweets.txt', 'Negative');
$op->train('datasets/positive_tweets.txt', 'Positive');
$op->train('datasets/neutral_tweets.txt', 'Neutral');

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Twitter history</title>

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

                            <?php
                            if (!isset($_GET['content'])) {
                            ?>
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">View twitter contents</h6>
                                    </div>
                                    <div class="card-body">

                                        <div class="container-fluid">

                                            <!-- Page Heading -->

                                            <!-- DataTales Example -->


                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                        <col width="10">
                                                        <col width="20">
                                                        <col width="200">
                                                        <col width="200">
                                                        <col width="200">
                                                        <col width="20">
                                                        <thead>
                                                            <tr>
                                                                <th>User name</th>
                                                                <th>Searched content</th>
                                                                <th>Comment</th>
                                                                <th>Sentiment</th>
                                                                <th>Sentiment given by Bayes classifier</th>
                                                                <th>Author</th>
                                                                <th>Link</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <?php
                                                            $sql = "SELECT * FROM twitter";
                                                            $run = mysqli_query($conn, $sql);

                                                            foreach ($run as $row) {
                                                            ?>
                                                                <tr style="background:<?php if (strcmp($row['twitter_sentiment'], $op->classify($row['searched_content'])) != 0) echo '#fad85f'; ?>;">
                                                                    <td><?php echo $row['username']; ?></td>
                                                                    <td><?php echo $row['searched_content']; ?></td>
                                                                    <td><?php echo $row['twitter_comment'] ?></td>
                                                                    <td><?php echo $row['twitter_sentiment'] ?></td>
                                                                    <td><?php echo $op->classify($row['searched_content']) ?></td>
                                                                    <td><?php echo $row['twitter_author'] ?></td>
                                                                    <td><a href="<?php echo $row['twitter_link'] ?>"><button class="btn btn-primary btn-icon-split mx-2">
                                                                                <span class="icon text-white-50">
                                                                                    <i class="fas fa-link"></i>
                                                                                </span>
                                                                                
                                                                            </button></a></td>

                                                                </tr>

                                                            <?php
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>



                                    </div>
                                </div>

                            <?php
                            } else {
                            ?>

                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">View twitter contents for <?php echo $_GET['content']; ?></h6>
                                    </div>
                                    <div class="card-body">

                                        <div class="container-fluid">

                                            <!-- Page Heading -->

                                            <!-- DataTales Example -->


                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                        <col width="10">
                                                        <col width="20">
                                                        <col width="100">
                                                        <col width="10">
                                                        <col width="10">
                                                        <col width="10">
                                                        <thead>
                                                            <tr>
                                                                <th>User name</th>
                                                                <th>Searched content</th>
                                                                <th>Comment</th>
                                                                <th>Sentiment</th>
                                                                <th>Sentiment given by Bayes classifier</th>
                                                                <th>Author</th>
                                                                <th>Link</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <?php
                                                            $sql = "SELECT * FROM twitter WHERE searched_content = '$_GET[content]'";
                                                            $run = mysqli_query($conn, $sql);

                                                            foreach ($run as $row) {
                                                            ?>
                                                                <tr style="background:<?php if (strcmp($row['twitter_sentiment'], $op->classify($row['searched_content'])) != 0) echo '#fad85f'; ?>;">
                                                                    <td><?php echo $row['username']; ?></td>
                                                                    <td><?php echo $row['searched_content']; ?></td>
                                                                    <td><?php echo $row['twitter_comment'] ?></td>
                                                                    <td><?php echo $row['twitter_sentiment'] ?></td>
                                                                    <td><?php echo $op->classify($row['searched_content']) ?></td>
                                                                    <td><?php echo $row['twitter_author'] ?></td>
                                                                    <td><a href="<?php echo $row['twitter_link'] ?>"><button class="btn btn-primary btn-icon-split mx-2">
                                                                                <span class="icon text-white-50">
                                                                                    <i class="fas fa-link"></i>
                                                                                </span>
                                                                               
                                                                            </button></a></td>

                                                                </tr>

                                                            <?php
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>



                                    </div>
                                </div>

                            <?php
                            }
                            ?>

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