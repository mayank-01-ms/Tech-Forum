<?php
session_start();
//Checking for session
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

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
//Creating object for category
$categoryObj = new Categories();

//Require functions file
require "includes/functions.php";

// Include meta information
include "files/meta.php";

//Array to store all error messages
$error = array();

//Fetching featured posts data
$featuredQuery = "SELECT posts.pid, posts.pdate, posts.pbanner, posts.uid, posts.slug, featured.newTitle 
                FROM posts INNER JOIN featured ON posts.pid = featured.pid
                WHERE featured.position = 1
                ORDER BY featured.dof desc
                LIMIT 7";
$featuredResult = $con->conn->query($featuredQuery);

if ($featuredResult->num_rows > 0){
    //This variable contains results to be dispalyed.
    $featuredArray = array();
    //Fetching and storing items onw by one
    while ($featuredRow = $featuredResult->fetch_assoc()){
        $featuredArray[] = $featuredRow;
    }
}
else {
    $error[] = "Cannot fetch featured data";
}

//Fetching data of main posts
$mainQuery = "SELECT * 
            FROM posts INNER JOIN featured ON posts.pid = featured.pid
            WHERE featured.position = 2
            ORDER BY featured.dof desc
            LIMIT 15";
$mainResult = $con->conn->query($mainQuery);

if ($mainResult->num_rows > 0){
    //Thus variable contains results to be dispalyed.
    $mainArray = array();
    while ($mainRow = $mainResult->fetch_assoc()){
        $mainArray[] = $mainRow;
    }
}
else {
    $error[] = "Cannot fetch main data";
}

//Fetching data for news section
$newsQuery = "SELECT posts.pid, posts.slug, featured.newTitle 
            FROM posts INNER JOIN featured ON posts.pid = featured.pid
            WHERE featured.position = 3
            ORDER BY featured.dof desc
            LIMIT 5";
$newsResult = $con->conn->query($newsQuery);
if ($newsResult->num_rows > 0){
    //Thus variable contains results to be dispalyed.
    $newsArray = array();
    while ($newsRow = $newsResult->fetch_assoc()){
        $newsArray[] = $newsRow;
    }
}
else {
    $error[] = "Cannot fetch news data";
}

?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#333">

    <meta name="keywords" content="<?php echo $metaInfo['keywords']; ?>">
    <meta name="description" content="<?php echo $metaInfo['description']?> ">

    <meta property="og:title" content="Home Page - <?php echo $metaInfo['keywords']?> " />
    <meta property="og:description" content="<?php echo $metaInfo['ogDescription']?> " />
    <meta property="og:image" content="<?php echo $metaInfo['domain'].'/'.$metaInfo['ogImage']?> " />
    <meta property="og:url" content="<?php echo $metaInfo['ogUrl']?> " />

    <link rel="stylesheet" href="<?php echo $metaInfo ['domain']; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo $metaInfo ['domain']; ?>/css/dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="<?php echo $metaInfo ['domain']; ?>/images/logo.png" type="image/png">

    <script
    src="https://code.jquery.com/jquery-3.5.1.js"
    integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
    crossorigin="anonymous">
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.theme.min.css">

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.0.0-beta.3/assets/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.0.0-beta.3/assets/owl.theme.default.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.0.0-beta.3/owl.carousel.min.js"></script>
    

    <style>
        @media only screen and (max-width: 600px){
            .top:nth-child(1){
            display: none;
            }
            .right{
                height: auto;
            }
            .right .top{
                background: var(--bg);
                box-shadow: none;
                margin-bottom: 0px;
            }
        }
    </style>

    <title>Home Page - <?php echo $metaInfo['keywords'];?></title>
