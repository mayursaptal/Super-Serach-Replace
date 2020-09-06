<?php
if (!defined('ABSPATH')) {
    die('DEAD END');
}
/**
 * Super Serach Replace
 *
 * @package           SuperSerachReplace
 * @author            Mayur Saptal
 * @copyright         2020 Mayur Saptal
 * @license           GPL-2.0-or-later
 * 
 */
if (!class_exists('SSRMysql')) {

    class SSRMysql
    {

        function __construct($servername, $username, $password, $dbname)
        {
            $this->conn =  new mysqli($servername, $username, $password, $dbname);
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
        }

        function query($query)
        {
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                return $stmt;
            }
            $stmt->execute();
            $res = $stmt->get_result();
            if (is_bool($res)) {
                $row =  $res;
            } else {
                $row = $res->fetch_all(MYSQLI_ASSOC);
            }
            $stmt->close();

            return $row;
        }

        function __destruct()
        {
            $this->conn->close();
        }
    }
}
