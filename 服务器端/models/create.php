<?php
/**
 * User: yqchilde
 * Date: 2019/7/17
 * Time: 16:15
 */

namespace dncModel;

class create
{
    /**
     * 创建班级
     */
    public function createClass()
    {
        $db = \dncTool\db::getInstance('class');
        $isExist = $db
            ->where('userid = ? AND classname = ?', array($_POST['userId'],$_POST['setName']))
            ->fetch();
        if (empty($isExist)) {
            $userList = array();
            $userList['className'] = $_POST['setName'];
//            $userList['userList'][] = array(
//                'name' => $_POST['realName'],
//                'userId' => $_POST['userId'],
//                'joinTime' => date("Y/m/d",time())
//            );
            $data = array(
                'classname' => $_POST['setName'],
                'peonum' => $_POST['setPeoNum'],
                'userid' => $_POST['userId'],
                'classInfo' => json_encode($userList, true)
            );
            $res = $db->add($data);
            if (empty($res)) {
                exit(jsonCode('error', '创建失败'));
            } else {
                exit(jsonCode('ok', '创建成功'));
            }
        } else {
            exit(jsonCode('error', '已创建过此班级'));
        }
    }

    /**
     * 获取班级列表
     */
    public function classList()
    {
        // 定义两个db
        $classDb = \dncTool\db::getInstance('class');
        $userDb = \dncTool\db::getInstance('user');
        // 通过tabbar的索引来判断加载哪个数据
        $tabIndex = $_POST['tabIndex'];
        $userId = $_POST['userId'];
        switch ($tabIndex) {
            case 0:
                $res = $classDb->where('userid = ?', array($userId))->order('classid DESC')->fetchAll();
                if (!empty($res)) {
                    exit(jsonCode('ok', $res));
                }
                break;
            case 1:
                // 现在t_user中取到加入班级的id
                $userRes = $userDb->where('userid = ?', array($userId))->fetch('userInfo');
                $userRes = json_decode($userRes['userInfo'], true);
                $classRes = null;
                foreach ($userRes['joinClass'] as $k => $v) {
                    // 拿到班级classId
                    $classRes[] = $classDb->where('classid = ?', array($v['cid']))->fetchAll();

                }
                $result = call_user_func_array('array_merge', $classRes);
                exit(jsonCode('ok',$result));
                break;
        }
    }

    /**
     * 编辑班级
     */
    public function editClass()
    {
        $db = \dncTool\db::getInstance('class');
        // 先去获取classInfo的json数据，然后在进行修改
        $classInfo = $db->where('classid = ?', array($_POST['classId']))->fetch('classInfo');
        $classInfo = $classInfo['classInfo'];
        // 先解析json为数组
        $jsonRes = json_decode($classInfo, true);
        // 解析之后开始换数据（className）
        $jsonRes['className'] = $_POST['newName'];
        // 再次转换成json格式
        $classInfo =  json_encode($jsonRes, true);
        // 判断修改后的班级容纳人数是否合格
        if (empty($jsonRes['userList'])) { // 为空说明是刚创建0个人
            $realPeoNum = 0;
        } else {
            $realPeoNum = count($jsonRes['userList']);
        }
        if ($_POST['newNum'] < $realPeoNum) {
            exit(jsonCode('error', '修改失败，人数少于容纳人数'));
        }
        $data = array(
            'classname' => $_POST['newName'],
            'peonum' => $_POST['newNum'],
            'classInfo' => $classInfo
        );
        $res = $db->where('classid = ?', array($_POST['classId']))->update($data);
        if ($res == false) {
            exit(jsonCode('error', '修改失败'));
        } else {
            exit(jsonCode('ok', '修改成功'));
        }
    }

    /**
     * 删除班级
     */
    public function delClass()
    {
        $db = \dncTool\db::getInstance('class');
        $res = $db->where('classid = ?', array($_POST['classId']))->delete();
        if ($res == false) {
            exit(jsonCode('error', '删除失败'));
        } else {
            exit(jsonCode('ok', '删除成功'));
        }
    }

    /**
     * 获取班级成员
     */
    public function getPeoList($classId = null)
    {
        $db = \dncTool\db::getInstance('class');
        if ($classId === null) {
            $classId = $_POST['classId'];
            $classInfo = $db->where('classid = ?', array($classId))->fetch('classInfo');
            if (!empty($classInfo)) {
                exit(jsonCode('ok', $classInfo['classInfo']));
            } else {
                exit(jsonCode('error', '数据异常'));
            }
        } else {
            $classInfo = $db->where('classid = ?', array($classId))->fetch('classInfo');
            if (!empty($classInfo)) {
                return  $classInfo['classInfo'];
            } else {
                return 'error';
            }
        }

    }

