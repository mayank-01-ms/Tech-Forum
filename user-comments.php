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
include "./files/categories.php";
$categoryObj = new Categories();

//Include meta information
include "files/meta.php";

//Checking for session
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

$uid = isset($_GET['uid']) ? $_GET['uid'] : false;

//Get page number for offset
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if (!filter_var($page, FILTER_VALIDATE_INT)){
    $page = 1;
}

if ($uid == false){
    header("Location: index.php");
    exit();
} else {

    //Creating empty variable set
    $found = 0;
    $title = "User not found";
    $profileImage = 'profile.png';

    if (!filter_var($uid, FILTER_VALIDATE_INT)){
        echo '<html>
            <head>
                <title>404 Not Found</title>
                <style>
                    *{
                        margin: 0;
                        padding: 0;
                    }
                </style>                
            <link rel="icon" href="'.$metaInfo ['domain'].'/images/logo.png" type="image/png">
            </head>
            <body>';
    echo '<iframe src="'.$metaInfo['domain'].'/error.php" frameBorder="0" width="100%" height="100%"></iframe>'  ;
    echo '</body></html>';
    exit();
    } else {
        
        //Fetch user data
        $details = $user->getUserDetails($uid);

        if ($details !== -1){

            $found = 1;
            $usrName = $title = $user->userName($uid);
            $userLevel = $user->userLevel($uid);
            $userGroup = $user->userGroup($userLevel);

            //user information
            $profileImage = $details['image'];
            if ($profileImage === 0){
                $profileImage = 'profile.png';
            }
            $igLink = $details['ig'] != '0' ? $details['ig'] : 'javascript:user()';
            $twLink = $details['tw'] != '0' ? $details['tw'] : 'javascript:user()';
            $fbLink = $details['fb'] != '0' ? $details['fb'] : 'javascript:user()';
            $lnLink = $details['ln'] != '0' ? $details['ln'] : 'javascript:user()';

            $numberOfComments = $user->userCommentsNo($uid); 
            
            if ($numberOfComments > 0) {
                $comments = $user->userComments($uid, $page);
                if ($comments == 0){
                    $comments = $user->userComments($uid);
                    $page = 1;
                }
            }

            //creating pagination
            $limit = 15;
            $totalPages = 1;
            
            if ($numberOfComments > $limit){
                $totalPages = ceil($numberOfComments / $limit);

                if ($page < 1 || $page > $totalPages){
                    $page = 1;
                }

                $previous = $page > 1 ? $page - 1 : '#';
                if ($page < $totalPages){
                    $next = $page + 1;
                } else {
                    $next = '#';
                }
            }
        }
    }
}

if ($found == 0){
    echo '<html>
            <head>
                <title>404 Not Found</title>
                <style>
                    *{
                        margin: 0;
                        padding: 0;
                    }
                </style>                
            <link rel="icon" href="'.$metaInfo ['domain'].'/images/logo.png" type="image/png">
            </head>
            <body>';
    echo '<iframe src="'.$metaInfo['domain'].'/error.php" frameBorder="0" width="100%" height="100%"></iframe>'  ;
    echo '</body></html>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#333">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="theme-color" content="#333">

    <meta name="keywords" content="<?php echo $metaInfo['keywords'];?>, comments, blog">
    <meta name="description" content="<?php echo $metaInfo['description']?> ">

    <meta property="og:title" content="<?php echo $title; ?> - Comments - <?php echo $metaInfo['keywords']?> " />
    <meta property="og:description" content="<?php echo $metaInfo['ogDescription']?> " />
    <meta property="og:image" content="<?php echo $metaInfo['domain'].'/profileImages/'.$profileImage; ?> " />
    <meta property="og:url" content="<?php echo $metaInfo['ogUrl']?> " />
    
    <link rel="stylesheet" href="<?php echo $metaInfo['domain']; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo $metaInfo['domain']; ?>/css/dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="<?php echo $metaInfo['domain']; ?>/images/logo.png" type="image/png">
    <style>
        #main-doc{
            margin: 0px auto;
            width: 1210px;
            display: grid;
            grid-template-rows: 1fr;
            grid-template-columns: 810px 300px;
            grid-column-gap: 100px;
            margin-top: 100px;
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
                margin-top: 60px;
            }
            .left{
                margin-top: 20px;
                box-shadow: none;
            }
            .right{
                display: none;
            }
        }
    </style>
    <title><?php echo $title; ?> - Comments - <?php echo $metaInfo['keywords']; ?></title>
