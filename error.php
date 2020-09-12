<?php

//require meta file
include 'files/meta.php';

?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#333">
    <link rel="stylesheet" href="<?php echo $metaInfo['domain'];?>/css/dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="<?php echo $metaInfo['domain'];?>/images/logo.png" type="image/png">
    <title>404 Not Found</title>

    <style>

        *{
            margin: 0;
            padding: 0;
        }
        body{
            background-color: #333;
            color: #999;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .four{
            font-size: 12em;  
            font-weight: bold;          
            display: flex;
            justify-content: center;
            align-items: center;            
            background: -webkit-linear-gradient(var(--gradientS), var(--gradientE));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0px 0px 20px var(--gradientE);
        }
        .nf{
            font-size: 3.5em;     
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #eee;
        }
        .para{
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .form{
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin-top: 25px;
        }
        .form form button{
            background: transparent;
            outline: none;
            border: none;
            cursor: pointer;
            position: relative;
            z-index: 2;
            transform: translateX(-30px);
        }
        .form .fa{
            font-size: 1.2em;
            color: #fff;
        }
        .form form input{
            border-radius: 30px;
            height: 30px;
            width: 250px;
            position: relative;
            background: #777;
            outline: none;
            border: none;
            margin-top: 10px;
            color: #fff;
            padding: 5px 10px;
        }
        .form form input::placeholder{
            color: #fff;
        }
        .form form input:focus{
            box-shadow: 0px 0px 5px dodgerblue;
        }

        @media only screen and (max-width: 600px) {
            .error{
                padding: 20px;
            }
        }

    </style>
</head>
<body>

    <div class="error">
        <div class="four">
            404
        </div>
        <div class="nf">
            Not Found
        </div>
        <div class="para">
            The page you are looking for doesn't exists on our server.
        </div>

        <div class="form">
            <p>Try searching for the article you are looking for here</p>
            <form action="<?php echo $metaInfo['domain'];?>/search.php" method="get" target="_blank">
                <input type="search" name="query" placeholder="Search...">
                <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
            </form>
        </div>
    </div> 
    
</body>
</html>