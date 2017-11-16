/**
 * Created by Administrator on 2015/5/26.
 */
/**
 * Created by stevenma on 15-4-7.
 */
var ST = function(){
    this.lv='';
}

ST.prototype.sha1 = function(str){
    if (!str)
        return false;

    return hex_sha1(str)
}

ST.prototype.base64 = function(str){
    var b = new Base64();
    var str = b.encode("admin:admin");
    alert("base64 encode:" + str);

    str = b.decode(str);
    alert("base64 decode:" + str);
}

ST.prototype.md5 = function(str){
    if (!str)
        return false;

    return hex_md5(str)
}

ST.prototype.init = function(){
    var hash=document.location.hash;
    if(!!hash){
        var obj=hash.substring(1);
        return obj;
    }
}

ST.prototype.request_get = function(){
    var url = location.search; // 获取url中"?"符后的字串
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);

        strs = str.split("&");
        for (var i = 0; i < strs.length; i++) {
            var decodeParam = decodeURIComponent(strs[i]);
            var param = decodeParam.split("=");
            theRequest[param[0]] = param[1];
        }

    }
    return theRequest;
}

ST.prototype.get=function(url,data,success,before){
    return $.ajax({
        url:url,
        data:data,
        type:'GET',
        dataType:'json',
        beforeSend:function(){
            if($.type(before) == 'function')
                before();
        },
        success:function(data){
            if($.type(success) == 'function')
                success(data);
        }
    });
}

ST.prototype.post=function(url,data,success,before,isAsync){
    $.ajax({
        async:isAsync == false ? false : true,//同步还是异步
        url:url,
        data:data,
        type:'POST',
        dataType:'json',
        beforeSend:function(){
            if($.type(before) == 'function')
                before();
        },
        success:function(data){
            if($.type(success) == 'function')
                success(data);
        }
    });
}

ST.prototype.postHaveImage=function(url,data,success,before){
    $.ajax({
        url:url,
        data:data,
        type:'POST',
        contentType: false,
        processData: false,
        dataType:'json',
        beforeSend:function(){
            if($.type(before) == 'function')
                before();
        },
        success:function(data){
            if($.type(success) == 'function')
                success(data);
        }
    });
}

ST.prototype.html=function(url,data,success,before){
    $.ajax({
        url:url,
        data:data,
        type:'GET',
        dataType:'html',
        beforeSend:function(){
            if($.type(before) == 'function')
                before();
        },
        success:function(data){
            if($.type(success) == 'function')
                success(data);
        }
    });
}

/*
jsonp jquery封装后的jsonp支付post请求 原生的jsonp好像是不支持post请求 同时 jquery在jsonp请求完毕后会自动删除之前生成的script对象
 */
ST.prototype.jpg=function(url,data,success,before){
    $.ajax({
        async:true,//同步还是异步
        url:url,
        type:"GET",//只能发送GET请求
        dataType:"jsonp",//请求类型是jsonp
        jsonp:"callback",//传递给请求处理程序或页面的，用以获得jsonp回调函数名的参数名(默认为:callback),php端返回给客户端的时候的返回格式是 echo "$_GET['callback'](json_encode(array(数据)))"
        //jsonpCallback:"success_jsonpCallback",//自定义的jsonp回调函数名称，默认为jQuery自动生成的随机函数名,如果要使用此自定义回调名称函数 需要在参数后面加上 "回调函数名=?"来生成随机函数名 例如 add.php?aaa=bbb&success_jsonpCallback=?
        data:data,
        beforeSend:function(){
            if($.type(before) == 'function')
                before();
        },
        success:function(data){
            if($.type(success) == 'function')
                success(data);
        }
    });
}

