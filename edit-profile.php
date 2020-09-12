<?php

session_start();

//Require database connection
require "files/dbconfig.php";
$con = new DBConfig();

//Require class user to get user details
require "files/user.php";
$user = new User();

//Including categories files
include "files/categories.php";
$categoryObj = new Categories();

//Require the functions page
require "includes/functions.php";

//Include meta information
include "files/meta.php";

//Creating empty variable 
$continue = 0;

//Checking for session
$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;
if ($loggedIn == false){
    header('Location: index.php');
}
if ($loggedIn == true){

    $uid = $_SESSION['uid'];

    if ($uid){
        $continue = 1;
        $details = $user->getUserDetails($uid);

        $firstName = $details['firstName'];
        $lastName = $details['lastName'];
        $dob = $details['dob'];
        $city = $details['city'];

        $ig = $details['ig'];
        $fb = $details['fb'];
        $tw = $details['tw'];
        $ln = $details['ln'];

        $ig = $ig == '0' ? '' : $ig;
        $fb = $fb == '0' ? '' : $fb;
        $tw = $tw == '0' ? '' : $tw;
        $ln = $ln == '0' ? '' : $ln;

        $interests = $details['interests'];
        $profileImage = $details['image'];
        if ($profileImage === 0){
            $profileImage = 'profile.png';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="theme-color" content="#333">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <meta name="keywords" content="Edit - profile<?php echo $metaInfo['keywords']; ?>">
    <meta name="description" content="<?php echo $metaInfo['description']?> ">

    <meta property="og:title" content="Edit profile - <?php echo $metaInfo['keywords']?> " />
    <meta property="og:description" content="<?php echo $metaInfo['ogDescription']?> " />
    <meta property="og:image" content="<?php echo $metaInfo['ogImage']?> " />
    <meta property="og:url" content="<?php echo $metaInfo['ogUrl']?> " />

    <link rel="stylesheet" href="<?php echo $metaInfo['domain']; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo $metaInfo['domain']; ?>/css/dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" href="<?php echo $metaInfo['domain']; ?>/images/logo.png" type="image/png">
    <script
    src="https://code.jquery.com/jquery-3.5.0.js"
    integrity="sha256-r/AaFHrszJtwpe+tHyNi/XCfMxYpbsRg2Uqn0x3s2zc="
    crossorigin="anonymous"></script>
    <style>
        #main-doc{
            margin: 0px auto;
            margin-top: 80px;
            width: 1210px;
            display: grid;
            grid-template-rows: 1fr;
            grid-template-columns: 810px 300px;
            grid-column-gap: 100px;
        }
        .left, .right{
            margin-top: 40px;
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
            .left{
                margin-top: 20px;
            }
            .pr-wrap{
                height: auto !important;
                margin-top: 0px;
            }
            .prow span{
                display: block !important;
                width: 100% !important;
                margin-bottom: -5px;
                font-weight: 550;
            }
            .prow input{
                margin-bottom: 20px;
                width: 85% !important;
                background: var(--mfg);
            }
            .prow textarea{
                background: var(--mfg);
                width: 85% !important;
                margin-bottom: 10px;
            }
        }
    </style>
    <title>Edit profile - <?php echo $metaInfo['keywords']; ?></title>
</head>
<body>
    <header>
        <?php
        include "includes/header.php";
        ?>
    </header>

    <main>
        <div id="main-doc">
            <?php
            if ($continue == 1){
            ?>
            <div class="left">
                <div class="pr-wrap">
                    <h3 style="margin-bottom: 20px; color: var(--textH);">Personal Information</h3>
                    <div class="pr-ta">
                        <form action="editprofile.php" method="POST">
                            <div class="prow">
                                <span>
                                    First Name
                                </span>
                                <input type="text" name="fname" class="einp" placeholder="First name" maxlength="30" value="<?php echo $firstName; ?>" required />
                            </div>
                            <div class="prow">
                                <span>
                                    Last Name
                                </span>
                                <input type="text" name="lname" class="einp" placeholder="Last name" maxlength="30" value="<?php echo $lastName; ?>" required />
                            </div>
                            <div class="prow">
                                <span>City</span>
                                <input type="text" name="city" class="einp" placeholder="Your current city" maxlength="30" value="<?php echo $city; ?>">
                            </div>
                            <div class="prow">
                                <span>DOB</span>
                                <span>
                                    <input type="date" name="dob" class="einp" id="dob" value="<?php echo $dob; ?>" />
                                </span>
                            </div>
                            <div class="prow">
                                <span>Instagram profile</span>
                                <span>
                                    <input type="url" name="ig" class="einp" id="ig" placeholder="Your Instagram profile link" maxlength="100" value="<?php echo $ig; ?>" />
                                </span>
                            </div>
                            <div class="prow">
                                <span>Facebook profile</span>
                                <span>
                                    <input type="url" name="fb" id="fb" class="einp" placeholder="Your Facebook profile link" maxlength="100" value="<?php echo $fb; ?>" />
                                </span>
                            </div>
                            <div class="prow">
                                <span>Twitter profile</span>
                                <span>
                                    <input type="url" name="tw" class="einp" placeholder="Your Twitter profile link" maxlength="100" value="<?php echo $tw; ?>" />
                                </span>
                            </div>
                            <div class="prow">
                                <span>LinkedIN profile</span>
                                <span>
                                    <input type="url" name="ln" class="einp" placeholder="Your linkedIn profile link" maxlength="100"value="<?php echo $ln; ?>" />
                                </span>
                            </div>
                            <div class="prow">
                                <span>Interests</span>
                                <span>
                                    <textarea name="interests" style="resize: none;" class="etinp" id="interests" placeholder="Your interests" maxlength="150"><?php echo $interests; ?></textarea>
                                </span>
                            </div>
                            <input type="hidden" name="uid" value="<?php echo $uid; ?>">
                            <div class="upbt">
                                <button name="update" type="submit" class="upbtn">Update</button>
                            </div>
                        </form>
                    </div>
                    <div class="pr-tc">
                        <div class="prtc-img">
                            <img src="<?php echo $metaInfo['domain'].'/profileImages/'.$profileImage; ?>" id="rpp" alt="Profile picture">
                        </div>
                        <div class="uploadbtn">
                            <input type="file" name="profile" id="profilepic" accept="image/*">
                            <label for="profilepic">
                                <p>
                                    <span class="material-icons" style="margin-right: 10px;">
                                        add_photo_alternate
                                    </span>
                                    <span class="new-load">Select new profile Image</span><br>
                                </p>
                            </label>
                        </div>
                        <div class="img-options">
                            <?php
                            if ($profileImage != 'profile.png' || $profileImage != 0){
                            ?>
                            <button class="remove-pp" title="Remove Profile picture">Remove Profile</button>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="pr-td">
                        <span id="changpwdt" onclick="close();">Change Password</span> 
                        <span id="close">+</span>
                        <div class="changepwd">
                            <form action="profile-password.php" id="change-pwd" method="post">
                                <div class="f-inp">
                                    <input type="password" id="opassword" name="opassword" placeholder="Current Password" required />
                                    <label for="cpassword" style="height: auto; width: auto;">
                                        <i class="fa fa-lock" aria-hidden="true"></i>
                                        <span>Current Password</span>
                                    </label>
                                </div>
                                <div class="f-inp">
                                    <input type="password" id="npassword" name="npassword" placeholder="New Password" required />
                                    <label for="npassword">
                                        <i class="fa fa-lock" aria-hidden="true"></i>
                                        <span>New Password</span>
                                    </label>
                                </div>
                                <div class="f-inp">
                                    <input type="password" id="cpassword" name="cpassword" placeholder="Re-enter New password" required />
                                    <label for="password">
                                        <i class="fa fa-lock" aria-hidden="true"></i>
                                        <span>Re-enter new Password</span>
                                    </label>
                                </div>
                                <div class="msg" style="font-size: 0.8em; color: red; margin-bottom: 5px;">
                                    
                                </div>
                                <button type="submit">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>
        </div>
    </main>
    
    <footer>
        <div class="footer">
            Copyright &copy; 2020 Mayank
        </div>
    </footer>
</body>


<script
    src="https://code.jquery.com/jquery-3.5.0.js"
    integrity="sha256-r/AaFHrszJtwpe+tHyNi/XCfMxYpbsRg2Uqn0x3s2zc="
    crossorigin="anonymous"></script>

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

            $("#changpwdt").click(function(){
                $(".changepwd").show();
                $(".pr-td #close").toggle();
            });

            $(".pr-td #close").click(function(){
                $(".changepwd").hide();
                $(".pr-td #close").hide();
            });

            var form = $('#change-pwd');
            form.submit(function(e){
                e.preventDefault();

                var oPassword = $("#opassword").val();
                var newPassword = $("#npassword").val();
                var cPassword = $("#cpassword").val();

                const action = 'change-password';

                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: {
                        oPassword: oPassword,
                        newPassword: newPassword,
                        cPassword: cPassword,
                        action: action
                    },

                    success: function(data){
                        var msg = '';
                        if (data == 0){
                            msg = 'Wrong password';
                        } else
                        if (data == -1){
                            msg = 'Something went wrong.';
                        } else
                        if (data == 'mismatch'){
                            msg = 'Passwords do not match';
                        } else
                        if (data == 1){
                            msg = '<span style="color: green;">Password changed successfully.</span>';
                        }

                        $('.msg').html(msg);
                    }
                });

            });

            $('#profilepic').change(function(){

                var file = document.getElementById("profilepic").files[0].name;
                var form_data = new FormData();

                var f = document.getElementById("profilepic").files[0];
                var file_size = f.size || f.fileSize;

                if (file_size > 500000){
                    alert('Please select a file size less than 500 kb');
                } else {

                    form_data.append("profilepic", document.getElementById("profilepic").files[0]);
                    $.ajax({
                        url: 'change-profile.php',
                        type: 'post',
                        data: form_data,
                        contentType: false,
                        cache: false,
                        processData: false,
                        beforeSend: function(){
                            $('.new-load').html('Uploading...');
                        },

                        success: function(data){

                            $('.new-load').html('Select new');
                            if (data != -1){
                                alert ('Profile picture changed successfully');
                                $('#rpp').attr('src', 'profileImages/'+data);
                            } else {
                                alert('Something went wrong. Refresh the page and try again');
                            }
                        }
                    })
                }

                
            })

            <?php 
            if ($profileImage != 'profile.png' || $profileImage != 0){
            ?>
            $('.remove-pp').click(function(){
                const action = 'removeProfile';

                $.ajax({
                    url: 'profile-password.php',
                    type: 'post',
                    data: {action},

                    success: function(data){
                        $('.remove-pp').hide();
                        $('#rpp').attr('src', '<?php echo $metaInfo['domain']; ?>/profileImages/profile.png');
                    }
                });

            });
            <?php
            }
            ?>
        });
    </script>

</html>