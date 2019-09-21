<?php
/**
 * User: yqchilde
 * Date: 2019/8/7
 * Time: 11:00
 */

namespace dncModel;


use dncTool\db;

class sign
{
    /**
     * 通过签到码创建签到
     */
    public function signInByCode()
    {
        // 转换数据
        $userId = $_POST['userId'];
        $roomId = $_POST['roomId'];
        $signTime = $_POST['signTime'];
        $signInWay = $_POST['signInWay'];
        $signCode = $_POST['signCode'];
        $time = time();
        // 数据库操作
        $signDb = \dncTool\db::getInstance('sign');
        $roomDb = \dncTool\db::getInstance('classroom');
        // 获取t_classroom表中的roomInfo数据
        $roomInfo = $roomDb->where('roomid = ?', array($roomId))->fetch('roomInfo');
        $roomInfo = json_decode($roomInfo['roomInfo'], true);
        // 判断roomInfo里面有没有userList,没有就补充
        if (empty($roomInfo['userList'])) {
            $newUserList = new \dncModel\create();
            $userList = $newUserList->getUserListByRoomId($roomId);
            $roomInfo['userList'] = $userList['userList'];
            // 判断没有人的情况
            if (count($roomInfo['userList']) === 0) {
                exit(jsonCode('error','该课堂没有成员'));
            }
        }
        $data = array('roomInfo' => json_encode($roomInfo,true));
        $roomRes = $roomDb->where('roomid = ?', array($roomId))->update($data);
        if ($roomRes  == false) {
            exit(jsonCode('error', '数据异常'));
        } else {
            // signInfo的json处理
            $signInfo = array();
            $signInfo['roomId'] = $roomId;
            $signInfo['roomName'] = $this->getRoomNameById($roomId);
            $signInfo['beginTime'] = $time;
            // 转换签到时长时间戳为几分钟后的时间戳
            switch ($signTime) {
                case '1min':
                    $signInfo['signTime'] = date(strtotime("+1 min"));
                    break;
                case '2min':
                    $signInfo['signTime'] = date(strtotime("+2 min"));
                    break;
                case '3min':
                    $signInfo['signTime'] = date(strtotime("+3 min"));
                    break;
                case '4min':
                    $signInfo['signTime'] = date(strtotime("+4 min"));
                    break;
                case '5min':
                    $signInfo['signTime'] = date(strtotime("+5 min"));
                    break;
                default:
                    break;
            }
            $signInfo['signInWay'] = $signInWay;
            $signInfo['signCode'] = $signCode;
            // 与数据库进行操作的数据
            $data = array(
                'userid' => $userId,
                'roomid' => $roomId,
                'begintime' => $time,
                'signtime' => $signTime,
                'signinway' => $signInWay,
                'signInfo' => json_encode($signInfo, true)
            );
            $signId = $signDb->add($data);
            if (empty($signId)) {
                exit(jsonCode('error', '发起签到失败'));
            } else {
                exit(jsonCode('ok', $signId));
            }
        }
    }

