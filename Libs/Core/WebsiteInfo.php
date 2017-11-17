<?php
/**
 * 总控制器类
 */
namespace Libs\Core;

use Libs\Core\Model as M;

class WebsiteInfo
{
    public function get_admin_website_info()
    {
        return M::Admin('System\\Website', 'findWebsiteByStatus');
    }

    public function get_admin_head_information_statistics()
    {
        return M::Admin('System\\Website', 'findHeadInformationStatistics');
    }

    public function get_vender_website_info()
    {
        return M::Vender('System\\Website', 'findWebsiteByStatus');
    }

    public function get_vender_head_information_statistics()
    {
        return M::Vender('System\\Website', 'findHeadInformationStatistics');
    }
}