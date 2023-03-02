<?php

session_start();
require_once('connection.php');

if (!isset($_SESSION['username'])) {
    header("location:index.php");
}

if (isset($_POST['Submit'])) {
    $u_name = $_SESSION['username'];
    $sql_check = "SELECT * FROM feedback WHERE username = '$u_name'";
    $run_check = mysqli_query($conn,$sql_check);

    $stars = 0;
    $category = "";
    if (isset($_POST['rating'])) $stars = $_POST['rating'];

    if ($_POST['options'] == "Suggestion") $category = "Suggestion";
    if ($_POST['options'] == "Something is not right") $category = "Something is not right";
    if ($_POST['options'] == "Compliment") $category = "Compliment";


    $feedback = $_POST['feedback'];

    $text = addslashes(str_replace('"', '“', $feedback));
    $split = preg_split("/[^\w]*([\s]+[^\w]*|$)/", $text, -1, PREG_SPLIT_NO_EMPTY);

    $filterExisting = "'" . implode("', '", $split) . "'";
    $sql = "SELECT * FROM dictionary WHERE term IN (" . $filterExisting . ") ";
    $result = mysqli_query($conn, $sql);
    $score = 0;
    $nr_terms = 0;
    $percentage_positive = 0;
    $percentage_negative = 0;

    $db_words = array();
    $word_list = array_count_values($split);
    $key_array = array();
    $value_array = array();

    krsort($word_list);

    $counter = -1;
    foreach ($word_list as $key => $value) {
        array_push($key_array, $key);
        array_push($value_array, $value);
        $counter = $counter + 1;
    }

    $sql1 = "SELECT * FROM dictionary WHERE term IN (" . $filterExisting . ") ";
    $result1 = mysqli_query($conn, $sql1);
    while ($row = mysqli_fetch_assoc($result1)) {
        array_push($db_words, $row['term']);
    }

    $common_terms = array_intersect($key_array, $db_words);
    sort($common_terms);

    $table_content = array();
    $table_value = array();
    $table_class = array();
    $table_score = array();

    for ($i = 0; $i < count($common_terms); $i++) {
        for ($j = 0; $j <= $counter; $j++) {
            if (strcmp($key_array[$j], $common_terms[$i]) === 0) {
                array_push($table_content, $key_array[$j]);
                array_push($table_value, $value_array[$j]);
                $sql2 = "SELECT * FROM dictionary WHERE term = '$key_array[$j]' ";
                $result2 = mysqli_query($conn, $sql2);
                while ($row2 = mysqli_fetch_assoc($result2)) {
                    array_push($table_class, $row2['class']);
                    array_push($table_score, $row2['score']);
                }
            }
        }
    }

    for ($i = 0; $i < count($common_terms); $i++) {
        if ($table_class[$i] == 'Positive') {
            $score = $score + $table_score[$i] * $table_value[$i];
        } else {
            $score = $score - $table_score[$i] * $table_value[$i];
        }
        $nr_terms = $nr_terms + $table_value[$i];
    }

    if ($score > 0) {
        $sentiment = "Positive";
    } else {
        if ($score < 0) {
            $sentiment = "Negative";
        } else {
            $sentiment = "Neutral";
        }
    }
    $date = date("Y/m/d");

    if(mysqli_num_rows($run_check) == 0)
    {
    $sql = "INSERT INTO feedback (username, feedbackStars, feedbackCategory, feedbackContent, feedbackSentiment, date) VALUES ('$u_name','$stars','$category','$text','$sentiment','$date')";
    $query = mysqli_query($conn, $sql);
    }
    else
    {
    $sql = "UPDATE feedback SET feedbackStars = '$stars', feedbackCategory = '$category', feedbackContent = '$text', feedbackSentiment = '$sentiment', date = '$date' WHERE username = '$u_name'";
    $query = mysqli_query($conn, $sql);   
    }
}

?>

<style>
    .rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center
    }

    .rating>input {
        display: none
    }

    .rating>label {
        position: relative;
        width: 1em;
        font-size: 3vw;
        color: #FFD600;
        cursor: pointer
    }

    .rating>label::before {
        content: "\2605";
        position: absolute;
        opacity: 0
    }

    .rating>label:hover:before,
    .rating>label:hover~label:before {
        opacity: 1 !important
    }

    .rating>input:checked~label:before {
        opacity: 1
    }

    .rating:hover>input:checked~label:before {
        opacity: 0.4
    }

    body {
        background: #222225;
        color: white
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

    <title>Give us a feedback</title>

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
                                    <h6 class="m-0 font-weight-bold text-primary">Send feedback</h6>
                                </div>
                                <div class="card-body">

                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <center>We would like your feedback to improve our site<center>
                                    </h6>
                                    <br>
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <center>What is your opinion of this website?<center>
                                    </h6>
                                    <hr>
                                    <form action="user_feedback.php" method="POST">
                                        <div class="rating">
                                            <input type="radio" name="rating" value="5" id="5"><label for="5">☆</label>
                                            <input type="radio" name="rating" value="4" id="4"><label for="4">☆</label>
                                            <input type="radio" name="rating" value="3" id="3"><label for="3">☆</label>
                                            <input type="radio" name="rating" value="2" id="2"><label for="2">☆</label>
                                            <input type="radio" name="rating" value="1" id="1" required><label for="1">☆</label>
                                        </div>
                                        <hr>
                                        <h6 class="m-0 font-weight-bold text-primary">Please select your feedback category below</h6>
                                        <br>

                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                            <label class="btn btn-primary">
                                                <input type="radio" name="options" id="option1" checked="" value="Suggestion">Suggestion
                                            </label>
                                            <label class="btn btn-primary">
                                                <input type="radio" name="options" id="option2" value="Something is not right">Something is not right
                                            </label>
                                            <label class="btn btn-primary active">
                                                <input type="radio" name="options" id="option3" value="Compliment">Compliment
                                            </label>
                                        </div>

                                        <br>
                                        <hr>
                                        <h6 class="m-0 font-weight-bold text-primary">Please leave your feedback below</h6>
                                        <br>
                                        <div class="form-group">
                                            <textarea required class="form-control" id="exampleFormControlTextarea1" name="feedback" rows="6" placeholder="Write your feedback here"></textarea>
                                        </div>
                                        <br>


                                        <button class="btn btn-success btn-icon-split mx-2" name="Submit" style="float: right;">
                                            <span class="icon text-white-50">
                                                <i class="fas fa-thumbs-up"></i>
                                                <i class="fas fa-thumbs-down"></i>
                                            </span>
                                            <span class="text">Submit your feedback</span>
                                        </button>
                                    </form>

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