    /**
     * 删除班级成员
     */
    public function delUser()
    {
        $db = \dncTool\db::getInstance('class');
        $data = array(
            'classInfo' => $_POST['newJoin']
        );
        $res = $db->where('classid = ?', array($_POST['classId']))->update($data);
        if ($res == false) {
            exit(jsonCode('error', '删除失败，请重试'));
        } else {
            exit(jsonCode('ok', '删除成功'));
        }
    }

    /**
     * 获取班级
     */
    public function getClass()
    {
        $db = \dncTool\db::getInstance('class');
        $res = $db->where('userid = ?', array($_POST['userId']))->fetchAll('classid,classname');
        $jsonArr = array();
        foreach ($res as $value) {
            array_push($jsonArr, array('classId' => $value['classid'], 'className' => $value['classname']));
        }
        $jsonArr = json_encode($jsonArr, true);
        exit(jsonCode('ok', $jsonArr));
    }

    /**
     * 创建课堂
     */
    public function createRoom()
    {
        $db = \dncTool\db::getInstance('classroom');
        $userId = $_POST['userId'];
        $roomName = $_POST['roomName'];
        $classes = $_POST['classes'];
        $isExist = $db
            ->where('userid = ? AND roomname = ?', array($userId, $roomName))
            ->fetch();
        if (empty($isExist)) {
            $roomList = array();
            $roomList['roomName'] = $roomName;
            $roomList['joinTime'] = time();
            $roomList['joinClass'][] = array(
                'cid' => explode(',', $classes),
            );
            $data = array(
                'roomname' => $roomName,
                'classes' => $classes,
                'userid' => $userId,
                'roomInfo' => json_encode($roomList, true)
            );
            $roomId = $db->add($data);
            if (empty($roomId)) {
                exit(jsonCode('error', '创建失败'));
            } else {
                //将roomId更新一下
                $roomList['roomId'] = $roomId;
                $data = array(
                    'roomInfo' => json_encode($roomList, true)
                );
                $res = $db->where('roomid = ?',array($roomId))->update($data);
                if ($res == false) {
                    exit(jsonCode('error', '创建失败'));
                } else {
                    exit(jsonCode('ok', '创建成功'));
                }

            }
        } else {
            exit(jsonCode('error', '已创建过该课堂'));
        }
    }

    /**
     * 获取课堂列表
     */
    public function roomList()
    {
        // 定义两个db
        $roomDb = \dncTool\db::getInstance('classroom');
        $userDb = \dncTool\db::getInstance('user');
        // 通过tabbar的索引来判断加载哪个数据
        $tabIndex = $_POST['tabIndex'];
        $userId = $_POST['userId'];
        switch ($tabIndex) {
            case 0:
                $roomInfo = $roomDb->where('userid = ?', array($_POST['userId']))->order('roomid DESC')->fetchAll('roomInfo');
                $roomInfo = array_column($roomInfo,'roomInfo');
                exit(jsonCode('ok', $roomInfo));
                break;
            case 1:
                // 现在t_user中取到加入班级的id
                $userRes = $userDb->where('userid = ?', array($userId))->fetch('userInfo');
                $userRes = json_decode($userRes['userInfo'], true);
                // 拿到加入班级的id
                $cid = array_column($userRes['joinClass'],'cid');
                // 在t_rooml里面拿到非自己创建的classes数据
                $classes = $roomDb->where('userid != ?', array($userId))->fetchAll('roomid,classes');
                $classes = array_column($classes,'classes','roomid');
                $arr = null;
                $roomRes = null;
                foreach ($classes as $k => $v) {
                    foreach ($cid as $k1 => $v1) {
                        // 判断cid是否在classes里面
                        if (strpos($v,$v1) !== false) {
                            $arr[] = $k;
                            break;
                        }
                    }
                }
                foreach ($arr as $k2 => $v2) {
                    $roomRes[] = $roomDb->where('roomid = ?', array($v2))->fetchAll('roomInfo');
                }
                $roomInfo = call_user_func_array('array_merge', $roomRes);
                $roomInfo = array_column($roomInfo,'roomInfo');
                exit(jsonCode('ok',$roomInfo));
                break;
        }
    }

    /**
     * 删除课堂
     */
    public function delRoom()
    {
        $db = \dncTool\db::getInstance('classroom');
        $res = $db->where('roomid = ?', array($_POST['roomId']))->delete();
        if ($res == false) {
            exit(jsonCode('error', '删除失败'));
        } else {
            exit(jsonCode('ok', '删除成功'));
        }
    }

