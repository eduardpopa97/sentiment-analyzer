<?php

session_start();
require_once('connection.php');
include_once('lib/Opinion.php');
include_once('lib/Category.php');

if (!isset($_SESSION['username'])) {
    header("location:index.php");
}


$bad_file_extension = 0;
$forceValue = 0;
$forceCategoryValue = 0;

$category = new Category();
$class = array("business", "entertainment", "politics", "sport", "tech");
$dir = "datasets/";
for ($i = 0; $i < count($class); $i++) {
    $a = scandir($dir . $class[$i]);
    for ($j = 2; $j < count($a); $j++) {
        $category->trainCategory($dir . $class[$i] . "/" . $a[$j], ucfirst($class[$i]));
    }
}

if (isset($_POST['Submit']) || isset($_POST['Submit_new_file'])) {
    if (file_exists($_FILES['file']['tmp_name'])) {

        $op = new Opinion();
        $op->train('datasets/negative_tweets.txt', 'Negative');
        $op->train('datasets/positive_tweets.txt', 'Positive');
        $op->train('datasets/neutral_tweets.txt', 'Neutral');


        $file = $_FILES['file'];
        $fileName = $file['name'];
        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));

        $allowed = array('txt');

        if (in_array($fileActualExt, $allowed)) {
            if ($fileActualExt == 'txt') {

                $text = addslashes(str_replace('"', '“', file_get_contents($file['tmp_name'])));
                $split = preg_split("/[^\w]*([\s]+[^\w]*|$)/", $text, -1, PREG_SPLIT_NO_EMPTY);
                //print_r($split);

                $sentimentBayes = $op->classify($text);
                $sentimentValues = $op->sentimentScores($text);
                if ($sentimentValues['Positive'] == 0 && $sentimentValues['Negative'] == 0 && $sentimentValues['Neutral'] == 0) $forceValue = 1;

                $identifiedCategory = $category->classifyText($text);
                $categoryValues = $category->categoryScores($text);
                if ($categoryValues['Politics'] == 0 && $categoryValues['Business'] == 0 && $categoryValues['Entertainment'] == 0 && $categoryValues['Sport'] == 0 && $categoryValues['Tech'] == 0) $forceCategoryValue = 1;

                $filterExisting = "'" . implode("', '", $split) . "'";
                $sql = "SELECT * FROM dictionary WHERE term IN (" . $filterExisting . ") ";
                $result = mysqli_query($conn, $sql);
                $score = 0;
                // $nr_terms = mysqli_num_rows($result);
                $nr_terms = 0;
                $percentage_positive = 0;
                $percentage_negative = 0;

                // //modificare
                $db_words = array();
                $word_list = array_count_values($split);
                $key_array = array();
                $value_array = array();

                krsort($word_list);
                //  print_r($word_list);

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
                // $length = count($common_terms);
                // print_r($length);
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

                // 

                // while ($row = mysqli_fetch_assoc($result)) {
                //     if ($row['class'] == 'Positive') {
                //         $score = $score + $row['score'];
                //     } else {
                //         $score = $score - $row['score'];
                //     }
                // }

                for ($i = 0; $i < count($common_terms); $i++) {
                    if ($table_class[$i] == 'Positive') {
                        $score = $score + $table_score[$i] * $table_value[$i];
                    } else {
                        $score = $score - $table_score[$i] * $table_value[$i];
                    }
                    $nr_terms = $nr_terms + $table_value[$i];
                }

                if ($nr_terms > 0) {
                    unset($_SESSION['wrong_dictionary_file']);
                    $score = $score / $nr_terms;
                    if ($score >= 0) {
                        $percentage_positive = 100 * (5 + $score) / 10;
                        $percentage_negative = 100 - $percentage_positive;
                    } else {
                        $percentage_negative = -100 * (-5 + $score) / 10;
                        $percentage_positive = 100 - $percentage_negative;
                    }
                } else {
                    // header("location:user_error.php");
                    header("location:user_upload_file.php");
                    $_SESSION['wrong_dictionary_file'] = 1;
                }

                $userID = $_SESSION['userID'];
                $username = $_SESSION['username'];
                if ($score > 0) {
                    $sentiment = "Positive";
                } else {
                    if ($score < 0) {
                        $sentiment = "Negative";
                    } else {
                        $sentiment = "Neutral";
                    }
                }

                $score = round($score, 2);
                if ($nr_terms > 0) {
                    $date = date("Y/m/d");
                    $origin = "file";
                    $query = "INSERT INTO analysishistory (userID, username, content, contentOrigin, sentiment, score, date) VALUES ('$userID','$username','$text','$origin','$sentiment','$score','$date')";
                    $query_run = mysqli_query($conn, $query);
                }

                unset($_SESSION['wrong_extension']);
            }
        } else {
            if (isset($_POST['Submit'])) {
                header("location:user_upload_file.php");
                $_SESSION['wrong_extension'] = 1;
            }
            if (isset($_POST['Submit_new_file'])) {
                $bad_file_extension = 1;
            }
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

    <title>Analyze file content</title>

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
        <?php
        if ($_SESSION['usertype'] == "user") include "user_sidebar.php";
        else include "admin_sidebar.php";
        ?>
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
                                    <h6 class="m-0 font-weight-bold text-primary">The analyzed file</h6>
                                </div>
                                <div class="card-body">
                                    <?php

                                    ?>

                                    <div class="form-group">
                                        <textarea class="form-control" id="exampleFormControlTextarea1" name="" rows="7" disabled placeholder="<?php echo stripslashes($text) ?>"></textarea>
                                    </div>

                                    <?php

                                    ?>

                                    <form action="user_file_result.php" method="POST" enctype="multipart/form-data">
                                        <input required type="file" name="file">
                                        <button class="btn btn-primary btn-icon-split mx-2" name="Submit_new_file" style="float: right;">
                                            <span class="icon text-white-50">
                                                <i class="fas fa-upload"></i>
                                            </span>
                                            <span class="text">Upload new file</span>
                                        </button>
                                    </form>


                                    <br>
                                    <?php
                                    if ($bad_file_extension == 1) {
                                    ?>
                                        <div class="alert alert-warning" role="alert">
                                            <center>The selected file doesn't match the predefined extension</center>
                                        </div>
                                    <?php

                                    }
                                    ?>



                                </div>
                            </div>

                            <?php

                            ?>
                            <div class="row">

                                <div class="col-xl-6 col-lg-5">
                                    <div class="card shadow mb-4">
                                        <!-- Card Header - Dropdown -->
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Sentiment's distribution chart for <strong>dictionary method</strong></h6>
                                        </div>
                                        <!-- Card Body -->
                                        <div class="card-body">
                                            <div class="chart-pie pt-4">
                                                <canvas id="myPieChart"></canvas>
                                            </div>
                                            <hr>
                                            The general score obtained for this analysis is <strong><?php echo round($score, 2) ?></strong>
                                            <br>Identified sentiment: <code><strong><?php echo $sentiment ?></strong></code>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-xl-6 col-lg-5">
                                    <div class="card shadow mb-4">
                                        <!-- Card Header - Dropdown -->
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Sentiment's distribution chart for <strong>Bayes classifier</strong></h6>
                                        </div>
                                        <!-- Card Body -->
                                        <div class="card-body">
                                            <div class="chart-pie pt-4">
                                                <canvas id="myPieChartBayes"></canvas>
                                            </div>
                                            <hr>
                                            Identified sentiment using Naive Bayes Algorithm: <code><strong><?php echo $sentimentBayes ?></strong></code>
                                        </div>
                                        <br>
                                    </div>
                                </div>

                            </div>

                            <!-- Identified Category -->

                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Identified category</h6>
                                </div>
                                <div class="card-body">

                                    <div class="row">

                                        <div class="col-xl-2 col-lg-5">

                                            <div class="card-header py-3" style="background-color: <?php if ($identifiedCategory == "Business") echo '#fff700'; ?>">
                                                <center>
                                                    <h6 class="m-0 font-weight-bold text-primary">Business</h6>
                                                </center>
                                            </div>
                                            <div class="card-body">
                                                <span>
                                                    <center><i class="fas fa-money-bill-alt fa-5x"></i></center>
                                                    <br>
                                                    <center>
                                                        <h6 class="m-0 font-weight-bold text-primary">
                                                            <?php
                                                            $total = $categoryValues['Business'] + $categoryValues['Entertainment'] + $categoryValues['Politics'] + $categoryValues['Sport'] + $categoryValues['Tech'];
                                                            if ($forceCategoryValue == 0) {
                                                                echo round($categoryValues['Business'] / $total * 100, 2);
                                                                echo "%";
                                                            } else {
                                                                if ($identifiedCategory == "Business") echo "100%";
                                                                else echo "0%";
                                                            }

                                                            ?></h6>
                                                    </center>
                                                </span>
                                            </div>

                                        </div>

                                        <div class="col-xl-3 col-lg-5">

                                            <div class="card-header py-3" style="background-color: <?php if ($identifiedCategory == "Entertainment") echo '#fff700'; ?>">
                                                <center>
                                                    <h6 class="m-0 font-weight-bold text-primary">Entertainment</h6>
                                                </center>
                                            </div>
                                            <div class="card-body">
                                                <span>
                                                    <center><i class="fa fa-film fa-5x"></i></center>
                                                    <br>
                                                    <center>
                                                        <h6 class="m-0 font-weight-bold text-primary">
                                                            <?php
                                                            if ($forceCategoryValue == 0) {
                                                                echo round($categoryValues['Entertainment'] / $total * 100, 2);
                                                                echo "%";
                                                            } else {
                                                                if ($identifiedCategory == "Entertainment") echo "100%";
                                                                else echo "0%";
                                                            }
                                                            ?></h6>
                                                    </center>
                                                </span>
                                            </div>

                                        </div>

                                        <div class="col-xl-2 col-lg-5">

                                            <div class="card-header py-3" style="background-color: <?php if ($identifiedCategory == "Politics") echo '#fff700'; ?>">
                                                <center>
                                                    <h6 class="m-0 font-weight-bold text-primary">Politics</h6>
                                                </center>
                                            </div>
                                            <div class="card-body">
                                                <span>
                                                    <center><i class="fas fa-vote-yea fa-5x"></i></center>
                                                    <br>

                                                    <center>
                                                        <h6 class="m-0 font-weight-bold text-primary">
                                                            <?php
                                                            if ($forceCategoryValue == 0) {
                                                                echo round($categoryValues['Politics'] / $total * 100, 2);
                                                                echo "%";
                                                            } else {
                                                                if ($identifiedCategory == "Politics") echo "100%";
                                                                else echo "0%";
                                                            }
                                                            ?></h6>
                                                    </center>
                                                </span>
                                            </div>

                                        </div>

                                        <div class="col-xl-2 col-lg-5">

                                            <div class="card-header py-3" style="background-color: <?php if ($identifiedCategory == "Sport") echo '#fff700'; ?>">
                                                <center>
                                                    <h6 class="m-0 font-weight-bold text-primary">Sport</h6>
                                                </center>
                                            </div>
                                            <div class="card-body">
                                                <span>
                                                    <center><i class="fas fa-basketball-ball fa-5x"></i></center>
                                                    <br>

                                                    <center>
                                                        <h6 class="m-0 font-weight-bold text-primary">
                                                            <?php
                                                            if ($forceCategoryValue == 0) {
                                                                echo round($categoryValues['Sport'] / $total * 100, 2);
                                                                echo "%";
                                                            } else {
                                                                if ($identifiedCategory == "Sport") echo "100%";
                                                                else echo "0%";
                                                            }
                                                            ?></h6>
                                                    </center>
                                                </span>
                                            </div>

                                        </div>

                                        <div class="col-xl-3 col-lg-5">

                                            <div class="card-header py-3" style="background-color: <?php if ($identifiedCategory == "Tech") echo '#fff700'; ?>">
                                                <center>
                                                    <h6 class="m-0 font-weight-bold text-primary">Tech</h6>
                                                </center>
                                            </div>
                                            <div class="card-body">
                                                <span>
                                                    <center><i class="fab fa-dev fa-5x"></i></center>
                                                    <br>

                                                    <center>
                                                        <h6 class="m-0 font-weight-bold text-primary">
                                                            <?php
                                                            if ($forceCategoryValue == 0) {
                                                                echo round($categoryValues['Tech'] / $total * 100, 2);
                                                                echo "%";
                                                            } else {
                                                                if ($identifiedCategory == "Tech") echo "100%";
                                                                else echo "0%";
                                                            }
                                                            ?></h6>
                                                    </center>
                                                </span>
                                            </div>

                                        </div>


                                    </div>

                                    <div class="container-fluid">

                                        <!-- Page Heading -->

                                        <!-- DataTales Example -->


                                    </div>

                                </div>
                            </div>


                            <!-- Project Card Example -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Terms found in dictionary</h6>
                                </div>
                                <div class="card-body">



                                    <div class="container-fluid">

                                        <!-- Page Heading -->

                                        <!-- DataTales Example -->

                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                    <col width="120">
                                                    <col width="120">
                                                    <col width="120">
                                                    <col width="120">
                                                    <thead>
                                                        <tr>
                                                            <th>Term</th>
                                                            <th>Class</th>
                                                            <th>Score</th>
                                                            <th>Number of appearances</th>


                                                        </tr>
                                                    </thead>

                                                    <tbody>

                                                        <!-- <?php
                                                                foreach ($result as $row) {

                                                                ?>
                                                                    <tr>
                                                                        <td><?php echo $row['term'] ?></td>
                                                                        <td><?php echo $row['class'] ?></td>
                                                                        <td><?php echo $row['score'] ?></td>

                                                                    </tr>
                                                                <?php

                                                                }
                                                                ?> -->

                                                        <?php
                                                        for ($i = 0; $i < count($common_terms); $i++) {
                                                        ?>

                                                            <tr>
                                                                <td><?php echo $table_content[$i] ?></td>
                                                                <td><?php echo $table_class[$i] ?></td>
                                                                <td><?php echo $table_score[$i] ?></td>
                                                                <td><?php echo $table_value[$i] ?></td>
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



    <!--
    <script src="js_sb2/demo/chart-pie-demo.js"></script>
    -->

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


    <script>
        var ctx = document.getElementById("myPieChart");
        var myPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["Positive", "Negative"],
                datasets: [{
                    data: [<?php echo round($percentage_positive, 2) ?>, <?php echo round($percentage_negative, 2) ?>],
                    backgroundColor: ['#4e73df', ' #de1616'],
                    hoverBackgroundColor: ['#2e59d9', '#ff0000'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: false
                },
                cutoutPercentage: 80,
            },
        });
    </script>

    <script>
        var ctx = document.getElementById("myPieChartBayes");
        var myPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["Positive", "Negative", "Neutral"],
                datasets: [{
                    data: [<?php if ($forceValue == 0) echo round(100 * $sentimentValues['Positive'] / ($sentimentValues['Positive'] + $sentimentValues['Negative'] + $sentimentValues['Neutral']), 2);
                            else if ($sentimentBayes == 'Positive') echo "100"; ?>,
                        <?php if ($forceValue == 0) echo round(100 * $sentimentValues['Negative'] / ($sentimentValues['Positive'] + $sentimentValues['Negative'] + $sentimentValues['Neutral']), 2);
                        else if ($sentimentBayes == 'Negative') echo "100" ?>,
                        <?php if ($forceValue == 0) echo round(100 * $sentimentValues['Neutral'] / ($sentimentValues['Positive'] + $sentimentValues['Negative'] + $sentimentValues['Neutral']), 2);
                        else if ($sentimentBayes == 'Neutral') echo "100" ?>
                    ],
                    backgroundColor: ['#4e73df', '#de1616', '#2fe05e'],
                    hoverBackgroundColor: ['#2e59d9', '#ff0000', '#179e10'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: false
                },
                cutoutPercentage: 80,
            },
        });
    </script>
</body>

</html>