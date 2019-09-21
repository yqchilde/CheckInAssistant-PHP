<template>
    <form @submit="formSubmit">
        <cu-custom></cu-custom> 
        <view class="zai-box">
            <image src="https://ae01.alicdn.com/kf/Ha0a11d254de7481ba7b3c7d569815a1eT.png" mode='aspectFit' class="zai-logo"></image>
            <view class="zai-title">签到助手</view>
            <view class="zai-form">
                <input type="number" class="zai-input" name="phoneNumber" placeholder-class placeholder="请输入手机号码" />
                <view class="zai-input-btn">
                    <input type="text" class="zai-input" name="verifyCode" placeholder-class placeholder="验证码" />
                    <view class="zai-checking" @tap="showModal" data-target="showVerify" v-if="state===false">获取验证码</view>
                    <view class="zai-checking zai-time" v-if="state===true">倒计时{{ currentTime }}s</view>
                </view>
                <input password class="zai-input" name="password" placeholder-class placeholder="请输入密码" />
                <button class="zai-btn" form-type="submit">立即注册</button>
                <navigator url="../login/login" open-type='navigateBack' hover-class="none" class="zai-label">已有账号，点此去登录.</navigator>
            </view>
        </view>
        
        <view class="cu-modal" :class="modalShow=='showVerify'?'show':''">
        	<view class="cu-dialog" style="width: 350upx;height: 200upx;" >
        		<view class="bg-img"  :style="{'background-image': verifySrc}" @click="getRandomCode">
        			<view class="cu-bar justify-end text-white">
        				<view class="action" @tap="hideModal">
        					<text class="cuIcon-close "></text>
        				</view>
        			</view>
        		</view>
        		<view class="cu-bar bg-white">
        			<view class="action margin-0 flex-sub  solid-left" @tap="hideModal">确定</view>
        		</view>
        	</view>
        </view>
        
    </form>
</template>

<script>
    var phoneNumber, password, verifyCode, that;
    export default {
        data() {
            return {
                state: false, //是否开启倒计时
                totalTime: 60, //总时间，单位秒
                recordingTime: 0, //记录时间变量
                currentTime: 0, //显示时间变量
                modalHidden: true, //模态框隐藏
                verifySrc: "" ,//验证码图片地址
                modalShow: null,
            }
        },
        onLoad() {
            that = this;
        },
        methods: {
            showModal: function(e) {
                this.modalShow = e.currentTarget.dataset.target;
                this.checking();
            },
            hideModal: function (e) {
            	this.modalShow = null
            },
            checking: function() {
                this.getRandomCode();
                //把显示时间设为总时间
                this.currentTime = this.totalTime;
                //开始倒计时
                this.state = true;
                //执行倒计时
                this.checkingTime();
            },
            checkingTime: function() {
                //判断是否开启
                if (this.state) {
                    //判断显示时间是否已到0，判断记录时间是否已到总时间
                    if (this.currentTime > 0 && this.recordingTime <= this.totalTime) {
                        //记录时间增加 1
                        this.recordingTime = this.recordingTime + 1;
                        //显示时间，用总时间 - 记录时间
                        this.currentTime = this.totalTime - this.recordingTime;
                        //1秒钟后，再次执行本方法
                        setTimeout(() => {
                            //定时器内，this指向外部，找不到vue的方法，所以，需要用that变量。
                            that.checkingTime();
                        }, 1000)
                    } else {
                        //时间已完成，还原相关变量
                        this.modalHidden = true;;
                        this.state = false; //关闭倒计时
                        this.recordingTime = 0; //记录时间为0
                        this.currentTime = this.totalTime; //显示时间为总时间
                    }
                } else {
                    //倒计时未开启，初始化默认变量
                    this.state = false;
                    this.recordingTime = 0;
                    this.currentTime = this.totalTime;
                }
            },
            getRandomCode: function() { // 生成验证码
                let code = new Array();
                let codeLength = 6; //验证码的长度               
                let selectChar = new Array(2, 3, 4, 5, 6, 7, 8, 9, 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k',
                    'l', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
                    'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
                    'Y', 'Z');
                for (let i = 0; i < codeLength; i++) {
                    let charIndex = Math.floor(Math.random() * 56); // 56是selectChar数组的长度
                    code += selectChar[charIndex];
                    verifyCode = code;
                }
                if (code.length != codeLength) {
                    this.getRandomCode();
                }                              
                uni.request({  
                    url: that.apiServer + 'user&m=verifyView',
                    method: 'POST',
                    header: {
                        'content-type': 'application/x-www-form-urlencoded'
                    },
                    data: {
                        code
                    },
                    success: (res) => {
                        res = res.data;
                        let verifyImg = 'url(' + res.data +')';
                        this.verifySrc = verifyImg;
                    }  
                });  
            },
            formSubmit: function(e) { // 检验表单数据
                phoneNumber = e.detail.value.phoneNumber;
                password = e.detail.value.password;
                let inputCode = e.detail.value.verifyCode;
                // 正则表达式验证
                let phoneNumberReg = /^1[3456789]\d{9}$/;
                let passwordReg = /^(\w){8,16}$/;
                let inputCodeReg = /^[A-Za-z0-9]{6,6}$/;
                
                if (phoneNumber == '') {
                    uni.showToast({
                        title: '手机号不能为空',
                        duration: 1500,
                        icon: 'none'
                    });
                    return false; 
                } else if (!phoneNumberReg.test(phoneNumber)) {
                    uni.showToast({
                        title: '手机号格式不正确',
                        duration: 1500,
                        icon: 'none'
                    });
                    return false;
                } else if (inputCode == '') {
                    uni.showToast({
                        title: '验证码不能为空',
                        duration: 1500,
                        icon: 'none'
                    });
                    return false;
                } else if (!inputCodeReg.test(inputCode)) {
                    uni.showToast({
                        title: '请输入6位验证码',
                        duration: 1500,
                        icon: 'none'
                    });
                    return false;
                } else if (!verifyCode) {
                    uni.showToast({
                        title: '请先获取验证码',
                        duration: 1500,
                        icon: 'none'
                    });
                    return false;
                } else if (inputCode.toLowerCase() !== verifyCode.toLowerCase()) {
                    uni.showToast({
                        title: '验证码不正确',
                        duration: 1500,
                        icon: 'none'
                    });
                    return false;
                } else if (password == '') {
                    uni.showToast({
                        title: '密码不能为空',
                        duration: 1500,
                        icon: 'none'
                    });
                    return false;
                } else if (!passwordReg.test(password)) {
                    uni.showToast({
                        title: '密码长度6~20且只能由字母、数字、下划线组成',
                        duration: 1500,
                        icon: 'none'
                    });
                    return false;
                } else {
                    uni.showLoading();
                    uni.request({
                        url: that.apiServer + 'user&m=register',
                        method: 'POST',
                        header: {
                            'content-type': 'application/x-www-form-urlencoded'
                        },
                        data: {
                            phoneNumber,
                            password,
                        },
                        // 请求成功执行的代码
                        success: res => {
                            res = res.data;
                            if (res.status == "error") {
                                // 注册失败
                                uni.hideLoading();
                                uni.showToast({
                                    title:res.data,
                                    image: '../../static/images/inc/icon_warning.png',
                                    duration: 2000
                                })
                                return false;
                            } else if (res.status == "ok") {
                                // 注册失败
                                uni.hideLoading();
                                uni.showToast({
                                    title:res.data,
                                    image: '../../static/images/inc/icon_success.png',
                                    duration: 2000
                                });
                                setTimeout(function() {
                                    uni.reLaunch({
                                    url:'../login/login?mode=register&phoneNumber='+phoneNumber+'&password='+password,
                                    })
                                },2500);
                            }
                        },
                    });
                }
            },
        }
    }
