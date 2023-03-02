<?php

session_start();
require_once('connection.php');
include_once('lib/Opinion.php');
include_once('../SentimentAnalyzer/simplehtmldom_1_9_1/simple_html_dom.php');
set_time_limit(100);
error_reporting(E_ERROR | E_PARSE);

if (!isset($_SESSION['username'])) {
    header("location:index.php");
}

if ($_SESSION['usertype'] != "admin") {
    header("location:user_page_error.php");
    exit;
}

$sql = "SELECT COUNT(*) AS rows FROM analysishistory";
$sql_run = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($sql_run);

$sql1 = "SELECT COUNT(*) AS rows1 FROM analysishistory WHERE sentiment='Positive'";
$sql_run1 = mysqli_query($conn, $sql1);
$row1 = mysqli_fetch_assoc($sql_run1);

$sql2 = "SELECT COUNT(*) AS rows2 FROM analysishistory WHERE sentiment='Neutral'";
$sql_run2 = mysqli_query($conn, $sql2);
$row2 = mysqli_fetch_assoc($sql_run2);

$sql3 = "SELECT COUNT(*) AS rows3 FROM analysishistory WHERE sentiment='Negative'";
$sql_run3 = mysqli_query($conn, $sql3);
$row3 = mysqli_fetch_assoc($sql_run3);

$sql4 = "SELECT COUNT(*) AS rows4 FROM analysishistory WHERE MONTH(date)='3' AND sentiment='Positive'";
$sql_run4 = mysqli_query($conn, $sql4);
$row4 = mysqli_fetch_assoc($sql_run4);

$sql5 = "SELECT COUNT(*) AS rows5 FROM analysishistory WHERE MONTH(date)='3' AND sentiment='Neutral'";
$sql_run5 = mysqli_query($conn, $sql5);
$row5 = mysqli_fetch_assoc($sql_run5);

$sql6 = "SELECT COUNT(*) AS rows6 FROM analysishistory WHERE MONTH(date)='3' AND sentiment='Negative'";
$sql_run6 = mysqli_query($conn, $sql6);
$row6 = mysqli_fetch_assoc($sql_run6);

$sql7 = "SELECT COUNT(*) AS rows7 FROM analysishistory WHERE MONTH(date)='4' AND sentiment='Positive'";
$sql_run7 = mysqli_query($conn, $sql7);
$row7 = mysqli_fetch_assoc($sql_run7);

$sql8 = "SELECT COUNT(*) AS rows8 FROM analysishistory WHERE MONTH(date)='4' AND sentiment='Neutral'";
$sql_run8 = mysqli_query($conn, $sql8);
$row8 = mysqli_fetch_assoc($sql_run8);

$sql9 = "SELECT COUNT(*) AS rows9 FROM analysishistory WHERE MONTH(date)='4' AND sentiment='Negative'";
$sql_run9 = mysqli_query($conn, $sql9);
$row9 = mysqli_fetch_assoc($sql_run9);

$sql10 = "SELECT COUNT(*) AS rows10 FROM analysishistory WHERE MONTH(date)='5' AND sentiment='Positive'";
$sql_run10 = mysqli_query($conn, $sql10);
$row10 = mysqli_fetch_assoc($sql_run10);

$sql11 = "SELECT COUNT(*) AS rows11 FROM analysishistory WHERE MONTH(date)='5' AND sentiment='Neutral'";
$sql_run11 = mysqli_query($conn, $sql11);
$row11 = mysqli_fetch_assoc($sql_run11);

$sql12 = "SELECT COUNT(*) AS rows12 FROM analysishistory WHERE MONTH(date)='5' AND sentiment='Negative'";
$sql_run12 = mysqli_query($conn, $sql12);
$row12 = mysqli_fetch_assoc($sql_run12);

$sql13 = "SELECT COUNT(*) AS rows13 FROM analysishistory WHERE MONTH(date)='6' AND sentiment='Positive'";
$sql_run13 = mysqli_query($conn, $sql13);
$row13 = mysqli_fetch_assoc($sql_run13);