    /**
     * 获取课堂详情
     */
    public function getRoomDetails()
    {
        $roomDb = \dncTool\db::getInstance('classroom');
        $roomId = $_POST['roomId'];
        $classRes = $roomDb->where('roomid = ?', array($roomId))->fetch('classes,roomInfo');
        $roomInfo = json_decode($classRes['roomInfo'],true);
        // 通过roomId来获取加入班级id
        $userList = $this->getUserListByRoomId($roomId);
        $roomInfo['userList'] = $userList['userList'];
        $data = array('roomInfo' => json_encode($roomInfo,true));
        $roomRes = $roomDb->where('roomid = ?', array($roomId))->update($data);
        if ($roomRes  == false) {
            exit(jsonCode('error', '数据异常'));
        } else {
            $roomInfo['joinClass'] = $userList['name'];
            // 注意这里更新数据库的json和返回的不一样
            exit(jsonCode('ok', $roomInfo));
        }
    }

    /**
     * 通过roomId获取成员列表
     * @param $roomId
     * @return array 参数一成员列表，参数二加入班级名字
     */
    public function getUserListByRoomId($roomId)
    {
        $roomDb = \dncTool\db::getInstance('classroom');
        $classRes = $roomDb->where('roomid = ?', array($roomId))->fetch('classes,roomInfo');
//        $roomInfo = json_decode($classRes['roomInfo'],true);
        $classes = explode(',', $classRes['classes']);
        // 遍历第一层数组取出对应的classId
        $list = null;
        $name = null;
        foreach ($classes as $k => $v) {
            $list[] = json_decode($this->getPeoList($v),true);
            $name[] = $this->getClassNameById($v);
        };
        // 因为是三维数组那我们先降到为二维数组
        $userList = $this->array3_to_array2(array_column($list,'userList'));
        // 去除二维数组中的重复值
        $userList = $this->arrayUnique($userList);
        return array(
            'userList' => $userList,
            'name' => $name
        );
    }

    /**
     * 三维数组转二维数组
     * @param $array
     * @return array
     */
    public function array3_to_array2($array){
        $array = array_filter($array);
        $array = array_values($array);
        foreach ($array as $k =>$v){
            $count = count($v);
            if($count > 1){
                for ($i = 0;$i < $count;$i++){
                    $resArr[] = $v[$i];
                }
            }else{
                $resArr[] = $v[0];
            }
        }
        return $resArr;
    }

    /**
     * 通过classId获取className
     * @param $roomId
     * @return mixed
     */
    public function getClassNameById($classId)
    {
        $db = \dncTool\db::getInstance('class');
        $res = $db->where('classid = ?', array($classId))->fetch('classname');
        $res = $res['classname'];
        return $res;
    }

    /**
     * 去除二维数组中的重复值
     * @param $array
     * @return array
     */
    public function arrayUnique($array)
    {
        $out = array();
        foreach ($array as $key=>$value) {
            if (!in_array($value, $out)){
                $out[$key] = $value;
            }
        }
        $out = array_values($out);
        return $out;
    }

    /**
     * 获取课堂
     */
    public function getRoom()
    {
        $db = \dncTool\db::getInstance('classroom');
        $res = $db->where('userid = ?', array($_POST['userId']))->fetchAll('roomid,roomname');
        $jsonArr = array();
        foreach ($res as $value) {
            array_push($jsonArr, array('roomId' => $value['roomid'], 'roomName' => $value['roomname']));
        }
        $jsonArr = json_encode($jsonArr, true);
        exit(jsonCode('ok', $jsonArr));
    }

