<?php
namespace dncTool;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//引入项目

$dir = dirname(__FILE__) . '/PHPMailer';
require ($dir . '/src/Exception.php');
require ($dir . '/src/PHPMailer.php');
require ($dir . '/src/SMTP.php');

class sendMail
{
    public static $Host = 'smtp.163.com'; //smtp服务器
    private static $From = '15555055952@163.com'; //发送者的邮件地址
    private static $FromName = '签到助手'; //发送邮件的用户昵称
    private static $Username = '15555055952@163.com'; //登录到邮箱的用户名
    private static $Password = '20171128yqchilde'; //第三方登录的授权码，在邮箱里面设置

    /**
     * 发送
     * @param $sendMail
     * @param $mailTitle
     * @param $content
     * @return bool
     */
    public function send($sendMail,$mailTitle,$content)
    {
        //实例化PHPMail类
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 0; // 关闭Dubug模式
            $mail->isSMTP(); // 使用SMTP发送邮件
            $mail->Host = self::$Host; //SMTP邮件服务器地址（腾讯企业邮为例）
            $mail->SMTPAuth = true; //发信认证
            $mail->Username = self::$Username; // SMTP 发件人邮箱
            $mail->Password = self::$Password; // SMTP 发件人邮箱密码
            $mail->SMTPSecure = 'ssl'; //ssl协议
            $mail->Port = 465; //ssl端口号

            //发件人
            $mail->setFrom(self::$From, self::$FromName); //发件人邮箱（同 $mail->Username项设置）、发件人名称

            //收件人。多收件人可设置多个addAddress
            $mail->addAddress($sendMail, ''); //收件人邮箱地址，收件人姓名（选填）
            //$mail->addAddress('ellen@example.com'); // 收件人邮箱地址

            //邮件内容
            $mail->isHTML(true); //发送html格式邮件
            $mail->Subject = $mailTitle; //邮件标题
            $mail->Body    = $content; //邮件内容
            $mail->AltBody = '邮件摘要'; //目测没什么用，可去掉

            //判断是否发送成功
            if ($mail->send()) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
//            echo $mail->ErrorInfo;
        }
    }
}