//个人中心点击左边的li切换样式
$("#side-menu>li").click(function() {
	$(this).css({
		"font-size": "18px"
	}).siblings().css({
		"font-size": ""
	})
})
$(".nav>li>a").click(function() {
	$(this).css("color", "#003366");
	$(this).parent().siblings().children("a").css("color", "")
})

//我的项目中下拉效果
//$(".procz").mouseenter(function(){
//	$(this).children("ol").show();
//})
//$(".procz").mouseleave(function(){
//	$(this).children("ol").hide();
//})

//$(".w_proqx").click(function() {
//	$(".w_prozz").css("display", "none");
//});

/*//		点击撤销
$(".delpro").click(function(){
//	$(this).parents("table").siblings(".w_tzz").show();
	$(this).siblings(".w_tzz").show()
	$(".delpro").attr("disabled",true)
})
$(".w_proqxx").click(function() {
	$(".w_tzz").css("display", "none");
	$(".delpro").attr("disabled",false)
});*/
