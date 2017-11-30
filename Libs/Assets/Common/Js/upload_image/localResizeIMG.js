//localResizeIMG.js
/**
 * author:liulin
 * createtime：2016-04-22
 * @param {Object} obj
 * @param {Number} [obj.width] 图片宽度
 * @param {Number} [obj.quality=0.8] 图片变化大小
 * @param {Function} [obj.before(this, blob, file)] 获取file的内容
 * @param {Function} obj.success(obj) 成功返回
 * @example
 *
 */
$.fn.ResizeIMG = function (obj) {
    this.on('change', function () {
        var file = this.files[0];
        var URL = window.URL || window.webkitURL;
        var blob = URL.createObjectURL(file);

        // 获取当前的
        if ($.isFunction(obj.before)) {
            obj.before(this, blob, file)
        };

        _create(blob, file);
        this.value = ''; // 上传完了以后把名字变为空
    });

    /**
     * @param blob 文件转换为base64
     */
    function _create(blob) {
        var img = new Image();
        img.src = blob;

        img.onload = function () {
            var that = this;

            //设定图片的宽高
            var w = that.width,
                h = that.height,
                scale = w / h;
            w = obj.width || w;
            h = w / scale;

            //canvas转换
            var canvas = document.createElement('canvas');
            var ctx = canvas.getContext('2d');
            $(canvas).attr({
                width: w,
                height: h
            });
            ctx.drawImage(that, 0, 0, w, h);

            /**
             * 转换调用obileBUGFix.js
             */
            var base64 = canvas.toDataURL('image/jpeg', obj.quality || 0.8);

            // IOS版转换
            if (navigator.userAgent.match(/iphone/i)) {
                var mpImg = new MegaPixImage(img);
                mpImg.render(canvas, {
                    maxWidth: w,
                    maxHeight: h,
                    quality: obj.quality || 0.8
                });
                base64 = canvas.toDataURL('image/jpeg', obj.quality || 0.8);
            }

            // android版转换
            if (navigator.userAgent.match(/Android/i)) {
                var encoder = new JPEGEncoder();
                base64 = encoder.encode(ctx.getImageData(0, 0, w, h), obj.quality * 100 || 80);
            }

            // 标定转换后的值
            var result = {
                base64: base64,
                clearBase64: base64.substr(base64.indexOf(',') + 1)
            };

            //输出
            obj.success(result);
        };
    }
};