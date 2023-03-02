<?php

session_start();
require_once('connection.php');
include_once('lib/Opinion.php');


$setTwitter = 0;
$noResults = 0;

if (!isset($_SESSION['username'])) {
    header("location:index.php");
}

if (isset($_GET['q']) && $_GET['q'] != '') {

    include_once('config.php');
    include_once('../SentimentAnalyzer/lib/TwitterSentimentAnalysis.php');

    $TwitterSentimentAnalysis = new TwitterSentimentAnalysis(
        TWITTER_CONSUMER_KEY,
        TWITTER_CONSUMER_SECRET,
        TWITTER_ACCESS_KEY,
        TWITTER_ACCESS_SECRET
    );

    if ($_GET['locationTweets'] == "No location selected") {
        $twitterSearchParams = array(
            'q' => $_GET['q'],
            'lang' => 'en',
            'count' => $_GET['numberTweets']
        );
    } else {
        $zone = new DateTimeZone($_GET['locationTweets']);
        $location = $zone->getLocation();
        $latitude = $location['latitude'];
        $longitude = $location['longitude'];
        $radius = "1000km";
        $geocode = $latitude . "," . $longitude . "," . $radius;
        $twitterSearchParams = array(
            'q' => $_GET['q'],
            'lang' => 'en',
            'count' => $_GET['numberTweets'],
            'geocode' => $geocode
        );
    }

    $results = $TwitterSentimentAnalysis->sentimentAnalysis($twitterSearchParams);
    if (empty($results)) $noResults = 1;
    $setTwitter = 1;
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

    <title>Analyze twitter posts</title>

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
                                    <h6 class="m-0 font-weight-bold text-primary">Analyze twitter posts</h6>
                                </div>
                                <div class="card-body">
                                    <form method="GET">
                                        <div class="form-group">
                                            <label class="m-0 font-weight-bold text-primary">Find tweets related to subject</label>
                                            <input required type="text" name="q" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label class="m-0 font-weight-bold text-primary">How many tweets do you want to analyze?</label>
                                            <input type="number" class="form-control bg-light border-0 small" name="numberTweets" value="1" min="1" max="20" />
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label class="m-0 font-weight-bold text-primary">Search tweets by a given location (optional)</label>
                                            <select class="form-control" id="exampleFormControlSelect" name="locationTweets">
                                                <option>No location selected</option>
                                                <?php
                                                $timezone_identifiers = DateTimeZone::listIdentifiers();
                                                for ($i = 0; $i < count($timezone_identifiers) - 1; $i++) {
                                                    echo "<option>$timezone_identifiers[$i]</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <br>
                                        <button type="submit" class="btn btn-primary btn-icon-split mx-2" name="Twitter" style="float: right;">
                                            <span class="icon text-white-50">
                                                <i class="fab fa-twitter"></i>
                                            </span>
                                            <span class="text">Analyze tweets</span>
                                        </button>

                                    </form>



                                </div>
                            </div>
                        </div>


                    </div>
                </div>



                <?php
                if ($setTwitter == 1) {
                ?>


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
                                        <h6 class="m-0 font-weight-bold text-primary">Twitter searched content</h6>
                                    </div>
                                    <div class="card-body">

                                        <span>You have searched: <strong><?php echo $_GET['q']; ?></strong></span>
                                        <br>
                                        <span>Result's number: <strong><?php echo $_GET['numberTweets']; ?></strong></span>
                                        <br>
                                        <span>Result's location: <strong><?php echo $_GET['locationTweets']; ?></strong></span>

                                        <div class="container-fluid">

                                            <!-- Page Heading -->

                                            <!-- DataTales Example -->



                                        </div>

                                    </div>
                                </div>
                            </div>




                        </div>
                    </div>









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





                        </div>
                    </div>








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
                                        <h6 class="m-0 font-weight-bold text-primary">Twitter posts analysis</h6>
                                    </div>
                                    <div class="card-body">

                                        <?php

                                        $op = new Opinion();
                                        $op->train('datasets/negative_tweets.txt', 'Negative');
                                        $op->train('datasets/positive_tweets.txt', 'Positive');
                                        $op->train('datasets/neutral_tweets.txt', 'Neutral');

                                        foreach ($results as $tweet) {
                                            $text = $tweet['text'];
                                            $split = preg_split("/[^\w]*([\s]+[^\w]*|$)/", $text, -1, PREG_SPLIT_NO_EMPTY);

                                            $sentimentBayes = $op->classify($text);

                                            $filterExisting = "'" . implode("', '", $split) . "'";
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
                                            if ($result1) {
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
                                                    $score = $score / $nr_terms;
                                                    if ($score >= 0) {
                                                        $percentage_positive = 100 * (5 + $score) / 10;
                                                        $percentage_negative = 100 - $percentage_positive;
                                                    } else {
                                                        $percentage_negative = -100 * (-5 + $score) / 10;
                                                        $percentage_positive = 100 - $percentage_negative;
                                                    }
                                                } else {
                                                    $score = 0;
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
                                            } else {
                                                $sentiment = "Neutral";
                                            }

                                            $username = $_SESSION['username'];
                                            $searched_content = $_GET['q'];
                                            $twitter_comment = addslashes(str_replace('"', '“', $tweet['text']));;
                                            $twitter_sentiment = $sentiment;
                                            $twitter_author = $tweet['user'];
                                            $twitter_link = $tweet['url'];

                                            $sql_twitter = "INSERT INTO twitter (username, searched_content, twitter_comment, twitter_sentiment, twitter_author, twitter_link) VALUES ('$username', '$searched_content', '$twitter_comment', '$twitter_sentiment', '$twitter_author', '$twitter_link')";
                                            $sql_result = mysqli_query($conn, $sql_twitter);

                                        ?>

                                            <div class="tab-content" id="myTabContent5">

                                                <div class="tab-pane fade active show" id="tab-2" role="tabpanel" aria-labelledby="product-tab-2">
                                                    <div class="review-block">

                                                        <p class="review-text font-italic m-0"><?php echo $tweet['text']; ?></p>
                                                        <p class="review-text font-italic m-0" style="float: right;"><strong>Dictionary method vs Bayesian classifier</strong></p><br>
                                                        <p class="review-text font-italic m-0" style="float: right;">
                                                            <?php
                                                            if ($sentiment == "Positive")
                                                                echo "<span style=\"font-size: 1.75rem;\">
                                                                    <span style=\"color: blue;\">Positive
                                                                            </span>
                                                                                        </span>";
                                                            if ($sentiment == "Negative")
                                                                echo "<span style=\"font-size: 1.75rem;\">
                                                                         <span style=\"color: red;\">Negative
                                                                                                 </span>
                                                                                                </span>";
                                                            if ($sentiment == "Neutral")
                                                                echo "<span style=\"font-size: 1.75rem;\">
                                                                            <span style=\"color: green;\">Neutral
                                                                                                       </span>
                                                                                                         </span>";
                                                            ?>
                                                            <span>/</span>
                                                            <?php
                                                            if ($sentimentBayes == "Positive")
                                                                echo "<span style=\"font-size: 1.75rem;\">
                                                                    <span style=\"color: blue;\">Positive
                                                                            </span>
                                                                                        </span>";
                                                            if ($sentimentBayes == "Negative")
                                                                echo "<span style=\"font-size: 1.75rem;\">
                                                                         <span style=\"color: red;\">Negative
                                                                                                 </span>
                                                                                                </span>";
                                                            if ($sentimentBayes == "Neutral")
                                                                echo "<span style=\"font-size: 1.75rem;\">
                                                                            <span style=\"color: green;\">Neutral
                                                                                                       </span>
                                                                                                         </span>";
                                                            ?>
                                                        </p>
                                                        <br>
                                                        <br>

                                                        <span>posted by</span>
                                                        <span class="text-dark font-weight-bold">
                                                            <?php echo $tweet['user'];
                                                            ?>
                                                        </span>
                                                        <br>
                                                        <span>at</span>
                                                        <a href="<?php echo $tweet['url']; ?>"><?php echo $tweet['url']; ?></a>

                                                    </div>



                                                </div>
                                            </div>
                                            <div class="review-block border-top mt-3 pt-3">

                                            <?php
                                        }

                                            ?>

                                            <?php
                                            if ($noResults == 1) {
                                            ?>
                                            <center><h3 class="m-0 font-weight-bold text-danger">No tweets were found for selected location! Please try again later!</h3></center>
                                            <?php
                                            }
                                            ?>


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