var w, h, picW, picH, scalex;
h = 1010;
w = h / window.innerHeight * window.innerWidth;
picW = 640;
picH = 1010;
scalex = w / 640;

var yunPath = "http://hdfile.wechatdpr.com/jd/2017/1111/assets/";
var firstEnter;
var loadimgs = [{
	name: "langan",
	path: yunPath + "img/langan.png"
}, {
	name: "priceBtn",
	path: yunPath + "img/priceBtn.png"
}, {
	name: "priceBtn2",
	path: yunPath + "img/priceBtn2.png"
}, {
	name: "buyBtn",
	path: yunPath + "img/buyBtn.png"
}, {
	name: "shareBtn",
	path: yunPath + "img/shareBtn.png"
}, {
	name: "shareBtn2",
	path: yunPath + "img/shareBtn2.png"
}, {
	name: "shareBtn3",
	path: yunPath + "img/shareBtn3.png"
}, {
	name: "continue1",
	path: yunPath + "img/continue1.png"
}, {
	name: "continue2",
	path: yunPath + "img/continue2.png"
}, {
	name: "continue3",
	path: yunPath + "img/continue3.png"
}, {
	name: "continue4",
	path: yunPath + "img/continue4.png"
}, {
	name: "submit",
	path: yunPath + "img/submitBtn.png"
}, {
	name: "mask",
	path: yunPath + "img/mask.png"
}, {
	name: "jiantou",
	path: yunPath + "img/jiantou.png"
}]

for(var i = 1; i < 28; i++) {
	loadimgs.push({
		name: "bg" + i,
		path: yunPath + "img/bg" + i + ".jpg"
	})
}
for(var i = 1; i < 6; i++) {
	loadimgs.push({
		name: "index-" + i,
		path: yunPath + "img/index-" + i + ".png"
	})
}
for(var i = 1; i < 15; i++) {
	loadimgs.push({
		name: "cata-" + i,
		path: yunPath + "img/cata-" + i + ".png"
	})
	loadimgs.push({
		name: "btn-" + i,
		path: yunPath + "img/btn-" + i + ".png"
	})
}
for(var i = 1; i < 8; i++) {
	loadimgs.push({
		name: "checkBtn-" + i,
		path: yunPath + "img/checkBtn-" + i + ".png"
	})
}

for(var i = 0; i < 51; i++) {
	loadimgs.push({
		name: "donghua" + i,
		path: yunPath + "img/donghua/donghua" + i + ".png"
	})
}

var config = [{
		bg: "bg3",
		bg2: "bg4",
		checkBtn: "checkBtn-1",
		bg3: "bg17"
	},
	{
		bg: "bg5",
		bg2: "bg6",
		checkBtn: "checkBtn-2",
		bg3: "bg18"
	},
	{
		bg: "bg7",
		bg2: "bg8",
		checkBtn: "checkBtn-3",
		bg3: "bg19"
	},
	{
		bg: "bg9",
		bg2: "bg10",
		checkBtn: "checkBtn-4",
		bg3: "bg20"
	},
	{
		bg: "bg11",
		bg2: "bg12",
		checkBtn: "checkBtn-5",
		bg3: "bg21"
	},
	{
		bg: "bg13",
		bg2: "bg14",
		checkBtn: "checkBtn-6",
		bg3: "bg22"
	},
	{
		bg: "bg15",
		bg2: "bg16",
		checkBtn: "checkBtn-7",
		bg3: "bg23"

	}
]

init(30, 'cvs', w, h, main);

function main() {
	LGlobal.align = LStageAlign.TOP_MIDDLE;
	LGlobal.stageScale = LStageScaleMode.SHOW_ALL;
	LGlobal.screen(LStage.FULL_SCREEN);

	loadingLayer = new LoadingSample4()
	addChild(loadingLayer);
	LLoadManage.load(
		loadimgs,
		function(progress) {
			loadingLayer.setProgress(progress);
		},
		loaded
	);
}

