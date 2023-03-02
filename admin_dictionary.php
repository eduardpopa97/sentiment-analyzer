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

if (isset($_POST['Add'])) {
    $add_term = $_POST['new_term'];
    $add_class = $_POST['new_class'];
    $add_score = $_POST['new_score'];

    $check_sql = "SELECT * FROM dictionary WHERE term='$add_term'";
    $check_result = mysqli_query($conn, $check_sql);
    if (mysqli_num_rows($check_result) > 0) {
        echo '<script>alert("This term already exists in dictionary")</script>';
    } else {
        $query = "INSERT INTO dictionary (term,class,score) VALUES ('$add_term','$add_class','$add_score')";
        $result = mysqli_query($conn, $query);
        header("location:admin_dictionary.php");
    }
}

if (isset($_POST['delete_btn'])) {
    $deleteID = $_POST['delete_id'];

    $sql = "DELETE FROM dictionary WHERE termID = '$deleteID'";
    $result = mysqli_query($conn, $sql);
}

if (isset($_POST['edit_btn'])) {
    $updated_id = $_POST['new_id'];
    $updated_term = $_POST['new_term'];
    $updated_class = $_POST['new_class'];
    $updated_score = $_POST['new_score'];

    $check_sql = "SELECT * FROM dictionary WHERE term='$updated_term'";
    $check_result = mysqli_query($conn, $check_sql);
    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>
                    alert('This term already exists in dictionary');
                    window.location.href='admin_dictionary.php';
              </script>";
    }
    else
    {
        $sql = "UPDATE dictionary SET term='$updated_term', class='$updated_class', score='$updated_score' 
        WHERE termID='$updated_id'";
        $sql_run = mysqli_query($conn, $sql);

        if ($sql_run) {
            echo '<script>alert("The term has been updated")</script>';
            header("location:admin_dictionary.php");
        } else {
            echo '<script>alert("Failed to update the dictionary")</script>';
            header("location:admin_dictionary.php");
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

    <title>Dictionary management</title>

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
                                    <h6 class="m-0 font-weight-bold text-primary">Add a new term</h6>
                                </div>
                                <div class="card-body">

                                    <form class="user" method="POST" action="admin_dictionary.php">

                                        <div class="form-group row">
                                            <div class="col-sm-3 mb-3 mb-sm-0">
                                                <label for="term">New Term</label>
                                                <input type="text" class="form-control form-control-user1" id="exampleInputPassword" placeholder="Fill the form with a word" required name="new_term">
                                            </div>
                                            <div class="col-sm-3 mb-3 mb-sm-0">
                                                <label for="class">Class</label>
                                                <select class="form-control" id="exampleFormControlSelect" name="new_class">
                                                    <option>Positive</option>
                                                    <option>Negative</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3 mb-3 mb-sm-0">
                                                <label for="score">Score</label>
                                                <select class="form-control" id="exampleFormControlSelect" name="new_score">
                                                    <option>1</option>
                                                    <option>2</option>
                                                    <option>3</option>
                                                    <option>4</option>
                                                    <option>5</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-3 mb-3 mb-sm-0">
                                                <label></label>
                                                <label></label>
                                                <button class="btn btn-primary btn-user btn-block" name="Add">
                                                    Add
                                                </button>
                                            </div>
                                        </div>


                                    </form>



                                </div>
                            </div>

                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Visualise dictionary</h6>
                                </div>
                                <div class="card-body">



                                    <div class="container-fluid">

                                        <!-- Page Heading -->

                                        <!-- DataTales Example -->


                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <?php
                                                $query = "SELECT * FROM dictionary";
                                                $result = mysqli_query($conn, $query);
                                                ?>
                                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                    <col width="140">
                                                    <col width="100">
                                                    <col width="100">
                                                    <col width="140">

                                                    <thead>
                                                        <tr>
                                                            <th>Term's content</th>
                                                            <th>Class</th>
                                                            <th>Score</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <?php
                                                        if (mysqli_num_rows($result) > 0) {
                                                            while ($row = mysqli_fetch_assoc($result)) {
                                                        ?>
                                                                <tr>
                                                                    <td><?php echo $row['term']; ?></td>
                                                                    <td><?php echo $row['class']; ?></td>
                                                                    <td><?php echo $row['score']; ?></td>
                                                                    <td class="action-column">
                                                                        


                                                                            <a href="#editModal<?php echo $row['termID']; ?>" data-toggle="modal" style="text-decoration: none;">
                                                                                <button class="btn btn-warning btn-icon-split mx-2">
                                                                                    <span class="icon text-white-50">
                                                                                        <i class="fas fa-pencil-alt"></i>
                                                                                    </span>
                                                                                    <span class="text">Edit</span>
                                                                                </button>
                                                                            </a>

                                                                            <!-- <button class="btn btn-warning btn-icon-split mx-2" name="edit_btn">
                                                                                <span class="icon text-white-50">
                                                                                    <i class="fas fa-pencil-alt"></i>
                                                                                </span>
                                                                                <span class="text">Edit</span>
                                                                            </button> -->


                                                                            <!-- <form action="admin_dictionary.php" method="POST">
                                                                            <input type="hidden" name="delete_id" value="<?php echo $row['termID'] ?>">
                                                                            <button class="btn btn-danger btn-icon-split mx-2" name="delete_btn">
                                                                                <span class="icon text-white-50">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </span>
                                                                                <span class="text">Delete</span>
                                                                            </button>

                                                                        </form> -->

                                                                            <a href="#deleteModal<?php echo $row['termID']; ?>" data-toggle="modal" style="text-decoration: none;">
                                                                                <button class="btn btn-danger btn-icon-split mx-2">
                                                                                    <span class="icon text-white-50">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </span>
                                                                                    <span class="text">Delete</span>
                                                                                </button>
                                                                            </a>

                                                                        
                                                                    </td>

                                                                    <!-- Delete Modal-->
                                                                    <div class="modal fade" id="deleteModal<?php echo $row['termID']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="exampleModalLabel">Ready to Delete?</h5>
                                                                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">Select "Delete term" below if you are sure to delete the term <strong><?php echo $row['term'] ?></strong> from dictionary.</div>
                                                                                <div class="modal-footer">
                                                                                    <form action="admin_dictionary.php" method="POST">
                                                                                        <input type="hidden" name="delete_id" value="<?php echo $row['termID'] ?>">
                                                                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                                                                        <button class="btn btn-danger" type="submit" name="delete_btn">Delete term</button>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- End of Delete Modal -->


                                                                    <!-- Edit Modal-->
                                                                    <div class="modal fade" id="editModal<?php echo $row['termID']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title" id="exampleModalLabel">Ready to Update?</h5>
                                                                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">Select "Edit term" below if you are sure to modify the term from dictionary.
                                                                                <hr>    
                                                                                   
                                                                                    <?php

                                                                                    $id = $row['termID'];
                                                                                    $query = "SELECT * FROM dictionary WHERE termID = '$id'";
                                                                                    $query_run = mysqli_query($conn, $query);

                                                                                    foreach ($query_run as $row) {

                                                                                    ?>

                                                                                        <form class="user" method="POST" action="admin_dictionary.php">

                                                                                            <input type="hidden" class="form-control form-control-user" id="exampleFirstName" placeholder="User Name" name="new_id" value="<?php echo $row['termID']; ?>">

                                                                                            
                                                                                            <div class="form-group">
                                                                                                <label for="term">Term</label>
                                                                                                <input type="text" class="form-control form-control-user" id="exampleFirstName" placeholder="User Name" name="new_term" value="<?php echo $row['term']; ?>">
                                                                                            </div>
                                                                                            <div class="form-group row">
                                                                                                <div class="col-sm-6 mb-3 mb-sm-0">
                                                                                                    <label for="class">Class</label>
                                                                                                    <select class="form-control" id="exampleFormControlSelect" name="new_class">
                                                                                                        <option value="" selected disabled hidden><?php echo $row['class']; ?></option>
                                                                                                        <option>Positive</option>
                                                                                                        <option>Negative</option>
                                                                                                    </select>
                                                                                                </div>

                                                                                                <div class="col-sm-6 mb-3 mb-sm-0">
                                                                                                <label for="score">Score</label>
                                                                                                    <select class="form-control" id="exampleFormControlSelect" name="new_score">
                                                                                                        <option value="" selected disabled hidden><?php echo $row['score']; ?></option>
                                                                                                        <option>1</option>
                                                                                                        <option>2</option>
                                                                                                        <option>3</option>
                                                                                                        <option>4</option>
                                                                                                        <option>5</option>
                                                                                                    </select>
                                                                                                </div>
                                                                                            </div>

                                              
                                                                                            <div class="modal-footer">

                                                                                                <input type="hidden" name="update_id" value="<?php echo $row['termID'] ?>">
                                                                                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                                                                                <button class="btn btn-warning" type="submit" name="edit_btn">Edit term</button>

                                                                                            </div>


                                                                                        </form>

                                                                                    <?php

                                                                                    }
                                                                                    ?>
                                                                                </div>
                                                                                <!-- <div class="modal-footer">
                                                                                    <form action="admin_dictionary.php" method="POST">
                                                                                        <input type="hidden" name="update_id" value="<?php echo $row['termID'] ?>">
                                                                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                                                                        <button class="btn btn-warning" type="submit" name="edit_btn">Edit term</button>
                                                                                    </form>
                                                                                </div> -->
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- End of Edit Modal -->

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