<?php

//设置编码
header('content-type:text/html; charset=utf-8');

// 接口认证
if (empty($_GET['token'])) {
    exit(jsonCode('error', 'token error'));
}
if ($_GET['token'] != 'api2019') {
    exit(jsonCode('error', 'token error'));
}

// 文件夹定义
define("DNC_DS", DIRECTORY_SEPARATOR); //系统分割符
define("DNC_ROOT", dirname(__FILE__) . DNC_DS); //后台根目录
define("DNC_CONTROLLERS", DNC_ROOT . 'controllers' . DNC_DS); //后台控制器目录
define("DNC_MODELS", DNC_ROOT . 'models' . DNC_DS); //后台业务目录
define("DNC_TOOLS", DNC_ROOT . 'tools' . DNC_DS); //后台工具类目录

/* 过滤及定义 POST */
if (!empty($_POST)) {
    define("IS_POST", false);
} else {
    define("IS_POST", true);
    $_POST = str_replace(array('<', '>', '"', "'"), array('&lt;', '&gt;', '&quot;', ''), $_POST);
}

/* 数据库配置 */
define('DNC_DB_HOST', '127.0.0.1'); // mysql 服务器地址
define('DNC_DB_NAME', 'ketangdianming');     // 数据库名称
define('DNC_DB_USER', 'ketangdianming');      // 数据库账号
define('DNC_DB_PWD', '20171128yjyA');      // 数据库密码
define('DNC_DB_PRE', 't_');    // 数据表统一前缀
define('DNC_DB_CHARSET', 'utf8');   // mysql 字符集类型

/* 自动加载 */
function dncAutoLoad($className)
{
    $className = explode('\\', $className);
    if (empty($className[0])) {
        array_shift($className);
    }
    if (count($className) != 2) {
        return false;
    }
    switch ($className[0]) {
        case 'dncModel':
            $classFileName = DNC_MODELS . $className[1] . '.php';
            break;
        case 'dncTool':
            $classFileName = DNC_TOOLS . $className[1] . '.php';
            break;
    }
    if (empty($classFileName)) {
        return false;
    }
    if (is_file($classFileName)) {
        require $classFileName;
    }
}

spl_autoload_register("dncAutoLoad", true, true);

/* 路由解析 */
$_GET['c'] = empty($_GET['c']) ? 'index' : $_GET['c'];
$_GET['m'] = empty($_GET['m']) ? 'index' : $_GET['m'];
$pattern = '/^[a-zA-Z]+[0-9]*[a-zA-Z]*$/';
if (!preg_match($pattern, $_GET['c'])) {
    $_GET['c'] = 'index';
}
if (!preg_match($pattern, $_GET['m'])) {
    $_GET['m'] = 'index';
}
$controllerFileName = DNC_CONTROLLERS . $_GET['c'] . '.php';
if (is_file($controllerFileName)) {
    require $controllerFileName;
    $className = '\\dnC\\' . $_GET['c'];
    $controller = new $className;
    if (method_exists($controller, $_GET['m'])) {
        call_user_func(array($controller, $_GET['m']));
    }
}

// json 输出
function jsonCode($status, $data)
{
    return json_encode(array('status' => $status, 'data' => $data));
}

// 验证用户合法性
function checkUser()
{
    if (empty($_POST['userid'])) {
        exit(jsonCode('error', 'uid error'));
    }
    if (empty($_POST['phoneNumber'])) {
        exit(jsonCode('error', 'phone error'));
    }
    $db = \dncTool\db::getInstance('user');
    $user = $db->where('userid = ?', array($_POST['userid']))->fetch();
    if (empty($user)) {
        exit(jsonCode('error', '用户不存在'));
    } else {
        $result = $db
            ->where('userid = ? AND phoneNumber = ?', array($_POST['userid'], $_POST['phoneNumber']))
            ->fetch('userid,phoneNumber,realname,email,class,regtime');
        if ($result == false) {
            exit(jsonCode('error', '该用户不合法'));
        } else {
            exit(jsonCode('ok', $result));
        }
    }
}

///**
// * 图片转base64
// * @param $ImageFile String 图片路径
// * @return string
// */
//function Base64EncodeImage($ImageFile)
//{
//    if (file_exists($ImageFile) || is_file($ImageFile)) {
//        $base64_image = '';
//        $image_info = getimagesize($ImageFile);
//        $image_data = fread(fopen($ImageFile, 'r'), filesize($ImageFile));
//        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
//        return $base64_image;
//    } else {
//        return false;
//    }
//}