function loaded(result) {
	loadlist = result;
	removeChild(loadingLayer);
	loadingLayer = null;
	layer = new LSprite();
	addChild(layer);
	index()
}

//首页
function index() {
	firstEnter = true;
	var content = new LSprite();
	content.x = (w - picW) / 2;
	var bg = new LspriteXY(loadlist['bg1']);
	bg.scaleX = scalex;

	layer.addChild(bg)
	layer.addChild(content)

	var p1 = new LspriteXY(loadlist['index-1'], 88, 146, 0)
	content.addChild(p1)
	var p2 = new LspriteXY(loadlist['index-2'], 34, 314)
	content.addChild(p2)
	var p3 = new LspriteXY(loadlist['index-3'], 132, 899, 0)
	content.addChild(p3)
	var p4 = new LspriteXY(loadlist['index-4'], 85, 879)
	content.addChild(p4)
	var p5 = new LspriteXY(loadlist['index-5'], 35, 374, 0)
	content.addChild(p5)
	var p6 = new LspriteXY(loadlist['index-4'], 520, 879)
	content.addChild(p6)
	LTweenLite.to(p1, 0.5, {
		y: 166,
		alpha: 1
	})
	LTweenLite.to(p2, 0.5, {
		onComplete: p2.fangda(1, 0, 1)
	})
	LTweenLite.to(p3, 0.5, {
		y: 899,
		alpha: 1,
		delay: 1,
		onComplete: p3.daxiao(1, 1.1)
	})
	LTweenLite.to(p5, 0.5, {
		alpha: 1,
		delay: 0.5
	})

	p3.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
		layer.removeAllChild();
		catalog()
	})
}

