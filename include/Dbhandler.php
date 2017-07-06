<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Dbhandler
 *
 * @author abert
 */
require_once '../include/DbConnect.php';

class Dbhandler {

    private $con;

    function __construct() {
        $connect = new DbConnect();
        $this->con = $connect->connect();
    }

    public function checkUserLogin($contact, $password) {
        $stmt = $this->con->prepare('select * from mobile_users where contact=? && password=?');
        $stmt->bind_param('ss', $contact, $password);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function IsUserExists($contact) {
        $stmt = $this->con->prepare('select * from mobile_users where contact=?');
        $stmt->bind_param('s', $contact);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function create_account($firstName, $lastName, $contact, $password) {
        $time_stamp = $this->getDatetimeNow();
        $stmt = $this->con->prepare('insert into mobile_users(firstName,lastName,contact,password,_when_added) values(?,?,?,?,?)');
        $stmt->bind_param('sssss', $firstName, $lastName, $contact, $password, $time_stamp);
        $stmt->execute();
        $stmt->close();
        return TRUE;
    }

    public function userDetails($contact) {
        $stmt = $this->con->prepare('select * from mobile_users where contact=?');
        $stmt->bind_param('s', $contact);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $result;
    }

    public function fetch_markets() {
        $stmt = $this->con->prepare('select * from markets m left join location l on(m.location_location_id=l.location_id)');
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function fetch_today_price($market_id) {
        $time_stamp = $this->getDatetimeNow();
        $stmt = $this->con->prepare('select * from prices p left join products pd on(p.product_product_id=pd.product_id)'
                . 'left join categories c on(pd.category_category_id=c.category_id) where p.market_market_id=? && p.time_stamp=?');
        $stmt->bind_param('ss', $market_id, $time_stamp);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    public function filter_by_date($market_id, $date) {
        $time_stamp = $this->getDatetimeNow();
        $stmt = $this->con->prepare('select * from prices p left join products pd on(p.product_product_id=pd.product_id)'
                . 'left join categories c on(pd.category_category_id=c.category_id) where p.market_market_id=? && p.time_stamp=?');
        $stmt->bind_param('ss', $market_id, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    }

    function getDatetimeNow() {
        $tz_object = new DateTimeZone('EAT');
        $datetime = new DateTime();
        $datetime->setTimezone($tz_object);
        return $datetime->format('Y\-m\-d');
    }

}
