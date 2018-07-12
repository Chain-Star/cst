var aa = $('.wzw_zixunAuto .wzw_radius div.wzw-radius-xian');
//获取屏幕宽度
var wid= document.body.clientWidth;
var leftWid=(wid-1200)/2;
//获取.wzw_zixunXiangqing的高度
var hei=$(".wzw_zixunXiangqing").height();
//设置左边的宽度设置左边的高度
$(".wzw_zixunXiangqing_left").css({"width":leftWid+800+"px","height":hei+"px"});
//设置右边的宽度设置右边的高度
$(".wzw_zixunXiangqing_right").css({"width":leftWid+400+"px","height":hei+"px"});
// aa.eq(aa.length-1).remove();
$('#carousel-example-generic').addClass('wzw_box');
$(".wzw_zixun li").hover(function(){
    var index=$(this).index();
    $(".wzw-radius").eq(index).css({"background":"#036699"});
    $(this).children().children().eq(1).stop(true,true).fadeIn();
},function(){
    var index=$(this).index();
    $(".wzw-radius").eq(index).css({"background":"#535353"});
    $(this).children().children().eq(1).stop(true,true).fadeOut();
});
$(".wzw-radius").hover(function(){
    var index=$(this).index();
    var index2=index/2
    $(this).css({"background":"#036699"});
    $(".wzw_zixun li").eq(index2).children().children("div").eq(1).stop(true,true).fadeIn();
},function(){
    var index=$(this).index();
    var index2=index/2;
    $(this).css({"background":"#535353"});
    $(".wzw_zixun li").eq(index2).children().children("div").eq(1).stop(true,true).fadeOut();
});
