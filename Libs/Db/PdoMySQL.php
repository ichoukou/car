<?php
namespace Libs\Db;

use Libs\Core\Log as Log;
class PdoMySQL
{
    public $db;

    public function __construct($conf)
    {
        if (empty($conf['db_host']) or empty($conf['db_name']) or empty($conf['db_username'])) {
            trigger_error("Error: The Pdo Configuration Parameter Can Not Be Null " ,E_USER_WARNING);
            return false;
        }

        try{
            $db_port = $conf['db_port'] ?: 3306;

            $this->db = new \PDO(
                "mysql:host={$conf['db_host']};port={$db_port};dbname={$conf['db_name']}",
                $conf['db_username'],
                $conf['db_password'],
                [
                    \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8",#字符集
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_PERSISTENT         => true #持久链接
                ]
            );
        } catch (\PDOException $e){
            Log::wirte_db_log($e->getTrace()[1], $e->getMessage());
            exit(trigger_error("Error: {$e->getMessage()}", E_USER_ERROR));
        }
    }

    /**
     * 直接执行sql语句
     *
     * @param String $sql sql语句
     * @return bool|result True or false/结果集
     */
    public function query($sql, $bool = true)
    {
        try{
            if (!empty($sql)) {
                if ($bool) {
                    return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
                } else {
                    return $this->db->query($sql)->fetch(\PDO::FETCH_ASSOC);
                }
            } else {
                trigger_error("Error: Query sql is null",E_USER_ERROR);
                return false;
            }
        } catch (\PDOException $e) {
            Log::wirte_db_log($e->getTrace()[1],$e->getMessage());
            exit(trigger_error("Error: {$e->getMessage()}", E_USER_ERROR));
        }
    }

    /**
     * 直接执行sql语句
     *
     * @param String $sql sql语句
     * @return bool|result True or false/结果集
     */
    public function execute($sql)
    {
        try{
            if (!empty($sql)) {
                return $this->db->exec($sql);
            } else {
                trigger_error("Error: Execute sql is null",E_USER_ERROR);
                return false;
            }
        } catch (\PDOException $e) {
            Log::wirte_db_log($e->getTrace()[1],$e->getMessage());
            exit(trigger_error("Error: {$e->getMessage()}", E_USER_ERROR));
        }
    }

    /**
     * 插入数据操作函数。
     * 如果插入多条数据,返回影响插入成功多少个。
     * 如果插入单条数据,返回生成的数据ID。
     * 如果失败,返回false
     *
     * @param String $sql INSERT INTO table (field1,field2...) VALUES sql语句
     * @param Array $data 数据数据,插入多条记录为二维数组,单条则为一维数组
     *
     * @return Bool|Int True or False/影响记录数 or 数据id
     */
    public function insert($sql, $data = [])
    {
        try{
            $_val = '';
            $d = $data;
            if (count($d) > 0 and is_array($d) and !empty($sql)) {
                #多条插入
                if (is_array(current($d))) {
                    reset($d);

                    foreach ($d as $value) {
                        if (!empty($_val)) $_val .= ',';
                        $_val .= '(';
                        $i = 0;
                        foreach ($value as $v) {
                            if ($i > 0) $_val .= ',';
                            $_val .= "{$this->db->quote(htmlspecialchars($v))}";
                            $i++;
                        }
                        $_val .= ')';
                    }
                } else {
                    reset($d);

                    $_val .= '(';
                    $i = 0;
                    foreach ($d as $value) {
                        if ($i > 0) $_val .= ',';
                        $_val .= "{$this->db->quote(htmlspecialchars($value))}";
                        $i++;
                    }
                    $_val .= ')';
                }

                $this->db->exec($sql.$_val);

                return $this->db->lastInsertId();
            } else {
                trigger_error("Error: Insert sql or param is null", E_USER_ERROR);
                return false;
            }
        } catch (\PDOException $e) {
            Log::wirte_db_log($e->getTrace()[1], $e->getMessage());
            exit(trigger_error("Error: {$e->getMessage()}", E_USER_ERROR));
        }
    }

    /**
     * 更新操作
     *
     * @param String $sql sql语句
     * @param Array $data 一维数组参数
     * @return Bool|Int True or False/影响记录数
     */
    public function update($sql, $data = [], $debug = false)
    {
        try{
            if (!empty($sql)) {
                $obj = $this->db->prepare($sql);

                if (count($data) > 0 and is_array($data)) {
                    foreach ($data as $param=>$value) {
                        if (is_numeric($value)) {
                            $obj->bindValue(':'.$param, $value, \PDO::PARAM_INT);
                        } else {
                            $obj->bindValue(':'.$param, $value);
                        }
                    }
                }

                $obj->execute();

                if($debug)
                    $obj->debugDumpParams();

                return $obj->rowCount();
            } else {
                trigger_error("Error: Update sql or param is null", E_USER_ERROR);
                return false;
            }
        } catch (\PDOException $e) {
            Log::wirte_db_log($e->getTrace()[1], $e->getMessage());
            exit(trigger_error("Error: {$e->getMessage()}", E_USER_ERROR));
        }
    }

