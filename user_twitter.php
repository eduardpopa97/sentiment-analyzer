<?php

session_start();
require_once('connection.php');
include_once('../SentimentAnalyzer/simplehtmldom_1_9_1/simple_html_dom.php');


$setTwitter = 0;

if (!isset($_SESSION['username'])) {
    header("location:index.php");
}

if (isset($_POST['Twitter'])) {
 
    // $text = "I'm a huge fan of Cowboy Behop";
    // $url = "https://jamiembrown-tweet-sentiment-analysis.p.mashape.com/api/?text=$text";
    // $response = file_get_contents($url);
    // var_dump(json_decode($response));

    $html = file_get_html($_POST['link']);
    $ret = $html->find('div');

    $c = curl_init($_POST['link']);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    $html1 = curl_exec($c);

    $dom = new DOMDocument;
    libxml_use_internal_errors(true);
    $dom->loadHTML($html1);

    $list = $dom->getElementsByTagName('title');
    $title = $list->length ? $list->item(0)->textContent : '';

    $comments = array();
    $anotherarray = array();
    $user = array();
    $nameX = array();
    $id = array();

    $h2 = $dom->getElementsByTagName('p');
    for ($i = 0; $i < count($h2); $i++) {
        if ($h2->item($i)->hasAttributes()) {
            foreach ($h2->item($i)->attributes as $attr) {
                $name = $attr->nodeName;
                $value = $attr->nodeValue;
                array_push($anotherarray, $dom->saveHTML($attr));
            }
        }
    }

    for ($counter = 0; $counter < count($anotherarray); $counter++) {
        if (strlen($anotherarray[$counter]) === strlen('class="TweetTextSize js-tweet-text tweet-text"') + 2) {
            if (is_object($dom->getElementsByTagName("p")->item($counter))) {
                if(($dom->getElementsByTagName("p")->item($counter)->textContent) != "")
                {
                array_push($comments, $dom->getElementsByTagName("p")->item($counter)->textContent);
                array_push($user, $counter);
                }
            }
        }
    }

    $h3 = $dom->getElementsByTagName('strong');
    for ($i = 0; $i < count($h3); $i++) {
        if ($h3->item($i)->hasAttributes()) {
            foreach ($h3->item($i)->attributes as $attr1) {
                $name1 = $attr->nodeName;
                $value1 = $attr->nodeValue;
                array_push($id, $dom->saveHTML($attr1));
            }
        }
    }


    for ($counter = 0; $counter < count($id); $counter++) {
        $var = $counter + 1;

        if (strlen($id[$counter]) === strlen('class="fullname show-popup-with-id u-textTruncate"') + 2) {
            if (is_object($dom->getElementsByTagName("strong")->item($counter))) {
                if(($dom->getElementsByTagName("strong")->item($counter)->textContent) != "") {
                array_push($nameX, $dom->getElementsByTagName("strong")->item($counter)->textContent);
                }
            }
        
        }

    }

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

    <title>Analyze twitter comments</title>

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
                                    <h6 class="m-0 font-weight-bold text-primary">Analyze a twitter link</h6>
                                </div>
                                <div class="card-body">
                                    <form action="user_twitter.php" method="POST">

                                        <div class="input-group">
                                            <input type="text" name="link" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary btn-icon-split mx-2" name="Twitter" style="float: right;">
                                                    <span class="icon text-white-50">
                                                        <i class="fab fa-twitter"></i>
                                                    </span>
                                                    <span class="text">Analyze twitter</span>
                                                </button>
                                            </div>
                                        </div>

                                    </form>

                                    <div class="container-fluid">

                                        <!-- Page Heading -->

                                        <!-- DataTales Example -->


                                        <div class="card-body">

                                        </div>
                                    </div>

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
                                        <h6 class="m-0 font-weight-bold text-primary">Twitter post link</h6>
                                    </div>
                                    <div class="card-body">


                                        <a href="<?php echo $_POST['link']; ?>"><?php echo $_POST['link']; ?></a>

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
                            <div class="col-lg-12 mb-4">

                                <!-- Project Card Example -->
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Twitter post title</h6>
                                    </div>
                                    <div class="card-body">


                                        <?php echo $title; ?>

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
                            <div class="col-lg-12 mb-4">

                                <!-- Project Card Example -->
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h6 class="m-0 font-weight-bold text-primary">Twitter post comments</h6>
                                    </div>
                                    <div class="card-body">

                                    <?php
                                    $ceva = 0;
                                    if(count($comments) >=4) $numar = 2;
                                    else $numar = 0;

                                    // $numar = 2;
                                    foreach($comments as $com)
                                    {
                                    if($ceva < 3)
                                    {
                                        $ceva = $ceva+1;    
                                        $text = $com;
                                        $split = preg_split("/[^\w]*([\s]+[^\w]*|$)/", $text, -1, PREG_SPLIT_NO_EMPTY);
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
                                        if($result1)
                                    {
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
                                    }
                                    else
                                    {
                                        $sentiment = "Neutral";
                                    }
                                        ?>

                                        <div class="tab-content" id="myTabContent5">

                                            <div class="tab-pane fade active show" id="tab-2" role="tabpanel" aria-labelledby="product-tab-2">
                                                <div class="review-block">
                                                    <p class="review-text font-italic m-0"><?php echo $com; ?></p>
                                                    <p class="review-text font-italic m-0" style="float: right;">
                                                        <?php 
                                                        if($sentiment == "Positive")
                                                            echo "<span style=\"font-size: 1.75rem;\">
                                                            <span style=\"color: blue;\">Positive
                                                            </span>
                                                          </span>";
                                                        if($sentiment == "Negative")
                                                        echo "<span style=\"font-size: 1.75rem;\">
                                                        <span style=\"color: red;\">Negative
                                                        </span>
                                                      </span>";
                                                        if($sentiment == "Neutral")
                                                        echo "<span style=\"font-size: 1.75rem;\">
                                                        <span style=\"color: green;\">Neutral
                                                        </span>
                                                      </span>";
                                                        ?>
                                                    </p>
                                                    <br>
                                                    <span>posted by</span>
                                                    <span class="text-dark font-weight-bold">
                                                        <?php echo $nameX[$numar];
                                                        $numar = $numar+1;
                                                        ?>
                                                        </span>
                                                </div>
                                            


                                            </div>
                                        </div>
                                        <div class="review-block border-top mt-3 pt-3">

                                        <?php
                                    }
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