$sql14 = "SELECT COUNT(*) AS rows14 FROM analysishistory WHERE MONTH(date)='6' AND sentiment='Neutral'";
$sql_run14 = mysqli_query($conn, $sql14);
$row14 = mysqli_fetch_assoc($sql_run14);

$sql15 = "SELECT COUNT(*) AS rows15 FROM analysishistory WHERE MONTH(date)='6' AND sentiment='Negative'";
$sql_run15 = mysqli_query($conn, $sql15);
$row15 = mysqli_fetch_assoc($sql_run15);

$op = new Opinion();
$op->train('datasets/negative_tweets.txt', 'Negative');
$op->train('datasets/positive_tweets.txt', 'Positive');
$op->train('datasets/neutral_tweets.txt', 'Neutral');

// $traintime1 = $op->trainTime('datasets/negative_tweets.txt', 'Negative');
// $traintime2 = $op->trainTime('datasets/positive_tweets.txt', 'Positive');
// $traintime3 = $op->trainTime('datasets/neutral_tweets.txt', 'Neutral');

// echo $traintime1 + $traintime2 + $traintime3;

// $sql_text = "SELECT * FROM analysishistory";
// $sql_twitter = "SELECT * FROM twitter";
// $sql_feedback = "SELECT * FROM feedback";
// $run_text = mysqli_query($conn, $sql_text);
// $run_twitter = mysqli_query($conn, $sql_twitter);
// $run_feedback = mysqli_query($conn, $sql_feedback);

// $start = microtime(true);
// foreach ($run_text as $text) {

//     if ($text['contentOrigin'] == "link") {
//         $op->classify(file_get_html($text['content'])->plaintext);
//     } else {
//         $op->classify($text['content']);
//     }
// }
// foreach ($run_twitter as $twitter) {

//     $op->classify($twitter['twitter_comment']);
// }
// foreach ($run_feedback as $feedback) {

//     $op->classify($feedback['feedbackContent']) ;
// }
// $end = microtime(true);
// echo ($end - $start)/(mysqli_num_rows($run_text) + mysqli_num_rows($run_twitter) + mysqli_num_rows($run_feedback));


if (!empty($_GET['fileDownload'])) {

    unlink('datasets/generated_dataset.txt');
    $sql_text = "SELECT * FROM analysishistory";
    $sql_twitter = "SELECT * FROM twitter";
    $sql_feedback = "SELECT * FROM feedback";
    $run_text = mysqli_query($conn, $sql_text);
    $run_twitter = mysqli_query($conn, $sql_twitter);
    $run_feedback = mysqli_query($conn, $sql_feedback);

    $file = fopen('datasets/generated_dataset.txt', "a");
    foreach ($run_text as $text) {
        fwrite($file, $text['content']);
        fwrite($file, "  =  ");
        if ($text['contentOrigin'] == "link") {
            fwrite($file, $op->classify(file_get_html($text['content'])->plaintext) . PHP_EOL);
        } else {
            fwrite($file, $op->classify($text['content']) . PHP_EOL);
        }
        fwrite($file, "-----------------------------------------------------------------------------------------------");
        fwrite($file, "" . PHP_EOL);
    }
    foreach ($run_twitter as $twitter) {
        fwrite($file, $twitter['twitter_comment']);
        fwrite($file, "  =  ");
        fwrite($file, $op->classify($twitter['twitter_comment']) . PHP_EOL);
        fwrite($file, "-----------------------------------------------------------------------------------------------");
        fwrite($file, "" . PHP_EOL);
    }
    foreach ($run_feedback as $feedback) {
        fwrite($file, $feedback['feedbackContent']);
        fwrite($file, "  =  ");
        fwrite($file, $op->classify($feedback['feedbackContent']) . PHP_EOL);
        fwrite($file, "-----------------------------------------------------------------------------------------------");
        fwrite($file, "" . PHP_EOL);
    }
    fclose($file);

    $fileName = basename($_GET['fileDownload']);
    $filePath = 'datasets/' . $fileName;

    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; fileName=$fileName");
    header("Content-Type: application/download");
    header("Content-Transfer-Encoding: binary");

    readfile($filePath);
    exit;
}

