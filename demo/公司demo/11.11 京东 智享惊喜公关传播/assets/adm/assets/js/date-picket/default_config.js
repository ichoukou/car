jQuery(function($){
    if($.datepicker){
        $.datepicker.regional['zh-CN'] = {
            closeText: '关闭',
            prevText: '<上月',
            nextText: '下月>',
            currentText: '今天',
            monthNames: ['一月','二月','三月','四月','五月','六月',
                '七月','八月','九月','十月','十一月','十二月'],
            monthNamesShort: ['一月','二月','三月','四月','五月','六月',
                '七月','八月','九月','十月','十一月','十二月'],
            dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
            dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
            dayNamesMin: ['日','一','二','三','四','五','六'],
            weekHeader: '周',
            dateFormat: 'yy-mm-dd',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: true,
            yearSuffix: '年',
            changeMonth: true
        };
        $.datepicker.setDefaults($.datepicker.regional['zh-CN']);
    }
    $(document).on('focus','input[limit=int],input[limit=float],input[limit=phone]',function(){
        zd.lv=$(this).val();
    });
    $(document).on('blur','input[limit=int],input[limit=float],input[limit=phone]',function(){
        zd.lv=$(this).val();
    });
    $(document).on('keydown keyup','input[limit=int]',function(){
        if(zd.lv==this.value) return;
        this.value=this.value.replace(/[^\d{1,}]/g,'');
        zd.lv=this.value;
    });
    $(document).on('keydown keyup','input[limit=float]',function(){
        if(zd.lv==this.value) return;
        //先把非数字的都替换掉，除了数字和.
        this.value = this.value.replace(/[^\d\.]/g,'');
        //必须保证第一个为数字而不是.
        this.value = this.value.replace(/^\./g,'');
        //保证只有出现一个.而没有多个.
        this.value = this.value.replace(/\.{2,}/g,'.');
        //保证.只出现一次，而不能出现两次以上
        this.value = this.value.replace('.','$#$').replace(/\./g,'').replace('$#$','.');
        //保留小数点后2位
        this.value = this.value.replace(/\.[0-9]{3}/g,this.value.substring(this.value.indexOf('.'),this.value.indexOf('.') + 3));

//        this.value = parseInt(this.value).toFixed(2);
        zd.lv=this.value;
    });
    $(document).on('keydown keyup','input[limit=phone]',function(){
        if(zd.lv==this.value) return;
        //先把非数字的都替换掉，除了数字和.
        this.value = this.value.replace(/[^\d\-]/g,'');
        //必须保证第一个为数字而不是.
        this.value = this.value.replace(/^\./g,'');
        //保证.只出现一次，而不能出现两次以上
        this.value = this.value.replace('-','$#$').replace(/\-/g,'').replace('$#$','-');
        zd.lv=this.value;
    });
});