ST.prototype.jpp=function(url,data,success,before){
    var callback = "callback";
    var sign1 = url.indexOf("?")!=-1 ? "" : "?";
    var sign2 = url.indexOf("&")!=-1 ? "&" : "";
    $.ajax({
        async:false,//同步还是异步
        url:url+sign1+sign2+callback+"=?",
        type:"POST",//只能发送GET请求
        dataType:"jsonp",//请求类型是jsonp
        jsonp:callback,//传递给请求处理程序或页面的，用以获得jsonp回调函数名的参数名(默认为:callback),php端返回给客户端的时候的返回格式是 echo "$_GET['callback'](json_encode(array(数据)))"
        //jsonpCallback:"success_jsonpCallback",//自定义的jsonp回调函数名称，默认为jQuery自动生成的随机函数名,如果要使用此自定义回调名称函数 需要在参数后面加上 "回调函数名=?"来生成随机函数名 例如 add.php?aaa=bbb&success_jsonpCallback=?
        //data:data+"&"+callback+"=?",//post请求不能自动生成随机函数名 所以调用post请求传参的时候 参数要序列化为aaa=bbb&ccc=ddd 最后在拼上&callback=? 才可以 或者把随机函数生成链接在url后面  这里参数的格式就可以是对象了
        data:data,
        beforeSend:function(){
            if($.type(before) == 'function')
                before();
        },
        success:function(data){
            if($.type(success) == 'function')
                success(data);
        }
    });
}

ST.prototype.get_date_t = function(time_stamp){
    var D;
    D = new Date();

    if (time_stamp) {
        D.setTime(time_stamp * 1000);
    }

    var y, m, d, h, i, s;
    y = D.getFullYear();
    m = D.getMonth();
    d = D.getDate();
    h = D.getHours();
    i = D.getMinutes();
    s = D.getSeconds();

    m = ( m < 10 ) ? '0' + m : m;
    d = ( d < 10 ) ? '0' + d : d;
    h = ( h < 10 ) ? '0' + h : h;
    i = ( i < 10 ) ? '0' + i : i;
    s = ( s < 10 ) ? '0' + s : s;

    return y + "-" + m + "-" + d + " " + h + ":" + i + ":" + s;
}

/*
html5 notification
 */
ST.prototype.notify_me = function(title,option){
    var n = new Notification(title,option);

    return n;
}

/*
    -demo-
    name:input的name
    message:验证信息
     var input_array = [
     {'name':'company_name','message':'请填写公司名称。'},
     {'name':'username','message':'请填写联系人名称。'}
     ];

     st.i_v({
     container:'',
     input_array:input_array
     });
 */
ST.prototype.i_v=function(options){
    var container=options.container || document;
    $.each(options.input_array,function(i,e){
        if(!$("input[name="+ e.name+"]",container).val()){
            $("input[name="+ e.name+"]",container).next().text(e.message);
            $("input[name="+ e.name+"]",container).next().show();
        }
    });
}

/*
    -demo-
    name:select的name
    message:验证信息
     var select_array = [
     {'name':'province','message':'请选择省份。'},
     {'name':'city','message':'请选择城市。'}
     ];

     st.s_v({
     container:'',
     select_array:select_array
     });
 */

ST.prototype.s_v=function(options){
    var container=options.container || document;
    $.each(options.select_array,function(i,e){
        if(!$("select[name="+ e.name+"]",container).val()){
            $("select[name="+ e.name+"]",container).next().text(e.message);
            $("select[name="+ e.name+"]",container).next().show();
        }
    });
}

ST.prototype.xhrUpload = function(url,data,progress,complate){
    var xhr = new XMLHttpRequest();
    xhr.open('POST',url,true); // 异步传输

    xhr.upload.addEventListener('progress',progress,false); //当前进度
    xhr.addEventListener('load',complate,false); //上传图片成功 返回的数据

    xhr.send(data);
}

/*
* btn:触发按钮的id
* param_name:参数所在数组的name 也就是input的name
* url:请求地址
* bool:是否是多图上传 true or false
* progress:进度条信息
* success:返回数据
* */
ST.prototype.xhrUpload.image = function(container,btn,param_name,url,bool,progress,success){
    if(!btn || !param_name || !url) return false;
    st.xhrUpload.createImageForm(param_name,bool); //创建input type=file 的上传文件框

    $("input[name='"+param_name+"'").off('change').on('change',function(){          //给创建好的input绑定change事件
        if(bool){
            var name = this.name;
            $.each(this.files,function(i,e){
                var fd = new FormData(); //html5新增的对象,可以包装字符,二进制信息
                fd.append(name,e);

                st.xhrUpload(url,fd,function(progress_json){
                    if(progress_json.lengthComputable && typeof progress == 'function') progress(progress_json);
                },function(success_json){
                    //var s="("+success_json.target.responseText+")";
                    //var s = eval(s)

                    var s = JSON.parse(success_json.target.responseText);
                    if(typeof success == 'function') success(s);
                });
            });
        }else{
            var fd = new FormData(); //html5新增的对象,可以包装字符,二进制信息
            fd.append(this.name,this.files[0]);//单图上传可以直接这样取图片

            st.xhrUpload(url,fd,function(progress_json){
                if(progress_json.lengthComputable && typeof progress == 'function') progress(progress_json);
            },function(success_json){
                //var s="("+success_json.target.responseText+")";
                //var s = eval(s)

                var s = JSON.parse(success_json.target.responseText);
                if(typeof success == 'function') success(s);
            });
        }
    });

    var container = container || document;
    $(btn,container).off('click').on('click',function(event){
        event.preventDefault();
        $("input[name='"+param_name+"'").trigger('click');
    });
}

