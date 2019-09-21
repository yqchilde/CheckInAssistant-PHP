<template>
	<form @submit="formSubmit">
        <view class="u-f-ajc u-f-column">
            <!-- 头部 -->
            <view class="header">
                <text class="header-text u-f-ajc" style="">创建签到</text>
                <image class="header-logo u-f-ajc" src="https://ae01.alicdn.com/kf/H137706df54584caea92f30332eb252a5y.png"></image>
            </view>
            <!-- 上课班级 -->
            <view class="room u-f u-f-jsb">
                <text class="roomTitle">选择课堂</text>
                <picker mode="selector" name="selectorRoom" @change="bindSelectRoom" :range="room_name">
                    <view v-if="roomShow">
                        <view class="roomTitleIf">{{selectRoom[roomIndex].roomName}}</view>
                    </view>
                    <view v-else>
                        <text class="roomTitle cuIcon-roundadd text-9152fa"></text>
                    </view>
                </picker>
            </view>
            <!-- 迟到时间 -->
            <view class="time u-f u-f-jsb">
                <text class="roomTitle">签到时间</text>
                <picker mode="selector" name="selectorTime" @change="bindSelectTime" :range="time_name">
                    <view v-if="timeShow">
                        <view class="roomTitleIf">{{selectTime[timeIndex].timeName}}</view>
                    </view>
                    <view v-else class="roomTitle text-9152fa">
                        请选择时间<text class="cuIcon-right"></text>
                    </view>
                </picker>
            </view>
            <!-- 签到方式 -->
            <view class="signWay">
                <view class="signWay-tips u-f u-f-jsb">
                    <text>签到方式</text>
                </view>
                <view class="signWay-option">                    
                    <radio-group class="block" @change="RadioChange">
                        <view class="cu-form-group margin-top">
                            <view>
                                <text class="title">签到码</text>
                                <radio class="blue" :class="radio=='code'?'checked':''" :checked="radio=='code'?true:false" value="code"></radio>
                            </view>
                            <view style="margin-left: 50rpx;">
                                <text class="title">GPS</text>
                                <radio class="blue" :class="radio=='gps'?'checked':''" :checked="radio=='gps'?true:false" value="gps"></radio>
                            </view>
                        </view>
                        <view v-if="signInWay == 'gps'">
                            <view style="color: #F4645F;padding: 20rpx;">温馨提示：为了更精确的定位，请开启WLAN</view>
                            <view style="padding: 3rpx 20rpx;">当前位置：{{dizhi}}</view>
                            <map style="width: 100%; height: 200px;" :latitude="latitude" :longitude="longitude" :markers="covers"></map>
                        </view>
                        <view v-else>
                            <text class=""></text>
                        </view>
                    </radio-group>
                </view>
            </view>
            <!-- 发起签到按钮 -->
            <button class="btnOk" formType="submit">开始签到</button>  
        </view>
	</form>
</template>

