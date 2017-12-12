<?php
namespace Vender\Model\Company;

use Libs\Core\DbFactory AS DbFactory;
use Libs\ExtendsClass\Common as C;

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

    public function findCompanyBySession()
    {
        $sql = "SELECT * FROM ".self::$dp."company WHERE `company_id` = :company_id";

        return self::$db->get_one($sql, ['company_id'=>$_SESSION['company_id']]);
    }

    public function findCompanys($data)
    {
        $params = $data['params'];

        $conditions = [
            'start'         => $params['start'],
            'limit'         => $params['limit'],
            'company_id'    => $params['company_id']
        ];

        $sql = "SELECT * FROM ".self::$dp."company WHERE `deleted` = 1 AND (`company_id` = :company_id OR `pid` = :company_id) ";

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

        $conditions = [
            'company_id'    => $params['company_id']
        ];

        $sql = "SELECT COUNT(*) AS total FROM ".self::$dp."company WHERE `deleted` = 1 AND (`company_id` = :company_id OR `pid` = :company_id) ";

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

        $salt = C::get_salt(10);

        $password = sha1($salt . sha1($salt . sha1($data['post']['password'])));

        $find_last_number_sql = "SELECT numbering FROM ".self::$dp."company ORDER BY company_id DESC LIMIT 1";
        $last_return = self::$db->get_one($find_last_number_sql);
        if (empty($last_return)) {
            $numbering = "CPY0000001";
        } else {
            $num = (int)substr($last_return['numbering'], 3) + 1;
            $numbering = "CPY" . sprintf("%07d", $num);
        }

        $sql = "INSERT INTO ".self::$dp."company (`password`,`salt`,`pid`,`tel`,`name`,`numbering`,`type`,`address`,`legal_person`,`registered_capital`,`date_time`,`operating_period`) VALUES ";
        $company_id = self::$db->insert(
            $sql,
            [
                $password,
                $salt,
                $_SESSION['company_id'],
                $data['post']['tel'],
                $data['post']['name'],
                $numbering,
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
            'type' => $data['post']['type'],
            'address' => $data['post']['address'],
            'company_address' => $data['post']['company_address'],
            'legal_person' => $data['post']['legal_person'],
            'registered_capital' => $data['post']['registered_capital'],
            'date_time' => $data['post']['date_time'],
            'operating_period' => $data['post']['operating_period']
        ];

        $conditions_sql = '';
        if (!empty($data['post']['password'])) {
            $salt = C::get_salt(10);
            $conditions_sql .= " ,`salt` = :salt ";
            $conditions['salt'] = $salt;

            $conditions_sql .= " ,`password` = :password ";
            $password = sha1($salt . sha1($salt . sha1($data['post']['password'])));
            $conditions['password'] = $password;
        }

        $update_sql = " UPDATE " . self::$dp . "company SET " .
                      " name = :name, type = :type, address = :address, company_address = :company_address, " .
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
}
