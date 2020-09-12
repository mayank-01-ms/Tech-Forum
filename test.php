<?php

require 'files/dbconfig.php';
$con = new DBConfig();


$query = 'INSERT INTO users (ulvl, uname, uemail, upwd) VALUES (?, ?, ?, ?);';
$query2 = 'INSERT INTO userdetails (image, firstName, lastName, dor, lastActiveTime) VALUES (?, ?, ?, ?, ?);';

        $stmt = $con->conn->prepare($query);
        $stmt2 = $con->conn->prepare($query2);
        $r =1;
        $s = 'h'; 

        $stmt->bind_param('isss', $r, $s, $s, $s);
        $stmt2->bind_param('sssss', $s, $s, $s, $s, $s);

        $stmt->execute();
        $stmt2->execute();

        $lastid = $con->conn->insert_id;
        echo $lastid;