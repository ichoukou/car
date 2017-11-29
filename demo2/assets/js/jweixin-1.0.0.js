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
          //ѡ���ļ�����ȡ�ļ���С��Ҳ�����������ȡ�ļ���ʽ�������û��ϴ���Ҫ���ʽ���ļ�  
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
          //�첽�ϴ��ļ�  
          uploadFile: function() {  
            var fd = new FormData();//���������ݶ���  
            var files = (doms.fileToUpload)[0].files;  
      //      console.log(files[0].name);  
            $("#fileName").text(files[0].name);  
            var count = files.length;  
            for (var index = 0; index < count; index++) {  
              var file = files[index];  
              fd.append(opts.file, file);//���ļ���ӵ���������  
             // funs.previewImage(file);//�ϴ�ǰԤ��ͼƬ��Ҳ����ͨ����������Ԥ��txt  
            }  
            var xhr = new XMLHttpRequest();  
            xhr.upload.addEventListener("progress", funs.uploadProgress, false);//�����ϴ�����  
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
            var d = eval("(" + text + ")"); //������ת��json  
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