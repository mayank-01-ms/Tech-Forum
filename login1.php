<?php

session_start();

$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;

if ($loggedIn == true){
    header("Location: index.php");
    exit();
}

else
if (isset($_POST['action']) && $_POST['action'] == 'login'){

    //Require database connection
    require "files/dbconfig.php";
    //Creating connection object
    $con = new DBConfig();

    //Initialising variables
    $emailid = $_POST['username'];
    $password = $_POST['password'];

    //Exit the script if entities are emppty
    if (empty($emailid) || empty($password)){
        echo "Empty fields";
        exit();
    }

    else {
        $sql = "SELECT * FROM users WHERE uname = ? OR uemail = ? LIMIT 1; ";
        
        $paramType = "ss";

        $stmt = $con->conn->prepare($sql);
        $stmt->bind_param($paramType, $emailid, $password);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0){

            //Fetching results
            $row = $result->fetch_assoc();         

            //Checking password
            $pwdHash = password_verify($password, $row['upwd']);
            if ($pwdHash == 0){

                //Send a message to login page that password is wrong
                echo 0;
                exit();
            }
            else if ($pwdHash == 1){
                $_SESSION['loggedIn'] = true;
                $_SESSION['uid'] = $row['uid'];

                //Send a message to login page that login is successful
                echo 1;

                exit();
            }
            else {

                //Send a message saying some error occured
                echo -1;
            }
        }
        else {
            //User not found 
            echo 2;
        }
    }
}
        
else {
    header("Location: index.php");
}

?>