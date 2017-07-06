<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DbConnect
 *
 * @author abert
 */
require_once '../include/Connection.php';

class DbConnect {

    private $con;

    function __construct() {
        
    }

    public function connect() {
        $this->con = mysqli_connect(SERVER_NAME, HOST_USER, HOST_PASS, SERVER_DATABASE);
        if (mysqli_connect_errno($this->con)) {
            echo 'mysqli connection Error ' . mysqli_connect_error();
        }
        return $this->con;
    }

}
