//音乐控制
function musicClick() {
	if (music.paused) {
		music.play();
		pausebtn.alpha = 0;
	} else {
		music.pause();
		pausebtn.alpha = 1;
	}
}
//音乐控制

/**
 * Lsprite改进版，增加xy定位<br>bitmap必填<br> x为0，自动居中显示<br>xywh从图片 xy点 裁剪宽w高h的图片
 * @bitmap 图片
 * @x left
 * @y top
 * @alpha 透明度
 * @xywh 此参数为[x,y,w,h]数组格式
 */
var LspriteXY = (function() {

	function LspriteXY(bitmap, x, y, alpha, xywh) {

		var s = this;
		LExtends(s, LSprite, []);

		//切割图片其中的某一部分显示
		if (typeof xywh != UNDEFINED) {
			s.bitmaps = new LBitmap(new LBitmapData(bitmap, xywh[0], xywh[1], xywh[2], xywh[3]));
		} else {
			s.bitmaps = new LBitmap(new LBitmapData(bitmap));
		}

		//如果xy 不为空
		if (typeof x != UNDEFINED) {
			//如果x为0或者未传值，自动居中
			s.x = (x == null || x == 0 ? (picW / 2 - s.bitmaps.getWidth() / 2) : x);
			s.y = (y == null ? 0 : y);
			s.alpha = (alpha == null ? 1 : alpha);
		}
		s.addChild(s.bitmaps);
	}

	return LspriteXY;

})();

/**
 * Lsprite 上下浮动效果  t为时间 f为幅度
 * @t 单幅度f的时间
 * @f 上下的幅度值
 */
LspriteXY.prototype.shangxia = function(t, f) {
	var self = this;
	var top = self.y;
	LTweenLite.to(self, t, {
		y: top - f,
		loop: true
	}).to(self, t, {
		y: top
	}).to(self, t, {
		y: top + f
	}).to(self, t, {
		y: top,
		onComplete: function() {}
	});
}

/**
 * Lsprite 放大缩小效果 t为时间 scalexy放大倍数
 * @t 单幅度f的时间
 * @scalexy 放大倍数
 */
LspriteXY.prototype.fangda = function(t, dl, scalexy) {
	var self = this;
	self.bitmaps.x = -self.bitmaps.getWidth() * 0.5;
	self.bitmaps.y = -self.bitmaps.getHeight() * 0.5;
	self.x = self.x + self.bitmaps.getWidth() / 2;
	self.y = self.y + self.bitmaps.getHeight() / 2;
	self.scaleX = 0;
	self.scaleY = 0;
	self.alpha = 1;
	setTimeout(go, dl * 1000);

	function go() {
		LTweenLite.to(self, t, {
			scaleX: scalexy,
			scaleY: scalexy,

			ease: LEasing.Back.easeOut
		});
		//console.log(self.scaleX);
	}

}

/**
 * Lsprite 放大缩小效果 t为时间 scalexy放大倍数
 * @t 单幅度f的时间
 * @scalexy 放大倍数
 */
LspriteXY.prototype.daxiao = function(t, scalexy) {
	var self = this;
	self.bitmaps.x = -self.bitmaps.getWidth() * 0.5;
	self.bitmaps.y = -self.bitmaps.getHeight() * 0.5;

	self.x = self.x + self.bitmaps.getWidth() / 2;
	self.y = self.y + self.bitmaps.getHeight() / 2;
	LTweenLite.to(self, t, {
		scaleX: scalexy,
		scaleY: scalexy,
		loop: true
	}).to(self, t, {
		scaleX: 1,
		scaleY: 1
	});
}
/*
 * 
 */
LspriteXY.prototype.xuanzhuan = function(t, dl) {
	var self = this;
	self.bitmaps.x = -self.bitmaps.getWidth() * 0.5;
	self.bitmaps.y = -self.bitmaps.getHeight() * 0.5;
	self.x = self.x + self.bitmaps.getWidth() / 2;
	self.y = self.y + self.bitmaps.getHeight() / 2;
	setTimeout(go, dl * 1000);

	function go() {
		LTweenLite.to(self, t, {
			rotate:180,
			loop:true,
			playStyle:LTweenLite.PlayStyle.Init
		}).to(self, t, {
			rotate: 360,
		});
	}

}
/**
 * Lsprite 左右旋转 t为时间 hudu向左旋转的单个弧度
 * @t 单幅度f的时间
 * @dl delay
 * @hudu 左右摆动弧度
 */
LspriteXY.prototype.baidong = function(t, dl, hudu) {
	var self = this;
	self.bitmaps.x = -self.bitmaps.getWidth() * 0.5;
	self.bitmaps.y = -self.bitmaps.getHeight() * 0.5;
	self.x = self.x + self.bitmaps.getWidth() / 2;
	self.y = self.y + self.bitmaps.getHeight() / 2;
	setTimeout(go, dl * 1000);

	function go() {
		LTweenLite.to(self, t, {
			rotate: hudu,
			loop: true
		}).to(self, t, {
			rotate: 0
		}).to(self, t, {
			rotate: -hudu
		}).to(self, t, {
			rotate: 0
		});
	}

}