</head>
<body>

    <header>
        <?php
        include "includes/header.php";
        ?>
    </header>
    <div id="main-doc">
        <div class="featured owl-carousel">
        <?php 
        
        foreach ($featuredArray as $frow){
            $profileImage = $user->profileImage($frow['uid']) ?? 'profile.png';
            echo '
                <div class="n" data-slug="'.$frow['slug'].'">
                    <a href="'.$metaInfo['domain'].'/view-post/'.$frow['slug'].'">
                        <img src="'.$metaInfo['domain'].'/banners/'.$frow['pbanner'].'" alt="banner">
                        <div class="contentx">
                            <div class="ctitle">
                                <a href="'.$metaInfo['domain'].'/view-post/'.$frow['slug'].'">
                                    '.$frow['newTitle'].'
                                </a>
                            </div>
                            <div class="csecond">
                                <div class="cpav">
                                    <a href="'.$metaInfo['domain'].'/view-profile/'.$user->userName($frow['uid']).'"><img src="'.$metaInfo['domain'].'/profileImages/'.$profileImage.'" alt="profile picture"></a>
                                </div>
                                <div class="cana">
                                    <a href="'.$metaInfo['domain'].'/view-profile/'.$user->userName($frow['uid']).'">'.$user->userName($frow['uid']).'</a>
                                </div>
                                <div class="cts">
                                    <i class="fa fa-history" aria-hidden="true"></i>
                                    '.timeIndex($frow['pdate']).' ago
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            ';
        }

        ?>
                         
        </div>
        <div class="left">
            <div class="post">
                <h3>Latest Posts</h3>
                <ul>
                    <?php

                    foreach ($mainArray as $mrow){
                        echo '
                            <li class="posts">
                                <div class="d-banner">
                                <a href="'.$metaInfo['domain'].'/view-post/'.$mrow['slug'].'">
                                    <img src="'.$metaInfo['domain'].'/banners/'.$mrow['pbanner'].'" alt="banner" class="banner">
                                </a>
                                </div>
                                <div class="p-right">
                                    <div class="category">'.$categoryObj->categoryName($mrow['pcat']).'</div>
                                    <div class="comments">'.$post->noComments($mrow['pid']).' <i class="fa fa-comments-o" aria-hidden="true"></i></div>
                                    <div class="views">'.numberShort($mrow['views']).'<i class="fa fa-eye" aria-hidden="true"></i></div>
                                    <div class="post-content">
                                        <div class="intro">
                                            <a href="'.$metaInfo['domain'].'/view-post/'.$mrow['slug'].'">'.$mrow['newTitle'].'
                                            </a>
                                        </div>
                                        <div class="info">
                                            <p>
                                                '.$mrow['description'].'
                                            </p>    
                                        </div>
                                    </div>
                                    <div class="author"><a href="'.$metaInfo['domain'].'/view-profile/'.$user->userName($mrow['uid']).'">'.$user->userName($mrow['uid']).'</a></div>
                                    <div class="time-stamp">'.$mrow['pdate'].'</div>
                                    <a href="'.$metaInfo['domain'].'/view-post/'.$mrow['slug'].'"><button class="read-more">Read more</button></a>
                                </div>
                            </li>
                        ';
                    }

                    ?>
                </ul>
            </div>
            <div class="lm">
                <button class="load-more" id="load-more">Load more</button>
            </div>
        </div>
        <div class="right">
            <div class="top">
                <h4 class="h4">Trending news</h4>
                <ul class="news">
                    
                <?php

                foreach ($newsArray as $nrow){
                    echo '
                        <li>
                            <a href="'.$metaInfo['domain'].'/view-post/'.$nrow['slug'].'">
                                '.$nrow['newTitle'].'
                            </a>
                        </li>
                    ';
                } 
                 
                ?>
                </ul>
            </div>
            <div class="top">
                <h4 class="h4">Latest Smartphones</h4>
                <ul class="sps">
                    <li class="sps-i">
                        <a href=""><img src="<?php echo $metaInfo['domain']; ?>/images/iph.jpg" alt="smartphone"></a>
                        <div class="specs">
                            <h4><a href="">Apple</a></h4>
                            <ul>
                                <li><i class="fa fa-laptop" aria-hidden="true"></i> Apple A13</li>
                                <li><i class="fa fa-camera" aria-hidden="true"></i> 12 MP</li>
                                <li><i class="fa fa-battery-three-quarters" aria-hidden="true"></i> 2000 mAh</li>
                                <li><i class="fa fa-bolt" aria-hidden="true"></i> No fast charging</li>
                            </ul>
                        </div>
                    </li>
                    <li class="sps-i">
                        <a href=""><img src="<?php echo $metaInfo['domain']; ?>/images/sam.jpg" alt="smartphone"></a>
                        <div class="specs">
                            <h4><a href="">Samsung</a></h4>
                            <ul>
                                <li><i class="fa fa-laptop" aria-hidden="true"></i> Exynos</li>
                                <li><i class="fa fa-camera" aria-hidden="true"></i> 32 MP</li>
                                <li><i class="fa fa-battery-three-quarters" aria-hidden="true"></i> 4000 mAh</li>
                                <li><i class="fa fa-bolt" aria-hidden="true"></i> Fast charging</li>
                            </ul>
                        </div>
                    </li>
                    <li class="sps-i">
                        <a href=""><img src="<?php echo $metaInfo['domain']; ?>/images/rn7p.jpg" alt="smartphone"></a>
                        <div class="specs">
                            <h4><a href="">Redmi Note 7 Pro</a></h4>
                            <ul>
                                <li><i class="fa fa-laptop" aria-hidden="true"></i>Snapdragon 675</li>
                                <li><i class="fa fa-camera" aria-hidden="true"></i> 48 MP Dual camera</li>
                                <li><i class="fa fa-battery-three-quarters" aria-hidden="true"></i> 4000 mAh</li>
                                <li><i class="fa fa-bolt" aria-hidden="true"></i> 18W fast charging</li>
                            </ul>
                        </div>
                    </li>
                    <li class="sps-i">
                        <a href=""><img src="<?php echo $metaInfo['domain']; ?>/images/rn9p.jpg" alt=""></a>
                        <div class="specs">
                            <h4><a href="">Redmi Note 9 Pro</a></h4>
                            <ul>
                                <li><i class="fa fa-laptop" aria-hidden="true"></i>Mediatek</li>
                                <li><i class="fa fa-camera" aria-hidden="true"></i> 64 MP Quad camera</li>
                                <li><i class="fa fa-battery-three-quarters" aria-hidden="true"></i> 4000 mAh</li>
                                <li><i class="fa fa-bolt" aria-hidden="true"></i> 33W Fast charging</li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
            <button class="more">More</button>
        </div>
    </div>
    <footer>
        <?php
        include "includes/footer.php";
        ?>
    </footer>
    <script type="text/javascript">

        function hamchange(){
            var hmc = document.getElementById('menu-btn').checked;
            return hmc;
        }

        $(document).ready(function() {
            $('.owl-carousel').owlCarousel({
                autoplay: true,
                autoWidth: true,
                dots: false,
                loop: true,
                autoplayTimeout: 1000,
                nav: true,
                responsive: {
                    0: {
                        items: 1,
                        autoWidth: false,
                        margin: -20,
                        stagePadding: 30,
                        autoplayTimeout: 2500
                    },
                    600: {
                        items: 4, 
                        margin: 4,
                        autoplayTimeout: 3000
                    }
                }
            });

            $("#menu-btn").click(function(){                
                var hmc = hamchange();
                if (hmc == true){
                    $('nav').show();
                } else {
                    $('nav').hide();
                }
            });

            $(".notification").click(function(){
                $(".notification-area").slideToggle();
            });

            $('.n').click(function(){
                window.location.assign('<?php echo $metaInfo['domain'];?>/view-post/'+ $(this).data('slug'));
            });

            let counter = "hj";
            $("#load-more").click(function(){
                $.ajax({
                    url: <?php echo '"'.$metaInfo['domain']?>/loadPosts.php",
                    type: 'post',
                    data: {
                        counter: counter,
                        action: "load"
                    },
                    beforeSend: function(){
                        $("#load-more").html("Loading...");
                        $(".empty-post-alert").hide();
                    },
                    success: function(response){
                        $("#load-more").html("load more");
                        counter += 10;
                        if (response != 0){
                            $(".post ul").append(response);
                        } else {
                            let div = document.createElement("div");
                            div.classList.add("empty-post-alert");
                            div.style.textAlign = "center";
                            div.style.margin = "20px 0";
                            div.style.background = "transparent";
                            div.style.color = "var(--textH)";
                            div.innerHTML = "Oops !! No more posts to show";
                            $(".post").append(div);
                            console.log(response)
                        }
                    }
                })
            });

        });
    </script>
        <?php

        //Include dark mode script
        include "includes/dark_mode_script.php";
        
        //Include notifications
        include 'load-notifications.php';
        ?>
</body>
</html>