//房间目录页
function catalog() {
	var content = new LSprite();
	content.scaleX = scalex
	var layer1 = new LSprite();
	var layer2 = new LSprite();
	var layer3 = new LSprite();

	var bg = new LspriteXY(loadlist['bg2']);
	bg.scaleX = scalex
	layer.addChild(bg)
	layer.addChild(content)
	content.addChild(layer1);

	var swich = true
	var timer = new LTimer(1000, 1000);
	timer.addEventListener(LTimerEvent.TIMER, change);
	timer.start()

	function change() {
		if(swich) {
			content.addChild(layer2);
			swich = false;
			return false;
		} else {
			content.removeChild(layer2);
			swich = true;
			return false
		}
	}

	var p1 = new LspriteXY(loadlist['cata-1'], 51, 73);
	layer1.addChild(p1)
	var p2 = new LspriteXY(loadlist['cata-2'], 233, 22);
	layer1.addChild(p2)
	var p3 = new LspriteXY(loadlist['cata-3'], 463, 96);
	layer1.addChild(p3)
	var p4 = new LspriteXY(loadlist['cata-4'], 54, 554);
	layer1.addChild(p4)
	var p5 = new LspriteXY(loadlist['cata-5'], 215, 441);
	layer1.addChild(p5)
	var p6 = new LspriteXY(loadlist['cata-13'], 293, 344);
	layer1.addChild(p6)
	var p7 = new LspriteXY(loadlist['cata-7'], 488, 348);
	layer1.addChild(p7)

	var p8 = new LspriteXY(loadlist['cata-8'], 51, 73);
	layer2.addChild(p8)
	var p9 = new LspriteXY(loadlist['cata-9'], 233, 22);
	layer2.addChild(p9)
	var p10 = new LspriteXY(loadlist['cata-10'], 463, 96);
	layer2.addChild(p10)
	var p11 = new LspriteXY(loadlist['cata-11'], 54, 554);
	layer2.addChild(p11)
	var p12 = new LspriteXY(loadlist['cata-12'], 215, 441);
	layer2.addChild(p12)
	var p13 = new LspriteXY(loadlist['cata-6'], 293, 344);
	layer2.addChild(p13)
	var p14 = new LspriteXY(loadlist['cata-14'], 488, 348);
	layer2.addChild(p14)

	var langan = new LspriteXY(loadlist["langan"], 0, 210);
	layer3.addChild(langan)
	layer3.scaleX = scalex

	if(p1click == true) {
		var btn1 = new LspriteXY(loadlist["btn-1"], 175, 183);
		layer3.addChild(btn1)
		btn1.daxiao(1, 1.1)
		btn1.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
			st.post('add_option.php', {
				num: 1
			}, function(data) {
                if (data.status == -2){
                    alert(data.result);
                    return false;
                }
				layer.removeAllChild();
				donghua(0)
			});
		})
	} else {
		var btn8 = new LspriteXY(loadlist["btn-8"], 175, 183);
		layer3.addChild(btn8)
        btn8.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
            st.post('add_option.php', {
                num: 1
            }, function(data) {
                if (data.status == -2){
                    alert(data.result);
                    return false;
                }
            });
        })
	}
	if(p2click) {
		var btn2 = new LspriteXY(loadlist["btn-2"], 270, 96);
		layer3.addChild(btn2)
		btn2.daxiao(1, 1.1)
		btn2.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
			st.post('add_option.php', {
				num: 2
			}, function(data) {
                if (data.status == -2){
                    alert(data.result);
                    return false;
                }
				layer.removeAllChild();
				donghua(1)
			});
		})
	} else {
		var btn9 = new LspriteXY(loadlist["btn-9"], 270, 96);
		layer3.addChild(btn9)
        btn9.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
            st.post('add_option.php', {
                num: 1
            }, function(data) {
                if (data.status == -2){
                    alert(data.result);
                    return false;
                }
            });
        })
	}
	if(p3click) {
		var btn3 = new LspriteXY(loadlist["btn-3"], 479, 119);
		layer3.addChild(btn3)
		btn3.daxiao(1, 1.1)
		btn3.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
			st.post('add_option.php', {
				num: 3
			}, function(data) {
                if (data.status == -2){
                    alert(data.result);
                    return false;
                }
				layer.removeAllChild();
				donghua(2)
			});
		})
	} else {
		var btn10 = new LspriteXY(loadlist["btn-10"], 479, 119);
		layer3.addChild(btn10)
        btn10.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
            st.post('add_option.php', {
                num: 1
            }, function(data) {
                if (data.status == -2){
                    alert(data.result);
                    return false;
                }
            });
        })
	}
	if(p4click) {
		var btn4 = new LspriteXY(loadlist["btn-4"], 77, 565);
		layer3.addChild(btn4)
		btn4.daxiao(1, 1.1)
		btn4.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
			st.post('add_option.php', {
				num: 4
			}, function(data) {
                if (data.status == -2){
                    alert(data.result);
                    return false;
                }
				layer.removeAllChild();
				donghua(3)
			});
		})
	} else {
		var btn11 = new LspriteXY(loadlist["btn-11"], 77, 565);
		layer3.addChild(btn11)
        btn11.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
            st.post('add_option.php', {
                num: 1
            }, function(data) {
                if (data.status == -2){
                    alert(data.result);
                    return false;
                }
            });
        })
	}
	if(p5click) {
		var btn5 = new LspriteXY(loadlist["btn-5"], 261, 577);
		layer3.addChild(btn5)
		btn5.daxiao(1, 1.1)
		btn5.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
			st.post('add_option.php', {
				num: 5
			}, function(data) {
                if (data.status == -2){
                    alert(data.result);
                    return false;
                }
				layer.removeAllChild();
				donghua(4)
			});
		})
	} else {
		var btn12 = new LspriteXY(loadlist["btn-12"], 261, 577);
		layer3.addChild(btn12)
        btn12.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
            st.post('add_option.php', {
                num: 1
            }, function(data) {
                if (data.status == -2){
                    alert(data.result);
                    return false;
                }
            });
        })
	}
	if(p6click) {
		var btn6 = new LspriteXY(loadlist["btn-6"], 291, 382);
		layer3.addChild(btn6)
		btn6.daxiao(1, 1.1)
		btn6.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
			st.post('add_option.php', {
				num: 6
			}, function(data) {
                if (data.status == -2){
                    alert(data.result);
                    return false;
                }
				layer.removeAllChild();
				donghua(5)
			});
		})
	} else {
		var btn13 = new LspriteXY(loadlist["btn-13"], 291, 382);
		layer3.addChild(btn13)
        btn13.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
            st.post('add_option.php', {
                num: 1
            }, function(data) {
                if (data.status == -2){
                    alert(data.result);
                    return false;
                }
            });
        })
	}
	if(p7click) {
		var btn7 = new LspriteXY(loadlist["btn-7"], 565, 442);
		layer3.addChild(btn7)
		btn7.daxiao(1, 1.1)
		btn7.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
			st.post('add_option.php', {
				num: 7
			}, function(data) {
                if (data.status == -2){
                    alert(data.result);
                    return false;
                }
				layer.removeAllChild();
				donghua(6)
			});
		})
	} else {
		var btn14 = new LspriteXY(loadlist["btn-14"], 565, 442);
		layer3.addChild(btn14)
        btn14.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
            st.post('add_option.php', {
                num: 1
            }, function(data) {
                if (data.status == -2){
                    alert(data.result);
                    return false;
                }
            });
        })
	}

	layer.addChild(layer3);

	if(firstEnter) {
		var mask = new LspriteXY(loadlist['mask']);
		mask.scaleX = scalex;
		layer.addChild(mask);
		mask.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
			layer.removeChild(mask)
		})
		setTimeout(function() {
			layer.removeChild(mask)
		}, 5000)
	}

}

