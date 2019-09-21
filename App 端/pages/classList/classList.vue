<template>
    <view>
        <cu-custom bgColor="bg-white" :isBack="true">
        	<block slot="content" class="text-xl">班级列表</block>
        </cu-custom>
        
        <swiper-tab :tabBars="tabBars" :tabIndex="tabIndex" @tabtap="tabtap"></swiper-tab>
        
        <view class="swiper-top">
            <swiper class="swiper-box" :style="{'height':swiperHeight + 'px'}" :current="tabIndex" @change="tabChange">
                <swiper-item v-for="(items,index) in classList" :key="index">
                    <scroll-view scroll-y class="list" :style="{'height':swiperHeight + 'px'}">
                        <template v-if="items.list.length>0">
                            <block v-for="(item, index1) in items.list" :key="index1">
                                <class-list :item="item" :index="index1" :tabIndex="tabIndex" @outClass="outClass" @goPeoList="goPeoList" @classModal="classModal" @delClass="delClass" @shareFc="shareFc"></class-list>
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
        <!-- 编辑班级 -->
        <form @submit="formEditClass">
            <view class="cu-modal" :class="classModalShow=='editClass'?'show':''">
                <view class="cu-dialog">
                    <view class="cu-bar bg-white justify-end">
                        <view class="content" style="color: #000; font-size: 35rpx;">修改班级信息</view>
                        <view class="action" @tap="classHide">
                            <text class="cuIcon-close text-red"></text>
                        </view>
                    </view>
                    <!-- input框 -->
                    <view class="bg-white padding-xs">
                        <view style="display: flex;justify-content: center">
                            <view style="margin-right: 15rpx;">班级名称：</view>
                            <view><input type="text" name="newName" class="text-left inputInfo" :value="inputName" placeholder="此处填写班级名称" /></view>
                        </view>
                        <view style="display: flex;justify-content: center;margin-top: 20rpx;">
                            <view style="margin-right: 15rpx;">容纳人数：</view>
                            <view><input type="text" name="newNum" class="text-left inputInfo" :value="inputPeoNum" placeholder="此处填写容纳人数" /></view>
                        </view>
                    </view>
                    <view class="cu-bar bg-white justify-end">
                        <view class="action">
                            <button class="cu-btn line-green text-green" @tap="classHide">取消</button>
                            <button form-type="submit" class="cu-btn bg-green margin-left" @tap="classHide">确定</button>
                        </view>
                    </view>
                </view>
            </view>
        </form>
        <!-- 邀请二维码 -->
        <view class="flex_row_c_c modalView" :class="qrShow?'show':''" @tap="hideQr()">
            <view class="flex_column">
                <view class="backgroundColor-white padding1vh border_radius_10px">
                    <image :src="poster.finalPath" mode="widthFix" class="posterImage"></image>
                </view>
                <view class="flex_row marginTop2vh">
                    <button type="primary" size="mini" @tap.prevent.stop="saveImage()">保存图片</button>
                    <button type="primary" size="mini" @tap.prevent.stop="share()">分享图片</button>
                </view>
            </view>
        </view>
        <view class="hideCanvasView">
            <canvas class="hideCanvas" canvas-id="ShareCanvasId" :style="{width: (poster.width||0) + 'px', height: (poster.height||0) + 'px'}"></canvas>
        </view>
    </view>
</template>

