<?php

session_start();
require_once('connection.php');
include_once('lib/Opinion.php');
include_once('../SentimentAnalyzer/simplehtmldom_1_9_1/simple_html_dom.php');

if (!isset($_SESSION['username'])) {
    header("location:index.php");
}

if ($_SESSION['usertype'] != "admin") {
    header("location:user_page_error.php");
    exit;
}

if (isset($_POST['delete_btn'])) {
    $deleteID = $_POST['delete_id'];

    $sql = "DELETE FROM analysishistory WHERE historyID = '$deleteID'";
    $result = mysqli_query($conn, $sql);
}

$op = new Opinion();
$op->train('datasets/negative_tweets.txt', 'Negative');
$op->train('datasets/positive_tweets.txt', 'Positive');
$op->train('datasets/neutral_tweets.txt', 'Neutral');

?>

<style>
    .demo {
        width: 140px;
        text-overflow: ellipsis;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
    }

    .demo:hover {
        overflow: visible;
        white-space: normal;
    }
</style>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Comments history</title>

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
                            if (!isset($_GET['comments'])) {
                            ?>
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Comments analysis history</h6>
                                    </div>
                                    <div class="card-body">



                                        <div class="container-fluid">

                                            <!-- Page Heading -->

                                            <!-- DataTales Example -->


                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <?php
                                                    $sql = "SELECT * FROM analysishistory";
                                                    $run = mysqli_query($conn, $sql);
                                                    ?>
                                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                        <col width="220">
                                                        <col width="120">
                                                        <col width="120">
                                                        <col width="120">
                                                        <col width="120">
                                                        <col width="120">
                                                        <col width="120">
                                                        <thead>
                                                            <tr>
                                                                <th>Comment</th>
                                                                <th>Sentiment</th>
                                                                <th>Sentiment given by Bayes classifier</th>
                                                                <th>Score</th>
                                                                <th>User</th>
                                                                <th>Comment origin</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody>
                                                            <?php

                                                            if (mysqli_num_rows($run) > 0) {
                                                                while ($row = mysqli_fetch_assoc($run)) {
                                                            ?>
                                                                    <tr style="background:<?php 
                                                                    if($row['contentOrigin'] != "link" && strcmp($row['sentiment'],$op->classify($row['content'])) != 0)
                                                                        echo '#fad85f';
                                                                    else
                                                                    { 
                                                                        if($row['contentOrigin'] == "link" && strcmp($row['sentiment'],$op->classify(addslashes(str_replace('"', '“', file_get_html($row['content'])->plaintext)))) != 0) 
                                                                        echo '#fad85f'; 
                                                                    } ?>
                                                                    ;">
                                                                        <td>
                                                                            <div class="demo">
                                                                                <?php echo $row['content'] ?>
                                                                            </div>
                                                                        </td>
                                                                        <td><?php echo $row['sentiment'] ?></td>
                                                                        <td><?php if($row['contentOrigin'] != "link") echo $op->classify($row['content']);
                                                                        else echo $op->classify(addslashes(str_replace('"', '“', file_get_html($row['content'])->plaintext)));?></td>
                                                                        <td><?php echo round($row['score'], 2) ?></td>
                                                                        <td><?php echo $row['username'] ?></td>
                                                                        <td><?php echo $row['contentOrigin'] ?></td>
                                                                        <td>
                                                                            <!-- <form action="admin_comments.php" method="POST">
                                                                            <input type="hidden" name="delete_id" value="<?php echo $row['historyID'] ?>">
                                                                            <button class="btn btn-danger btn-icon-split mx-2" name="delete_btn">
                                                                                <span class="icon text-white-50">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </span>
                                                                                <span class="text">Delete</span>
                                                                            </button>
                                                                        </form>
                                                                        <br> -->
                                                                            <form action="admin_comments_result.php" method="POST">
                                                                                <input type="hidden" name="rerun_analysis" value="<?php echo $row['content'] ?>">
                                                                                <button class="btn btn-success btn-icon-split mx-2" name="rerun">
                                                                                    <span class="icon text-white-50">
                                                                                        <i class="fas fa-sync"></i>
                                                                                    </span>
                                                                                    <span class="text">Rerun</span>
                                                                                </button>
                                                                            </form>
                                                                            <p>
                                                                                <a href="#deleteModal<?php echo $row['historyID']; ?>" data-toggle="modal" style="text-decoration: none;">
                                                                                    <button class="btn btn-danger btn-icon-split mx-2">
                                                                                        <span class="icon text-white-50">
                                                                                            <i class="fas fa-trash"></i>
                                                                                        </span>
                                                                                        <span class="text">Delete</span>
                                                                                    </button>
                                                                                </a>
                                                                        </td>

                                                                        <!-- Delete Modal-->
                                                                        <div class="modal fade" id="deleteModal<?php echo $row['historyID']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog" role="document">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title" id="exampleModalLabel">Ready to Delete?</h5>
                                                                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                                            <span aria-hidden="true">×</span>
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">Select "Delete comment" below if you are sure to delete the comment.</div>
                                                                                    <div class="modal-footer">
                                                                                        <form action="admin_comments.php" method="POST">
                                                                                            <input type="hidden" name="delete_id" value="<?php echo $row['historyID'] ?>">
                                                                                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                                                                            <button class="btn btn-danger" type="submit" name="delete_btn">Delete comment</button>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

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
                            <?php
                            }
                            ?>

                            <?php if (isset($_GET['comments'])) 
                            {
                            ?>
                                <?php
                                if ($_GET['comments'] == "positive") {
                                ?>
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Positive comments analysis history</h6>
                                        </div>
                                        <div class="card-body">



                                            <div class="container-fluid">

                                                <!-- Page Heading -->

                                                <!-- DataTales Example -->


                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <?php
                                                        $sql = "SELECT * FROM analysishistory WHERE sentiment='positive'";
                                                        $run = mysqli_query($conn, $sql);
                                                        ?>
                                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                            <col width="22120">
                                                            <col width="120">
                                                            <col width="120">
                                                            <col width="120">
                                                            <col width="120">
                                                            <col width="120">
                                                            <col width="120">
                                                            <thead>
                                                                <tr>
                                                                    <th>Comment</th>
                                                                    <th>Sentiment</th>
                                                                    <th>Sentiment given by Bayes classifier</th>
                                                                    <th>Score</th>
                                                                    <th>User</th>
                                                                    <th>Comment origin</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <?php

                                                                if (mysqli_num_rows($run) > 0) {
                                                                    while ($row = mysqli_fetch_assoc($run)) {
                                                                ?>
                                                                    <tr style="background:<?php 
                                                                    if($row['contentOrigin'] != "link" && strcmp($row['sentiment'],$op->classify($row['content'])) != 0)
                                                                        echo '#fad85f';
                                                                    else
                                                                    { 
                                                                        if($row['contentOrigin'] == "link" && strcmp($row['sentiment'],$op->classify(addslashes(str_replace('"', '“', file_get_html($row['content'])->plaintext)))) != 0) 
                                                                        echo '#fad85f'; } ?>
                                                                    ;">
                                                                            <td>
                                                                                <div class="demo">
                                                                                    <?php echo $row['content'] ?>
                                                                                </div>
                                                                            </td>
                                                                            <td><?php echo $row['sentiment'] ?></td>
                                                                            <td><?php if($row['contentOrigin'] != "link") echo $op->classify($row['content']);
                                                                            else echo $op->classify(addslashes(str_replace('"', '“', file_get_html($row['content'])->plaintext)));?></td>
                                                                            <td><?php echo round($row['score'], 2) ?></td>
                                                                            <td><?php echo $row['username'] ?></td>
                                                                            <td><?php echo $row['contentOrigin'] ?></td>
                                                                            <td>
                                                                                <!-- <form action="admin_comments.php" method="POST">
                                                                            <input type="hidden" name="delete_id" value="<?php echo $row['historyID'] ?>">
                                                                            <button class="btn btn-danger btn-icon-split mx-2" name="delete_btn">
                                                                                <span class="icon text-white-50">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </span>
                                                                                <span class="text">Delete</span>
                                                                            </button>
                                                                        </form>
                                                                        <br> -->
                                                                                <form action="admin_comments_result.php" method="POST">
                                                                                    <input type="hidden" name="rerun_analysis" value="<?php echo $row['content'] ?>">
                                                                                    <button class="btn btn-success btn-icon-split mx-2" name="rerun">
                                                                                        <span class="icon text-white-50">
                                                                                            <i class="fas fa-sync"></i>
                                                                                        </span>
                                                                                        <span class="text">Rerun</span>
                                                                                    </button>
                                                                                </form>
                                                                                <p>
                                                                                    <a href="#deleteModal<?php echo $row['historyID']; ?>" data-toggle="modal" style="text-decoration: none;">
                                                                                        <button class="btn btn-danger btn-icon-split mx-2">
                                                                                            <span class="icon text-white-50">
                                                                                                <i class="fas fa-trash"></i>
                                                                                            </span>
                                                                                            <span class="text">Delete</span>
                                                                                        </button>
                                                                                    </a>
                                                                            </td>

                                                                            <!-- Delete Modal-->
                                                                            <div class="modal fade" id="deleteModal<?php echo $row['historyID']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                                <div class="modal-dialog" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title" id="exampleModalLabel">Ready to Delete?</h5>
                                                                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                                                <span aria-hidden="true">×</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        <div class="modal-body">Select "Delete comment" below if you are sure to delete the comment.</div>
                                                                                        <div class="modal-footer">
                                                                                            <form action="admin_comments.php" method="POST">
                                                                                                <input type="hidden" name="delete_id" value="<?php echo $row['historyID'] ?>">
                                                                                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                                                                                <button class="btn btn-danger" type="submit" name="delete_btn">Delete comment</button>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

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
                                <?php
                                }
                                ?>








                                <?php
                                if ($_GET['comments'] == "neutral") {
                                ?>
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Neutral comments analysis history</h6>
                                        </div>
                                        <div class="card-body">



                                            <div class="container-fluid">

                                                <!-- Page Heading -->

                                                <!-- DataTales Example -->


                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <?php
                                                        $sql = "SELECT * FROM analysishistory WHERE sentiment='neutral'";
                                                        $run = mysqli_query($conn, $sql);
                                                        ?>
                                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                            <col width="22120">
                                                            <col width="120">
                                                            <col width="120">
                                                            <col width="120">
                                                            <col width="120">
                                                            <col width="120">
                                                            <col width="120">
                                                            <thead>
                                                                <tr>
                                                                    <th>Comment</th>
                                                                    <th>Sentiment</th>
                                                                    <th>Sentiment given by Bayes classifier</th>
                                                                    <th>Score</th>
                                                                    <th>User</th>
                                                                    <th>Comment origin</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <?php

                                                                if (mysqli_num_rows($run) > 0) {
                                                                    while ($row = mysqli_fetch_assoc($run)) {
                                                                ?>
                                                                    <tr style="background:<?php 
                                                                    if($row['contentOrigin'] != "link" && strcmp($row['sentiment'],$op->classify($row['content'])) != 0)
                                                                        echo '#fad85f';
                                                                    else
                                                                    { 
                                                                        if($row['contentOrigin'] == "link" && strcmp($row['sentiment'],$op->classify(addslashes(str_replace('"', '“', file_get_html($row['content'])->plaintext)))) != 0) 
                                                                        echo '#fad85f'; } ?>
                                                                    ;">
                                                                            <td>
                                                                                <div class="demo">
                                                                                    <?php echo $row['content'] ?>
                                                                                </div>
                                                                            </td>
                                                                            <td><?php echo $row['sentiment'] ?></td>
                                                                            <td><?php if($row['contentOrigin'] != "link") echo $op->classify($row['content']);
                                                                            else echo $op->classify(addslashes(str_replace('"', '“', file_get_html($row['content'])->plaintext)));?></td>
                                                                            <td><?php echo round($row['score'], 2) ?></td>
                                                                            <td><?php echo $row['username'] ?></td>
                                                                            <td><?php echo $row['contentOrigin'] ?></td>
                                                                            <td>
                                                                                <!-- <form action="admin_comments.php" method="POST">
                                                                            <input type="hidden" name="delete_id" value="<?php echo $row['historyID'] ?>">
                                                                            <button class="btn btn-danger btn-icon-split mx-2" name="delete_btn">
                                                                                <span class="icon text-white-50">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </span>
                                                                                <span class="text">Delete</span>
                                                                            </button>
                                                                        </form>
                                                                        <br> -->
                                                                                <form action="admin_comments_result.php" method="POST">
                                                                                    <input type="hidden" name="rerun_analysis" value="<?php echo $row['content'] ?>">
                                                                                    <button class="btn btn-success btn-icon-split mx-2" name="rerun">
                                                                                        <span class="icon text-white-50">
                                                                                            <i class="fas fa-sync"></i>
                                                                                        </span>
                                                                                        <span class="text">Rerun</span>
                                                                                    </button>
                                                                                </form>
                                                                                <p>
                                                                                    <a href="#deleteModal<?php echo $row['historyID']; ?>" data-toggle="modal" style="text-decoration: none;">
                                                                                        <button class="btn btn-danger btn-icon-split mx-2">
                                                                                            <span class="icon text-white-50">
                                                                                                <i class="fas fa-trash"></i>
                                                                                            </span>
                                                                                            <span class="text">Delete</span>
                                                                                        </button>
                                                                                    </a>
                                                                            </td>

                                                                            <!-- Delete Modal-->
                                                                            <div class="modal fade" id="deleteModal<?php echo $row['historyID']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                                <div class="modal-dialog" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title" id="exampleModalLabel">Ready to Delete?</h5>
                                                                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                                                <span aria-hidden="true">×</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        <div class="modal-body">Select "Delete comment" below if you are sure to delete the comment.</div>
                                                                                        <div class="modal-footer">
                                                                                            <form action="admin_comments.php" method="POST">
                                                                                                <input type="hidden" name="delete_id" value="<?php echo $row['historyID'] ?>">
                                                                                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                                                                                <button class="btn btn-danger" type="submit" name="delete_btn">Delete comment</button>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

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
                                <?php
                                }
                                ?>




                                <?php
                                if ($_GET['comments'] == "negative") {
                                ?>
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Negative comments analysis history</h6>
                                        </div>
                                        <div class="card-body">



                                            <div class="container-fluid">

                                                <!-- Page Heading -->

                                                <!-- DataTales Example -->


                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <?php
                                                        $sql = "SELECT * FROM analysishistory WHERE sentiment='negative'";
                                                        $run = mysqli_query($conn, $sql);
                                                        ?>
                                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                            <col width="22120">
                                                            <col width="120">
                                                            <col width="120">
                                                            <col width="120">
                                                            <col width="120">
                                                            <col width="120">
                                                            <col width="120">
                                                            <thead>
                                                                <tr>
                                                                    <th>Comment</th>
                                                                    <th>Sentiment</th>
                                                                    <th>Sentiment given by Bayes classifier</th>
                                                                    <th>Score</th>
                                                                    <th>User</th>
                                                                    <th>Comment origin</th>
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody>
                                                                <?php

                                                                if (mysqli_num_rows($run) > 0) {
                                                                    while ($row = mysqli_fetch_assoc($run)) {
                                                                ?>
                                                                    <tr style="background:<?php 
                                                                    if($row['contentOrigin'] != "link" && strcmp($row['sentiment'],$op->classify($row['content'])) != 0)
                                                                        echo '#fad85f';
                                                                    else
                                                                    { 
                                                                        if($row['contentOrigin'] == "link" && strcmp($row['sentiment'],$op->classify(addslashes(str_replace('"', '“', file_get_html($row['content'])->plaintext)))) != 0) 
                                                                        echo '#fad85f'; } ?>
                                                                    ;">
                                                                            <td>
                                                                                <div class="demo">
                                                                                    <?php echo $row['content'] ?>
                                                                                </div>
                                                                            </td>
                                                                            <td><?php echo $row['sentiment'] ?></td>
                                                                            <td><?php if($row['contentOrigin'] != "link") echo $op->classify($row['content']);
                                                                            else echo $op->classify(addslashes(str_replace('"', '“', file_get_html($row['content'])->plaintext)));?></td>
                                                                            <td><?php echo round($row['score'], 2) ?></td>
                                                                            <td><?php echo $row['username'] ?></td>
                                                                            <td><?php echo $row['contentOrigin'] ?></td>
                                                                            <td>
                                                                                <!-- <form action="admin_comments.php" method="POST">
                                                                            <input type="hidden" name="delete_id" value="<?php echo $row['historyID'] ?>">
                                                                            <button class="btn btn-danger btn-icon-split mx-2" name="delete_btn">
                                                                                <span class="icon text-white-50">
                                                                                    <i class="fas fa-trash"></i>
                                                                                </span>
                                                                                <span class="text">Delete</span>
                                                                            </button>
                                                                        </form>
                                                                        <br> -->
                                                                                <form action="admin_comments_result.php" method="POST">
                                                                                    <input type="hidden" name="rerun_analysis" value="<?php echo $row['content'] ?>">
                                                                                    <button class="btn btn-success btn-icon-split mx-2" name="rerun">
                                                                                        <span class="icon text-white-50">
                                                                                            <i class="fas fa-sync"></i>
                                                                                        </span>
                                                                                        <span class="text">Rerun</span>
                                                                                    </button>
                                                                                </form>
                                                                                <p>
                                                                                    <a href="#deleteModal<?php echo $row['historyID']; ?>" data-toggle="modal" style="text-decoration: none;">
                                                                                        <button class="btn btn-danger btn-icon-split mx-2">
                                                                                            <span class="icon text-white-50">
                                                                                                <i class="fas fa-trash"></i>
                                                                                            </span>
                                                                                            <span class="text">Delete</span>
                                                                                        </button>
                                                                                    </a>
                                                                            </td>

                                                                            <!-- Delete Modal-->
                                                                            <div class="modal fade" id="deleteModal<?php echo $row['historyID']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                                <div class="modal-dialog" role="document">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h5 class="modal-title" id="exampleModalLabel">Ready to Delete?</h5>
                                                                                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                                                <span aria-hidden="true">×</span>
                                                                                            </button>
                                                                                        </div>
                                                                                        <div class="modal-body">Select "Delete comment" below if you are sure to delete the comment.</div>
                                                                                        <div class="modal-footer">
                                                                                            <form action="admin_comments.php" method="POST">
                                                                                                <input type="hidden" name="delete_id" value="<?php echo $row['historyID'] ?>">
                                                                                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                                                                                <button class="btn btn-danger" type="submit" name="delete_btn">Delete comment</button>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>

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
                                <?php
                                }
                                ?>


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