<?php
namespace Vender\Model\Company;

use Libs\Core\DbFactory AS DbFactory;

class Company extends DbFactory
{
    public function findCompanyByEmail($data)
    {
        $sql = "SELECT * FROM ".self::$dp."company WHERE `email` = :email";

        return self::$db->get_one($sql, ['email'=>$data['email']]);
    }

    public function findCompanyByTel($data)
    {
        $sql = "SELECT * FROM ".self::$dp."company WHERE `tel` = :tel";

        return self::$db->get_one($sql, ['tel'=>$data['tel']]);
    }

    public function findCompanyByCompanyId($data)
    {
        $sql = "SELECT * FROM ".self::$dp."company WHERE `company_id` = :company_id";

        return self::$db->get_one($sql, ['company_id'=>$data['company_id']]);
    }

    public function findCompanys($data)
    {
        $params = $data['params'];

        $conditions = [
            'start' => $params['start'],
            'limit' => $params['limit']
        ];

        $sql = "SELECT * FROM ".self::$dp."company WHERE `deleted` = 1 ";

        if (!empty($params['filter_name'])) {
            $sql .= "AND `name` LIKE :name ";
            $conditions['name'] = "%".$params['filter_name']."%";
        }

        if (!empty($params['filter_parent_name'])) {
            $sql .= "AND `parent_name` LIKE :parent_name ";
            $conditions['parent_name'] = "%".$params['filter_parent_name']."%";
        }

        if (!empty($params['filter_tel'])) {
            $sql .= "AND `tel` LIKE :tel ";
            $conditions['tel'] = "%".$params['filter_tel']."%";
        }

        if (!empty($params['filter_job'])) {
            $sql .= "AND `job` LIKE :job ";
            $conditions['job'] = "%".$params['filter_job']."%";
        }

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        $sql .= " ORDER BY company_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findCompanysCount($data)
    {
        $params = $data['params'];

        $conditions = [];

        $sql = "SELECT COUNT(*) AS total FROM ".self::$dp."company WHERE `deleted` = 1 ";

        if (!empty($params['filter_name'])) {
            $sql .= "AND `name` LIKE :name ";
            $conditions['name'] = "%".$params['filter_name']."%";
        }

        if (!empty($params['filter_parent_name'])) {
            $sql .= "AND `parent_name` LIKE :parent_name ";
            $conditions['parent_name'] = "%".$params['filter_parent_name']."%";
        }

        if (!empty($params['filter_tel'])) {
            $sql .= "AND `tel` LIKE :tel ";
            $conditions['tel'] = "%".$params['filter_tel']."%";
        }

        if (!empty($params['filter_job'])) {
            $sql .= "AND `job` LIKE :job ";
            $conditions['job'] = "%".$params['filter_job']."%";
        }

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        return self::$db->count($sql, $conditions);
    }

    public function addCompany($data)
    {
        $find_sql = "SELECT * FROM ".self::$dp."company WHERE `tel`=:tel";
        $return = self::$db->get_one($find_sql, ['tel'=>$data['post']['tel']]);

        if (!empty($return) or $return === true)
            return -1;

        $salt = $this->token(10);

        $password = sha1($salt . sha1($salt . sha1($data['post']['password'])));

        $sql = "INSERT INTO ".self::$dp."company (`password`,`salt`,`tel`,`name`,`numbering`,`type`,`address`,`legal_person`,`registered_capital`,`date_time`,`operating_period`) VALUES ";
        $company_id = self::$db->insert(
            $sql,
            [
                $password,
                $salt,
                $data['post']['tel'],
                $data['post']['name'],
                $data['post']['numbering'],
                $data['post']['type'],
                $data['post']['address'],
                $data['post']['legal_person'],
                $data['post']['registered_capital'],
                $data['post']['date_time'],
                $data['post']['operating_period']
            ]
        );

        return $company_id;
    }

    public function editCompany($data)
    {
        $conditions = [
            'company_id' => $data['post']['company_id'],
            'name' => $data['post']['name'],
            'numbering' => $data['post']['numbering'],
            'type' => $data['post']['type'],
            'address' => $data['post']['address'],
            'legal_person' => $data['post']['legal_person'],
            'registered_capital' => $data['post']['registered_capital'],
            'date_time' => $data['post']['date_time'],
            'operating_period' => $data['post']['operating_period']
        ];

        $conditions_sql = '';
        if (!empty($data['post']['password'])) {
            $salt = $this->token(10);
            $conditions_sql .= " ,`salt` = :salt ";
            $conditions['salt'] = $salt;

            $conditions_sql .= " ,`password` = :password ";
            $password = sha1($salt . sha1($salt . sha1($data['post']['password'])));
            $conditions['password'] = $password;
        }

        $update_sql = " UPDATE " . self::$dp . "company SET " .
                      " numbering = :numbering, name = :name, type = :type, address = :address, " .
                      " legal_person = :legal_person, registered_capital = :registered_capital, " .
                      " date_time = :date_time, operating_period = :operating_period " .
                      " {$conditions_sql} WHERE `company_id` = :company_id";

        self::$db->update($update_sql, $conditions);
    }

    public function removeCompany($data)
    {
        $sql = "UPDATE ".self::$dp."company SET `deleted`=2 WHERE `company_id` = :company_id";

        return self::$db->update($sql, ['company_id'=>$data['company_id']]);
    }

    public function removeCompanys($data)
    {
        $sql = "UPDATE ".self::$dp."company SET `deleted`=2 WHERE `company_id` = :company_id";

        foreach ($data['company_ids'] as $company_id) {
            self::$db->update($sql, ['company_id'=>$company_id]);
        }

        return true;
    }

    public function token($length = 20)
    {
        $string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        $max = strlen($string) - 1;

        $token = '';

        for ($i = 0; $i < $length; $i++) {
            $token .= $string[mt_rand(0, $max)];
        }

        return $token;
    }
}
