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

//Require functions file
require "includes/functions.php";

//Require comments class
require 'files/comments.php';
$commentsObj = new Comments();

//Require notification class
require 'files/notificationClass.php';
$notification = new notification();

//Require class authentication
require 'files/authentication.php';
$auth = new authentication;

//Require meta info for creating links
require 'files/meta.php';

$found = 0;  

if ($loggedIn == true){
    $uid = $_SESSION['uid'];
    $userlvl = $user->userLevel($uid);
    $permission = $user->userPermissions($userlvl);
    $canComment = $permission['comment'];
}

$pid = isset($_POST['pid']) ? $_POST['pid'] : false;
if ($pid == false){
    echo '
    <div style="color: var(--textHM);"> There was an error </div>
    ';
    exit();
} elseif (!filter_var($pid, FILTER_VALIDATE_INT)){
    echo 'Some error occured  
    ';
    exit();
}

$postData = $post->getPostsInfo($pid);

if ($postData != -1 && $postData['available'] == 1){
    $allowCom = $postData['allow-com'];
    $postTitile = $postData['title'];
    $postuid = $postData['uid'];
    $found = 1;
}


//Add comment to the database after proper validation
if ($loggedIn == true && isset($_POST['action']) && $_POST['action'] == 'add-comment'){

    if ($found == 1 && $allowCom != 'disabled' && $canComment == 1){

        //setting date
        date_default_timezone_set("Asia/Calcutta");
        $date = date("Y-m-d H:i:s");

        //Getting comment body
        $commentBody = validData($_POST['comment']);

        $cid = $commentsObj->addComment($uid, $pid, $date, $commentBody);
        $auth->lastActive($_SESSION['uid']);

        //Setting message for the user
        $message = 'Your post <a href="'.$metaInfo['domain'].'/view-post.php?pid='.$pid.'">'.$postTitile.'</a> ' ;
        $message .= 'got a new comment "'.$commentBody.'" by <a href="'.$metaInfo['domain'].'/view-profile.php?uid='.$uid.'">';
        $message .= $user->userName($_SESSION['uid']).'</a>';

        //session Uid must not be same as post uid
        if ($uid != $postuid){
            $notification->insertNoti($postuid, $message);
        }

        //get the inserted comment
        $showComment = $commentsObj->getComment($cid);

        //AUTHOR DETAILS
            $authorName = $user->userName($showComment['uid']);
            $userlvl = $user->userLevel($showComment['uid']);
            $userGroup = $user->userGroup($userlvl);
            $profileImage = $user->profileImage($showComment['uid']);
            if ($profileImage === 0){
                $profileImage = 'profile.png';
            }

            if ($userlvl == 4 || $userlvl == 5){
                $star = '<div class="star-ao">                                    
                            <span><i class="fa fa-star" aria-hidden="true"></i></span>
                        </div>';
            } else {
                $star = NULL;
            }
         
            echo '
            <div class="c-p-i">
                <div class="c-a-info">
                    <a href="'.$metaInfo['domain'].'/view-profile/'.$user->userName($showComment['uid']).'">
                    <img src="'.$metaInfo['domain'].'/profileImages/'.$profileImage.'" alt="profileImage">
                    '.$star.'
                    <div class="c-a-name">
                        <a href="'.$metaInfo['domain'].'/view-profile/'.$user->userName($showComment['uid']).'">'.$authorName.'</a>
                    </div>
                    <div class="c-a-lvl">
                        '.$userGroup.'
                    </div>
                </div>
                <div class="c-body">
                    '.nl2br($showComment['cbody']).'
                </div>
                <div class="c-t-date">
                    '.$showComment['cdate'].'
                </div>
            </div>
            ';

    } else {
        echo '
        <div class="c-p-i">
        <p style="color: var(--textHM);">Cannot process the action</p>
        </div>
        ';
    }

}

