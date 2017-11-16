<?php
/**
 * 总控制器类
 */
namespace Libs\Core;

use Libs\Core\Model as M;

class WebsiteInfo
{
    public function get_website_info()
    {
        return M::Admin('System\\Website', 'findWebsiteByStatus');
    }

    public function get_head_information_statistics()
    {
        return M::Admin('System\\Website', 'findHeadInformationStatistics');
    }
}