function createRoom(num) {
	var layer1 = new LSprite();
	layer1.scaleX = scalex
	var layer2 = new LSprite();
	var bg = new LspriteXY(loadlist[config[num]['bg']]);
	var bg2 = new LspriteXY(loadlist[config[num]['bg2']]);
	bg2.alpha = 0
	layer.addChild(layer1)
	layer.addChild(layer2)
	layer1.addChild(bg);
	layer1.addChild(bg2);
	
	var jiantou=new LspriteXY(loadlist['jiantou'],290,900,0);
	layer2.addChild(jiantou);
	LTweenLite.to(jiantou,1,{alpha:1,y:800,loop:true}).to(jiantou,0,{alpha:0,y:900})
	
	layer1.addEventListener(LMouseEvent.MOUSE_DOWN,function(){
		layer2.removeChild(jiantou)
	})
	
	var swich = true
	var timer = new LTimer(1000, 1000);
	timer.addEventListener(LTimerEvent.TIMER, change);
	timer.start()

	function change() {
		if(swich) {
			bg2.alpha = 1
			swich = false;
			return false;
		} else {
			bg2.alpha = 0
			swich = true;
			return false
		}
	}

	var btn1 = new LspriteXY(loadlist[config[num]['checkBtn']], 46, 2093, 0);
	layer1.addChild(btn1);
	btn1.daxiao(0.8, 1.05)
	var btn2 = new LspriteXY(loadlist['priceBtn'], 340, 2093, 0);
	layer1.addChild(btn2);

	btn1.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
		layer.removeAllChild();
		createInfo(num)
	})
	btn2.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
		if(num == 0) {
			p1click = false;
		}
		if(num == 1) {
			p2click = false;
		}
		if(num == 2) {
			p3click = false;
		}
		if(num == 3) {
			p4click = false;
		}
		if(num == 4) {
			p5click = false;
		}
		if(num == 5) {
			p6click = false;
		}
		if(num == 6) {
			p7click = false;
		}
		st.post("award.php", {
			num: num + 1
		}, function(data) {
			if(data.status == 1) { //是
				var awardType = data.type;
				if(awardType == 1) {
					layer.removeAllChild();
					priceResult1()
				} else if(awardType == 2) {
					layer.removeAllChild();
					priceResult2()
				} else {
					layer.removeAllChild();
					priceResult3()
				}
				allNum += 1;
			} else {
				layer.removeAllChild();
				priceResult3()
			}
		});
	})
	layer1.addEventListener(LMouseEvent.MOUSE_DOWN, ondown);
	layer1.addEventListener(LMouseEvent.MOUSE_UP, onup);
	layer1.addEventListener(LMouseEvent.MOUSE_MOVE, move);

	function createInfo(num2) {
		var content = new LSprite();
		content.x = (w - picW) / 2
		var bg = new LspriteXY(loadlist[config[num2]['bg3']]);
		bg.scaleX = scalex;
		layer.addChild(bg)
		layer.addChild(content)
		var buyBtn = new LspriteXY(loadlist['buyBtn'], 153, 639, 0);
		content.addChild(buyBtn)
		LTweenLite.to(buyBtn, 0.5, {
			alpha: 1,
			onComplete: buyBtn.daxiao(0.8, 1.05)
		})
		var priceBtn = new LspriteXY(loadlist['priceBtn2'], 35, 1500)
		content.addChild(priceBtn)
		LTweenLite.to(priceBtn, 2, {
			y: 824,
			ease: LEasing.Back.easeInOut
		})
		priceBtn.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
			if(num2 == 0) {
				p1click = false;
			}
			if(num2 == 1) {
				p2click = false;
			}
			if(num2 == 2) {
				p3click = false;
			}
			if(num2 == 3) {
				p4click = false;
			}
			if(num2 == 4) {
				p5click = false;
			}
			if(num2 == 5) {
				p6click = false;
			}
			if(num2 == 6) {
				p7click = false;
			}

			st.post("award.php", {
				num: num + 1
			}, function(data) {
				if(data.status == 1) { //是
					var awardType = data.type;
					if(awardType == 1) {
						layer.removeAllChild();
						priceResult1()
					} else if(awardType == 2) {
						layer.removeAllChild();
						priceResult2()
					} else {
						layer.removeAllChild();
						priceResult3()
					}
					allNum += 1;
				} else {
					layer.removeAllChild();
					priceResult3()
				}
			});
		})
		var shareBtn = new LspriteXY(loadlist['shareBtn'], 314, 1500)
		content.addChild(shareBtn)
		LTweenLite.to(shareBtn, 2, {
			y: 824,
			ease: LEasing.Back.easeInOut
		})
		shareBtn.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
			layer.removeAllChild();
			share()
		})

	}

	function ondown(e) {
		e.clickTarget.startDrag(e.touchPointID);
	}

	function onup(e) {
		e.clickTarget.stopDrag();
	}

	function move(e) {
		e.clickTarget.x = 0;
		if(e.clickTarget.y > 0) e.clickTarget.y = 0;
		if(e.clickTarget.y < -1330) e.clickTarget.y = -1330;
		if(e.clickTarget.y < -1000) {
			LTweenLite.to(btn1, 0.5, {
				alpha: 1
			})
			LTweenLite.to(btn2, 0.5, {
				alpha: 1
			})
		}
	}
}
var chance = 1;