<script>
    var that, userId;
	export default {
		data() {
			return {
                // 选择班级
                selectRoom: [],
                roomIndex: 0,
                room_name: [],
                room_id: [],
                roomShow: false,
                // 签到时间
                selectTime: [  //时间数据
                        { "timeId" : "1min" , "timeName":"1分钟"},
                        { "timeId" : "2min" , "timeName":"2分钟"},  
                        { "timeId" : "3min" , "timeName":"3分钟"},  
                        { "timeId" : "4min" , "timeName":"4分钟"},  
                        { "timeId" : "5min" , "timeName":"5分钟"}
                    ],
                timeIndex: 0, //选择的picker索引
                time_name:[],//picker中range属性值
                time_id:[],//存储id数组
                timeShow: false,
                // 选择签到方式
                signInWay: '',
                radio: 'code',
                norRadio: false,
                
                dizhi:'',//地址
                latitude: 0,//纬度
                longitude: 0,//经度
                covers: [{//标记
                    latitude: 0,
                    longitude: 0,
                    iconPath: './static/images/inc/location.png'
                }]
 
			}
		},
        onLoad() {
            that = this;
            let loginRes = this.checkLogin('../roomList/roomList', 1);
            if(!loginRes){return ;}
            this.getRoom();
            // 将selectTime遍历赋值
            this.selectTime.forEach( e => {
                this.time_name.push(e.timeName);
                this.time_id.push(e.timeId);
            })
            // 动态创建标题
            uni.setNavigationBarTitle({
            	title: '创建签到'
            });
        },
		methods: {
            //绑定选择课堂
            bindSelectRoom: function(e) {
                that.roomShow = true;
            	that.roomIndex = e.detail.value;
            },
            //绑定选择时间
			bindSelectTime: function(e) {
			    that.timeShow = true;
				that.timeIndex = e.detail.value;
			},
            //获取课堂信息
            getRoom: function(e) {
                userId = uni.getStorageSync('USERID');
                uni.request({
                    url: that.apiServer + 'create&m=getRoom',
                    method: 'POST',
                    header : {'content-type':'application/x-www-form-urlencoded'},
                    data: {
                        userId
                    },
                    success: res => {
                        res = res.data;
                        if (res.status == 'ok') {
                            let jsonArr = JSON.parse(res.data);
                            this.selectRoom = jsonArr;
                        }
                        this.selectRoom.forEach( e => {
                            this.room_name.push(e.roomName);
                            this.room_id.push(e.roomId);
                        })
                    },
                });
            },
            //单选框切换
            RadioChange: function(e) {
            	this.signInWay = e.detail.value
                this.norRadio = true
                //判断具体功能
                if (e.detail.value == 'gps') {
                    //获取本地经纬度和位置
                    uni.getLocation({
                        type: 'gcj02',
                        success: (res) => {
                            this.latitude = res.latitude;
                            this.longitude = res.longitude;
                            this.covers[0].latitude = res.latitude;
                            this.covers[0].longitude = res.longitude;
                            //获取位置                     
                            var point = new plus.maps.Point(this.longitude, this.latitude);
                            plus.maps.Map.reverseGeocode(
                                point,
                                {},
                                function(event) {
                                    var address = event.address; // 转换后的地理位置
                                    var point = event.coord; // 转换后的坐标信息
                                    var coordType = event.coordType; // 转换后的坐标系类型
                                    that.dizhi = address;
                                },
                                
                            );
                        }
                    });
                }
            },
            //表单提交代码
            formSubmit: function (e) { // 创建班级
                // 用户ID
                let userId = uni.getStorageSync('USERID');
                // 课堂
                let roomId = this.room_id[e.detail.value.selectorRoom];
                // 签到时间
                let signTime = this.time_id[e.detail.value.selectorTime];
                // 签到方式
                let signInWay = this.signInWay;
                // 判断是否为空
                if (typeof(roomId) == 'undefined') {
                    uni.showToast({title:'请选择课堂',duration: 1500,icon: 'none'});
                    return false;
                } else if (typeof(signTime) == 'undefined') {
                    uni.showToast({title:'请选择时间',duration: 1500,icon: 'none'});
                    return false;
                } else if (this.norRadio == true && signInWay.length == 0) {
                    uni.showToast({title:'请选择签到方式',duration: 1500,icon: 'none'});
                    return false;
                } else if (this.norRadio == false) {
                    signInWay = 'code'; // 默认为普通方式
                }
                
                switch (signInWay){
                    case 'code':
                        var signCode = this.getRand();
                        uni.request({
                            url: that.apiServer + 'sign&m=signInByCode',
                            method: 'POST',
                            header : {'content-type':'application/x-www-form-urlencoded'},
                            data: {
                                userId,roomId,signTime,signInWay,signCode
                            },
                            success: res => {
                                res = res.data;
                                if (res.status == 'ok') {
                                    uni.showToast({title:'发起签到成功',image: '/static/images/inc/icon_success.png',icon: 'none'});
                                    setTimeout(function(e) {
                                        uni.navigateTo({
                                            url:'../signer/signer?signId='+res.data+'&openMode=create'
                                        })
                                    },500);
                                } else if (res.status == 'error') {
                                    uni.showToast({title:res.data,image: '/static/images/inc/icon_warning.png',icon: 'none'});
                                }
                            },
                        });
                        break;
                    case 'gps':
                        let latitude = this.latitude;
                        let longitude = this.longitude;
                        uni.request({
                            url: that.apiServer + 'sign&m=signInByGps',
                            method: 'POST',
                            header : {'content-type':'application/x-www-form-urlencoded'},
                            data: {
                                userId,roomId,signTime,signInWay,latitude,longitude
                            },
                            success: res => {
                                res = res.data;
                                if (res.status == 'ok') {
                                    uni.showToast({title:'发起签到成功',image: '/static/images/inc/icon_success.png',icon: 'none'});
                                    setTimeout(function(e) {
                                        uni.navigateTo({
                                            url:'../signer/signer?signId='+res.data+'&openMode=create'
                                        })
                                    },500);
                                } else if (res.status == 'error') {
                                    uni.showToast({title:res.data,image: '/static/images/inc/icon_warning.png',icon: 'none'});
                                }
                            },
                        });
                        break;
                    default:
                        break;
                }
            },
            //获得随机码
            getRand: function() {
                let code = new Array();
                let codeLength = 4;            
                let selectChar = new Array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
                for (let i = 0; i < codeLength; i++) {
                    let charIndex = Math.floor(Math.random() * 10); // 56是selectChar数组的长度
                    code += selectChar[charIndex];
                }
                if (code.length != codeLength) {
                    this.getRandomCode();
                }
                return code;
            }
		}
	}
