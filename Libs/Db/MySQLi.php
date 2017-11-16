<?php
/**
 * MySQLi数据库类
 */
namespace Libs\Db;

final class MySQLi
{
    /**
     * 数据库连接
     *
     * @var $link
     */
    public $link;

    /**
     * 数据库实例化
     *
     * @param String $host_name host地址
     * @param String $user_name 用户名称
     * @param String $password  用户密码
     * @param String $database  数据库名称
     * @param int $port  数据库端口号
     *
     * @return Bool true/false
     */
    public function __construct($host_name,$user_name,$password,$database,$port = 3306)
    {
        $this->link = new \mysqli($host_name, $user_name,$password, $database,$port);

        if ($this->link->connect_error) {
            trigger_error("Error: Could Not Make A Database Link ({$this->link->connect_errno})",E_USER_ERROR);
            return false;
        }

        $this->link->set_charset("utf8");
        #$this->link->query("SET SQL_MODE = ''");
    }

    public function query($sql)
    {
        $query = $this->link->query($sql);

        if (!$this->link->errno) {
            if ($query instanceof \mysqli_result) {
                $data = array();

                while ($row = $query->fetch_assoc()) {
                    $data[] = $row;
                }

                $result = [];
                $result['num_rows'] = $query->num_rows;
                $result['row'] = isset($data[0]) ? $data[0] : array();
                $result['rows'] = $data;

                $query->close();

                return $result;
            } else {
                if ($this->link->affected_rows > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            trigger_error("Error: '{$this->link->error}' <br /> Error No: '{$this->link->errno}'<br />" . $sql,E_USER_ERROR);
            return false;
        }
    }

    public function count($sql)
    {
        $query = $this->link->query($sql);

        if (!$this->link->errno) {
            if ($query instanceof \mysqli_result) {

                $result = [];
                $result['count'] = $query->num_rows;

                $query->close();

                return $result;
            } else {
                return true;
            }
        } else {
            trigger_error("Error: '{$this->link->error}' <br /> Error No: '{$this->link->errno}'<br />" . $sql,E_USER_ERROR);
            return false;
        }
    }

    public function escape($value)
    {
        return $this->link->real_escape_string($value);
    }

    public function count_affected()
    {
        return $this->link->affected_rows;
    }

    public function get_last_id()
    {
        return $this->link->insert_id;
    }

    public function __destruct()
    {
        $this->link->close();
    }
}