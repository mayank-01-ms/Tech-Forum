<?php

include "includes/functions.php";
require "files/dbconfig.php";
$con = new DBConfig();

//Including categories files
include "files/categories.php";
//Creating object for category
$categoryObj = new Categories();

// Include meta information
include "files/meta.php";

//Require classs to get post data
require "files/posts.php";
$post = new Posts();

//Require class user to get user details
require "files/user.php";
$user = new User();

$action = $_POST['action'];

if ($action == 'load'){
    $limit = validData($_POST['counter']);
    $offset = 10;

    if (!filter_var($limit, FILTER_VALIDATE_INT)){
        exit();
    }

    $query = "SELECT * 
            FROM posts INNER JOIN featured ON posts.pid = featured.pid
            WHERE featured.position = 2
            ORDER BY featured.dof desc
            LIMIT ".$limit.",".$offset.";";

    $result = $con->conn->query($query);

    if ($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            echo '
                <li class="posts">
                <div class="d-banner">
                <a href="'.$metaInfo['domain'].'/view-post/'.$row['slug'].'">
                    <img src="'.$metaInfo['domain'].'/banners/'.$row['pbanner'].'" alt="banner" class="banner">
                </a>
                </div>
                <div class="p-right">
                    <div class="category">'.$categoryObj->categoryName($row['pcat']).'</div>
                    <div class="comments">'.$post->noComments($row['pid']).' <i class="fa fa-comments-o" aria-hidden="true"></i></div>
                    <div class="views">'.numberShort($row['views']).'<i class="fa fa-eye" aria-hidden="true"></i></div>
                    <div class="post-content">
                        <div class="intro">
                            <a href="'.$metaInfo['domain'].'/view-post/'.$row['slug'].'">'.$row['newTitle'].'
                            </a>
                        </div>
                        <div class="info">
                            <p>
                                '.$row['description'].'
                            </p>    
                        </div>
                    </div>
                    <div class="author"><a href="'.$metaInfo['domain'].'/view-profile/'.$user->userName($row['uid']).'">'.$user->userName($row['uid']).'</a></div>
                    <div class="time-stamp">'.$row['pdate'].'</div>
                    <a href="'.$metaInfo['domain'].'/view-post/'.$row['slug'].'"><button class="read-more">Read more</button></a>
                </div>
            </li>
                ';
        }        
    } else {
        echo 0;
    }
}

?>