function priceResult1() {
	var canclick = true
	var bg = new LspriteXY(loadlist['bg24']);
	bg.scaleX = scalex;
	var content = new LSprite()
	content.x = (w - picW) / 2
	layer.addChild(bg)
	layer.addChild(content)

	var text1 = new LTextField()
	text1.x = 302;
	text1.y = 156;
	text1.text = (allNum + 1) + "次";
	text1.color = "#c64627";
	text1.size = 32;
	text1.weight = "bold"
	content.addChild(text1)

	var text2 = new LTextField()
	text2.x = 213;
	text2.y = 197;
	text2.text = (6 - allNum) + "次";
	text2.color = "#c64627";
	text2.size = 32;
	text2.weight = "bold"
	content.addChild(text2)
	LGlobal.preventDefault = false;
	var nameText = new Shuru({
		x: 270,
		y: 475,
		size: 30,
		w: 130,
		h: 50,
		color: '#7c3b2b',
		lineColor: '#ff0000'
	})
	var phoneText = new Shuru({
		x: 270,
		y: 540,
		size: 30,
		w: 130,
		h: 50,
		color: '#7c3b2b',
		lineColor: '#ff0000'
	})
	content.addChild(nameText)
	content.addChild(phoneText)
	var submitBtn = new LspriteXY(loadlist['submit'], 394, 487)
	content.addChild(submitBtn)
	var btn1 = new LspriteXY(loadlist['continue1'], 41, 701, 0);
	content.addChild(btn1)
	LTweenLite.to(btn1, 0.5, {
		alpha: 1
	})
	btn1.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
		layer.removeAllChild();
		firstEnter = false;
		catalog()
	})
	var btn2 = new LspriteXY(loadlist['continue2'], 320, 701, 0)
	content.addChild(btn2)
	LTweenLite.to(btn2, 0.5, {
		alpha: 1,
		onComplete: btn2.daxiao(0.8, 1.05)
	})
	btn2.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
		layer.removeAllChild();
		firstEnter = false;
		catalog()
	})
	var share = new LspriteXY(loadlist['shareBtn2'], 270, 867, 0)
	content.addChild(share)
	LTweenLite.to(share, 0.5, {
		alpha: 1
	})
	share.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
		layer.removeAllChild();
		share()
	})
	submitBtn.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
		username = nameText.text;
		phoneNum = phoneText.text;
		var amount = 10;
		if(nameText.text != "" && phoneText.text != "") {
			if(!mobile(phoneNum)) {
				alert('请填写11位手机号码');
				return false
			}
			console.log(username, phoneNum);
		} else if(nameText.text == "") {
			alert("请填写您的姓名！")
			return false;
		} else if(phoneText.text == "") {
			alert("请填写您的电话！")
			return false;
		}

		if(canclick) {
			st.post('add_info.php', {
				name: username,
				tel: phoneNum
			}, function(data) {
				if(data.status == 1) {
					alert("提交成功");
					canclick = false;
				} else {
					alert(data.result);
				}

			});
		} else {
			alert("您已提交过信息")
			return false
		}
	})
}

