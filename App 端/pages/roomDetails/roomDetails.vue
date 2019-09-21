<template>
    <view>
        <cu-custom bgColor="bg-white" :isBack="true">
            <block slot="content" class="text-xl">{{roomName}}</block>
        </cu-custom>
        <view class="u-f-column u-f-ac">
            <view class="card-head u-f u-f-jsb">
                <view class="card-title">创建时间</view>
                <view class="card-info">{{joinTime}}</view>
            </view>
            <view class="card-head">
                <view class="card-title">组成班级</view>
                <view class="card-info" v-for="(item,index) in classes" :key="index">
                    {{item}}
                </view>
            </view>
            <view class="card-list">
                <view class="cu-list menu-avatar">
                    <view class="cu-item card-list-item margin-bottom-sm" v-for="(item,index) in peoList" :key="index">
                        <view class="cu-avatar round lg" :style="[{backgroundImage:'url(/static/images/me/header-default.png)'}]"></view>
                        <view class="content">
                            <view class="text-black text-lg">{{item.name}}</view>
                            <view class="text-gray text-sm">
                                <text>{{item.joinTime}}加入</text>
                            </view>
                        </view>
                        <view class="action">
                            <view class="cuIcon-roundcheckfill text-green" style="font-size: 37rpx;"></view>
                        </view>
                        <view class="move">
                            <view class="bg-red" @click="delUser(item.userId)">删除</view>
                        </view>
                    </view>
                </view>
            </view>
        </view>
    </view>
</template>

<script>
    var that, roomId;
    import timestampToTime from "../../common/timestampToTime.js"
	export default {
		data() {
			return {
                peoList: [],
                swiperHeight:0,
                roomName:'',
                joinTime:'',
                classes:''
			}
		},
        onLoad(option) {
            that = this; 
            let loginRes = this.checkLogin('../roomList/roomList', 1);
            if(!loginRes){return ;}
            
            roomId = option.roomId;
            this.getRoomDetails();
            uni.getSystemInfo({
                success: (res) => {
                    let height = res.windowHeight - this.CustomBar - uni.upx2px(105);
                    this.swiperHeight = height;
                }
            })
        },
        onPullDownRefresh() {
            setTimeout(()=> {
                this.getRoomDetails();
                uni.stopPullDownRefresh();
            }, 2000);
        },
		methods: {
            getRoomDetails: function() {
                uni.request({
                    url: that.apiServer + 'create&m=getRoomDetails',
                    method: 'POST',
                    header: {
                        'content-type': 'application/x-www-form-urlencoded'
                    },
                    data: {
                        roomId
                    },
                    success: res => {
                        res = res.data;
                        if (res.status == 'ok') {
                            this.roomName = res.data.roomName;
                            this.joinTime = timestampToTime.timestampToTime(res.data.joinTime,5);
                            this.classes = res.data.joinClass;
                            this.peoList = res.data.userList;
                        } else if (res.status == 'error') {
                            uni.showToast({title:res.data,icon:'none'});
                        }
                    },
                });
            }
		}
	}
</script>

<style>
    /* 顶部card */
    .card-head {
        width: 94%;
        height: auto;
        margin-top: 12rpx;
        padding: 20rpx;
        background: #FFFFFF;
        border-radius: 8rpx;
        box-shadow: 0 0 2px 0 rgba(0,0,0,0.2);
    }
    .card-list {
        width: 94%;
    }
    .card-list-item{
        margin-top: 12rpx;
        background: #FFFFFF;
        border-radius: 8rpx;
        box-shadow: 0 0 2px 0 rgba(0,0,0,0.2);
    }
    .card-title {
        font-size: 34rpx;
    }
    .card-info {
        font-size: 30rpx;
        color: #6E6E6E;
    }
    
    /* scroll */
    
    .page.show {
    	overflow: hidden;
    }
    .cu-list {
        border-radius: 8rpx;
    }
</style>
