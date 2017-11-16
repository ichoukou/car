1、多图重名问题
    原生的版本，上传多图会出现只上传一张或者几张成功的情况，是因为名称重复的原因，修改ueditor/php/Uploader.class.php,
找到getFullName方法,在298行左右，已经修改完成。
2、使用上传word功能的图片限制问题
    找到ueditor/dialogs/wordimage/wordimage.html，找到flashOptions数据变量，里面的maxNum属性就是图片多少的限制，如果修改以后不生效，应该是缓存的问题，
重新上传整个目录文件应该就可以