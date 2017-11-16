$(function() {
// 弹出	
// $(".bg_menu").click(function(){
//     $("#bg1").fadeIn();
//
// });
$(".bg1_menu").click(function(){
	
	$("#bg1").fadeOut();
	
	
    $("#bg").fadeIn();

});



// $(".bg1_lb_xx2").click(function(){
//     $(".bg1_tc2").fadeIn();
//
// });

    $(".bg1_lb_xx2").off('click').on('click', function(){
        $(".bg1_tc2").fadeIn();
    });

$(".bg1_tc2").click(function(){
    $(".bg1_tc2").fadeOut();

});

// $(".bg1_lb_xx3").click(function(){
//     $(".bg1_tc1").fadeIn();
//
// });

    $(".bg1_lb_xx3").off('click').on('click', function(){
        $(".bg1_tc1").fadeIn();
    });
    

$(".bg1_tc1").click(function(){
    $(".bg1_tc1").fadeOut();

});



// $(".bg2_menu").click(function(){
//
// 	$("#bg2").fadeOut();
// 	$("#bg").fadeOut();
//
//     $("#bg1").fadeIn();
//
// });












// var zzsc = $(".zzsc a");
// 	zzsc.click(function(){
// 		$(this).addClass("thisclass");
// });
//
//
//
// var zzsc = $(".zzsc1 a");
// 	zzsc.click(function(){
// 		$(this).addClass("thisclass1");
// });
//
// var zzsc = $(".zzsc2 a");
// 	zzsc.click(function(){
// 		$(this).addClass("thisclass2");
// 	});




})