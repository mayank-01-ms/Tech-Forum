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

//Include functions
include 'includes/functions.php';

//Checking for session
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

$uid = isset($_GET['uid']) ? $_GET['uid'] : false;

$name = isset($_GET['name']) ? $_GET['name'] : false;

if ($uid == false && $name == false){
    header("Location: index.php");
    exit();
} elseif ($name != false){
    $name = $con->conn->real_escape_string($name);

    $sql = 'SELECT * FROM users WHERE uname = "'.$name.'" LIMIT 1;';

    $result = $con->conn->query($sql);

    if ($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $uid = $row['uid'];
        $found = 1;
    }
    else{
        $found = 0;
    }
}

if ($uid != false){

    //Creating empty variable set
    $found = 0;
    $title = "User not found";
    $profileImage = 'profile.png';
    $description = $metaInfo['ogDescription'];

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
            $firstName = $details['firstName'];
            $lastName = $details['lastName'];
            $profileImage = $details['image'];
            if ($profileImage === 0){
                $profileImage = 'profile.png';
            }
            $dor = $details['dor'];
            $dob = $details['dob'];
            $city = $details['city'];
            $interests = $details['interests'];
            $description = substr($details['interests'], 0, 100);
            $lastActive = $details['lastActive'];
            
            $igLink = $details['ig'] != '0' ? $details['ig'] : 'javascript:user()';
            $twLink = $details['tw'] != '0' ? $details['tw'] : 'javascript:user()';
            $fbLink = $details['fb'] != '0' ? $details['fb'] : 'javascript:user()';
            $lnLink = $details['ln'] != '0' ? $details['ln'] : 'javascript:user()';
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
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#333">

    <meta name="keywords" content="<?php echo $metaInfo['keywords'];?>">
    <meta name="description" content="<?php echo $metaInfo['description']?> ">

    <meta property="og:title" content="<?php echo $usrName.' - Profile';?>" />
    <meta property="og:description" content="<?php echo $description?> " />
    <meta property="og:image" content="<?php echo $metaInfo['domain'].'/profileImages/'.$profileImage ;?> " />
    <meta property="og:url" content="<?php echo $metaInfo['ogUrl']?>" />
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
            grid-template-rows: 1fr 50px;
            grid-template-columns: 810px 300px;
            grid-column-gap: 100px;
            margin-top: 90px;
        }
        .left, .right{
            margin-top: 40px;
        }
        .right{
            height: auto;
        }
        .pr-wrap{
            height: 500px !important;
        }
        .vp-aim{
            box-shadow: ;
        }

        @media only screen and (max-width: 600px){
            #main-doc{
                width: 100%;
                display: block;
                margin-top: 50px;
            }
            .right{
                height: 250px;
            }
            .right .top:nth-child(2){
                display: none;
            }
            .left{
                margin-top: 20px;
            }
        }
    </style>
    <title><?php echo $title; ?> - View profile - <?php echo $metaInfo['keywords']; ?></title>
</head>
<body>
    <header>
        <?php
        include "includes/header.php";
        ?>
    </header>

    <main>
        <?php
        if ($found === 1){
        ?>
        <div id="main-doc">
            <div class="left">
                <div class="pr-wrap">
                    <h3 style="margin-bottom: 20px; color: var(--textH);">Personal Information</h3>
                    <div class="pr-ta">
                        <div class="prow">
                            <span>Name</span>
                            <span><?php echo $firstName.' '.$lastName; ?></span>
                        </div>
                        <div class="prow">
                            <span>uid</span>
                            <span><?php echo $uid; ?></span>
                        </div>
                        <div class="prow">
                            <span>Last Active</span>
                            <span><?php echo $lastActive; ?></span>
                        </div>
                        <div class="prow">
                            <span>Date of registeration</span>
                            <span><?php echo $dor; ?></span>
                        </div>
                        <div class="prow">
                            <span>DOB</span>
                            <span><?php echo $dob; ?></span>
                        </div>
                        <div class="prow">
                            <span>Current City</span>
                            <span><?php echo $city; ?></span>
                        </div>
                        <div class="prow">
                            <span>Interests</span>
                            <span><?php echo nl2br($interests); ?>
                            </span>
                        </div>
                        <br/><br />
                        <div class="pr-opt">
                            <?php 
                            if ($loggedIn == true && ($uid == $_SESSION['uid'])){
                            ?>
                            <div class="p-edit" style="float: right;">
                                <a href="<?php echo $metaInfo['domain']; ?>/edit-profile.php">Edit</a>
                            </div>
                            <?php
                            }
                            if ($loggedIn == true && ($_SESSION['uid'] != $uid)
                            && ($user->userLevel($_SESSION['uid']) == 4 || $user->userLevel($_SESSION['uid']) == 5)){
                            ?>
                            <div class="prow-mu" style="float: right;">
                                Manage user
                                <div class="mu-o">
                                    <span>
                                        <a href="<?php echo $metaInfo['domain'].'/admin/ban_user.php?uid='.$uid;?>">Ban</a>
                                        &nbsp;
                                    </span>
                                    <span>
                                        <a href="<?php echo $metaInfo['domain'].'/admin/change_ug.php?uid='.$uid;?>">Change User level</a>
                                    </span>
                                </div>
                            </div>
                            <?php 
                            }
                            ?>
                        </div>
                    </div>
                    <div class="pr-tb">
                        <div class="ptbl">
                            <a href="<?php echo $metaInfo['domain']; ?>/user-posts.php?uid=<?php echo $uid; ?>"><?php echo numberShort($user->userPostsNo($uid)); ?> Posts</a>
                        </div>
                        <div class="ptbl">
                            <a href="<?php echo $metaInfo['domain']; ?>/user-comments.php?uid=<?php echo $uid; ?>"><?php echo numberShort($user->userCommentsNo($uid)); ?> Comments</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right vp-top">
                <div class="aimo vp-aim">
                    <h4 class="h4"><?php echo $usrName; ?>'s Profile</h4>
                    <div class="a-info">
                        <div class="a-inf">
                            <div class="avtar">
                                <a href="<?php echo $metaInfo['domain']; ?>/view-profile/<?php echo $user->userName($uid);?>"><img src="<?php echo $metaInfo['domain']; ?>/profileImages/<?php echo $profileImage; ?>" alt="Profile Image"></a>
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
                                    <a href="<?php echo $metaInfo['domain']; ?>/view-profile/<?php echo $user->userName($uid);?>"><?php echo $usrName; ?></a>
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
                    <ul class="news">
                        <?php 
                        if ($topPosts != 0){

                            echo '
                            <h4 class="h4">Top Articles of '.$usrName.'</h4>                        
                            ';
                            
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
                        } 
                        ?>
                </div>
            </div>
        </div>

        <?php
        }
        else {
            echo '
            <div id="main-doc" style="color: var(--textHM); margin-top: 120px; display: block;">
            User Not found. <br />
            Click <a href="'.$metaInfo['domain'].'" style="display: inline;">here</a> to go to home page.
            </div>
            ';
        }
        ?>
    </main>
    
    <footer>
        <?php
        include 'includes/footer.php';
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
        function user(){
            alert('Not set by user');
        }
        $(document).ready(function(){
            $('.srrba').click(function(){
                window.location.assign('<?php echo $metaInfo['domain'];?>/view-post/'+ $(this).data('slug'));
            });
        });
    </script>
</html>