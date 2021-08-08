<?php 

//Require meta info for creating links
require '../files/meta.php';

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