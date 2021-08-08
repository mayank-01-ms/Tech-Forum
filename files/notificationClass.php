<?php

class notification{

    //Property to hold connection
    private $con;

    //Creating connection object using constructor
    public function __construct(){
        $this->con = new DBConfig();
    }

    //Function to get notifications
    public function fetchNotification(int $uid, int $offset = 1, int $limit = 15){

        $query = "SELECT * FROM notifications WHERE uid = ? ORDER BY date desc LIMIT ?, ?;";

        $paramType = "iii";
        
        $offset = $offset - 1;
        $offset = $offset * $limit;

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $uid, $offset, $limit);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows >  0){

            //Variable to store all the comments
            $notifications = array();

            while ($row = $result->fetch_assoc()){
                $notifications[] = $row;
            }

            return $notifications;
        }
        else{
            return 0;
        }
    }

    //Mark unread notifications as read
    public function markRead(int $uid){
        $update_status_sql = 'UPDATE notifications SET status = 1 WHERE status = 0 AND uid = ?;';
        $result = $this->con->conn->prepare($update_status_sql);
        $result->bind_param('i', $uid);

        $result->execute();
    }

    //Number of unread notifications
    public function unreadNotificationsNo(int $uid){
        $query = "SELECT nid FROM notifications WHERE status = 0 AND uid = ?;";

        $paramType = "i";

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $uid);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->num_rows;
    }

    //Number of total notifications
    public function notificationsNo(int $uid){
        $query = "SELECT nid FROM notifications WHERE uid = ?;";

        $paramType = "i";

        $stmt = $this->con->conn->prepare($query);
        $stmt->bind_param($paramType, $uid);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->num_rows;
    }

    function insertNoti(int $uid, $message){
        date_default_timezone_set("Asia/Calcutta");
        $date = date("Y-m-d H:i:s");
        $status = 0;
                
        $query = 'INSERT INTO notifications (uid, message, date, status) VALUES (?, ?, ?, ?);';

        $stmt = $this->con->conn->prepare($query);

        $stmt->bind_param('isss', $uid, $message, $date, $status);
        $stmt->execute();
    }

}