</script>

<style>
    page{
        background-color:#F2F2F2;
    }
    .header{
        height: 250rpx;
        width: 100%;
        background:linear-gradient(#9152fa,#67A2FA);
        border-radius: 0 0 60% 60%/0 0 25% 25% ;
        border-top: none;
    }
    .header-text{
        margin-top: 80rpx;
        font-size: 50rpx;
        color: #fff;
    }
    .header-logo{
        width: 100rpx;
        height: 100rpx;
        margin: 30rpx auto;
    }
    picker{
        align-self: center;
    }
    .room{
        width: 95%;
        height: auto;
        margin: 50rpx 0 15rpx 0;
        border-radius: 15rpx;
        background-color: #fff;
        box-shadow:0rpx 2rpx 2rpx #aaaaaa;
    }
    .roomTitle{
        padding: 20rpx;
        font-size: 38rpx;
    }
    .roomTitleIf{
        padding: 20rpx;
        font-size: 35rpx;
        color: #57A3FB;
    }
    .time{
        width: 95%;
        height: auto;
        margin: 15rpx;
        border-radius: 15rpx;
        background-color: #fff;
        box-shadow:0rpx 2rpx 2rpx #aaaaaa;
    }
    .signWay{
        width: 95%;
        height: auto;
        margin: 15rpx;
        border-radius: 15rpx;
        background-color: #fff;
        box-shadow:0rpx 2rpx 2rpx #aaaaaa;
    }
    .signWay-tips text{
        padding: 20rpx;
        font-size: 38rpx;
    }
    .cu-form-group{
        background: none;
        justify-content: flex-start;
    }
    .btnOk{
        width: 300rpx;
        height: auto;
        background:linear-gradient(right,#9152fa,#67A2FA);
        border-radius: 70rpx;
        box-shadow:0rpx 5rpx 2rpx #aaaaaa;
        margin: 100rpx 0 30px 0;
        font-size: 38rpx;
        color: #fff;
    }
</style>