//Show comments after proper validation
if (isset($_POST['action']) && $_POST['action'] == 'show-comments'){

    $page = isset($_POST['page']) ? $_POST['page'] : 1;

    if (!filter_var($page, FILTER_VALIDATE_INT)){
        echo 'Some error occured 
        ';
        exit();
    }

    if ($found == 1 && $pid != false){
        $numberOfComments = $post->noComments($pid);

        $limit = 10;
        $pagesTtl = ceil($numberOfComments / $limit);

        if ($page < 1 || $page > $pagesTtl){
            $page = 1;
        }
        
            if ($numberOfComments > 0){
            $comments = $commentsObj->showComments($pid, $page, 10);
            
            foreach ($comments as $commentRow){

                //Get user details
                $commentUserName = $user->userName($commentRow['uid']);
                $commentUserLevel = $user->userLevel($commentRow['uid']);
                $commentUserGroup = $user->userGroup($commentUserLevel);
                $commentProfileImage = $user->profileImage($commentRow['uid']);

                if ($commentUserLevel == 4 || $commentUserLevel == 5){
                    $star = '<div class="star-ao">                                    
                                <span><i class="fa fa-star" aria-hidden="true"></i></span>
                            </div>';
                } else {
                    $star = NULL;
                }

                $checkAvailableComment = $commentRow['available'];
                if ($checkAvailableComment == -1){
                    $commentRow['cbody'] = '<i>This comment was deleted</i>';
                }

                if ($loggedIn == true && $checkAvailableComment != -1 && ($user->userLevel($_SESSION['uid']) == 4 || $user->userLevel($_SESSION['uid']) == 5) ){
                    $editOptions = '
                        <div class="c-options">
                            <ul>
                                <li class="del-com" data-cid="'.$commentRow['cid'].'"><a href="javascript:0">Delete</a></li>
                            </ul>
                        </div>
                    ';
                } else {
                    $editOptions = '';
                }

                echo '
                <div class="c-p-i">
                    <div class="c-a-info">
                        <a href="'.$metaInfo['domain'].'/view-profile/'.$user->userName($commentRow['uid']).'">
                        <img src="'.$metaInfo['domain'].'/profileImages/'.$commentProfileImage.'" alt="profile Image">
                        '.$star.'
                        </a>
                        <div class="c-a-name">
                            <a href="'.$metaInfo['domain'].'/view-profile/'.$user->userName($commentRow['uid']).'">'.$commentUserName.'</a>
                        </div>
                        <div class="c-a-lvl">
                            '.$commentUserGroup.'
                        </div>
                    </div>
                    <div class="c-body">
                        <p>'.nl2br($commentRow['cbody']).'
                        </p>
                    </div>
                    <div class="c-t-date">
                        '.$commentRow['cdate'].'
                    </div>
                    '.$editOptions.'
                </div>
                ';
            }
            
            //Creating pagination 
            $totalPages = 1;

            if ($numberOfComments > 0){

                if ($numberOfComments > $limit){
                    $totalPages = ceil($numberOfComments / $limit);

                    $previous = $page > 1 ? $page - 1 : '#';
                    if ($page <= $totalPages){
                        $next = $page + 1;
                    } else {
                        $next = '';
                    }
                }
            }
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
        }
    }
}

if(isset($_POST['action']) && $_POST['action'] == 'delete-comment'){
    $cid = $_POST['cid'];

    if (!filter_var($cid, FILTER_VALIDATE_INT)){
        echo 'Some error occured 
        ';
        exit();
    }

    $commentDetails = $commentsObj->getComment($cid);
    $uidfornotidelete = $commentDetails['uid'];
    $commentuserlvldelete = $user->userLevel($uidfornotidelete);
    $cbody = $commentDetails['cbody'];

    if ($commentDetails != -1){
        $sessionUserLevel = $user->userLevel($_SESSION['uid']);

        if ($sessionUserLevel == 4 || $sessionUserLevel == 5){

            //Setting message for the user            
            $deletemessage = 'Your comment ';
            $deletemessage .= '"'.$cbody.'" was deleted by <a href="'.$metaInfo['domain'].'/view-profile.php?uid='.$_SESSION['uid'].'">';
            $deletemessage .= $user->userName($_SESSION['uid']).'</a>';

            if ($commentuserlvldelete != 5){                    
                $commentsObj->deleteComment($cid);
                $notification->insertNoti($uidfornotidelete, $deletemessage);
                echo 1;
            } else
            if ($commentuserlvldelete == 5){
                if ($user->userLevel($_SESSION['uid']) == 5){
                    $commentsObj->deleteComment($cid);
                    $notification->insertNoti($uidfornotidelete, $deletemessage);
                    echo 1;
                }
            } 
            else{
                echo 0;
            }
        }
    }
    
}

