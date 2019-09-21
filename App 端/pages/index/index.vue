<template>
    <view class="container">
        <cu-custom bgColor="bg-white" :isDef="true">
            <block slot="defIcon">
                <text class="cuIcon-scan" style="font-size: 40rpx;margin-left: 20rpx;" @tap="scanQr"></text>
            </block>
        	<block slot="content"><text class="text-xxl">签到助手</text></block>
        </cu-custom>
        <view class="card u-f u-f-jsa">
            <navigator url="../classList/classList" hover-class="none">
                <image class="tool-img" src="https://ae01.alicdn.com/kf/Hbbe30d92efb74f1c9ca5642f999ed679e.png" mode="widthFix"></image>
            </navigator>
            <navigator url="../roomList/roomList" hover-class="none">
                <image class="tool-img" src="https://ae01.alicdn.com/kf/H224ff34e1491483a8b38ec76ea8fdf4dg.png" mode="widthFix"></image>
            </navigator>
        </view>
        <view class="card u-f u-f-jsa">
            <navigator url="../record/record" hover-class="none">
                <image class="tool-img" src="https://ae01.alicdn.com/kf/Hb7e941c76c52478391a7026040ca0d44R.png" mode="widthFix"></image>
            </navigator>
            <navigator url="../statistics/statistics" hover-class="none">
                <image class="tool-img" src="https://ae01.alicdn.com/kf/H5408b14bd2634f20b9dbfa1d6dc646d0t.png" mode="widthFix"></image>
            </navigator>
        </view>
        <view class="begin u-f-jc">
            <navigator url="../signIn/signIn" hover-class="none">
                <image src="https://ae01.alicdn.com/kf/H97acef9fcb1046cf9d55a1755fdad608W.png" mode=""></image>
            </navigator>
        </view>
        <view class="end u-f u-f-jsb">
            <navigator url="../me/me" hover-class="none">
                <image class="left-img" src="https://ae01.alicdn.com/kf/H14aba6a03e3a40cbbcc48031fa1d06f27.png"></image>
            </navigator>
            <navigator url="../setup/setup" hover-class="none">
                <image class="right-img" src="https://ae01.alicdn.com/kf/H2551e68025904791b7c0bd79420fe181c.png"></image>
            </navigator>
        </view>
    </view>
</template>

<script>
    // #ifdef APP-PLUS
    import permision from "@/common/permission.js";
    import charCode from "../../common/charcode.js";
    // #endif
    var that;
    export default {
        data() {
            return {
            }
        },
        onLoad() {
            that = this;
            let loginRes = this.checkLogin('../index/index', 1);
            if(!loginRes){return ;}
        },
        methods: {
            async scanQr() {
            	// #ifdef APP-PLUS
            	let status = await this.checkPermission();
            	if (status !== 1) {
            		return;
            	}
            	// #endif
            	uni.scanCode({
            		success: res => {
            			let qrRes = decodeURIComponent(charCode.deCode(res.result));
                        // 开始扫码加入班级
                        let name = uni.getStorageSync('realName');
                        let userId = uni.getStorageSync('USERID');
                        // 判断是否有真实姓名
                        if (name == null) {
                            uni.showToast({title:'请绑定自己的真实姓名',icon:'none','duration':2000});
                            uni.navigateTo({
                                url: '../me/me',
                            });
                            return;
                        }
                        uni.request({
                            url: that.apiServer + 'create&m=joinClass',
                            method: 'POST',
                            header: {'content-type': 'application/x-www-form-urlencoded'},
                            data: {qrRes, name, userId},
                            success: res => {
                                res = res.data;
                                if (res.status == 'sorry') {
                                    uni.showToast({title:res.data,icon:'none','duration':2000});
                                    return;
                                } else if (res.status == 'ok') {
                                    uni.showToast({title:res.data,icon:'none','duration':2000});
                                    setTimeout(e => {
                                        uni.navigateTo({
                                            url:'../classList/classList?tabIndex=1'
                                        })
                                    },500)
                                } else if (res.status == 'error') {
                                    uni.showToast({title:res.data,icon:'none','duration':2000});
                                    return;
                                }
                            },
                        });
            		},
            		fail: err => {
            			console.log(err);
            		}
            	});
            }
            // #ifdef APP-PLUS
            ,
            async checkPermission(code) {
            	let status = permision.isIOS ? await permision.requestIOS('camera') : await permision.requestAndroid(
            		'android.permission.CAMERA');
            
            	if (status === null || status === 1) {
            		status = 1;
            	} else {
            		uni.showModal({
            			content: '需要相机权限',
            			confirmText: '设置',
            			success: function(res) {
            				if (res.confirm) {
            					permision.gotoAppSetting();
            				}
            			}
            		});
            	}
            	return status;
            }
            // #endif
        }
    }
</script>

<style>
    page {
        background: white;
    }
    .tool-img{
        width: 300rpx;
        height: 200rpx;
        opacity: 0.8;
        box-shadow:5rpx 5rpx 5rpx #aaaaaa;
        border-radius: 30rpx;
    }
    .card{
        margin-top: 80rpx;
        margin-bottom: 30rpx;
    }
    .begin{
        margin-top: 50rpx;
        padding: 50rpx;
    }
    .begin image{
        height: 300rpx;
        width: 300rpx;
        box-shadow: 0 0 5px 0 rgba(0,0,0,0.8);
        border-radius: 50%;
    }
    .end>navigator:first-child{
        position: fixed;
        left: 25rpx;
        bottom: 15rpx;
    }
    .end>navigator:last-child{
        position: fixed;
        right: 25rpx;
        bottom: 15rpx;
    }
    .end image{
        width: 70rpx;
        height: 70rpx;
    }
</style>
