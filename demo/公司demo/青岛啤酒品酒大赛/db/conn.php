<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/9
 * Time: 10:04
 */

$link = mysqli_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE) or die("Error " . mysqli_error($link));
$link->set_charset('utf8');

function query($sql){
    global $link;
    $query = $link->query($sql);
    if (!$link->errno) {
            if ($query instanceof \mysqli_result) {
            $data = array();

            while ($row = $query->fetch_assoc()) {
                $data[] = $row;
            }

            $result = new \stdClass();
            $result->num_rows = $query->num_rows;
            $result->row = isset($data[0]) ? $data[0] : array();
            $result->rows = $data;

            $query->close();

            return $result;
        } else {
            return true;
        }
    } else {
        trigger_error('Error: ' . $link->error  . '<br />Error No: ' . $link->errno . '<br />' . $sql);
    }
}

function getLastId() {
    global $link;
    return $link->insert_id;
}