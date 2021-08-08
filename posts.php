<?php

session_start();

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

$category_name = isset($_GET['name']) ? $_GET['name'] : false;

//Get page number for offset
$page = isset($_GET['page']) ? $_GET['page'] : 1;
if (!filter_var($page, FILTER_VALIDATE_INT)){
    $page = 1;
}

if ($category_name == false){
    header('Location: index.php');
    exit();
} else {
    //Empty variables
    $found = 0;
    $title = 'Category not found';

    $name = $con->conn->real_escape_string($category_name);

    $sql = 'SELECT * FROM categories WHERE cname = "'.$name.'";';

    $result = $con->conn->query($sql);

    if ($result->num_rows > 0){
        $row = $result->fetch_assoc();
        $category_id = $row['cid'];
        $title = $row['cname'];
        
        $categoryExists = $categoryObj->categoryName($category_id);
        if ($categoryExists != -1){
            $found = 1;
        }

        $numberOfPosts = $categoryObj->totalPosts($category_id);
    }


    if ($found == 1) {
         
        //Show category posts
        $posts = $categoryObj->getPosts($category_id, $page);

                //Creating pagination
            $limit = 15;
            $totalPages = 1;

            if ($numberOfPosts > $limit){
                $totalPages = ceil($numberOfPosts / $limit);

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

    } else {
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
}
?>
<!DOCTYPE html>
<html lang="en" data-theme='light'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#333">

    <meta name="keywords" content="<?php echo $metaInfo['keywords'];?>">
    <meta name="description" content="<?php echo $metaInfo['description']?> ">

    <meta property="og:title" content="<?php echo $title.' - '.$metaInfo['keywords']?> " />
    <meta property="og:description" content="<?php echo $metaInfo['ogDescription']?> " />
    <meta property="og:image" content="<?php echo $metaInfo['domain'].'/'.$metaInfo['ogImage']?> " />
    <meta property="og:url" content="<?php echo $metaInfo['ogUrl']?> " />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $metaInfo['domain']; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo $metaInfo['domain']; ?>/css/dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="<?php echo $metaInfo['domain']; ?>/images/logo.png" type="image/png">
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
        .left{
            background: var(--bg);
            box-shadow:  10px 10px 20px var(--boxShadowd), 
                        -10px -10px 20px var(--boxShadowl);
        }
        .posts .info{
            margin: 0px;
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
            .right .srrba{
                margin-bottom: 10px;
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

    <title><?php echo $title.' - '.$metaInfo['keywords']; ?></title>
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
            if ($posts != 0){
            ?>
            <div class="sr-banner">
                <img src="<?php echo $metaInfo['domain'].'/images/'.$categoryexists; ?>.jpg" alt="banner">
                <div class="sr-bcontent">
                    <a href="<?php echo $metaInfo['domain']; ?>/index.php">Home</a> &gt;
                    <a href="#"><?php echo $categoryExists; ?></a>
                </div>
            </div>
            <div class="srcontent">
                <ul>
                    <?php
                    foreach ($posts as $row){
                        $title = htmlspecialchars($row['ptitle']);
                        $info = strip_tags(substr($row['pcontent'], 0,200));
                        $date = $row['pdate'];
                        $category = $categoryObj->categoryName($row['pcat']);
                        $views = $row['views'];
                        $comments = $post->noComments($row['pid']);
                        $banner = $row['pbanner'];
                        $usrName = $user->userName($row['uid']);

                        //Check if a post is available or not
                        if ($row['available'] != 1){
                            $title = $info = '<i>This post has been deleted.</i>';
                            $banner = 'default.png';
                        }

                        echo '
                            <li class="posts">
                                <div class="d-banner">
                                <img src="'.$metaInfo['domain'].'/banners/'.$banner.'" alt="banner" class="banner">
                                </div>
                                <div class="p-right">
                                    <div class="category">'.$category.'</div>
                                    <div class="comments">'.numberShort($comments).' <i class="fa fa-comments-o" aria-hidden="true"></i></div>
                                    <div class="views">'.numberShort($views).'<i class="fa fa-eye" aria-hidden="true"></i></div>
                                    <div class="post-content">
                                        <div class="intro">
                                            <a href="'.$metaInfo['domain'].'/view-post/'.$row['slug'].'">'.$title.'</a>
                                        <div class="info">
                                        <p>
                                            '.($info).'
                                        </p>
                                        </div>
                                    </div>
                                    <div class="author"><a href="'.$metaInfo['domain'].'/view-profile/'.$user->userName($row['uid']).'">'.$usrName.'</a></div>
                                    <div class="time-stamp">'.$date.'</div>
                                    <a href="'.$metaInfo['domain'].'view-post/'.$row['slug'].'"><button class="read-more">Read more</button></a>
                                </div>
                            </li>
                        ';
                    }
                    ?>
                </ul>
            </div>
            <?php
            } else {
                echo '
                <div style="color: var(--textHM); margin: 30px;">
                Currently there are no posts in this category. Check back later. 
                </div>
                ';
            } 
            ?>
        </div>
        <?php 
        if ($posts != 0){
        ?>
        <div class="right">
            <div class="top">
            <h4 class="h4">Popular posts</h4>
            <?php
                    //Getting top posts of a user
                    $topPosts = $categoryObj->topPosts($category_id, 4);
                    ?>
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
</body>


<?php 
include "includes/footer.php";
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
                window.location.assign('posts.php?name=<?php echo $name;?>&page='+$(this).data('page'));
            });

            $('.srrba').click(function(){
                window.location.assign('<?php echo $metaInfo['domain'];?>/view-post/'+ $(this).data('slug'));
            });
        });
</script>

</html>