<script>
    var that;
    import classList from "../../components/class-list.vue";
    import swiperTab from "../../components/swiper-tab.vue";
    import uniFab from '@/components/uni-fab/uni-fab.vue';
    import _app from '@/common/QS-SharePoster/app.js';
    import getSharePoster from '@/common/QS-SharePoster/QS-SharePoster.js';
    import charCode from "../../common/charcode.js";
    import noThing from "../../components/no-thing.vue";
    
	export default {
        components: {
        	uniFab,
            classList,
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
                classList: [
                    {
                        list:[]
                    },
                    {
                        list:[]
                    }
                ], //班级列表数据
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
                classModalShow: null,
                inputName: '',
                inputPeoNum: '',
                postClassId: '',
                poster: {},
                qrShow: false,
                canvasId: 'ShareCanvasId'
			}
		},
        onLoad(option) {
            that = this;
            let loginRes = this.checkLogin('../classList/classList', 1);
            if(!loginRes){return ;}
            // tabbar定位
            if (typeof(option.tabIndex) != 'undefined') {
                this.tabIndex = option.tabIndex;
            } else {
                this.tabIndex = 0;
            }
            this.getInfo(this.tabIndex);
            uni.removeStorageSync('tmpIndex');
            uni.getSystemInfo({
                success: (res) => {
                    let height = res.windowHeight - this.CustomBar - uni.upx2px(105);
                    this.swiperHeight = height;
                }
            })
        },
		methods: {
            // tabbar点击切换
            tabtap(index) {
                this.tabIndex = index
            },
            // tabbar滑动切换
            tabChange(e) {
                this.tabIndex = e.detail.current;
                uni.showLoading({
                    title:'获取数据...'
                })
                setTimeout(()=> {
                    this.classList[this.tabIndex].list = [];
                    this.getInfo(this.tabIndex);
                    uni.hideLoading();
                }, 500);
            },
            // 获取信息
            getInfo(tabIndex = 0) {
                let userId = uni.getStorageSync('USERID');
                uni.request({
                    url: that.apiServer + 'create&m=classList',
                    method: 'POST',
                    header: {
                        'content-type': 'application/x-www-form-urlencoded'
                    },
                    data: {
                        userId,tabIndex
                    },
                    success: res => {
                        res = res.data;
                        // 判断是否获取到数据
                        if (typeof(res.data) == 'undefined' || res.data == null) {
                            this.classList[this.tabIndex].list = [];
                            return;
                        } else{
                            for (let i = 0; i < res.data.length; i++) { //处理一下拼装随机颜色
                                var List = res.data[i]; // 接口获取到的所有班级数据
                                // 判断是否有成员
                                if (typeof(JSON.parse(res.data[i].classInfo).userList) == 'undefined') {
                                    List['realPeoNum'] = 0; //真实班级人数
                                } else {
                                    List['realPeoNum'] = JSON.parse(res.data[i].classInfo).userList.length; //真实班级人数
                                }
                                let j = i % this.leftColor.length; //排列颜色数组
                                List['leftColor'] = this.leftColor[j];
                                this.classList[this.tabIndex].list.push(List);
                            }
                        }
                    },
                });
            },
            // 班级modal
            classModal(index) {
                this.classModalShow = 'editClass';
                this.inputName = this.classList[this.tabIndex].list[index].classname;
                this.inputPeoNum = this.classList[this.tabIndex].list[index].peonum;
                this.postClassId = this.classList[this.tabIndex].list[index].classid;
            },       
            // 班级modal隐藏
            classHide(e) {
            	this.classModalShow = null
            },
            // 跳转创建班级页面
            toCreate(e) {
                uni.navigateTo({
                    url: '../createClass/createClass',
                    success: res => {},
                    fail: () => {},
                    complete: () => {}
                });
            },
            // 修改班级信息
            formEditClass(e) { 
                let classId = this.postClassId;
                let newName = e.detail.value.newName;
                let newNum = e.detail.value.newNum;
                uni.request({
                    url: that.apiServer + 'create&m=editClass',
                    method: 'POST',
                    header: {
                        'content-type': 'application/x-www-form-urlencoded'
                    },
                    data: {
                        classId,
                        newName,
                        newNum
                    },
                    success: res => {
                        res = res.data;
                        if (res.status == 'ok') {
                            uni.showToast({title:res.data,icon:'none'});
                            //重载页面数据
                            this.classList[this.tabIndex].list = [];
                            this.getInfo();
                        } else if (res.status == 'error') {
                            uni.showToast({title:res.data,icon:'none'});
                        }
                    },
                });
            },
            // 获取班级成员
            goPeoList(classId) {
                uni.navigateTo({
                    url:'../classPeopleList/classPeopleList?classId='+classId+'&tabIndex='+this.tabIndex
                })
            },
            // 删除班级
            delClass(classId) {
                uni.showModal({
                    title:'温馨提示',
                    content:'确定删除该班级吗？',
                    success: (res) => {
                        if (res.confirm) {
                            uni.request({
                                url: that.apiServer + 'create&m=delClass',
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
                                        uni.showToast({title:res.data,icon:'none'});
                                        //重载页面数据
                                        this.classList[this.tabIndex].list = [];
                                        this.getInfo(this.tabIndex);
                                    } else if (res.status == 'error') {
                                        uni.showToast({title:res.data,icon:'none'});
                                    }
                                },
                            });
                        }
                    }
                })
            },
            // 退出班级
            outClass(classId) { 
                let userId = uni.getStorageSync('USERID');
                uni.showModal({
                    title:'温馨提示',
                    content:'确定退出该班级吗？',
                    success: (res) => {
                        if (res.confirm) {
                            uni.request({
                                url: that.apiServer + 'create&m=outClass',
                                method: 'POST',
                                header: {'content-type': 'application/x-www-form-urlencoded'},
                                data: { classId,userId },
                                success: res => {
                                    res = res.data;
                                    if (res.status == 'ok') {
                                        uni.showToast({title:res.data,icon:'none'});
                                        //重载页面数据
                                        this.classList[this.tabIndex].list = [];
                                        this.getInfo(this.tabIndex);
                                    } else if (res.status == 'error') {
                                        uni.showToast({title:res.data,icon:'none'});
                                    }
                                },
                            });
                        }
                    }
                })
            },
            // 分享邀请二维码，参数index是列表点击的索引
            async shareFc(index) {
                if (uni.getStorageSync('tmpIndex') === index) { //判断临时索引是否是点击的索引
                    this.qrShow = true;
                } else {
                    try{
                        uni.setStorageSync('tmpIndex', index); //将点击的索引绑定给本地sync，作为临时索引
                        var qrMsg = encodeURIComponent(JSON.stringify({uid:this.classList[this.tabIndex].list[index].userid,cid:this.classList[this.tabIndex].list[index].classid}));
                        let d = await getSharePoster({ //传入两个await参数，一个是类型，一个是canvasID
                        	type: 'testShareType',
                        	posterCanvasId: this.canvasId,
                        	qrCodeArray: (bgObj, type) => {
                        		return [{
                        			text: charCode.enCode(qrMsg),//传入userid和classid
                        			size: 580, //二维码大小
                        			dx: (bgObj.width-580)/2, //x坐标
                        			dy: bgObj.height-1200 //y坐标
                        		}]
                        	},
                        	imagesArray: (bgObj, type) => { //接收的第一个参数为背景图片的信息, 第二个参数是自定义标识（感觉这里用不到）, 图片为示例图片
                        		return [{
                        			url: 'https://ss3.bdstatic.com/70cFv8Sh_Q1YnxGkpoWK1HF6hhy/it/u=1314428097,3858988978&fm=26&gp=0.jpg',
                        			dx: 100,
                        			dy: bgObj.height - 300,
                        			circleSet: { // 圆形图片
                        				circle: true
                        			},
                        			infoCallBack(imageInfo) {
                        				let scale = 200/imageInfo.height;
                        				return {
                        					dWidth: imageInfo.width*scale,
                        					dHeight: 200
                        				}
                        			}
                        		}]
                        	},
                        	setCanvasWH: (bgObj, type) => { // 为动态设置画布宽高的方法，
                        		this.poster = bgObj;
                        	},
                        	setDraw: (obj) => {
                        		let {
                        			Context,
                        			bgObj,
                        			type
                        		} = obj;
                        		Context.setFillStyle('black');
                        		Context.setGlobalAlpha(0.3);
                        		Context.fillRect(0, bgObj.height - 400, bgObj.width, 400);
                        		Context.setGlobalAlpha(1);
                        		Context.setFillStyle('white');
                        		Context.setFontSize(100);
                                //输出姓名
                        		let text = uni.getStorageSync('realName');
                        		Context.fillText(text, bgObj.width - text.length * 50 - 160, bgObj.height - 175);
                                //输出班级
                                Context.setFillStyle('orange');
                                Context.setFontSize(110);
                                let textTitle = this.classList[this.tabIndex].list[index].classname;
                                Context.fillText(textTitle, (bgObj.width - textTitle.length * 110)/2, bgObj.height - 430);
                        	}
                        });
                        console.log('海报生成成功， 临时路径: ' + d.poster.tempFilePath)
                        this.poster.finalPath = d.poster.tempFilePath;
                        console.log(this.poster.finalPath);
                        this.qrShow = true;
                    }catch(e){
                        _app.hideLoading();
                        _app.showToast(JSON.stringify(e));
                        console.log(JSON.stringify(e));
                    }
                }
            },
            // 保存图片
            saveImage() {
            	uni.saveImageToPhotosAlbum({
            		filePath: this.poster.finalPath,
            		success(res) {
            			_app.showToast('保存成功');
            		}
            	})
            },
            // 分享
            share() {
            	// #ifdef APP-PLUS
            	_app.getShare(false, false, 2, '', '', '', this.poster.finalPath, false, false);
            	// #endif
            
            	// #ifndef APP-PLUS
            	_app.showToast('分享了');
            	// #endif
            },
            // 隐藏生成的图片
            hideQr() {
            	this.qrShow = false;
            }
		}
	}
