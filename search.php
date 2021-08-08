<?php

session_start();

//Require database connection
require "files/dbconfig.php";
$con = new DBConfig();

//Require class user to get user details
require "files/user.php";
$user = new User();

//Require classs to get post data
require "files/posts.php";
$post = new Posts();

//Including categories files
include "files/categories.php";
$categoryObj = new Categories();

//Include meta information
include "files/meta.php";

//Require functions file
require "includes/functions.php";

//Checking for session
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

$query = isset($_GET['query']) ? $_GET['query'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : 1;

if (!filter_var($page, FILTER_VALIDATE_INT)){
    $page = 1;
}

//Empty variable
$found = $topresults = $totalPages = 0;

if ($query != ''){
    $query = $con->conn->real_escape_string($query);

    
    $limit = 15;
    $offset = $page - 1;
    $totalPages = 1;

    //Counting the number of results
    $countResult = 'SELECT pid FROM posts WHERE ptitle LIKE "%'.$query.'%" OR pcontent LIKE "%'.$query.'%";';
    $countResultstmt = $con->conn->query($countResult);
    $totalResults = $countResultstmt->num_rows;

    //quering results accordingly
    $sql = 'SELECT * FROM posts WHERE ptitle LIKE "%'.$query.'%" OR pcontent LIKE "%'.$query.'%" ORDER BY pdate desc LIMIT '.$offset.', '.$limit.';';
    
    $result = $con->conn->query($sql);

    if ($result->num_rows > 0){

        $results = array();
        while ($row = $result->fetch_assoc()){
            $results[] = $row;
            $found = 1;
        }

        //Creating pagination

        if ($totalResults > $limit){
            $totalPages = ceil($totalResults / $limit);

            if ($page < 1 || $page > $totalPages){
                $page = 1;
            }

            $previous = $page > 1 ? $page - 1 : '#';
            if ($page < $totalPages) {
                $next = $page + 1;
            } else {
                $next = '#';
            }
        }

    } else{
        $found = 2;
    }

    //Creating top articles
    if ($found == 1 && $totalResults > 4){
        $toplimit = 4;
    } else {
        $toplimit = 2;
    }
    $topsql = 'SELECT * FROM posts ORDER BY views, pdate desc LIMIT '.$toplimit;
    
    $topresult = $con->conn->query($topsql);

    if ($topresult->num_rows > 0){

        $topresults = array();
        while ($toprow = $topresult->fetch_assoc()){
            $topresults[] = $toprow;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="images/logo.png" type="image/png">
    <style>
        #main-doc{
            margin: 0px auto;
            width: 1210px;
            display: grid;
            grid-template-rows: 1fr 50px;
            grid-template-columns: 810px 300px;
            grid-column-gap: 100px;
            margin-top: 90px;
        }
        .left, .right{
            margin-top: 40px;
        }
        .left{
            background: var(--bg);
            box-shadow:  10px 10px 20px var(--boxShadowd), 
                        -10px -10px 20px var(--boxShadowl);
        }
        @media only screen and (max-width: 600px){
            #main-doc{
                width: 100%;
                display: block;
                margin-top: 50px;
            }
            .left{
                margin-top: 20px;
            }
            .right{
                height: auto;
                margin-bottom: 15px;
            }
            .right .top:nth-child(2){
                display: none;
            }
            .right .top {
                margin: 0 3%;
                box-shadow: none !important;
            }
            .right .top .h4{
                text-align: left;
            }
        }
    </style>
    <title>Search - <?php echo $metaInfo['keywords'];?></title>
</head>
<body>
    <header>
        <?php
        include 'includes/header.php';
        ?>
    </header>

    <main>
        <div id="main-doc">
            <div class="left">
                <div class="search-page">
                    <form action="search.php" method="get">
                        <input type="search" name="query" placeholder="Search..." value="<?php echo $query;?>"/>
                        <button class="search-page-btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </form>
                </div>
                <div class="srcontent">
                    <ul>
                    <?php
                    if ($found == 1){
                        foreach ($results as $resultvalue){ 
                            $desc = strip_tags(substr($resultvalue['pcontent'], 0, 200));
                            echo '
                                <li class="posts">
                                    <div class="d-banner">
                                    <a href="'.$metaInfo['domain'].'/view-post/'.$resultvalue['slug'].'">
                                        <img src="'.$metaInfo['domain'].'/banners/'.$resultvalue['pbanner'].'" alt="banner" class="banner">
                                    </a>
                                    </div>
                                    <div class="p-right">
                                        <div class="category">'.$categoryObj->categoryName($resultvalue['pcat']).'</div>
                                        <div class="comments">'.$post->noComments($resultvalue['pid']).' <i class="fa fa-comments-o" aria-hidden="true"></i></div>
                                        <div class="views">'.numberShort($resultvalue['views']).'<i class="fa fa-eye" aria-hidden="true"></i></div>
                                        <div class="post-content">
                                            <div class="intro">
                                                <a href="'.$metaInfo['domain'].'/view-post/'.$resultvalue['slug'].'">'.$resultvalue['ptitle'].'
                                                </a>
                                            </div>
                                            <div class="info">
                                                <p>
                                                    '.$desc.'
                                                </p>    
                                            </div>
                                        </div>
                                        <div class="author"><a href="'.$metaInfo['domain'].'/view-profile/'.$user->userName($resultvalue['uid']).'">'.$user->userName($resultvalue['uid']).'</a></div>
                                        <div class="time-stamp">'.$resultvalue['pdate'].'</div>
                                        <a href="'.$metaInfo['domain'].'/view-post/'.$resultvalue['slug'].'"><button class="read-more">Read more</button></a>
                                    </div>
                                </li>
                            ';
                            }
                        } elseif ($found == 2) {
                            echo '
                            <div class="no-res">
                            There are no results matching your query
                            </div>
                            ';
                        } else {
                            echo '
                            <div class="no-res">
                            Search for the posts here
                            </div>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class="right" id="">
                <div class="top">
                <?php if ($topresults){
                    echo '<h4 class="h4">Top Articles</h4>';
                    foreach ($topresults as $topresultrow){
                        echo '
                            <div class="srrba" data-slug="'.$topresultrow['slug'].'">
                                <a href="'.$metaInfo['domain'].'/view-post/'.$topresultrow['slug'].'"><img src="'.$metaInfo['domain'].'/banners/'.$topresultrow['pbanner'].'">
                                    <div class="srrba-t">
                                        <a href="'.$metaInfo['domain'].'/view-post/'.$topresultrow['slug'].'">
                                        '.$topresultrow['ptitle'].'
                                        </a>
                                    </div>
                                </a>
                            </div>
                            ';
                            }
                        }
                        ?>              
                    
                </div>
            </div>
        </div>
    </main>

    <?php
    if ($totalPages > 1){
        echo '<div class="pagination">
                <ul>
                    <li class="previous number" data-page="'.$previous.'">
                        <i class="fa fa-angle-double-left" aria-hidden="true"></i>
                        </li>
                        ';    
                        
        if ($totalPages > 1 && $totalPages < 6){
            
            for ($i = 1; $i <= $totalPages; $i++){
                $active = $page == $i ? 'active' : '';
                echo '
                <li class="number '.$active.'" data-page="'.$i.'">
                    '.$i.'
                </li> 
                ';
            }
        }
        if ($totalPages > 5){
            if ($page == 1 || $page == 2 || $page == 3) {

                for($i = 1; $i < 4; $i++){
                    $active = $page == $i ? 'active' : '';
                    echo '
                        <li class="number '.$active.'" data-page="'.$i.'">
                            '.$i.'
                        </li> 
                ';
                }
                echo '
                        <li class="cont">
                            ...
                        </li>
                ';
                for($i = $totalPages-1; $i <= $totalPages; $i++){
                    
                    $active = $page == $i ? 'active' : '';
                    echo '
                        <li class="number '.$active.'" data-page="'.$i.'">
                            '.$i.'
                        </li> 
                ';
                }

            } elseif ($page <= $totalPages){

                echo '
                <li class="number" data-page="1">
                    1
                </li>
                <li class="cont">
                    ...
                </li>
                ';

                for($i = $totalPages-4; $i <= $totalPages; $i++){
                    $active = $page == $i ? 'active' : '';
                    echo '
                        <li class="number '.$active.'" data-page="'.$i.'">
                            '.$i.'
                        </li> 
                    ';
                }
            }
        }
        echo '
            <li class="next number" data-page="'.$next.'">
                <i class="fa fa-angle-double-right" aria-hidden="true"></i>
            </li>
            </div>
        ';
    }

    ?>
    
    <footer>
        <?php
        include 'includes/footer.php';
        ?>
    </footer>
    <script
    src="https://code.jquery.com/jquery-3.5.1.js"
    integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
    crossorigin="anonymous">
</script>
    <?php

    //Include dark mode script
    include "includes/dark_mode_script.php";
    //Include navigation bar script
    include "includes/nav_toggle_js.php";

    //Include notifications
    include 'load-notifications.php';
    
    ?>

<script>
        $(document).ready(function(){
            $('.number').click(function(){
                window.location.assign('search.php?query=<?php echo $query;?>&page='+$(this).data('page'));
            });

            $('.srrba').click(function(){
                window.location.assign('<?php echo $metaInfo['domain'];?>/view-post/'+ $(this).data('slug'));
            });
        });
</script>
</body>
</html>