    /**
     * 通过gps创建签到
     */
    public function signInByGps()
    {
        // 转换数据
        $userId = $_POST['userId'];
        $roomId = $_POST['roomId'];
        $signTime = $_POST['signTime'];
        $signInWay = $_POST['signInWay'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $time = time();
        // 数据库操作
        $signDb = \dncTool\db::getInstance('sign');
        $roomDb = \dncTool\db::getInstance('classroom');
        // 获取t_classroom表中的roomInfo数据
        $roomInfo = $roomDb->where('roomid = ?', array($roomId))->fetch('roomInfo');
        $roomInfo = json_decode($roomInfo['roomInfo'], true);
        // 判断roomInfo里面有没有userList,没有就补充
        if (empty($roomInfo['userList'])) {
            $newUserList = new \dncModel\create();
            $userList = $newUserList->getUserListByRoomId($roomId);
            $roomInfo['userList'] = $userList['userList'];
            // 判断没有人的情况
            if (count($roomInfo['userList']) === 0) {
                exit(jsonCode('error','该课堂没有成员'));
            }
        }
        $data = array('roomInfo' => json_encode($roomInfo,true));
        $roomRes = $roomDb->where('roomid = ?', array($roomId))->update($data);
        if ($roomRes  == false) {
            exit(jsonCode('error', '数据异常'));
        } else {
            // signInfo的json处理
            $signInfo = array();
            $signInfo['roomId'] = $roomId;
            $signInfo['roomName'] = $this->getRoomNameById($roomId);
            $signInfo['beginTime'] = $time;
            // 转换签到时长时间戳为几分钟后的时间戳
            switch ($signTime) {
                case '1min':
                    $signInfo['signTime'] = date(strtotime("+1 min"));
                    break;
                case '2min':
                    $signInfo['signTime'] = date(strtotime("+2 min"));
                    break;
                case '3min':
                    $signInfo['signTime'] = date(strtotime("+3 min"));
                    break;
                case '4min':
                    $signInfo['signTime'] = date(strtotime("+4 min"));
                    break;
                case '5min':
                    $signInfo['signTime'] = date(strtotime("+5 min"));
                    break;
                default:
                    break;
            }
            $signInfo['signInWay'] = $signInWay;
            $signInfo['latitude'] = $latitude;
            $signInfo['longitude'] = $longitude;
            // 与数据库进行操作的数据
            $data = array(
                'userid' => $userId,
                'roomid' => $roomId,
                'begintime' => $time,
                'signtime' => $signTime,
                'signinway' => $signInWay,
                'signInfo' => json_encode($signInfo, true)
            );
            $signId = $signDb->add($data);
            if (empty($signId)) {
                exit(jsonCode('error', '发起签到失败'));
            } else {
                exit(jsonCode('ok', $signId));
            }
        }
    }

    /**
     * 通过人脸创建签到
     */
    public function signInByFace()
    {

    }

    /**
     * 通过roomId获取roomName
     * @param $roomId
     * @return mixed
     */
    public function getRoomNameById($roomId)
    {
        $db = \dncTool\db::getInstance('classroom');
        $res = $db->where('roomid = ?', array($roomId))->fetch('roomname');
        $res = $res['roomname'];
        return $res;
    }

    /**
     * 通过userId获取realName
     * @param $userId
     * @return mixed
     */
    public function getUserNameById($userId)
    {
        $db = \dncTool\db::getInstance('user');
        $res = $db->where('userid = ?', array($userId))->fetch('realname');
        $res = $res['realname'];
        return $res;
    }

    /**
     * 获取签到信息
     */
    public function getSignInfo()
    {
        $db = \dncTool\db::getInstance('sign');
        $signId = $_POST['signId'];
        $userId = $_POST['userId'];
        $signRes = $db->where('signid = ?', array($signId))->fetch('userid,signInfo,signList');
//        签到状态（0表示待签到、1表示已签到、2表示签到结束、3表示时间结束且已签到）
        if (empty($signRes['signList'])) {
            $signStatus = 0;
        } else {
            $signList = json_decode($signRes['signList'],true);
            foreach ($signList as $k => $v) {
                if ($v['userId'] == $userId) {
                    $signStatus = 1;
                    break;
                } else {
                    $signStatus = 0;
                }
            }
        }
        $signInfo = json_decode($signRes['signInfo'],true);
        // 获取发起者名字
        $uName = $this->getUserNameById($signRes['userid']);
        $signInfo['uName'] = $uName;
        $signInfo['signStatus'] = $signStatus;
        // 获取当前用户签到情况
        exit(jsonCode('ok', $signInfo));
    }

    /**
     * 获取签到列表
     */
    public function getSignList()
    {
        $signDb = \dncTool\db::getInstance('sign');
        $roomDb = \dncTool\db::getInstance('classroom');
        $userId = $_POST['userId'];
        $tabIndex = $_POST['tabIndex'];
        switch ($tabIndex) {
            case 0:
                // 拿到所有数据
                $signInfo = $signDb->where('userid = ?', array($userId))->order('signid DESC')->fetchAll();
                $signList = array();
                foreach ($signInfo as $k => $v) {
                    $signId = $v['signid'];
                    $v = json_decode($v['signInfo'], true);
                    if (time() > $v['signTime']) {
                        array_push($signList, array(
                            'signId' => $signId,
                            'roomName' => date("m.d", $v['beginTime']) . " " . $v['roomName'],
                            'status' => "已结束"
                        ));
                    } else {
                        array_push($signList, array(
                            'signId' => $signId,
                            'roomName' => date("m.d", $v['beginTime']) . " " . $v['roomName'],
                            'status' => "进行中"
                        ));
                    }
                }
                exit(jsonCode('ok', $signList));
                break;
            case 1:
                // 拿到所有数据
                $signInfo = $signDb->where('userid != ?', array($userId))->order('signid DESC')->fetchAll();
                $arr = null;
                $signList = array();
                foreach ($signInfo as $k => $v) {
                    // 先拿到roomId,通过他去获取该班级是不是已加入的班级
                    $roomInfo = $roomDb->where('roomid = ?',array($v['roomid']))->fetch('roomInfo');
                    $roomInfo = json_decode($roomInfo['roomInfo'],true);
                    // 判断是否加入，是就返回signId
                    foreach ($roomInfo['userList'] as $k1 => $v1) {
                        if ($v1['userId'] == $userId) {
                            $arr[] = $v['signid'];
                        }
                    }
                }
                foreach ($arr as $k => $v) {
                    // $v是signId
                    $signInfo = $signDb->where('signid = ?', array($v))->fetch('signInfo');
                    $signInfo = json_decode($signInfo['signInfo'],true);
                    if (time() > $signInfo['signTime']) {
                        array_push($signList, array(
                            'signId' => $v,
                            'roomName' => date("m.d", $signInfo['beginTime']) . " " . $signInfo['roomName'],
                            'status' => "已结束"
                        ));
                    } else {
                        array_push($signList, array(
                            'signId' => $v,
                            'roomName' => date("m.d", $signInfo['beginTime']) . " " . $signInfo['roomName'],
                            'status' => "进行中"
                        ));
                    }

                }
                exit(jsonCode('ok', $signList));
                break;
        }
    }

    /**
     * 删除
     */
    public function delRecord()
    {
        $db = \dncTool\db::getInstance('sign');
        $signId = $_POST['signId'];
        $res = $db->where('signid = ?', array($signId))->delete();
        if ($res == false) {
            exit(jsonCode('error', '删除失败'));
        } else {
            exit(jsonCode('ok', '删除成功'));
        }
    }

    /**
     * 去签到
     */
    public function toSignIn()
    {
        $db = \dncTool\db::getInstance('sign');
        $signId = $_POST['signId'];
        $userId = $_POST['userId'];
        $listRes = $db->where('signId = ?',array($signId))->fetch('signList');
        //判断是否json里面已经有数据
        if (empty($listRes['signList'])) {
            //json空的时候之间插入就行
            $signList[] = array(
                'userId' => $userId,
                'userName' => $this->getUserNameById($userId),
                'signTime' => time()
            );
            $signList = json_encode($signList,true);
            $data = array('signList'=>$signList);
            $res = $db->where('signid = ?',array($signId))->update($data);
            if ($res == false) {
                exit(jsonCode('error', '签到失败'));
            } else {
                exit(jsonCode('ok', '签到成功'));
            }
        } else {
            // json不为空，需要在后面插入
            $signList = json_decode($listRes['signList'],true);

            array_push($signList,array(
                'userId' => $userId,
                'userName' => $this->getUserNameById($userId),
                'signTime' => time()
            ));
            $signList = json_encode($signList,true);
            $data = array('signList'=>$signList);
            $res = $db->where('signid = ?',array($signId))->update($data);
            if ($res == false) {
                exit(jsonCode('error', '签到失败'));
            } else {
                exit(jsonCode('ok', '签到成功'));
            }
        }
    }

    /**
     * 查看签到情况
     */
    public function viewSign()
    {
        $signDb = \dncTool\db::getInstance('sign');
        $signId = $_POST['signId'];
        $signRes = $signDb->where('signid = ?',array($signId))->fetch('roomid,signList');
        $roomId = $signRes['roomid'];
        $signList = json_decode($signRes['signList'],true);
        if (empty($signList)) {
            exit(jsonCode('error', '当前发起的签到未有人参与'));
        }
        //查询一下当前签到课堂的所有用户
        $newUserList = new \dncModel\create();
        $userList = $newUserList->getUserListByRoomId($roomId);
        $userList = $userList['userList'];
        //遍历所有用户，比对已签到用户
        $add = 0; //初始计次
        foreach ($userList as $k => $v) {
            //先给所有用户签到时间初始为null
            $userList[$k]['signTime'] = 'null';
            foreach ($signList as $k1 => $v1) {
                if ($v['userId'] == $v1['userId']) {
                    $userList[$k]['signTime'] = $v1['signTime'];
                    $add++;
                }
            }
        }
        // 开始包装要返回的数组
        $newList = array(
            'headCount' => count($userList),
            'signCount' => $add,
            'signList' => $userList
        );
        exit(jsonCode('ok', $newList));
    }

    /**
     * 获取最近的签到数据作为统计
     */
    public function getStatistics()
    {
        $signDb = \dncTool\db::getInstance('sign');
        $userId = $_POST['userId'];
        $signId = $signDb->where('userid = ?',array($userId))->order('signid DESC')->fetchAll('signid');
        $signId = array_column($signId,'signid');
        // 开始遍历所有signId的内容
        $arr = null;
        foreach ($signId as $v) {
            $arr[] = $this->getStatisticsById($v);
        }
        exit(jsonCode('ok',$arr));
    }

    /**
     * 通过signId获取签到数据作为统计
     * @param $signId
     * @return array
     */
    public function getStatisticsById($signId)
    {
        $signDb = \dncTool\db::getInstance('sign');
        $signRes = $signDb->where('signid = ?',array($signId))->fetch('roomid,signList');
        $roomId = $signRes['roomid'];
        $signList = json_decode($signRes['signList'],true);
        //查询一下当前签到课堂的所有用户
        $newUserList = new \dncModel\create();
        $userList = $newUserList->getUserListByRoomId($roomId);
        $userList = $userList['userList'];
        //遍历所有用户，比对已签到用户
        $add = 0; //初始计次

        foreach ($userList as $k => $v) {
            //先给所有用户签到时间初始为null
            $userList[$k]['signTime'] = 'null';
            foreach ((array)$signList as $k1 => $v1) {
                if ($v['userId'] == $v1['userId']) {
                    $add++;
                }
            }
        }
        // 开始包装要返回的数组
        $newList = array(
            'signId' => $signId,
            'roomName' => $this->getRoomNameById($roomId),
            'headCount' => count($userList),
            'signCount' => $add,
        );
        return $newList;
    }
}