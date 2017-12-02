<?php
namespace Front\Model\Reservation;

use Libs\Core\DbFactory AS DbFactory;
use Libs\ExtendsClass\Common as C;
use Libs\ExtendsClass\WxCommon as WC;

class Reservation extends DbFactory
{
    public function findReservationByCompanyId($data)
    {
        $sql = "SELECT * FROM ".self::$dp."company WHERE `company_id` = :company_id";
        $info = self::$db->get_one($sql, ['company_id'=>$data['company_id']]);

        if(empty($info))
            return '';

        $update_sql = "UPDATE ".self::$dp."company SET `views` = views + 1 WHERE `company_id` = :company_id";
        self::$db->update($update_sql, ['company_id'=>$data['company_id']]);

        return $info;
    }

    public function findCompanyDetailsByCompanyId($data)
    {
        $sql = "SELECT * FROM ".self::$dp."company_detail WHERE `company_id` = :company_id";
        $info = self::$db->get_one($sql, ['company_id'=>$data['company_id']]);

        if (empty($info) and !empty($data['name'])) {
            $return_all = WC::http_request('http://i.yjapi.com/ECIV4/GetDetailsByName?key=3d30f9114712494aa3abff529d05359d&keyword=' . $data['name']);

            if ($return_all['Status'] == 200) {
                $sql = "INSERT INTO ".self::$dp."company_detail ( " .
                    " `company_id`,`key_no`,`name`,`no`,`belong_org`,`oper_name`,`start_date`,`end_date`,`status`,`province`,`updated_date`,`credit_code`, " .
                    " `regist_capi`,`econ_kind`,`address`,`scope`,`term_start`,`team_end`,`check_date`,`org_no`,`is_on_stock`,`stock_number`,`stock_type`, " .
                    " `website_name`,`website_url`,`phone_number`,`email`,`industry`,`sub_industry` " .
                    " ) VALUES ";
                $return = $return_all['Result'];
                $param =
                    [
                        $data['company_id'],
                        $return['KeyNo'],
                        $return['Name'],
                        $return['No'],
                        $return['BelongOrg'],
                        $return['OperName'],
                        empty($return['StartDate']) ? '' : date('Y-m-d H:i:s', strtotime($return['StartDate'])),
                        $return['EndDate'],
                        $return['Status'],
                        $return['Province'],
                        empty($return['UpdatedDate']) ? '' : date('Y-m-d H:i:s', strtotime($return['UpdatedDate'])),
                        $return['CreditCode'],
                        $return['RegistCapi'],
                        $return['EconKind'],
                        $return['Address'],
                        $return['Scope'],
                        empty($return['TermStart']) ? '' : date('Y-m-d H:i:s', strtotime($return['TermStart'])),
                        empty($return['TeamEnd']) ? '' : date('Y-m-d H:i:s', strtotime($return['TeamEnd'])),
                        empty($return['CheckDate']) ? '' : date('Y-m-d H:i:s', strtotime($return['CheckDate'])),
                        $return['OrgNo'],
                        $return['IsOnStock'] == 0 ? '未上市' : '已上市',
                        $return['StockNumber'],
                        $return['StockType'],
                        $return['ContactInfo']['WebSite']['Name'],
                        $return['ContactInfo']['WebSite']['Url'],
                        $return['ContactInfo']['PhoneNumber'],
                        $return['ContactInfo']['Email'],
                        $return['Industry']['Industry'],
                        $return['Industry']['SubIndustry']
                    ];

                $detail_id = self::$db->insert($sql, $param);

                if ($detail_id > 0) {
                    $sql = "SELECT * FROM ".self::$dp."company_detail WHERE `company_id` = :company_id";
                    $info = self::$db->get_one($sql, ['company_id'=>$data['company_id']]);
                }
            }
        }

        return $info;
    }

    public function findReservations($data)
    {
        $params = $data['params'];

        $conditions = [
            'start'         => $params['start'],
            'limit'         => $params['limit']
        ];

        $sql = "SELECT * FROM ".self::$dp."company WHERE `deleted` = 1 ";

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        $sql .= " ORDER BY company_id DESC LIMIT :start,:limit ";

        $result = self::$db->get_all($sql, $conditions);

        return $result;
    }

    public function findReservationsCount($data)
    {
        $params = $data['params'];

        $conditions = [
        ];

        $sql = "SELECT  COUNT(*) AS total FROM ".self::$dp."company WHERE `deleted` = 1 ";

//        if (!empty($params['filter_create_time'])) {
//            $sql .= "AND `create_time` LIKE :create_time ";
//            $conditions['create_time'] = "%".$params['filter_create_time']."%";
//        }

        return self::$db->count($sql, $conditions);
    }

    public function addReservation($data)
    {
        $find_sql = "SELECT u.user_id,u.tel,uc.car_id FROM ".self::$dp."user AS u LEFT JOIN ".self::$dp."user_car AS uc ON u.user_id = uc.user_id WHERE u.`user_id` = :user_id";
        $user_info = self::$db->get_one($find_sql, ['user_id'=>$_SESSION['user_id']]);

        $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));

        $bill = date('YmdHis', time()).$orderSn.$_SESSION['user_id'];

        $sql = "INSERT INTO ".self::$dp."reservation (`company_id`,`user_id`,`car_id`,`bill`,`reservation_time`,`description`) VALUES ";
        $reservation = self::$db->insert(
            $sql,
            [
                $data['post']['company_id'],
                $user_info['user_id'],
                $user_info['car_id'],
                $bill,
                $data['post']['reservation_time'],
                $data['post']['description']
            ]
        );

        return $reservation;
    }
}
