<script>
    import Vue from 'vue'
	export default {
		onLaunch: function() {
			uni.getSystemInfo({
                success: function(e) {
                    // #ifndef MP
                    Vue.prototype.StatusBar = e.statusBarHeight;
                    if (e.platform == 'android') {
                        Vue.prototype.CustomBar = e.statusBarHeight + 50;
                    } else {
                        Vue.prototype.CustomBar = e.statusBarHeight + 45;
                    };
                    // #endif      
                }
            }),
            // 网路监听（用户目前断网，切换wifi）
            this.lib.NetWork.On();
            // #ifdef APP-PLUS
            // 更新检测
            this.lib.Update();
            // #endif
		},
		onShow: function() {
			console.log('App Show')
		},
		onHide: function() {
			console.log('App Hide')
		}
	}
</script>

<style>
    @import "colorui/main.css";
    @import "colorui/icon.css";
    /* 引入自定义图标库 */
    @import './common/iconfont.css';
    @import './common/uni.css';
    /* 公共样式 */
    @import './common/common.css';
    ::-webkit-scrollbar {
        display: none;
    }
</style>