    /**
     * 查询多条数据,成功返回数组,失败false
     *
     * @param String $sql sql语句
     * @param Array $data 一维数组参数
     *
     * @return Array|Bool
     */
    public function get_all($sql, $data = [], $debug = false)
    {
        try{
            if (!empty($sql)) {
                $obj = $this->db->prepare($sql);

                if (count($data) > 0 and is_array($data)) {
                    foreach ($data as $param=>$value) {
                        if (is_numeric($value)) {
                            $obj->bindValue(':'.$param, $value, \PDO::PARAM_INT);
                        } else {
                            $obj->bindValue(':'.$param, $value);
                        }
                    }
                }

                $obj->execute();

                if($debug)
                    $obj->debugDumpParams();

                return $obj->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                trigger_error("Error: Select get_all sql is null", E_USER_ERROR);
                return false;
            }
        } catch (\PDOException $e) {
            Log::wirte_db_log($e->getTrace()[1], $e->getMessage());
            exit(trigger_error("Error: {$e->getMessage()}", E_USER_ERROR));
        }
    }

    /**
     * 查询单条数据,成功返回数组,失败false
     *
     * @param String $sql sql语句
     * @param Array $data 一维数组参数
     *
     * @return Array|Bool
     */
    public function get_one($sql, $data = [], $debug = false)
    {
        try{
            if (!empty($sql)) {
                $obj = $this->db->prepare($sql);

                if (count($data) > 0 and is_array($data)) {
                    foreach ($data as $param=>$value) {
                        if (is_numeric($value)) {
                            $obj->bindValue(':'.$param, $value, \PDO::PARAM_INT);
                        } else {
                            $obj->bindValue(':'.$param, $value);
                        }
                    }
                }

                $obj->execute();

                if($debug)
                    $obj->debugDumpParams();

                return $obj->fetch(\PDO::FETCH_ASSOC);
            } else {
                trigger_error("Error: Insert get_one sql is null", E_USER_ERROR);
                return false;
            }
        } catch (\PDOException $e) {
            Log::wirte_db_log($e->getTrace()[1], $e->getMessage());
            exit(trigger_error("Error: {$e->getMessage()}", E_USER_ERROR));
        }
    }

    /**
     * 统计数据,成功返回数量,失败false
     *
     * @param String $sql sql语句
     * @param Array $data 一维数组参数
     *
     * @return Int|Bool
     */
    public function count($sql, $data = [], $debug = false)
    {
        try{
            if (!empty($sql)) {
                $obj = $this->db->prepare($sql);

                if (count($data) > 0 and is_array($data)) {
                    foreach ($data as $param=>$value) {
                        if (is_numeric($value)) {
                            $obj->bindValue(':'.$param, $value, \PDO::PARAM_INT);
                        } else {
                            $obj->bindValue(':'.$param, $value);
                        }
                    }
                }

                $obj->execute();

                if($debug)
                    $obj->debugDumpParams();

                return $obj->fetch(\PDO::FETCH_ASSOC);
            } else {
                    trigger_error("Error: Select count sql is null", E_USER_ERROR);
                return false;
            }
        } catch (\PDOException $e) {
            Log::wirte_db_log($e->getTrace()[1], $e->getMessage());
            exit(trigger_error("Error: {$e->getMessage()}", E_USER_ERROR));
        }
    }

    /**
     * 删除操作
     *
     * @param String $sql sql语句
     * @param Array $data 一维数组参数
     * @return Bool|Int True or False/影响记录数
     */
    public function delete($sql, $data = [], $debug = false)
    {
        try{
            if (!empty($sql)) {
                $obj = $this->db->prepare($sql);

                if (count($data) > 0 and is_array($data)) {
                    foreach ($data as $param=>$value) {
                        if (is_numeric($value)) {
                            $obj->bindValue(':'.$param, $value, \PDO::PARAM_INT);
                        } else {
                            $obj->bindValue(':'.$param, $value);
                        }
                    }
                }

                $obj->execute();

                if($debug)
                    $obj->debugDumpParams();

                return $obj->rowCount();
            } else {
                trigger_error("Error: Delete sql is null", E_USER_ERROR);
                return false;
            }
        } catch (\PDOException $e) {
            Log::wirte_db_log($e->getTrace()[1], $e->getMessage());
            exit(trigger_error("Error: {$e->getMessage()}", E_USER_ERROR));
        }
    }

    /**
     * 获得最后插入操作,成功后的ID
     *
     * @return Int|Bool
     */
    public function get_last_id()
    {
        try{
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            Log::wirte_db_log($e->getTrace()[1], $e->getMessage());
            exit(trigger_error("Error: {$e->getMessage()}", E_USER_ERROR));
        }
    }

    /*
     * 参数转译
     */
    public function quote($data)
    {
        return $this->db->quote($data);
    }

    #public function __destruct()
    #{
    #    $this->link->close();
    #}
}