</script>

<style>
    page{
        background: white;
    }
    .zai-box {
        padding: 0 100upx;
        position: relative;
    }

    .zai-logo {
        width: 100%;
        width: 100%;
        height: 300upx;
    }

    .zai-title {
        position: absolute;
        top: 0;
        line-height: 360upx;
        font-size: 68upx;
        color: #fff;
        text-align: center;
        width: 100%;
        margin-left: -100upx;
    }

    .zai-form {
        margin-top: 250upx;
    }

    .zai-input {
        background: #e2f5fc;
        margin-top: 30upx;
        border-radius: 100upx;
        padding: 20upx 40upx;
        font-size: 36upx;
        -webkit-box-sizing: content-box;
        box-sizing: content-box;
    }

    .input-placeholder,
    .zai-input {
        color: #94afce;
    }

    .zai-label {
        padding: 60upx 0 30upx 0;
        text-align: center;
        font-size: 30upx;
        color: #a7b6d0;
    }

    .zai-btn {
        background: #ff65a3;
        color: #fff;
        border: 0;
        border-radius: 100upx;
        font-size: 36upx;
        margin-top: 50upx;
    }

    .zai-btn:after {
        border: 0;
    }

    /*验证码输入框*/
    .zai-input-btn {
        position: relative;
    }

    .zai-input-btn .zai-input {
        padding-right: 260upx;
    }

    .zai-checking {
        position: absolute;
        right: 0;
        top: 0;
        background: #ff65a3;
        color: #fff;
        border: 0;
        border-radius: 110upx;
        font-size: 36upx;
        margin-left: auto;
        margin-right: auto;
        padding-left: 28upx;
        padding-right: 28upx;
        box-sizing: border-box;
        text-align: center;
        text-decoration: none;
        line-height: 2.55555556;
        -webkit-tap-highlight-color: transparent;
        overflow: hidden;
        padding-top: 2upx;
        padding-bottom: 2upx;
    }

    .zai-checking.zai-time {
        background: #a7b6d0;
    }

    /*按钮点击效果*/
    .zai-btn.button-hover {
        transform: translate(1upx, 1upx);
    }

    .verifyCode {
        display: flex;
        flex-flow: nowrap;
        justify-content: center;
    }

    #verifyCode {
        height: 100upx;
        width: 200upx;
        margin-bottom: 10upx;
    }
</style>