</head>
<body>
    <header>
        <?php
        include "includes/header.php";
        ?>
    </header>

    <main>
        <div id="main-doc">  
            <div class="left">
                <?php 
                if ($found == 1){
                ?>
                <div class="srcontent">
                    <div class="srcontent-name">
                        <span><?php echo $usrName; ?></span>
                        <span>Comments</span>
                    </div>
                    <?php 
                    if ($numberOfComments > 0){
                    ?>
                    <ul>
                        <?php
                        for($i = 0; $i < count($comments); $i++){
                            $postData = $post->getPostsInfo($comments[$i]['pid']);
                            $ptitle = $postData['title'];
                            if ($postData['available'] != 1){
                                $ptitle = '<i>This Post has been deleted</i>';
                                $comments[$i]['cbody'] = '<i>The post was deleted</i>';
                            }

                            $checkAvailableComment = $comments[$i]['available'];
                            if ($checkAvailableComment == -1){
                                $comments[$i]['cbody'] = '<i>This comment was deleted</i>';
                            }
                            echo '
                                <li class="user-com">
                                    <div class="com-des">
                                        <p>
                                        '.$comments[$i]['cbody'].'
                                        </p>
                                    </div>
                                    <div class="post-des">
                                        <a href="'.$metaInfo['domain'].'/view-post/'.$postData['slug'].'">'.$ptitle.'</a>
                                    </div>
                                    <div class="dt-des">
                                        '.$comments[$i]['cdate'].'
                                    </div>
                                </li>
                            ';

                        }
                        ?>
                        
                    </ul>
                    <?php
                    }
                    else {
                        echo '<div style="color: var(--textHM); margin: 30px;">
                        There are no comments by this user
                        </div> 
                        ';
                    }
                    ?>
                </div>
                <?php
                } else {
                    echo '
                    <div style="color: var(--textHM); margin: 30px;">
                    User not found
                    </div>
                    ';
                }            
                ?>
            </div>
            <?php 
            if ($found === 1){
            ?>
            <div class="right">
                <div class="top aimo">
                    <h4 class="h4">User Info</h4>
                    <div class="a-info">
                        <div class="a-inf">
                            <div class="avtar">
                                <a href="<?php echo $metaInfo['domain'].'/view-profile/'.$user->userName($uid);?>"><img src="<?php echo $metaInfo['domain'].'/profileImages/'.$profileImage; ?>" alt="Profile image"></a>
                                <?php
                                if ($userLevel == 4 || $userLevel == 5){
                                ?>
                                <div class="ad-pi">
                                    <span><i class="fa fa-star" aria-hidden="true"></i></span>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                            <div class="a-inf-n">
                            <div class="a-name">
                                    <a href="<?php echo $metaInfo['domain'].'/view-profile/'.$user->userName($uid);?>"><?php echo $usrName; ?></a>
                                </div>
                                <div class="a-lvl">
                                    <?php echo $userGroup; ?>
                                </div>
                            </div>
                        </div>
                        <div class="a-sm">
                            <span>Follow me on my social media</span>
                            <ul class="a-sm-l">
                                <li><a href="<?php echo $fbLink; ?>"><i title="facebook profile" class="fa fa-facebook"></i></a></li>
                                <li><a href="<?php echo $twLink; ?>"><i title="Twitter profile" class="fa fa-twitter"></i></a></li>
                                <li><a href="<?php echo $igLink; ?>"><i title="Instagram profile" class="fa fa-instagram"></i></a></li>
                                <li><a href="<?php echo $lnLink; ?>"><i title="Linkedin profile" class="fa fa-linkedin"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="top">
                    <?php
                    //Getting top posts of a user
                    $topPosts = $user->userTopPosts($uid, 2);
                    ?>
                    <h4 class="h4">Popular Posts of <?php echo $usrName;?></h4>
                    <ul class="news">
                        <?php 
                        foreach ($topPosts as $row){
                            echo '
                            <div class="srrba" data-slug="'.$row['slug'].'">
                                <a href="'.$metaInfo['domain'].'/view-post/'.$row['slug'].'"><img src="'.$metaInfo['domain'].'/banners/'.$row['pbanner'].'">
                                    <div class="srrba-t">
                                        <p>
                                            '.htmlspecialchars($row['ptitle']).'
                                        </p>
                                    </div>
                                </a>
                            </div>
                            ';
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <?php
            }
            ?>
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
        include "includes/footer.php";
        ?>
    </footer>

</body>

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
                window.location.assign('user-comments.php?uid=<?php echo $uid; ?>&page='+$(this).data('page'));
            });
            $('.srrba').click(function(){
                window.location.assign('<?php echo $metaInfo['domain'];?>/view-post/'+ $(this).data('slug'));
            });
        });
    </script>
    
    <script>
        function user(){
            alert('Not set by user');
        }
    </script>
</html>
