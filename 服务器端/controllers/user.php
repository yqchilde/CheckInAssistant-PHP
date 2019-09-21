<?php
/**
 * User: yqchilde
 * Date: 2019/7/1
 * Time: 22:06
 */

namespace dnC;

class user
{
    public function index()
    {
        exit(jsonCode('error', '非法请求'));
    }

    /**
     * 获取验证码
     */
    public function verifyView()
    {
        if (isset($_POST['code'])) {
            $imgModel = new \dncTool\verifyImage();
            $imgModel->build($_POST['code']);
        } else {
            exit(jsonCode('error', '获取验证码失败'));
        }
    }

    /**
     * 注册
     */
    public function register()
    {
        $userModel = new \dncModel\user();
        $userModel->register();
    }

    /**
     * 登录
     */
    public function login()
    {
        $userModel = new \dncModel\user();
        $userModel->login();
    }

    /**
     * 获取用户信息
     */
    public function getUserInfo()
    {
        checkUser(); //检查用户合法性
    }

    /**
     * 编辑用户姓名
     */
    public function editName()
    {
        $userModel = new \dncModel\user();
        $userModel->editName();
    }

    /**
     * 设置邮箱
     */
    public function setEmail()
    {
        $userModel = new \dncModel\user();
        $userModel->setEmail();
    }

    /**
     * 设置班级
     */
    public function setClass()
    {
        $userModel = new \dncModel\user();
        $userModel->setClass();
    }

    /**
     * 修改密码
     */
    public function editPwd()
    {
        $userModel = new \dncModel\user();
        $userModel->editPwd();
    }

    /**
     * 下载地址
     */
    public function shareDown()
    {
        $appDb = \dncTool\db::getInstance('app');
        // 取出来所有版本编号
        $v_id = $appDb->where('status = ?','1')->fetchAll('v_id');
        // 拿到最近的一次版本编号
        $max_id = max(array_column($v_id,'v_id'));
        // 拿到版本号再进行比较版本号
        $appInfo = $appDb->where('v_id = ?', array($max_id))->fetch('shareDown');
        $shareDown = $appInfo['shareDown'];
        exit(jsonCode('ok',$shareDown));
    }

    /**
     * 找回密码
     */
    public function getbackPwd()
    {
        $userModel = new \dncModel\user();
        $userModel->getbackPwd();
    }
}