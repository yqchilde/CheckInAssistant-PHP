module.exports = {
    //转换时间戳为时间
    timestampToTime: function(timestamp, type=1) {
        var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
        let Y = date.getFullYear() + '.';
        let M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '.';
        let D = date.getDate() + ' ';
        let h = date.getHours() + ':';
        let m = date.getMinutes()
        let s = date.getSeconds();
        if (type == 1) { //全格式
            return Y+M+D+h+m+':'+s;
        } else if (type == 2) { //年月日
            return Y+M+D;
        } else if (type == 3) { //时分
            return h+m;
        } else if (type == 4) { //月日
            return M+D;
        } else if (type == 5) { //年月日时分
            return Y+M+D+h+m;
        }
    }
}