    /**
     * 通过扫码加入班级
     */
    public function joinClass()
    {
        // 定义两个db
        $classDb = \dncTool\db::getInstance('class');
        $userDb = \dncTool\db::getInstance('user');
        // 接收post数据
        $qrRes = $_POST['qrRes'];
        $qrRes = json_decode($qrRes, true);
        $classId = $qrRes['cid'];
        $name = $_POST['name'];
        $userId = $_POST['userId'];
        // 判断创建者是不是扫码者，非创建者才能加入
        $uid = $classDb->where('classid = ?', array($classId))->fetch('userid');
        if ($uid['userid'] == $userId) {
            exit(jsonCode('sorry', '请邀请同学加入'));
        }
        // 开始写入classInfo
        $classInfo = $classDb->where('classid = ?', array($classId))->fetch('classInfo');
        $classInfo = $classInfo['classInfo'];
        // 解析json
        $classInfo = json_decode($classInfo, true);
        // 判断有没有人加入
        if (empty($classInfo['userList'])) {
            // 没有人加入就生成一个userList
            $classInfo['userList'][] = array(
                'name' => $name,
                'userId' => $userId,
                'joinTime' => date("Y/m/d",time())
            );
            // 解析成json
            $data1 = array('classInfo' => json_encode($classInfo, true));
            // 数据库进行更新操作
            $classRes = $classDb->where('classid = ?', array($classId))->update($data1);
            // 在此同时向t_user数据表中userInfo写入数据
            $userInfo = $this->userInfo($userId, $classId);
            $data2 = array('userInfo' => $userInfo);
            $userRes = $userDb->where('userid = ?', array($userId))->update($data2);
            if ($classRes == false || $userRes == false) {
                exit(jsonCode('error', '加入班级失败，请重试'));
            } else {
                exit(jsonCode('ok', '成功加入班级'));
            }
        } else {
            // 班级中已经有人加入
            foreach ($classInfo['userList'] as $k => $v) {
                // 判断自己是否已经在班级中
                if ($v['userId'] == $userId) {
                    exit(jsonCode('ok', '你已经在此班级中'));
                }
            }
            array_push($classInfo['userList'],array(
                'name' => $name,
                'userId' => $userId,
                'joinTime' => date("Y/m/d",time())
            ));
            // 解析成json
            $data1 = array('classInfo' => json_encode($classInfo, true));
            // 数据库进行更新操作
            $classRes = $classDb->where('classid = ?', array($classId))->update($data1);
            // 在此同时向t_user数据表中userInfo写入数据
            $userInfo = $this->userInfo($userId, $classId);
            $data2 = array('userInfo' => $userInfo);
            $userRes = $userDb->where('userid = ?', array($userId))->update($data2);
            if ($classRes == false || $userRes == false) {
                exit(jsonCode('error', '加入班级失败，请重试'));
            } else {
                exit(jsonCode('ok', '成功加入班级'));
            }
        }
    }

    public function userInfo($userId, $classId)
    {
        $userDb = \dncTool\db::getInstance('user');
        // 先取出数据，判断是否已经有存在
        $fetchRes = $userDb->where('userid = ?', array($userId))->fetch('userInfo');
        // 取到json里面的userInfo对象
        $fetchRes = $fetchRes['userInfo'];
        $userInfo = array();
        // 判断取到的结果是否存在
        if (empty($fetchRes)) {
            // 不存在说明没有班级加入，直接丢进去一个新的json就行
            $userInfo['joinClass'][] = array(
                'cid' => $classId
            );
            $userInfo = json_encode($userInfo,true);
        } else {
            // 存在说明已经有了班级加入，那么就要进行数组push
            // 解析内部对象
            $fetchRes = json_decode($fetchRes, true);
            foreach ($fetchRes['joinClass'] as $k => $v) {
                // 判断当前要加入的班级是否已经加入
                if ($v['cid'] == $classId) {
                    exit(jsonCode('ok', '你已在此班级中'));
                }
            }
            array_push($fetchRes['joinClass'], array('cid'=>$classId));
            $userInfo = json_encode($fetchRes,true);
        }
        return $userInfo;
    }

    public function outClass()
    {
        $classDb = \dncTool\db::getInstance('class');
        $userDb = \dncTool\db::getInstance('user');
        // 接收post数据
        $classId = $_POST['classId'];
        $userId = $_POST['userId'];
        $classInfo = $classDb->where('classid = ?', array($classId))->fetch('classInfo');
        // 删除classInfo中的数据
        $classInfo = json_decode($classInfo['classInfo'],true);
        foreach ($classInfo['userList'] as $k => $v) {
            if ($v['userId'] == $userId) {
                array_splice($classInfo['userList'],$k,1);
            }
        };
        $classInfo = json_encode($classInfo,true);
        // 删除userInfo中的数据
        $userInfo = $userDb->where('userid = ?', array($userId))->fetch('userInfo');
        $userInfo = json_decode($userInfo['userInfo'],true);
        foreach ($userInfo['joinClass'] as $k => $v) {
            if ($v['cid'] == $classId) {
                array_splice($userInfo['joinClass'],$k,1);
            }
        };
        $userInfo = json_encode($userInfo, true);
        $data = array('classInfo'=>$classInfo);
        $data2 = array('userInfo'=>$userInfo);
        $classRes = $classDb->where('classid = ?',array($classId))->update($data);
        $userRes = $userDb->where('userid = ?', array($userId))->update($data2);
        if ($classRes == false || $userRes == false) {
            exit(jsonCode('error', '退出失败，请重新尝试'));
        } else {
            exit(jsonCode('ok', '已成功退出'));
        }
    }
}