ST.prototype.xhrUpload.createImageForm = function(param_name,bool){
    if(bool){
        var form = '<form><input style="width:0px;height: 0px;display: none;" multiple type="file" name="'+param_name+'"/></form>';
    }else{
        var form = '<form><input style="width:0px;height: 0px;display: none;" type="file" name="'+param_name+'"/></form>';
    }
    $("input[name='"+param_name+"'").parent().remove();
    $(document).find('body').append(form);
}

/*
 * btn:触发按钮的id
 * param_name:参数所在数组的name 也就是input的name
 * bool:是否是多图上传 true or false
 * progress:进度条信息
 * success:返回数据
 * */
ST.prototype.xhrUpload.localFile = function(container,btn,param_name,bool,progress,success){
    if(!btn || !param_name) return false;

    if(bool){
        $("input[name='"+param_name+"'").attr('multiple',true);
    }else{
        $("input[name='"+param_name+"'").attr('multiple',false);
    }

    $("input[name='"+param_name+"'").off('change').on('change',function(){          //给创建好的input绑定change事件
        if(bool){
            $(btn).next('span').remove();
            $(btn).after('<span></span>');
            var data;
            $.each(this.files,function(i,file){
                if(/image\/\w+/.test(file.type)){
                    var reader = new FileReader();
                    reader.readAsDataURL(file);

                    reader.onload = function(e){
                        $(btn).next().append('<img src="'+this.result+'" style="width:200px;"/>&nbsp;');
                    }
                }else{
                    $(btn).next().append(file.name+'&nbsp;&nbsp;&nbsp;');
                }
            });
        }else{
            $(btn).next().remove();
            var file = this.files[0];
            if(/image\/\w+/.test(file.type)){
                var reader = new FileReader();
                reader.readAsDataURL(file);

                reader.onload = function(e){
                    $(btn).after('<span><img src="'+this.result+'" style="width:200px;"/></span>');
                }
            }else{
                $(btn).after('<span>'+this.files[0].name+'</span>');
            }

            success(this.name,file);
        }

        //success();
    });

    var container = container || document;
    $(btn,container).off('click').on('click',function(event){
        event.preventDefault();
        $("input[name='"+param_name+"'").trigger('click');
    });
}

/*
    使用encodeURIComponent对数据进行处理 如果data的值是object或array 会同时返回 array或object格式和string格式的返回值 根据下标0和1来使用
    如果data是string 则只返回string格式的返回值

    param:data 参数可以是对象{a:'b',c:'d'} 可以是数组[a:'b',c:'d'] 可以是数组对象[{a:'b'},{c:'d'}] 可以是string a=b或a=b&c=d 其他格式处理结果会有误
 */
ST.prototype.euc = function(data){
    if(!data) return false;
    if(data instanceof Object || !!$.isArray(data)){
        //is array or object
        var param_str = '';
        var param_arr = [];
        $.each(data,function(i,e){
            var o = new Object();
            //如果data是 serializeArray 对象数组 进行特殊处理 处理后的格式 param_arr = [{a:'b'},{c:'d'},{e:'f'}] 和 param_str = 'a=b&c=d&e=f'
            if(!!e) {
                if (e instanceof Object) {
                    o.name = this.name;
                    o.value = encodeURIComponent(this.value);
                    param_arr[encodeURIComponent(i)] = o;
                    param_str += ((!!param_str ? "&" : "") + this.name + "=" + encodeURIComponent(this.value));
                } else {
                    //如果data是 对象或数组 处理后的格式 param_arr = {a:'b',c:'d',e:'f'} 和 param_str = 'a=b&c=d&e=f'
                    o.name = i;
                    o.value = encodeURIComponent(e);
                    param_arr.push(o)
                    param_str += ((!!param_str ? "&" : "") + i + "=" + encodeURIComponent(e));
                }
            }
        });
        var arr = [];
        arr.push(param_arr);
        arr.push(param_str);
        return arr;
    }else if(typeof data == "string"){
        //is string
        var arr = data.split("&");
        var str = '';
        $.each(arr,function(i,e){
            if(!!e){
                var v = e.split("=");
                str += ((!!str ? "&" : "") + v[0] + "=" + (!!encodeURIComponent(v[1]) ? encodeURIComponent(v[1]) : ""));
            }
        });

        return str;
    }
}

