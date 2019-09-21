<template>
    <view class="nothing u-f-ajc" :style="{'height':swiperHeight + 'px'}">
        <image src="https://ae01.alicdn.com/kf/Hc83ee639638b4b1b85b4a68d90567a49Q.png" mode="widthFix"></image>
    </view>
</template>

<script>
    export default {
        props:{
            swiperHeight:Number
        }
    }
</script>

<style scoped>
    .nothing{
        background: #FFFFFF;
    }
    .nothing image{
        width: 50%;
    }
</style>
