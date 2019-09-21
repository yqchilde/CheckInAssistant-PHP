<?php
/**
 * User: yqchilde
 * Date: 2019/6/5
 * Time: 12:51
 */

namespace dnC;

class index
{
    public function index()
    {
        exit(jsonCode('ok','api 1.0.0'));
    }

    public function checkVersion()
    {
        $version  = $_POST['version'];
        $appDb = \dncTool\db::getInstance('app');
        // 取出来所有版本编号
        $v_id = $appDb->where('status = ?','1')->fetchAll('v_id');
        // 拿到最近的一次版本编号
        $max_id = max(array_column($v_id,'v_id'));
        // 拿到版本号再进行比较版本号
        $appInfo = $appDb->where('v_id = ?', array($max_id))->fetch();
        if (empty($appInfo)) {
            exit(jsonCode('error', '暂无更新版本'));
        }
        if ($appInfo['version'] == $version) {
            exit(jsonCode('error', '当前已是最新版'));
        }
        exit(jsonCode('ok', $appInfo));
    }
}