//中奖30元话费
function priceResult2() {
	var canclick = true;
	var bg = new LspriteXY(loadlist['bg25']);
	bg.scaleX = scalex;
	var content = new LSprite()
	content.x = (w - picW) / 2
	layer.addChild(bg)
	layer.addChild(content)

	var text1 = new LTextField()
	text1.x = 302;
	text1.y = 156;
	text1.text = (allNum + 1) + "次";
	text1.color = "#c64627";
	text1.size = 32;
	text1.weight = "bold"
	content.addChild(text1)

	var text2 = new LTextField()
	text2.x = 213;
	text2.y = 197;
	text2.text = (6 - allNum) + "次";
	text2.color = "#c64627";
	text2.size = 32;
	text2.weight = "bold"
	content.addChild(text2)
	LGlobal.preventDefault = false;
	var nameText = new Shuru({
		x: 270,
		y: 475,
		size: 30,
		w: 130,
		h: 50,
		color: '#7c3b2b',
		lineColor: '#ff0000'
	})
	var phoneText = new Shuru({
		x: 270,
		y: 540,
		size: 30,
		w: 130,
		h: 50,
		color: '#7c3b2b',
		lineColor: '#ff0000'
	})
	content.addChild(nameText)
	content.addChild(phoneText)
	var submitBtn = new LspriteXY(loadlist['submit'], 394, 487)
	content.addChild(submitBtn)
	var btn1 = new LspriteXY(loadlist['continue1'], 41, 701, 0);
	content.addChild(btn1)
	LTweenLite.to(btn1, 0.5, {
		alpha: 1
	})

	btn1.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
		layer.removeAllChild();
		firstEnter = false;
		catalog()
	})
	var btn2 = new LspriteXY(loadlist['continue2'], 320, 701, 0)
	content.addChild(btn2)
	LTweenLite.to(btn2, 0.5, {
		alpha: 1,
		onComplete: btn2.daxiao(0.8, 1.05)
	})
	btn2.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
		layer.removeAllChild();
		firstEnter = false;
		catalog()
	})
	var share = new LspriteXY(loadlist['shareBtn2'], 270, 867, 0)
	content.addChild(share)
	LTweenLite.to(share, 0.5, {
		alpha: 1
	})
	share.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
		layer.removeAllChild();
		share()
	})
	submitBtn.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
		username = nameText.text;
		phoneNum = phoneText.text;
		var amount = 30;
		if(nameText.text != "" && phoneText.text != "") {
			if(!mobile(phoneNum)) {
				alert('请填写11位手机号码');
				return false
			}
			console.log(username, phoneNum);
		} else if(nameText.text == "") {
			alert("请填写您的姓名！")
			return false;
		} else if(phoneText.text == "") {
			alert("请填写您的电话！")
			return false;
		}

		if(canclick) {
			st.post('add_info.php', {
				name: username,
				tel: phoneNum
			}, function(data) {
				if(data.status == 1) {
					alert("提交成功");
					canclick = false;
				} else {
					alert(data.result);
				}

			});
		} else {
			alert("您已提交过信息")
			return false
		}

	});

}