ST.prototype.wxshare = function(shareParams,share_interface){
    wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: shareParams.wxappid, // 必填，公众号的唯一标识
        timestamp: shareParams.wxtimestamp, // 必填，生成签名的时间戳
        nonceStr: shareParams.wxnonceStr, // 必填，生成签名的随机串
        signature: shareParams.wxsignature,// 必填，签名，见附录1
        jsApiList:  share_interface// 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });
}

//验证码
ST.prototype.yzm=function(){
    //var arr = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9'];
    var arr = ['0','1','2','3','4','5','6','7','8','9'];
    var str = '';
    for(var i = 0 ; i < 4 ; i ++ )
        str += ''+arr[Math.floor(Math.random() * arr.length)];
    return str;
}

ST.prototype.validate_phone = function(tel){//判断手机号是否符合要求
    if(/^1[3|5|7|8][0-9]\d{8}$/.test(tel)){
        return true;
    }else{
        alert('请输入正确的手机号码');
        return false;
    }
}

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
        st.lv=$(this).val();
    });
    $(document).on('blur','input[limit=int],input[limit=float],input[limit=phone]',function(){
        st.lv=$(this).val();
    });
    $(document).on('keydown keyup','input[limit=int]',function(){
        if(st.lv==this.value) return;
        this.value=this.value.replace(/[^\d{1,}]/g,'');
        st.lv=this.value;
    });
    $(document).on('keydown keyup','input[limit=float]',function(){
        if(st.lv==this.value) return;
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
        st.lv=this.value;
    });
    $(document).on('keydown keyup','input[limit=phone]',function(){
        if(st.lv==this.value) return;
        //先把非数字的都替换掉，除了数字和.
        this.value = this.value.replace(/[^\d\-]/g,'');
        //必须保证第一个为数字而不是.
        this.value = this.value.replace(/^\./g,'');
        //保证.只出现一次，而不能出现两次以上
        this.value = this.value.replace('-','$#$').replace(/\-/g,'').replace('$#$','-');
        st.lv=this.value;
    });
});

var st = new ST();

//ST.prototype.upload=function(btn,url,name,multiple,complete,before){
//    return new AjaxUpload(btn, {
//        action:url,
//        name: name,
//        accept:'.jpg,.gif,.png',//这是上传图片的后缀 如果是上传文件 修改后缀
//        multiple:multiple,
//        autoSubmit: true,
//        responseType: 'json',
//        onSubmit: function(file, extension) {
//            // $(btn).after('<img src="catalog/view/theme/responsive/image/loading.gif" class="loading" style="padding-left: 5px;" />');
//            // $(btn).attr('disabled', true);
//            // $('.error').remove();
//            if(typeof(before)=='function') before();
//        },
//        onComplete: function(file, json) {
//            // $(btn).attr('disabled', false);
//            // $('#upload_button').attr('disabled', false);
//            // $('.loading').remove();
//            if(typeof(complete)=='function') complete(json);
//        }
//    });
//}
//
//ST.prototype.upload.logo=function(btn,url){
//    return st.upload(btn,url,'logo',false,function(json){
//        if(json.ret==1){
//            $("#thumb").attr('src',json.path);
//            $("input[type=hidden][name=logo]").val(json.name);
//            //$("#logo").attr('src',json.path);
//        }else if(json.ret==-1){
//            $('.error_logo').html(json.error);
//        }
//    });
//}
//
//ST.prototype.upload.images=function(btn,url,success){
//    return st.upload(btn,url,'image[]',true,function(json){
//        success(json);
//    });
//}

