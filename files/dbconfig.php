<?php

class DBConfig{

    //Connection property
    public $conn = NULL;

    //Database properties
    private $server = 'localhost';
    private $username = 'root';
    private $password = '';
    private $dbname = 'blog';

    //Initialise connection using constructor
    public function __construct(){
        $this->conn = new mysqli($this->server, $this->username, $this->password, $this->dbname);

        if ($this->conn->connect_error){
            echo "Connection failed".$this->conn->connect_error;
        }

    }

    public function __destruct(){
        $this->closeConnection();
    }

    //Close connection
    protected function closeConnection(){
        if ($this->conn != NULL) {
            $this->conn->close();
            $this->conn = NULL;
        }
    }

}
    
?>