function priceResult3() {
	var bg = new LspriteXY(loadlist['bg26']);
	bg.scaleX = scalex;
	layer.addChild(bg)
	var content = new LSprite()
	content.x = (w - picW) / 2
	layer.addChild(content)
	var text1 = new LTextField()
	text1.x = 302;
	text1.y = 158;
	text1.text = (allNum + 1) + "次";
	text1.color = "#c64627";
	text1.size = 32;
	text1.weight = "bold"
	content.addChild(text1)

	var text2 = new LTextField()
	text2.x = 213;
	text2.y = 200;
	text2.text = (6 - allNum) + "次";
	text2.color = "#c64627";
	text2.size = 32;
	text2.weight = "bold"
	content.addChild(text2)

	var continue1 = new LspriteXY(loadlist["continue3"], 154, 415, 0)
	content.addChild(continue1);
	LTweenLite.to(continue1, 0.5, {
		alpha: 1,
		onComplete: continue1.daxiao(0.8, 1.05)
	})
	continue1.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
		layer.removeAllChild();
		firstEnter = false;
		catalog()
	})
	var continue2 = new LspriteXY(loadlist["continue4"], 30, 699, 0);
	content.addChild(continue2);
	LTweenLite.to(continue2, 0.5, {
		alpha: 1
	})
	continue2.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
		layer.removeAllChild();
		firstEnter = false;
		catalog()
	})
	var shareBtn = new LspriteXY(loadlist["shareBtn3"], 305, 699, 0);
	content.addChild(shareBtn)
	LTweenLite.to(shareBtn, 0.5, {
		alpha: 1
	})
	shareBtn.addEventListener(LMouseEvent.MOUSE_DOWN, function() {
		layer.removeAllChild();
		share()
	})
}

function share() {
	var bg = new LspriteXY(loadlist['bg27'])
	bg.scaleX = scalex
	layer.addChild(bg)
}

function donghua(num) {
	var datas = [];
	var listchild = [];
	for(var i = 0; i < 51; i++) {
		datas.push(new LBitmapData(loadlist["donghua" + i]));
		listchild.push({
			dataIndex: i,
			x: 0,
			y: 0,
			width: 640,
			height: 1010,
			sx: 0,
			sy: 0
		});
	}

	var playerRight = new LAnimationTimeline(datas, [listchild]);
	layer.addChild(playerRight)
	playerRight.addEventListener(LEvent.COMPLETE, function() {
		layer.removeAllChild()
		createRoom(num)
	})
}