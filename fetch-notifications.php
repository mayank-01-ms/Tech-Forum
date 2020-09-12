<?php

session_start();
//Checking for session

//Require database connection
require "files/dbconfig.php";
$con = new DBConfig();

//Require notification class
require 'files/notificationClass.php';
$notification = new notification();

//Require meta file
require 'files/meta.php';

$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

if ($loggedIn == false){
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'load'){

    //If read set notifications to read
    if (isset($_POST['read']) && $_POST['read'] == 'read'){
        
        $notification->markRead($_SESSION['uid']);
    }

    //Else continue with the code
    $noOfNoti = $notification->notificationsNo($_SESSION['uid']);
    $noOfUnseen = $notification->unreadNotificationsNo($_SESSION['uid']);

    if ($noOfUnseen < 100){
        $noOfUnseen = $noOfUnseen;
    } else {
        $noOfUnseen = '99+';
    }

    if ($noOfNoti == 0){
        echo '
        <div style="color: var(--textM);">
        There are no notifications
        </div>
        ';

        exit();
    }

    $notifications = $notification->fetchNotification($_SESSION['uid'], 1, 5);

    $output = '
    <span>Notifications</span>
    <ul>
    ';

    foreach ($notifications as $rowNoti){
        $read = $rowNoti['status'];
        if ($read == 1){
            $read = 'read';
        } else {
            $read = 'unread';
        }
        $output .= '
        <li class="noti-'.$read.'">
        '.$rowNoti['message'].'
        <div class="noti-date">'.$rowNoti['date'].'</div>
        </li>
        ';
    }

    $output .= '</ul>';

    if ($noOfNoti > 5){
        $output .= '<div class="more-n">';
        $output .= '<a href="'.$metaInfo['domain'].'/notifications.php">More...</a>';
        $output .= '</div>';
    }

    $data = array(
        'notifications' => $output,
        'unseen' => $noOfUnseen
    );

    echo json_encode($data);
    
}
?>