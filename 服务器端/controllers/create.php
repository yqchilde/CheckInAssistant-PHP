<?php
/**
 * User: yqchilde
 * Date: 2019/7/1
 * Time: 20:13
 */

namespace dnC;

class create
{
    public function index()
    {
        exit(jsonCode('error', '非法请求'));
    }

    public function createClass()
    {
        $createModel = new \dncModel\create();
        $createModel->createClass();
    }

    public function classList()
    {
        $createModel = new \dncModel\create();
        $createModel->classList();
    }

    public function editClass()
    {
        $createModel = new \dncModel\create();
        $createModel->editClass();
    }

    public function delClass()
    {
        $createModel = new \dncModel\create();
        $createModel->delClass();
    }

    public function getPeoList()
    {
        $createModel = new \dncModel\create();
        $createModel->getPeoList();
    }

    public function delUser()
    {
        $createModel = new \dncModel\create();
        $createModel->delUser();
    }

    public function getClass()
    {
        $createModel = new \dncModel\create();
        $createModel->getClass();
    }

    public function createRoom()
    {
        $createModel = new \dncModel\create();
        $createModel->createRoom();
    }

    public function roomList()
    {
        $createModel = new \dncModel\create();
        $createModel->roomList();
    }

    public function delRoom()
    {
        $createModel = new \dncModel\create();
        $createModel->delRoom();
    }

    public function getRoomDetails()
    {
        $createModel = new \dncModel\create();
        $createModel->getRoomDetails();
    }

    public function getRoom()
    {
        $createModel = new \dncModel\create();
        $createModel->getRoom();
    }

    public function joinClass()
    {
        $createModel = new \dncModel\create();
        $createModel->joinClass();
    }

    public function outClass()
    {
        $createModel = new \dncModel\create();
        $createModel->outClass();
    }
}