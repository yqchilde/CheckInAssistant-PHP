<?php
/**
 * 验证码类
 * User: yqchilde
 * Date: 2019/7/6
 * Time: 21:55
 */

namespace dncTool;

class verifyImage {
    public function __construct() {
        $this->font = '/static/font/simhei.ttf';
    }

    static function create(){
        static $instance ;
        if (!$instance){
            $instance = new verifyImage();
        }
        return $instance;
    }

    private $code;//验证码
    private $codeLen = 6;//验证码长度
    private $width = 130;//宽度
    private $height = 50;//高度
    private $img;//图形资源句柄
    private $font;//指定的字体
    private $fontsize = 20;//指定字体大小
    private $fontcolor;//指定字体颜色

    //生成随机码
    private function createCode($code) {
        $this->code .= $code;
    }
    //生成背景
    private function createBg() {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color = imagecolorallocate($this->img, mt_rand(157,255), mt_rand(157,255), mt_rand(157,255));
        imagefilledrectangle($this->img,0,$this->height,$this->width,0,$color);
    }
    //生成文字
    private function createFont() {
        $_x = $this->width / $this->codeLen;
        //$this->code = array("天", "地", "人", "和", "梦",);//随机因子
        for ($i=0;$i<$this->codeLen;$i++) {
            $this->fontcolor = imagecolorallocate($this->img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
            imagettftext($this->img,$this->fontsize,mt_rand(-30,30),$_x*$i+mt_rand(1,5),$this->height / 1.4,$this->fontcolor,$this->font,$this->code[$i]);
        }
    }
    //生成线条、雪花
    private function createLine() {
        //线条
        for ($i=0;$i<6;$i++) {
            $color = imagecolorallocate($this->img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
            imageline($this->img,mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,$this->width),mt_rand(0,$this->height),$color);
        }
        //雪花
        for ($i=0;$i<100;$i++) {
            $color = imagecolorallocate($this->img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
            imagestring($this->img,mt_rand(1,5),mt_rand(0,$this->width),mt_rand(0,$this->height),'*',$color);
        }
    }
    //输出
    private function outPut() {
        ob_start();
//        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
        $image_data = ob_get_contents();
        ob_end_clean ();
        $image_data_base64 = "data:image/png;base64,". base64_encode($image_data);
        exit(jsonCode('ok', $image_data_base64));
    }
    //对外生成
    public function vCodePicture($code) {
        $this->createBg();
        $this->createCode($code);
        $this->createLine();
        $this->createFont();
        $this->outPut();
    }
    //获取验证码
    public function getCode() {
        return strtolower($this->code);
    }

    public function build($code)
    {
        $this->vCodePicture($code);
    }
}

