<?php
/**
 * User: yqchilde
 * Date: 2019/7/1
 * Time: 22:22
 */

namespace dncModel;

class user
{
    /**
     * 用户注册
     */
    public function register()
    {
        // 查询手机号/账号是否已经存在
        $db = \dncTool\db::getInstance('user');
        $phoneNumber = $db->where(
            'phoneNumber = ?', array($_POST['phoneNumber'])
        )->fetch();
        // 账号未注册
        if (empty($phoneNumber)) {
            $phoneNumber = array();
            $phoneNumber['phoneNumber'] = $_POST['phoneNumber'];
            $phoneNumber['password'] = md5($_POST['phoneNumber'] . $_POST['password']);
            $phoneNumber['regtime'] = time();
            $res = $db->add($phoneNumber);
        } else {
            exit(jsonCode('error', '手机号已注册'));
        }

        if (empty($res)) {
            exit(jsonCode('error', '注册失败'));
        }else {
            exit(jsonCode('ok', '注册成功'));
        }
    }

    /**
     * 用户登录
     */
    public function login()
    {
        // 查询手机号/账号是否存在
        $db = \dncTool\db::getInstance('user');
        $result = $db
            ->where('phoneNumber = ?', array($_POST['phoneNumber']))
            ->fetch();
        // 账号未注册
        if ($result == false) {
            // 为空说明没有注册
            exit(jsonCode('error', '该账号未注册'));
        } else {
            $result = $db
                ->where('password = ?', array(md5($_POST['phoneNumber'] . $_POST['password'])))
                ->fetch('userid,phoneNumber,realname');
            if ($result == false) {
                exit(jsonCode('error', '账号或密码错误'));
            } else {
                // 查到信息返回字段为userid和phoneNumber的
                exit(jsonCode('ok', $result));
            }
        }
    }

    /**
     * 编辑姓名
     */
    public function editName()
    {
        $db = \dncTool\db::getInstance('user');
        $data = array(
            'realname' => $_POST['realName']
        );
        $res = $db->where('userid = ?', array($_POST['userId']))->update($data);
        if ($res == false) {
            exit(jsonCode('error', '名字修改失败'));
        } else {
            exit(jsonCode('ok', '修改成功'));
        }
    }

    /**
     * 设置邮箱
     */
    public function setEmail()
    {
        $db = \dncTool\db::getInstance('user');
        $data = array(
            'email' => $_POST['email']
        );
        $res = $db->where('userid = ?', array($_POST['userId']))->update($data);
        if ($res == false) {
            exit(jsonCode('error', '邮箱绑定失败'));
        } else {
            exit(jsonCode('ok', '绑定成功'));
        }
    }

    /**
     * 设置班级
     */
    public function setClass()
    {
        $db = \dncTool\db::getInstance('user');
        $data = array(
            'class' => $_POST['className']
        );
        $res = $db->where('userid = ?', array($_POST['userId']))->update($data);
        if ($res == false) {
            exit(jsonCode('error', '班级绑定失败'));
        } else {
            exit(jsonCode('ok', '绑定成功'));
        }
    }

    /**
     * 修改密码
     */
    public function editPwd()
    {
        $userId = $_POST['userId'];
        $newPwd = $_POST['newpwd'];
        $phoneNumber = $_POST['phoneNumber'];
        $userDb = \dncTool\db::getInstance('user');
        $data = array(
            'password' => md5($phoneNumber . $newPwd)
        );
        $upRes = $userDb->where('userid = ?', array($userId))->update($data);
        if ($upRes == false) {
            exit(jsonCode('error', '密码修改失败'));
        } else {
            exit(jsonCode('ok', '密码修改成功'));
        }
    }

    /**
     * 找回密码
     */
    public function getbackPwd()
    {
        $phoneNumber = $_POST['phoneNumber'];
        $email = $_POST['email'];
        $userDb = \dncTool\db::getInstance('user');
        $fetchRes = $userDb->where('phoneNumber = ? AND email = ?',array(
            $phoneNumber,$email
        ))->fetch('userid');
        $userId = $fetchRes['userid'];
        if (empty($userId)) {
            exit(jsonCode('error', '该手机号不存在，或用户未设置邮箱'));
        }
        // 信息正确将密码发送到用户邮箱中
        $mailSend = new \dncTool\sendMail();
        //多邮件示例
        $sendMail=$email;
        //邮件标题
        $mailTitle = "您收到签到助手的一条邮件";
        //$content为邮件内容
        $newPwd = $this->makeCardPassword();
        $content="<div><b>您的新密码为：" . $newPwd . ",请尽快登陆并修改密码</b></div>";

        //执行发信
        $sendRes = $mailSend->send($sendMail,$mailTitle,$content);
        if ($sendRes == false) {
            exit(jsonCode('error','服务错误，请重新尝试'));
        } else {
            $data = array(
                'password' => md5($phoneNumber . $newPwd)
            );
            $updateRes = $userDb->where('userid = ?',array($userId))->update($data);
            if ($updateRes == true) {
                exit(jsonCode('ok','已将重置后的密码发送到您的邮箱中！'));
            }

        }
    }

    /**
     * 生成随机密码
     * @return string
     */
    public function makeCardPassword()
    {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0,25)]
            .strtoupper(dechex(date('m')))
            .date('d').substr(time(),-5)
            .substr(microtime(),2,5)
            .sprintf('%02d',rand(0,99));
        for(
            $a = md5( $rand, true ),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < 8;
            $g = ord( $a[ $f ] ),
            $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
            $f++
        );
        return  $d;
    }
}