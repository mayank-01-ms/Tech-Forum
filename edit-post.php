<?php

$pid = isset($_GET['pid']) ? $_GET['pid'] : false;

if ($pid == false){
    header("Location: index.php");
    exit();
}

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

//Require the functions page
require "includes/functions.php";

//Include meta information
include "files/meta.php";

//Checking for session
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

if ($loggedIn == true){
    $sessionUlvl = $user->userLevel($_SESSION['uid']);
    $permission = $user->userPermissions($sessionUlvl);

    $title = "Post not found";
    $found = 0;

    if (!filter_var($pid, FILTER_VALIDATE_INT)){
        echo '404 Not Found <br />
        <a href="'.$metaInfo['domain'].'">Home page</a>   
        ';
        exit();
    }

    //Fetch post data
    $postData = $post->getPostsInfo($pid);

    if ($postData !== -1){

        //Checking user permission
        $auid = $postData['uid'];

        $available = $postData['available'];
        if ($available == 1){

            if (($permission['post'] == 1) && ($auid == $_SESSION['uid'] || ($sessionUlvl == 4 || $sessionUlvl == 5))){
                    
            $found = 1;
            //Setting up variables
            $title = $postData['title'];

            //Get category id
            $category = $postData['cat']; 
            
            $banner = $postData['banner'];
            $content = $postData['content'];
            $allowCom = $postData['allow-com'];
            }

        }
        
        
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme='light'>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#333">

    <meta name="keywords" content="Edit Post - <?php echo $metaInfo['keywords']; ?>">
    <meta name="description" content="<?php echo $metaInfo['description']?> ">

    <meta property="og:title" content="Edit post - <?php echo $metaInfo['keywords']?> " />
    <meta property="og:description" content="<?php echo $metaInfo['ogDescription']?> " />
    <meta property="og:image" content="<?php echo $metaInfo['ogImage']?> " />
    <meta property="og:url" content="<?php echo $metaInfo['ogUrl']?> " />

    <link rel="stylesheet" href="<?php echo $metaInfo['domain']; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo $metaInfo['domain']; ?>/css/dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="<?php echo $metaInfo['domain']; ?>/images/logo.png" type="image/png">
    <style>
        #mceu_13{
            margin-top: 40px;
        }
        #doc{
            margin-top: 120px;
        }
    </style>
    <script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
    <?php 
    if ($found == 1){
    ?>
    <script>
          tinymce.init({
              selector: "textarea",
              height: 400,
              menubar: false,
              plugins: [
                  "advlist autolink lists link image charmap print preview anchor textcolor",
                  "searchreplace visualblocks code fullscreen",
                  "insertdatetime media table contextmenu paste code wordcount"
              ],
              mobile: {
                  theme: "mobile"
              },
              
              toolbar: "styleselect | bold | italic | alignleft | aligncenter | alignright | alignjustify | bullist | numlist | outdent | indent | link | image",
              content_css: [
                "//fonts.googleapis.com/css?family=Lato:300,300i,400,400i",
                "//www.tiny.cloud/css/codepen.min.css"
                ],
          });
    </script>
    <?php
    }
    ?>
    <title><?php echo $title.' - Edit Post';?> - <?php echo $metaInfo['keywords']; ?></title>
</head>
<body>
    <header>
        <?php
        include "includes/header.php";
        ?>
    </header>
    <div id="doc">
        <?php 
        if ($loggedIn == true){
            if ($found == 1){
        ?>
        <div class="post-content">
            <div class="post-form">
                <div class="pfTitle">
                    <h3>Edit Post</h3>
                </div>
                <form onsubmit="return validate()" action="edit.php?pid=<?php echo $pid;?>" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <div class="p-str">
                        <div class="p-left">
                        <input type="text" name="p-title" id="p-title" value="<?php echo $title;?>" placeholder="Enter Post Title" maxlength="100" onkeyup="checkTitle()" onblur="checkTitle()" />
                        <div class="char">
                            <span id="number">100</span> Charachters left
                        </div>
                        <div class="p-category">
                            <select name="p-cat" id="p-cat">
                                <option value="0">Category</option>
                            <?php 
                            $categories = $categoryObj->getCategories();
                            
                            foreach ($categories as $row){
                                if ($category == $row['cid']){
                                    $selected = ' selected';
                                } else {
                                    $selected = '';
                                }
                                echo '<option value="'.$row['cid'].'"'.$selected.'>'.$row['cname'].'</option>';
                            }
                            ?>
                            </select>
                        </div>
                        <div class="p-body">
                            <textarea name="p-body" id="p-body" style="padding: 10px;"><?php echo $content; ?></textarea>
                        </div>
                        </div>
                        <div class="p-right">
                            <div class="p-b-pre">
                                <span>No image selected</span>
                                <img src="<?php echo $metaInfo['domain'].'/banners/'.$banner; ?>" id="p-b-p">
                            </div>
                            <div class="p-banner">
                                <input type="file" name="p-banner" id="p-banner" accept="image/*" style="display: none;" />
                                <label for="p-banner">
                                    <span class="material-icons">
                                        add_photo_alternate
                                    </span>
                                    Update Banner
                                </label>
                            </div>
                            <div class="pa-options">
                                <h4>Optional</h4>
                                <div class="pa-o1">
                                    <ul>
                                        <li>
                                            <input type="checkbox" name="p-com" id="dc" value="disabled"<?php
                                            if ($allowCom == 'disabled'){
                                                echo 'checked';
                                            } ?> /><label for="dc"> Disable Comments</label> 
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <button class="p-button" type="submit" name="edit-post">Post</button>
                </form>
            </div>
        </div>
        <?php
            }
            else if($available != 1){
                echo '
                <div style="color: var(--textM)">
                You cannot edit deleted post
                </div>';
            } else {
                echo '
                <div style="color: var(--textM)">
                You cannot edit this post
                </div>';
            }
        }
        else {
            echo '
            <div style="color: var(--textM)">
            You are not logged in.
            </div>';
        }
        ?>
    </div>
</body>
    <script
        src="https://code.jquery.com/jquery-3.5.1.js"
        integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
        crossorigin="anonymous">
    </script>

    <script>
        function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
            $('#p-b-p').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        $("#p-banner").change(function() {
            readURL(this);
        });
    </script>
    <script>

        function checkTitle(){

            var text = document.getElementById("p-title").value;
            var length = text.length;
            document.getElementById("number").innerHTML = 100-length;

        }

        function validate(){
            var title = document.getElementById("p-title").value;
            var cat = document.getElementById("p-cat").value;
            var banner = document.getElementById("p-banner");
            var body = document.getElementById("p-body").value;

            if (title.trim().length<10){
                alert("Title must be atleast 10 charachters");
                return false;
            }
            else
            if (cat == 0){
                alert("Please select a category");
                return false;
            }
            else
            if (body.trim().length<100){
                alert("Content must of at least 100 charachters");
                return false;
            }

            return true;
        }

    </script>

    <?php

    //Include dark mode script
    include "includes/dark_mode_script.php";
    //Include navigation bar script
    include "includes/nav_toggle_js.php";

    //Include notifications
    include 'load-notifications.php';

    ?>
</html>