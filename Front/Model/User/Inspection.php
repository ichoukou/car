<?php
namespace Front\Model\User;

use Libs\Core\DbFactory AS DbFactory;
use Libs\ExtendsClass\Common as C;

class Evaluation extends DbFactory
{
    public function addEvaluation($data)
    {
        $sql = "INSERT INTO ".self::$dp."evaluation (`reservation_id`,`company_id`,`user_id`,`car_id`,`bill`,`score`) VALUES ";
        $evaluation = self::$db->insert(
            $sql,
            [
                $data['post']['id'],
                $data['post']['company_id'],
                $data['post']['user_id'],
                $data['post']['car_id'],
                $data['post']['bill'],
                $data['post']['score']
            ]
        );

        $update_company_sql = " UPDATE " . self::$dp . "company SET " .
            " `score` = score + {$data['post']['score']}, `score_count` = score_count + 1 " .
            " WHERE `company_id` = :company_id";
        self::$db->update(
            $update_company_sql,
            [
                'company_id' => $data['post']['company_id']
            ]
        );

        $update_reservation_sql = " UPDATE " . self::$dp . "reservation SET " .
            " `status` = 5 " .
            " WHERE `reservation_id` = :reservation_id";
        self::$db->update(
            $update_reservation_sql,
            [
                'reservation_id' => $data['post']['id']
            ]
        );

        return $evaluation;
    }

    public function findEvaluation($data)
    {
        $sql = "SELECT * FROM ".self::$dp."evaluation " .
            " WHERE `user_id` = :user_id AND `reservation_id` = :reservation_id";

        return self::$db->get_one(
            $sql,
            [
                'reservation_id' => $data['reservation_id'],
                'user_id' => $_SESSION['user_id']
            ]
        );
    }
}
