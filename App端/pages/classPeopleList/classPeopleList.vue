<template>
    <view>
        <cu-custom bgColor="bg-white" :isBack="true">
            <block slot="content" class="text-xl">班级人员</block>
        </cu-custom>
        <template v-if="tabIndex == 0">
            <scroll-view :scroll-y="modalName==null" class="page" :class="modalName!=null?'show':''">
                <view class="cu-list menu-avatar margin-top">
                    <view class="cu-item" :class="modalName=='move-box-'+ index?'move-cur':''" v-for="(item,index) in peoList" :key="index"
                     @touchstart="ListTouchStart" @touchmove="ListTouchMove" @touchend="ListTouchEnd" :data-target="'move-box-' + index">
                        <view class="cu-avatar round lg" :style="[{backgroundImage:'url(/static/images/me/header-default.png)'}]"></view>
                        <view class="content">
                            <view class="text-black">{{item.name}}</view>
                            <view class="text-gray text-sm">
                                <text>{{item.joinTime}}加入</text>
                            </view>
                        </view>
                        <view class="action">
                            <view class="cuIcon-roundcheckfill text-green"></view>
                        </view>
                        <view class="move">
                            <view class="bg-red" @click="delUser(item.userId)">删除</view>
                        </view>
                    </view>
                </view>
            </scroll-view>
        </template>
        <template v-else-if="tabIndex == 1">
            <scroll-view :scroll-y="modalName==null" class="page" :class="modalName!=null?'show':''">
                <view class="cu-list menu-avatar margin-top">
                    <view class="cu-item" :class="modalName=='move-box-'+ index?'move-cur':''" v-for="(item,index) in peoList" :key="index">
                        <view class="cu-avatar round lg" :style="[{backgroundImage:'url(/static/images/me/header-default.png)'}]"></view>
                        <view class="content">
                            <view class="text-black">{{item.name}}</view>
                            <view class="text-gray text-sm">
                                <text>{{item.joinTime}}加入</text>
                            </view>
                        </view>
                        <view class="action">
                            <view class="cuIcon-roundcheckfill text-green"></view>
                        </view>
                    </view>
                </view>
            </scroll-view>
        </template>
    </view>
</template>

<script>
    var that, classId, classInfo;
	export default {
		data() {
			return {
                modalName: null,
                listTouchStart: 0,
                listTouchDirection: null,
                peoList: [],
                tabIndex:0
			}
		},
        onLoad(option) {
            that = this; 
            let loginRes = this.checkLogin('../classList/classList', 1);
            if(!loginRes){return ;}
            this.tabIndex = option.tabIndex;
            classId = option.classId;
            this.getPeoList();
        },
		methods: {
            getPeoList: function() {
                uni.request({
                    url: that.apiServer + 'create&m=getPeoList',
                    method: 'POST',
                    header: {
                        'content-type': 'application/x-www-form-urlencoded'
                    },
                    data: {
                        classId
                    },
                    success: res => {
                        res = res.data;
                        if (res.status == 'ok') {
                            res = JSON.parse(res.data);
                            if (typeof(res.userList) == 'undefined' || res.userList.length == 0) {
                                uni.showToast({title:'当前班级还未有成员加入',icon:'none'});
                            } else {
                                this.peoList = res.userList;
                                classInfo = res;
                            }
                        } else if (res.status == 'error') {
                            uni.showToast({title:res.data,icon:'none'});
                        }
                    },
                });
            },
			showModal(e) {
				this.modalName = e.currentTarget.dataset.target
			},
			hideModal(e) {
				this.modalName = null
			},
			// ListTouch触摸开始
			ListTouchStart(e) {
				this.listTouchStart = e.touches[0].pageX
			},
			// ListTouch计算方向
			ListTouchMove(e) {
				this.listTouchDirection = e.touches[0].pageX - this.listTouchStart > 0 ? 'right' : 'left'
			},
			// ListTouch计算滚动
			ListTouchEnd(e) {
				if (this.listTouchDirection == 'left') {
					this.modalName = e.currentTarget.dataset.target
				} else {
					this.modalName = null
				}
				this.listTouchDirection = null
			},
            delUser: function(userId) {
                let delId = userId;
                let joinArr = classInfo.userList;
                for (let i = 0; i < joinArr.length; i++) {
                    if (delId == joinArr[i].userId) {
                        joinArr.splice(i, 1);
                        let newJoin = JSON.stringify(classInfo);
                        uni.request({
                            url: that.apiServer + 'create&m=delUser',
                            method: 'POST',
                            header: {
                                'content-type': 'application/x-www-form-urlencoded'
                            },
                            data: {
                                newJoin,
                                classId
                            },
                            success: res => {
                                res = res.data;
                                if (res.status == 'ok') {
                                    uni.showToast({title:res.data,icon:'none'});
                                } else if (res.status == 'error') {
                                    uni.showToast({title:res.data,icon:'none'});
                                }
                            },
                            fail: () => {},
                            complete: () => {}
                        });
                    }
                }
            }
		}
	}
</script>

<style>    
    .page {
    	height: 100Vh;
    	width: 100vw;
    }
    
    .page.show {
    	overflow: hidden;
    }
    
    .cu-list>.cu-item.move-cur {
    	transform: translateX(-130upx)
    }
    
    .cu-list>.cu-item .move {
    	width: 130upx;
    }
</style>
