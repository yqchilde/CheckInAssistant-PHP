<template>
    <view>
        <cu-custom bgColor="bg-white" :isBack="true">
        	<block slot="content" class="text-xl">课堂列表</block>
        </cu-custom>
        
        <swiper-tab :tabBars="tabBars" :tabIndex="tabIndex" @tabtap="tabtap"></swiper-tab>
        
        <view class="swiper-top">
            <swiper class="swiper-box" :style="{'height':swiperHeight + 'px'}" :current="tabIndex" @change="tabChange">
                <swiper-item v-for="(items,index) in roomList" :key="index">
                    <scroll-view scroll-y class="list" :style="{'height':swiperHeight + 'px'}">
                        <template v-if="items.list.length>0">
                            <block v-for="(item, index1) in items.list" :key="index1">
                                <room-list :item="item" :index="index1" :tabIndex="tabIndex" @getRoomDetails="getRoomDetails" @delRoom="delRoom"></room-list>
                            </block>
                        </template>
                        <template v-else>
                            <no-thing :swiperHeight="swiperHeight"></no-thing>
                        </template>
                    </scroll-view>
                </swiper-item>
            </swiper>
        </view>
        
        <uni-fab :style="{display: floatButton}" ref="fab" :pattern="pattern" :horizontal="horizontal" :vertical="vertical" :direction="direction" @tap="toCreate" />
    </view>
</template>

<script>
    var that;
    import uniFab from '@/components/uni-fab/uni-fab.vue';
    import roomList from "../../components/room-list.vue";
    import swiperTab from "../../components/swiper-tab.vue";
    import noThing from "../../components/no-thing.vue";
    import timestampToTime from "../../common/timestampToTime.js"
	export default {
        components: {
        	uniFab,
            roomList,
            swiperTab,
            noThing
        },
		data() {
			return {
                tabIndex: 0,
                tabBars: [
                    {name: '已创建',id: 'create'},
                    {name: '已加入',id: 'join'},
                ],
                swiperHeight:0,
                leftColor: ['#9152fa','#2692ff','#fc7c1c','#0cd452','#fc424b'], //左框颜色
                roomList: [
                    {
                        list:[]
                    },
                    {
                        list:[]
                    }
                ], //课堂列表数据
                horizontal: 'right', //水平向右
                vertical: 'bottom', //垂直向下
                direction: 'horizontal', //弹出方向为水平方向
                pattern: {
                	color: '#7A7E83', //阴影颜色
                	backgroundColor: '#fff', //加号颜色
                	selectedColor: '#9152fa', //选中菜单颜色
                	buttonColor: '#9152fa'//悬浮按钮背景颜色
                },
                floatButton: 'inline',
                flag: 0,
                lastX: 0,
                lastY: 0,
			}
		},
        onLoad() {
            that = this;
            let loginRes = this.checkLogin('../roomList/roomList', 1);
            if(!loginRes){return ;}
            this.tabIndex = 0;
            this.getInfo(this.tabIndex);
            uni.getSystemInfo({
                success: (res) => {
                    let height = res.windowHeight - this.CustomBar - uni.upx2px(105);
                    this.swiperHeight = height;
                }
            })
        },
		methods: {
            tabtap(index) {
                this.tabIndex = index
            },
            tabChange(e) {
                this.tabIndex = e.detail.current;
                uni.showLoading({
                    title:'获取数据...'
                })
                setTimeout(()=> {
                    this.roomList[this.tabIndex].list = [];
                    this.getInfo(this.tabIndex);
                    uni.hideLoading();
                }, 500);
            },
            getInfo(tabIndex = 0) { //获取数据信息
                let userId = uni.getStorageSync('USERID');
                uni.request({
                    url: that.apiServer + 'create&m=roomList',
                    method: 'POST',
                    header: {
                        'content-type': 'application/x-www-form-urlencoded'
                    },
                    data: {
                        userId,tabIndex
                    },
                    success: res => {
                        res = res.data;
                        if (typeof(res.data) == 'undefined' || res.data == null) {
                            this.roomList[this.tabIndex].list = [];
                            return;
                        } else {
                            for (let i = 0; i < res.data.length; i++) { //处理一下拼装随机颜色
                                var List = JSON.parse(res.data[i]); // 接口获取到的所有课堂数据
                                let j = i % this.leftColor.length; //排列颜色数组
                                List.leftColor = this.leftColor[j];
                                List.roomName = timestampToTime.timestampToTime(List.joinTime,4) + List.roomName;
                                List.joinTime = timestampToTime.timestampToTime(List.joinTime,2);
                                this.roomList[this.tabIndex].list.push(List);
                            }
                        }
                    },
                });
            },
            toCreate: function(e) {
                uni.navigateTo({
                    url: '../createRoom/createRoom',
                    success: res => {},
                    fail: () => {},
                    complete: () => {}
                });
            },
            delRoom: function(roomId) { // 删除课堂
                uni.showModal({
                    title:'温馨提示',
                    content:'确定删除该课堂吗？',
                    success: (res) => {
                        if (res.confirm) {
                            uni.request({
                                url: that.apiServer + 'create&m=delRoom',
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
                                        uni.showToast({title:res.data,icon:'none'});
                                        //重载页面数据
                                        this.roomList[this.tabIndex].list = [];
                                        this.getInfo(this.tabIndex);
                                    } else if (res.status == 'error') {
                                        uni.showToast({title:res.data,icon:'none'});
                                    }
                                },
                                fail: () => {},
                                complete: () => {}
                            });
                        }
                    }
                })
            },
            getRoomDetails: function(roomId) {
                uni.navigateTo({
                    url:'../roomDetails/roomDetails?roomId=' + roomId
                })
            }
		}
	}
</script>
<style>
    /* 列表 */
    .swiper-top{
        position: relative;
        top: 90rpx;
    }
    .swiper-box{
        margin-top: 15rpx;
    }
</style>
