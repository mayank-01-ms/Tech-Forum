<?php

if ($loggedIn == true){
    $userName = $user->userName($_SESSION['uid']);
    $userlevel = $user->userLevel($_SESSION['uid']);
    $userLvl = $user->userGroup($userlevel);

    $linkHref = $metaInfo['domain']."/view-profile/".$user->userName($_SESSION['uid']);
    $profilePicture = $user->profileImage($_SESSION['uid']);
}

else {
    $userName = "Guest";
    $userLvl = '';

    $linkHref = $metaInfo['domain']."/login.php";
    $profilePicture = 'profile.png';
}

$categoryName = $categoryObj->getCategories();


?>


<div class="header">
    <a href="<?php echo $metaInfo['domain']; ?>">
        <img src="<?php echo $metaInfo['domain']; ?>/images/logo.png" alt="Site Logo" class="logo" />
    </a>
    <div class="head-title">
        <span>Tech</span>
        <span>@</span>
        <span>GLANCE</span>
    </div>
    <nav>
        <div class="leftmo" >
            <div class="left-upim">
                <img src="<?php echo $metaInfo['domain'].'/profileImages/'.$profilePicture; ?>" >
            </div>
            <div class="left-upd" >
                <span><a href="<?php echo $linkHref; ?>"><?php echo $userName; ?></a></span>
                <span><?php echo $userLvl; ?></span>
            </div>
        </div>
        <div class="leftmo-dm">
            <p>
                <label for="dml">Dark theme    
                <input type="checkbox" name="darkMode" id="dml" class="dM"></label>
            </p>
        </div>
        
        <ul id="test">
        <?php

        for ($i=0; $i<count($categoryName); $i++){
            echo '<li><a href="'.$metaInfo['domain'].'/category/'.$categoryName[$i]['cname'].'">'.$categoryName[$i]['cname'].'</a></li>';
        }
        ?>
        </ul>
    </nav>
    <div class="rt-header">
        <div class="search">
            <form action="<?php echo $metaInfo['domain']; ?>/search.php" method="GET">
                <input type="text" name="query" class="search-box" placeholder="Search">
                <button class="search-btn" type="submit"><i class="fa fa-search"></i></button>
            </form>
        </div>
        <?php
        if ($loggedIn == true){
        ?>
        <div class="notification" title="Notifications">
            <i class="fa fa-bell" aria-hidden="true"></i>
            <span class="noti-no">&nbsp;</span>
            <div class="notification-area">
                <span>Notifications</span>
            </div>
        </div>
        <?php
        }
        ?>
        <div class="dropdown">
            <img src="<?php echo $metaInfo['domain'].'/profileImages/'.$profilePicture; ?>" alt="Profile" class="profile" />
            <div class="user-space">
                <div class="cc">
                    <img src="<?php echo $metaInfo['domain'].'/profileImages/'.$profilePicture; ?>" alt="Avtar" class="avtar" />
                </div>
                <div class="options">
                <h4 class="u-n"><a href="<?php echo $linkHref; ?>"><?php echo $userName; ?></a></h4>
                <p><?php echo $userLvl; ?></p>

                <?php 
                if ($loggedIn == true){
                    ?>
                    <ul>
                        <a href="<?php echo $metaInfo['domain']; ?>/create-post.php">
                            <li>
                                <span class="material-icons">
                                    add_box
                                </span>
                                <p>New Post</p>
                            </li>
                        </a>
                        <a href="<?php echo $metaInfo['domain']; ?>/user-posts.php?uid=<?php echo $_SESSION['uid'] ?>">
                            <li>
                                <span class="material-icons">
                                    home
                                </span>
                                <p>Posts</p>
                            </li>
                        </a>
                        <li>
                            <span class="material-icons">
                                nights_stay
                            </span>
                            <p>
                                <label for="dm">Dark theme    
                                <input type="checkbox" name="darkMode" id="dm" class="dM" /></label>
                            </p>
                        </li>
                        <?php
                                if ($loggedIn == true && ($userlevel == 4 || $userlevel == 5)){
                                    echo '
                                    <a href="'.$metaInfo['domain'].'/admin/feature-posts.php">
                                        <li>
                                            <span class="material-icons">
                                            home
                                            </span>
                                            <p>Admin center</p>
                                        </li>
                                    </a>
                                    ';
                                }
                                ?>
                        <a href="<?php echo $metaInfo['domain']; ?>/edit-profile.php">
                            <li>
                                <span class="material-icons">
                                    settings
                                </span>
                                <p>Settings</p>
                            </li>
                        </a>
                        <a href="<?php echo $metaInfo['domain']; ?>/logout.php">
                            <li>
                                <span class="material-icons">
                                    exit_to_app
                                </span>
                                <p>Logout</p>
                            </li>
                        </a>
                    </ul>
                <?php 
                }
                else {
                ?>
                <ul>
                    <a href="<?php echo $metaInfo['domain']; ?>/login.php">
                        <li>
                            <span class="material-icons">
                                drafts
                            </span>
                            <p>Login</p>
                        </li>
                    </a>
                    <li>
                        <span class="material-icons">
                            nights_stay
                        </span>
                        <p>
                            <label for="dm">Dark theme    
                                <input type="checkbox" name="darkMode" id="dm" class="dM">
                            </label>
                        </p>
                    </li>
                </ul>
                <?php
                }
                ?>
                    
                </div>
            </div>
        </div>
    </div>
    <div class="hamburger">
        <input type="checkbox" class="menu-btn" id="menu-btn" />
        <label for="menu-btn" class="menu-icon">
            <span class="nav-icon"></span>
        </label>
    </div>
</div>