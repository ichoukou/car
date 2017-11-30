<?php
namespace Front\Controller\User;

use Libs\Core\ControllerFront AS Controller;
use Libs\Core\Model as M;
use Libs\Core\Loader as L;
use Libs\ExtendsClass\Pagination;
use Libs\ExtendsClass\Common as C;

class Evaluation extends Controller
{
    public function index()
    {
        $this->is_login();

        $reservation_id = (int)$_GET['reservation_id'];
        if (empty($reservation_id))
            exit(header("location:{$this->data['entrance']}route=Front/User/Reservation{$this->data['url']}"));

        $info = M::Front('User\\Reservation', 'findReservationByReservationId', ['reservation_id'=>$reservation_id]);
        if (empty($info))
            exit(header("location:{$this->data['entrance']}route=Front/User/Reservation{$this->data['url']}"));

        $evaluation_info = M::Front('User\\Evaluation', 'findEvaluation', ['reservation_id'=>$reservation_id]);
        if (!empty($evaluation_info)) {
            exit(header("location:{$this->data['entrance']}route=Front/User/Evaluation/evaluation_success&id={$evaluation_info['reservation_id']}"));
        }

        $this->data['reservation_info'] = $info;

        $this->create_page();

        L::output(L::view('User\\EvaluationAdd', 'Front', $this->data));
    }

    public function evaluation_success()
    {
        $reservation_id = (int)$_GET['id'];
        if (empty($reservation_id))
            exit(header("location:{$this->data['entrance']}route=Front/User/Reservation{$this->data['url']}"));

        $info = M::Front('User\\Evaluation', 'findEvaluation', ['reservation_id'=>$reservation_id]);
        if (empty($info))
            exit(header("location:{$this->data['entrance']}route=Front/User/Reservation{$this->data['url']}"));

        $this->data['evaluation_info'] = $info;

        $this->create_page();

        L::output(L::view('User\\EvaluationSuccess', 'Front', $this->data));
    }

    public function do_add_evaluation()
    {
        if ($post = $this->validate_default()) {
            $evaluation = M::Front('User\\Evaluation', 'addEvaluation', ['post'=>$post]);

            if ($evaluation > 0) {
                setcookie('success_info', '评价成功', time() + 60);

                exit(json_encode(['status'=>1, 'result'=>$post['id']], JSON_UNESCAPED_UNICODE));
            } else {
                exit(json_encode(['status'=>-1, 'result'=>'评价失败'], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    public function validate_default()
    {
        $this->is_login();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $post = C::hsc($_POST);
            $errors = [];

            if (empty($post['id'])) {
                $errors ['other_error'] = '缺少待评价订单标识';
            } else {
                $info = M::Front('User\\Reservation', 'findReservationByReservationId', ['reservation_id'=>$post['id']]);
                if (empty($info)) {
                    $errors ['other_error'] = '没有找到待评价订单信息';
                }
            }

            $score = (int)$post['score'];
            if ($score < 1 or $score > 5) {
                $errors ['reservation_time'] = '请评价，分数为1~5分';
            }

            $post['company_id'] = $info['company_id'];
            $post['company_id'] = $info['company_id'];
            $post['user_id'] = $info['user_id'];
            $post['car_id'] = $info['car_id'];
            $post['bill'] = $info['bill'];

            if (!empty($errors)) exit(json_encode(['status'=>-1, 'result'=>$errors], JSON_UNESCAPED_UNICODE));

            return $post;
        } else {
            return false;
        }
    }
}