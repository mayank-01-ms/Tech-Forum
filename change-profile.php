<?php

session_start();

//Require database connection
require 'files/dbconfig.php';
$con = new DBConfig();

//Require user class
require 'files/user.php';
$user = new user();

$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;
if ($loggedIn == false){
    header("Location: index.php");
    exit();
}

if ($loggedIn == true){
    
    $image = isset($_FILES) ? $_FILES['profilepic'] : false;

    if ($image == false){
        exit();
    }

    if ($image != false){
        $imageName = $_FILES['profilepic']['name'];
        $tmpLocation = $_FILES['profilepic']['tmp_name'];
        $imageSize = $_FILES['profilepic']['size'];
        $imageError = $_FILES['profilepic']['error'];

        $iamgeExt = explode('.', $imageName);
        $imageActExt = strtolower(end($iamgeExt));

        $permitted = array('jpg', 'jpeg', 'png', 'gif');
        
        if (in_array($imageActExt, $permitted)){
            if  ($imageError === 0){
                if ($imageSize < 1000000){
                    $imageNewName = uniqid('profile').".".$imageActExt;
                    $destination = 'profileImages/'.$imageNewName;
                    move_uploaded_file($tmpLocation, $destination);

                    //Removing old pic
                    $oldImage = $user->profileImage($_SESSION['uid']);
                    if ($oldImage != 'profile.png'){
                        unlink('profileImages/'.$oldImage);
                    }
                    $user->setProfile($_SESSION['uid'], $imageNewName);
                    echo $imageNewName;

                }
                else {
                    echo -1;
                }
            }
            else {
                echo -1;
            }
        }
        else{
            echo -1;
        }
    }
}
