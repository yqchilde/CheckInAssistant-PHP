<?php
/**
 * User: yqchilde
 * Date: 2019/8/7
 * Time: 10:59
 */

namespace dnC;


class sign
{
    public function index()
    {
        exit(jsonCode('error', '非法请求'));
    }

    public function signInByCode()
    {
        $signModel = new \dncModel\sign();
        $signModel->signInByCode();
    }

    public function signInByGps()
    {
        $signModel = new \dncModel\sign();
        $signModel->signInByGps();
    }

    public function signInByFace()
    {
        $signModel = new \dncModel\sign();
        $signModel->signInByFace();
    }

    public function getSignInfo()
    {
        $signModel = new \dncModel\sign();
        $signModel->getSignInfo();
    }

    public function getSignList()
    {
        $signModel = new \dncModel\sign();
        $signModel->getSignList();
    }

    public function delRecord()
    {
        $signModel = new \dncModel\sign();
        $signModel->delRecord();
    }

    public function toSignIn()
    {
        $signModel = new \dncModel\sign();
        $signModel->toSignIn();
    }

    public function viewSign()
    {
        $signModel = new \dncModel\sign();
        $signModel->viewSign();
    }

    public function getStatistics()
    {
        $signModel = new \dncModel\sign();
        $signModel->getStatistics();
    }
}