</script>
<style>
    .classList{
        width: 90%;
        margin: 0 0 25rpx 0;
        border-radius: 15rpx;
        background-color: #fff;
        box-shadow:0rpx 2rpx 2rpx #aaaaaa;
        border-left-style: solid;
        border-left-width: 15rpx;
    }
    .classList-title, .classList-info, .classList-btn{
        padding: 20rpx;
        font-size: 38rpx;
    }
    .classList-img{
        width: 50rpx;
        height: 50rpx;
    }
    .classList-info text{
        font-size: 30rpx;
    }
    .classBtn{
        background: #F2F2F2;
        border-radius: 50rpx;
        padding: 10rpx;
        font-size: 28rpx;
        color: #969696;
    }
    .inputInfo{
        height: 40rpx;
        border: solid 1rpx #eee;
        border-radius: 10rpx;
        padding-left: 10rpx;
        font-size: 28rpx;
    }
    /* 邀请二维码 */
    .hideCanvasView {
    	position: relative;
    }
    
    .hideCanvas {
    	position: fixed;
    	top: -99999rpx;
    	left: -99999rpx;
    	z-index: -99999;
    }
    
    .flex_row_c_c {
    	display: flex;
    	flex-direction: row;
    	justify-content: center;
    	align-items: center;
    }
    
    .modalView {
    	width: 100%;
    	height: 100%;
    	position: fixed;
    	top: 0;
    	left: 0;
    	right: 0;
    	bottom: 0;
    	opacity: 0;
    	outline: 0;
    	transform: scale(3);
    	perspective: 2500rpx;
    	background: rgba(0, 0, 0, 0.6);
    	transition: all .3s ease-in-out;
    	pointer-events: none;
    	backface-visibility: hidden;
    	z-index: 999;
    }
    
    .modalView.show {
    	opacity: 1;
    	transform: scale(1);
    	pointer-events: auto;
    }
    
    .flex_column {
    	display: flex;
    	flex-direction: column;
    }
    
    .backgroundColor-white {
    	background-color: white;
    }
    
    .border_radius_10px {
    	border-radius: 10px;
    }
    
    .padding1vh {
    	padding: 1vh;
    }
    
    .posterImage {
    	width: 60vw;
    }
    
    .flex_row {
    	display: flex;
    	flex-direction: row;
    }
    
    .marginTop2vh {
    	margin-top: 2vh;
    }
    
    /* 列表 */
    .swiper-top{
        position: relative;
        top: 90rpx;
    }
    .swiper-box{
        margin-top: 15rpx;
    }
</style>
