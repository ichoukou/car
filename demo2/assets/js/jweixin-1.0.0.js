(function($) {  
  $.extend($.fn, {  
    fileUpload: function(opts) {  
      this.each(function(){  
        var $self = $(this);  
        var doms = {  
          "fileToUpload": $self.find(".fileToUpload"),  
          "thumb": $self.find(".thumb"),  
          "progress": $self.find(".upload-progress")  
        };  
        var funs = {  
          //选择文件，获取文件大小，也可以在这里获取文件格式，限制用户上传非要求格式的文件  
          fileSelected: function() {  
            var files = (doms.fileToUpload)[0].files;  
            var count = files.length;  
//            console.log(files);  
//            console.log(count);  
              
            for (var index = 0; index < count; index++) {  
              var file = files[index];  
              var fileSize = 0;  
              if (file.size > 1024 * 1024)  
                fileSize = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';  
              else  
                fileSize = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';  
           //   console.log(fileSize);  
            }  
            funs.uploadFile();  
          },  
          //异步上传文件  
          uploadFile: function() {  
            var fd = new FormData();//创建表单数据对象  
            var files = (doms.fileToUpload)[0].files;  
      //      console.log(files[0].name);  
            $("#fileName").text(files[0].name);  
            var count = files.length;  
            for (var index = 0; index < count; index++) {  
              var file = files[index];  
              fd.append(opts.file, file);//将文件添加到表单数据中  
             // funs.previewImage(file);//上传前预览图片，也可以通过其他方法预览txt  
            }  
            var xhr = new XMLHttpRequest();  
            xhr.upload.addEventListener("progress", funs.uploadProgress, false);//监听上传进度  
            xhr.addEventListener("load", funs.uploadComplete, false);  
            xhr.addEventListener("error", opts.uploadFailed, false);  
            xhr.open("POST", opts.url);  
            console.log(fd);  
            xhr.send(fd);  
          },  
          uploadProgress: function(evt) {  
//            console.log("----------------------");  
//            console.log(evt);  
//            console.log(evt.lengthComputable);  
            if (evt.lengthComputable) {  
              var percentComplete = Math.round(evt.loaded * 100 / evt.total);  
              //doms.progress.html(percentComplete.toString() + '%');  
              $("#qin_progress").css("width",percentComplete.toString() + '%');  
                
            }  
          },  
          uploadComplete: function(evt) {  
            console.log(evt);  
            console.log(evt.target.responseText);  
            var text=evt.target.responseText;  
            var d = eval("(" + text + ")"); //把数据转成json  
            var mp4Url = d.fileVal;  
            console.log(mp4Url);  
            document.cookie = "mp4Url="+mp4Url;  
              
          }  
        };  
        doms.fileToUpload.on("change", function() {  
          doms.progress.find("span").width("0");  
          funs.fileSelected();  
        });  
      });  
    }  
  });  
})(Zepto); 