/**
 * Lsprite 左右旋转 t为时间 hudu向左旋转的单个弧度
 * @t 单幅度f的时间
 * @dl delay
 * @hudu 左右摆动弧度
 */
LspriteXY.prototype.jiarou1 = function(t, dl, hudu) {
	var self = this;
	self.bitmaps.x = -self.bitmaps.getWidth();
	self.bitmaps.y = -self.bitmaps.getHeight();
	self.x = self.x + self.bitmaps.getWidth();
	self.y = self.y + self.bitmaps.getHeight();
	setTimeout(go, dl * 1000);

	function go() {
		LTweenLite.to(self, t, {
			rotate: hudu,
			loop: true
		}).to(self, t, {
			rotate: 0
		});
	}

}
LspriteXY.prototype.jiarou2 = function(t, dl, hudu) {
	var self = this;

	self.bitmaps.y = -self.bitmaps.getHeight();

	self.y = self.y + self.bitmaps.getHeight();
	setTimeout(go, dl * 1000);

	function go() {
		LTweenLite.to(self, t, {
			rotate: hudu,
			loop: true
		}).to(self, t, {
			rotate: 0
		});
	}

}

/**
 * Lsprite 盖章效果，从屏幕中间大倍数缩小到原始大小位置  t为时间 scalexy为多大倍数下来
 * @t 单幅度f的时间
 * @scalexy 放大倍数
 * @fn 回调
 */
LspriteXY.prototype.gaizhang = function(t, delay1, scalexy, fn) {
	var self = this;
	self.bitmaps.x = -self.bitmaps.getWidth() * 0.5;
	self.bitmaps.y = -self.bitmaps.getHeight() * 0.5;
	self.x = self.x + self.bitmaps.getWidth() / 2;
	self.y = self.y + self.bitmaps.getHeight() / 2;

	setTimeout(function() {
		self.alpha = (self.alpha == 0 ? 1 : self.alpha);
		self.scaleX = scalexy;
		self.scaleY = scalexy;
		self.alpha = 1;
		LTweenLite.to(self, t, {

			scaleX: 1,
			scaleY: 1,
			ease: LEasing.Elastic.easeOut,
			onComplete: fn
		});
	}, delay1 * 1000);

}

/**
 * Lsprite 盖章效果，从屏幕中间大倍数缩小到原始大小位置  t为时间 scalexy为多大倍数下来
 * @t 单幅度f的时间
 * @scalexy 放大倍数
 */
LspriteXY.prototype.maopao = function(t) {
	var self = this;
	self.bitmaps.x = -self.bitmaps.getWidth() * 0.5;
	self.bitmaps.y = -self.bitmaps.getHeight() * 0.5;
	self.x = self.x + self.bitmaps.getWidth() / 2;
	self.y = self.y + self.bitmaps.getHeight() / 2;
	self.alpha = 1;

	LTweenLite.to(self, t, {
		scaleX: 1,
		scaleY: 1,
		ease: LEasing.Elastic.easeOut
	});
}

/**
 * Lsprite alpha 从无到有
 * @t 单幅度f的时间
 * @a 透明度
 */
LspriteXY.prototype.xianshi = function(t, delay1, a) {
	var self = this;
	if (delay1 == 0) {
		LTweenLite.to(self, t, {
			alpha: a,
		});
	} else {
		LTweenLite.to(self, t, {
			delay: delay1,
			alpha: a,
		});
	}

}

/**
 * 取字符串mainStr从starnum到endnum
 * @mainStr 操作的字符串
 * @starnum 开始位置
 * @endnum 结束位置
 */
function mid(mainStr, starnum, endnum) {
	if (mainStr.length >= 0) {
		return mainStr.substr(starnum, endnum)
	} else {
		return null
	}
}

/**
 * 取Min到Max之间的随机数
 * @Min 最小数
 * @Max 最大数
 */
function GetRandomNum(Min, Max) {

	var Range = Max - Min;

	var Rand = Math.random();

	return (Min + Math.round(Rand * Range));

}

function shuchu(s) {
	console.log(s);
}

function Shuru(param, text) {

	var el = this;
	LExtends(el, LTextField, []);

	var inputs = new LSprite();
	inputs.graphics.drawRect(0, "transparent", [-10, -10, param.w, param.h], true, "#ffffff");
	inputs.alpha = 1;
	el.setType(LTextFieldType.INPUT, inputs);
	el.x = param.x;
	el.y = param.y;
	el.text = text || "";
	el.size = param.size;
	el.weight="bold";
	//shuru1.textBaseline = "middle"
	el.color = param.color;

	return el;
}
	function random(min, max) {
		var range = max - min;
		var rand = Math.random();
		return (min + Math.round(rand * range));
	}

	function random2(min, max) {
		var range = max - min;
		var rand = Math.random();
		return (min + (rand * range));
	}
	
function mobile(value){
	var length = value.length;
	var mobile = /^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/;
	if(length == 11 && mobile.test(value)){
		return true;
	}else{
		return false;
	}
}