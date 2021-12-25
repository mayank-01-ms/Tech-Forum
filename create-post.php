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

//Require the functions page
require "includes/functions.php";

//Include meta information
include "files/meta.php";

//Checking for session
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

$canPost = 0;

if ($loggedIn == true){
    $userlvl = $user->userLevel($_SESSION['uid']);
    $permission = $user->userPermissions($userlvl);
    $canPost = $permission['post'];
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

    <meta property="og:title" content="Create post - <?php echo $metaInfo['keywords']?> " />
    <meta property="og:description" content="<?php echo $metaInfo['ogDescription']?> " />
    <meta property="og:image" content="<?php echo $metaInfo['domain'].'/'.$metaInfo['ogImage']?> " />
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    <?php 
    if ($canPost == 1){
    ?>
    <script src="https://cdn.tiny.cloud/1/fc0qclzntxn2ot76rapk4e9qnuv15m4heqig46zd523nlu6q/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
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
    <title>Create Post - <?php echo $metaInfo['keywords']; ?></title>
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
            if ($canPost == 1){
        ?>
        <div class="post-content">
            <div class="post-form">
                <div class="pfTitle">
                    <h3>Create New Post</h3>
                </div>
                <form onsubmit="return validate()" action="post.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <div class="p-str">
                        <div class="p-left">
                        <input type="text" name="p-title" id="p-title" placeholder="Enter Post Title" maxlength="100" onkeyup="checkTitle()" onblur="checkTitle()" />
                        <div class="char">
                            <span id="number">100</span> Charachters left
                        </div>
                        <div class="p-category">
                            <select name="p-cat" id="p-cat">
                                <option value="0">Category</option>
                            <?php 
                            $categories = $categoryObj->getCategories();
                            
                            foreach ($categories as $row){
                                echo '<option value="'.$row['cid'].'">'.$row['cname'].'</option>';
                            }
                            ?>
                            </select>
                        </div>
                        <div class="p-body">
                            <textarea name="p-body" id="p-body" cols="" rows="" style="padding: 10px;"></textarea>
                        </div>
                        </div>
                        <div class="p-right">
                            <div class="p-b-pre">
                                <span>No image selected</span>
                                <img src="#" id="p-b-p">
                            </div>
                            <div class="p-banner">
                                <input type="file" name="p-banner" id="p-banner" accept="image/*" style="display: none;" />
                                <label for="p-banner">
                                    <span class="material-icons">
                                        add_photo_alternate
                                    </span>
                                    Upload Banner
                                </label>
                            </div>
                            <div class="pa-options">
                                <h4>Optional</h4>
                                <div class="pa-o1">
                                    <ul>
                                        <li>
                                            <input type="checkbox" name="p-com" id="dc" value="disabled" /> 
                                            <label for="dc"> Disable Comments </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <button class="p-button" name="cpbtn" type="submit">Post</button>
                </form>
            </div>
        </div>
        <?php
            }
            else {
                echo '
                <div style="color: var(--textM)">
                You are not allowed to post
                </div>';
            }
        }
        else {
            echo '
            <div style="color: var(--textHM)">
            You are not logged in.
            </div>';
        }
        ?>
    </div>

</body>

<script
    src="https://code.jquery.com/jquery-3.5.0.js"
    integrity="sha256-r/AaFHrszJtwpe+tHyNi/XCfMxYpbsRg2Uqn0x3s2zc="
    crossorigin="anonymous"></script>
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
            if (banner.files.length == 0){
                alert("No banner slected");
                return false;
            }
            else
            if (body.trim().length<50){
                alert("Content must of at least 50 charachters");
                return false;
            }
            else 
            if (banner.file.size>1000000){
                alert("File size greater than 1 MB. Please select file smaller than 1 MB");
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