function dictionaryAnalysis($conn, $doc)
{
    include_once('connection.php');
    $text = addslashes(str_replace('"', '“', $doc));
    $split = preg_split("/[^\w]*([\s]+[^\w]*|$)/", $text, -1, PREG_SPLIT_NO_EMPTY);

    $filterExisting = "'" . implode("', '", $split) . "'";

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

    if ($nr_terms > 0) {
        $score = $score / $nr_terms;
        if ($score >= 0) {
            $percentage_positive = 100 * (5 + $score) / 10;
            $percentage_negative = 100 - $percentage_positive;
        } else {
            $percentage_negative = -100 * (-5 + $score) / 10;
            $percentage_positive = 100 - $percentage_negative;
        }
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

    return $sentiment;
}

// $sql_text = "SELECT * FROM analysishistory";
// $sql_twitter = "SELECT * FROM twitter";
// $sql_feedback = "SELECT * FROM feedback";
// $run_text = mysqli_query($conn, $sql_text);
// $run_twitter = mysqli_query($conn, $sql_twitter);
// $run_feedback = mysqli_query($conn, $sql_feedback);

// $start = microtime(true);
// foreach ($run_text as $text) {

//     if ($text['contentOrigin'] == "link") {
//         $val = dictionaryAnalysis($conn, file_get_html($text['content'])->plaintext);
//     } else {
//         $val = dictionaryAnalysis($conn, $text['content']);
//     }
// }
// foreach ($run_twitter as $twitter) {

//     $val = dictionaryAnalysis($conn, $twitter['twitter_comment']);
// }
// foreach ($run_feedback as $feedback) {

//     $val = dictionaryAnalysis($conn, $feedback['feedbackContent']) ;
// }
// $end = microtime(true);
// echo ($end - $start)/(mysqli_num_rows($run_text) + mysqli_num_rows($run_twitter) + mysqli_num_rows($run_feedback));

?>

<style>
    .checked {
        color: #FFD600;
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

    <title>Admin statistics</title>

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


    <link href="assets/vendor/fonts/circular-std/style.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/vendor/charts/chartist-bundle/chartist.css">

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

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <a href="admin_statistics.php?fileDownload=generated_dataset.txt" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate and download datasets from Sentiment Analyzer</a>
                    </div>

                    <!-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <a href="admin_statistics.php?fileDownload=generated_dataset.txt">
                    <button class="btn btn-primary btn-icon-split mx-2" style="float: right;">
                                            <span class="icon text-white-50">
                                                <i class="fas fa-download fa-sm text-white-50"></i>
                                            </span>
                                            <span class="text">Generate and download datasets from Sentiment Analyzer</span>
                                        </button>
                    </a>
                    </div> -->

                    <div class="row">

                        <!-- Earnings (Monthly) Card Example -->


                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <a href="admin_comments.php" style="text-decoration: none;">
                                <div class="card border-left-warning shadow h-100 py-2">

                                    <div class="card-body">

                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Number of runs</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $row['rows'] ?></div>
                                            </div>

                                        </div>

                                    </div>
                            </a>
                        </div>
                    </div>

                    <!-- Earnings (Monthly) Card Example -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <a href="admin_comments.php?comments=positive" style="text-decoration: none;">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Positive comments</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $row1['rows1'] ?></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Earnings (Monthly) Card Example -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <a href="admin_comments.php?comments=neutral" style="text-decoration: none;">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Neutral comments</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $row2['rows2'] ?></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Pending Requests Card Example -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <a href="admin_comments.php?comments=negative" style="text-decoration: none;">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Negative comments</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $row3['rows3'] ?></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Earnings (Monthly) Card Example -->

                    <!-- Pending Requests Card Example -->


                    <!-- Content Row -->



                    <!-- Area Chart -->

                    <!-- Card Body -->


                    <!-- Pie Chart -->

                    <!-- Card Body -->

                    <!-- Content Row -->

                    <!-- Content Column -->
                    <div class="col-xl-6 col-md-6 mb-4">

                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <h3 class="m-0 font-weight-bold text-primary mb-3">Dictionary method's <strong>overall</strong> accuracy in comparison with Bayesian classifier</h3>
                                        <?php
                                        $sql_text = "SELECT * FROM analysishistory";
                                        $sql_twitter = "SELECT * FROM twitter";
                                        $sql_feedback = "SELECT * FROM feedback";
                                        $run_text = mysqli_query($conn, $sql_text);
                                        $run_twitter = mysqli_query($conn, $sql_twitter);
                                        $run_feedback = mysqli_query($conn, $sql_feedback);

                                        $counter1 = 0;
                                        $counter2 = 0;
                                        $counter3 = 0;
                                        $correct1 = 0;
                                        $correct2 = 0;
                                        $correct3 = 0;
                                        foreach ($run_text as $text) {
                                            $counter1++;
                                            if ($text['contentOrigin'] == "link") {
                                                if (strcmp($op->classify(addslashes(str_replace('"', '“', file_get_html($row['content'])->plaintext))), $text['sentiment']) == 0) $correct1++;
                                            } else {
                                                if (strcmp($op->classify($text['content']), $text['sentiment']) == 0) $correct1++;
                                            }
                                        }
                                        foreach ($run_twitter as $twitter) {
                                            $counter2++;
                                            if (strcmp($op->classify($twitter['twitter_comment']), $twitter['twitter_sentiment']) == 0) $correct2++;
                                        }
                                        foreach ($run_feedback as $feedback) {
                                            $counter3++;
                                            if (strcmp($op->classify($feedback['feedbackContent']), $feedback['feedbackSentiment']) == 0) $correct3++;
                                        }
                                        ?>
                                        <div class="h4 mb-0 font-weight-bold text-gray-800"><code><?php echo round(($correct1 + $correct2 + $correct3) / ($counter1 + $counter2 + $counter3) * 100, 2);
                                                                                                    echo "%"; ?></code></div>
                                        <p class="review-text font-weight-bold m-0" style="float: right;"><span><?php echo $correct1 + $correct2 + $correct3; ?> out of <?php echo $counter1 + $counter2 + $counter3; ?></span></p>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-xl-6 col-md-6 mb-4">

                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <h3 class="m-0 font-weight-bold text-primary mb-3">Dictionary method's accuracy for <strong>texts</strong> in comparison with Bayesian classifier</h3>

                                        <div class="h4 mb-0 font-weight-bold text-gray-800"><code><?php echo round($correct1 / $counter1 * 100, 2);
                                                                                                    echo "%"; ?></code></div>

                                        <p class="review-text font-weight-bold m-0" style="float: right;"><span><?php echo $correct1; ?> out of <?php echo $counter1; ?></span></p>


                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-xl-6 col-md-6 mb-4">

                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <h3 class="m-0 font-weight-bold text-primary mb-3">Dictionary method's accuracy for <strong>tweets</strong> in comparison with Bayesian classifier</h3>

                                        <div class="h4 mb-0 font-weight-bold text-gray-800"><code><?php echo round($correct2 / $counter2 * 100, 2);
                                                                                                    echo "%"; ?></code></div>
                                        <p class="review-text font-weight-bold m-0" style="float: right;"><span><?php echo $correct2; ?> out of <?php echo $counter2; ?></span></p>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-xl-6 col-md-6 mb-4">

                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <h3 class="m-0 font-weight-bold text-primary mb-3">Dictionary method's accuracy for <strong>feedbacks</strong> in comparison with Bayesian classifier</h3>

                                        <div class="h4 mb-0 font-weight-bold text-gray-800"><code><?php echo round($correct3 / $counter3 * 100, 2);
                                                                                                    echo "%"; ?></code></div>
                                        <p class="review-text font-weight-bold m-0" style="float: right;"><span><?php echo $correct3; ?> out of <?php echo $counter3; ?></span></p>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="col-xl-12 col-md-6 mb-4">

                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <h3 class="m-0 font-weight-bold text-primary mb-3">Bayesian classifier's accuracy</h3>

                                        <?php
                                        // $i = 0;
                                        // $t = 0;
                                        // $f = 0;
                                        // $op1 = new Opinion();
                                        // $op1->train('datasets/negative_tweets.txt', 'Negative');
                                        // $op1->train('datasets/positive_tweets.txt', 'Positive');
                                        // $op1->train('datasets/neutral_tweets.txt', 'Neutral');
                                        // $file1 = fopen('datasets/negative_tweets.txt', 'r');
                                        // $file2 = fopen('datasets/positive_tweets.txt', 'r');
                                        // $file3 = fopen('datasets/neutral_tweets.txt', 'r');
                                        // while ($line = fgets($file1)) {
                                        //     if ($i++ > 4001) {
                                        //         if ($op1->classify($line) == 'Negative') {
                                        //             $t++;
                                        //         } else {
                                        //             $f++;
                                        //         }
                                        //     }
                                        // }
                                        // while ($line = fgets($file2)) {
                                        //     if ($i++ > 4001) {
                                        //         if ($op1->classify($line) == 'Positive') {
                                        //             $t++;
                                        //         } else {
                                        //             $f++;
                                        //         }
                                        //     }
                                        // }
                                        // while ($line = fgets($file3)) {
                                        //     if ($i++ > 3001) {
                                        //         if ($op1->classify($line) == 'Neutral') {
                                        //             $t++;
                                        //         } else {
                                        //             $f++;
                                        //         }
                                        //     }
                                        // }
                                        ?>

                                        <div class="h4 mb-0 font-weight-bold text-gray-800"><code>84.36%</code></div>
                                        <p class="review-text font-weight-bold m-0" style="float: right;"><span>6851 out of 8121</span></p>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="col-xl-12 col-md-6 mb-4">

                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <h3 class="m-0 font-weight-bold text-primary mb-3">Dictionary method's accuracy using datasets</h3>

                                        <?php
                                        // $t = 0;
                                        // $f = 0;
                                        // $file1 = fopen('datasets/negative_tweets.txt', 'r');
                                        // $file2 = fopen('datasets/positive_tweets.txt', 'r');
                                        // $file3 = fopen('datasets/neutral_tweets.txt', 'r');
                                        // while ($line = fgets($file1)) {
                                        //     if (dictionaryAnalysis($conn, $line) == 'Negative') {
                                        //         $t++;
                                        //     } else {
                                        //         $f++;
                                        //     }
                                        // }
                                        // while ($line = fgets($file2)) {
                                        //     if (dictionaryAnalysis($conn, $line) == 'Positive') {
                                        //         $t++;
                                        //     } else {
                                        //         $f++;
                                        //     }
                                        // }
                                        // while ($line = fgets($file3)) {
                                        //     if (dictionaryAnalysis($conn, $line) == 'Neutral') {
                                        //         $t++;
                                        //     } else {
                                        //         $f++;
                                        //     }
                                        // }
                                        ?>

                                        <div class="h4 mb-0 font-weight-bold text-gray-800"><code>43.72%</code></div>
                                        <p class="review-text font-weight-bold m-0" style="float: right;"><span>5300 out of 12123</span></p>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>


                </div>


                <div class="row">
                    <div class="col-xl-4 col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">The most searched Twitter content</h6>
                            </div>
                            <div class="card-body">

                                <?php
                                $subject = array();
                                $sqlTwitter = "SELECT * FROM twitter";
                                $queryTwitter = mysqli_query($conn, $sqlTwitter);
                                foreach ($queryTwitter as $tweetContent) {
                                    array_push($subject, $tweetContent['searched_content']);
                                }
                                $tweetsList = array_count_values($subject);
                                arsort($tweetsList);
                                ?>

                                <ul class="list-group">
                                    <?php
                                    $counter = 0;
                                    foreach ($tweetsList as $key => $value) {
                                        if ($counter < 6) {
                                    ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a href="admin_twitter.php?content=<?php echo $key; ?>" style="text-decoration: none;"><strong><?php echo $key; ?></strong></a>
                                                <span class="badge badge-primary badge-pill"><?php echo $value; ?></span>
                                            </li>
                                    <?php
                                            $counter++;
                                        }
                                    }
                                    ?>

                                </ul>
                                <br>
                            </div>
                        </div>
                    </div>


                    <div class="col-xl-8 col-lg-5">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Statistics chart</h6>
                            </div>
                            <div class="card-body">
                                <div class="chart-bar">
                                    <canvas id="myBarChart"></canvas>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>








                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Feedbacks from application's users</h6>
                    </div>
                    <div class="card-body">



                        <div class="container-fluid">

                            <!-- Page Heading -->

                            <!-- DataTales Example -->


                            <div class="card-body">

                                <div class="row">
                                    <div class="col-xl-6 col-lg-5">

                                        <?php
                                        $ratingSum = 0;
                                        $sqlrating = "SELECT * FROM feedback";
                                        $runrating = mysqli_query($conn, $sqlrating);
                                        foreach ($runrating as $rating) {
                                            $ratingSum += $rating['feedbackStars'];
                                        }
                                        $ratingSum /= mysqli_num_rows($runrating);
                                        ?>
                                        <br>
                                        <br>
                                        <center>
                                            <h2 class="m-0 font-weight-bold text-primary"><?php echo round($ratingSum, 1) ?> / 5</h2>
                                            <br>
                                            <?php
                                            $stars = 0;
                                            if (round($ratingSum, 1) >= floor($ratingSum) + 0.5) $stars = floor($ratingSum) + 1;
                                            else $stars = floor($ratingSum);
                                            for ($i = 1; $i <= $stars; $i++) {
                                            ?>
                                                <span class="fa fa-star checked fa-2x"></span>
                                            <?php
                                            }
                                            ?>
                                            <?php
                                            for ($i = 0; $i < 5 - $stars; $i++) {
                                            ?>
                                                <span class="fa fa-star fa-2x"></span>
                                            <?php
                                            }
                                            ?>
                                        </center>

                                    </div>

                                    <div class="col-xl-6 col-lg-5">

                                        <!-- <h4 class="small font-weight-bold">5 stars</h4>
                                                
                                                <div class="progress mb-4">
                                                    <div class="progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <h4 class="small font-weight-bold">4 stars</h4>
                                                <div class="progress mb-4">
                                                    <div class="progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <h4 class="small font-weight-bold">3 stars</h4>
                                                <div class="progress mb-4">
                                                    <div class="progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <h4 class="small font-weight-bold">2 stars</h4>
                                                <div class="progress mb-4">
                                                    <div class="progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <h4 class="small font-weight-bold">1 star</h4>
                                                <div class="progress mb-4">
                                                    <div class="progress-bar" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <div class="card border-left-info shadow h-100 py-2">
                                                    <div class="card-body">
                                                        <div class="row no-gutters align-items-center">
                                                            <div class="col mr-2">
                                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">5 stars</div>
                                                                <div class="row no-gutters align-items-center">
                                                                    <div class="col-auto">
                                                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">50%</div>
                                                                    </div>
                                                                    <div class="col">
                                                                        <div class="progress progress-sm mr-2">
                                                                            <div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->

                                        <?php
                                        $sql1 = "SELECT * FROM feedback WHERE feedbackStars = 5";
                                        $sqlrun1 = mysqli_query($conn, $sql1);
                                        $stars5 = mysqli_num_rows($sqlrun1);

                                        $sql2 = "SELECT * FROM feedback WHERE feedbackStars = 4";
                                        $sqlrun2 = mysqli_query($conn, $sql2);
                                        $stars4 = mysqli_num_rows($sqlrun2);

                                        $sql3 = "SELECT * FROM feedback WHERE feedbackStars = 3";
                                        $sqlrun3 = mysqli_query($conn, $sql3);
                                        $stars3 = mysqli_num_rows($sqlrun3);

                                        $sql4 = "SELECT * FROM feedback WHERE feedbackStars = 2";
                                        $sqlrun4 = mysqli_query($conn, $sql4);
                                        $stars2 = mysqli_num_rows($sqlrun4);

                                        $sql5 = "SELECT * FROM feedback WHERE feedbackStars = 1";
                                        $sqlrun5 = mysqli_query($conn, $sql5);
                                        $stars1 = mysqli_num_rows($sqlrun5);

                                        ?>

                                        <div class="ct-chart-horizontal ct-golden-section"></div>

                                    </div>
                                </div>
                                <br>

                                <div class="table-responsive">
                                    <?php
                                    $query = "SELECT * FROM feedback";
                                    $result = mysqli_query($conn, $query);
                                    ?>
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <col width="150">
                                        <col width="200">
                                        <col width="100">
                                        <col width="150">
                                        <col width="100">
                                        <col width="100">
                                        <col width="200">
                                        <thead>
                                            <tr>
                                                <th>Username</th>
                                                <th>Stars</th>
                                                <th>Category</th>
                                                <th>Feedback</th>
                                                <th>Sentiment</th>
                                                <th>Sentiment given by Bayes classifier</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php

                                            foreach ($result as $row) {
                                            ?>
                                                <tr style="background:<?php if (strcmp($row['feedbackSentiment'], $op->classify($row['feedbackContent'])) != 0) echo '#fad85f'; ?>;">
                                                    <td> <?php echo $row['username']; ?></td>
                                                    <td>
                                                        <?php
                                                        for ($i = 1; $i <= $row['feedbackStars']; $i++) {
                                                        ?>
                                                            <span class="fa fa-star checked"></span>
                                                        <?php
                                                        }
                                                        ?>
                                                        <?php
                                                        for ($i = 0; $i < 5 - $row['feedbackStars']; $i++) {
                                                        ?>
                                                            <span class="fa fa-star"></span>
                                                        <?php
                                                        }
                                                        ?>
                                                    </td>

                                                    <td><?php echo $row['feedbackCategory'] ?></td>
                                                    <td><?php echo $row['feedbackContent'] ?></td>
                                                    <td><?php echo $row['feedbackSentiment'] ?></td>
                                                    <td><?php echo $op->classify($row['feedbackContent']) ?></td>
                                                    <td><?php echo $row['date'] ?></td>

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




                <!-- Page Heading -->

                <!-- Content Row -->

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

    <script>
        var ctx = document.getElementById("myBarChart");
        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ["March", "April", "May", "June"],
                datasets: [{
                        label: "Positive",
                        backgroundColor: "#4e73df",
                        hoverBackgroundColor: "#2e59d9",
                        borderColor: "#4e73df",
                        data: [<?php echo $row4['rows4'] ?>, <?php echo $row7['rows7'] ?>,
                            <?php echo $row10['rows10'] ?>, <?php echo $row13['rows13'] ?>
                        ],
                    },
                    {
                        label: "Neutral",
                        backgroundColor: "#1cc88a",
                        hoverBackgroundColor: "#38c949",
                        borderColor: "#4e73df",
                        data: [<?php echo $row5['rows5'] ?>, <?php echo $row8['rows8'] ?>,
                            <?php echo $row11['rows11'] ?>, <?php echo $row14['rows14'] ?>
                        ]
                    },
                    {
                        label: "Negative",
                        backgroundColor: "#de1616",
                        hoverBackgroundColor: "#ff0000",
                        borderColor: "#4e73df",
                        data: [<?php echo $row6['rows6'] ?>, <?php echo $row9['rows9'] ?>,
                            <?php echo $row12['rows12'] ?>, <?php echo $row15['rows15'] ?>
                        ]
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'month'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 6
                        },
                        maxBarThickness: 25,
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            max: 10,
                            maxTicksLimit: 5,
                            padding: 10,


                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: false
                },
                tooltips: {
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,

                },
            }
        });
    </script>

    <script src="assets/vendor/charts/chartist-bundle/chartist.min.js"></script>
    <script>
        (function(window, document, $, undefined) {
            "use strict";
            $(function() {
                if ($('.ct-chart-horizontal').length) {
                    new Chartist.Bar('.ct-chart-horizontal', {
                        labels: ['1 star', '2 stars', '3 stars', '4 stars', '5 stars'],
                        series: [
                            [<?php echo $stars1; ?>, <?php echo $stars2; ?>,
                                <?php echo $stars3; ?>, <?php echo $stars4; ?>,
                                <?php echo $stars5; ?>
                            ]
                        ]
                    }, {
                        seriesBarDistance: 10,
                        reverseData: false,
                        horizontalBars: true,
                        axisY: {
                            offset: 70,
                            showGrid: false
                        },
                        axisX: {
                            onlyInteger: true,
                            showGrid: false
                        }
                    });

                }

            });

        })(window, document, window.jQuery);
    </script>


</body>

</html>