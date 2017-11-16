<?php
/**
 * http://www.poluoluo.com/jzxy/201404/270493.html memcache存储session
 * http://www.cnblogs.com/jianmingyuan/p/6074002.html session执行顺序机制
 * https://segmentfault.com/a/1190000003036291?_ea=280173 session执行顺序机制
 */
namespace Libs\Core\Session;

use Libs\Core\Session\SessionDbModel;

class DbSession extends \SessionHandler
{
    private $db_model;

    /**
     * 初始化session相关的数据库模型
     *
     * @param $config　配置文件
     */
    public function __construct($config)
    {
        $this->db_model = new SessionDbModel($config);
    }

    /**
     * 判断是否要创建session_id
     *
     * @return string session_id
     */
    public function create_sid()
    {
        return $this->db_model->session_create();
    }

    /**
     * 在运行session_start()时执行
     */
    public function open($path, $name)
    {
        return true;
    }

    /**
     * 在运行session_start()时执行,因为在session_start时,会去read当前session数据
     */
    public function read($session_id)
    {
        if (empty($session_id)) {
            $session_id = $this->create_sid();
        }

        return $this->db_model->session_read($session_id);
    }

    /**
     * 此方法在脚本结束和使用session_write_close()强制提交SESSION数据时执行
     */
    public function write($session_id, $data)
    {
        if (empty($session_id)) {
            $session_id = $this->create_sid();
        }

        $data_array = explode(';',$data);
        $data_count = count($data_array) - 1;
        $return_data = [];
        $index = $offset = 0;
        while ($index < $data_count) {
            #筛选出数组的key
            $dividing_line_index = strpos($data_array[$index], "|", $offset);
            $filed_name = substr($data_array[$index], $offset, $dividing_line_index);

            #筛选出数组的value
            $unserialize_data = unserialize(substr($data_array[$index].';', $dividing_line_index + 1));
            $return_data[$filed_name] = $unserialize_data;

            $index++;
        }

        $this->db_model->session_write($session_id, $data, $return_data, $this->config);

		return true;
    }

    /**
     * 在脚本执行完成或调用session_write_close() 或 session_destroy()时被执行,即在所有session操作完后被执行
     */
    public function close()
    {
        return true;
    }

    /**
     * 清除当前客户端在数据库里的数据
     *
     * 在运行session_destroy()时执行
     */
    public function destroy($session_id)
    {
        $this->db_model->session_destroy($session_id);

        return true;
    }

    /**
     * 清理数据库里所有过期的session
     *
     * 执行概率由session.gc_probability 和 session.gc_divisor的值决定,时机是在open,read之后,session_start会相继执行open,read和gc
     */
    public function gc($maxlifetime)
    {
        $this->db_model->session_clear($maxlifetime);

        return true;
    }	
}