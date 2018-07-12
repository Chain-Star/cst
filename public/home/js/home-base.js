//				点击导航发现功能
$(".header_nav_li2").hover(function(){
    $(".faxianClickShow").stop(true,true).slideDown(800);
},function(){
    $(".faxianClickShow").stop(true,true).slideUp(800);
    $(".faxianClickShow").hover(function(){
        $(this).stop(true,true).show();
    },function(){
        $(this).stop(true,true).slideUp(800);
    })
})
//				发现里面6个li的 鼠标hover效果
$(".ImgHover>ul>li").hover(function(){
    var index=$(this).index();
    $(".ImgHover>ul>li").eq(index).attr("style","border: 4px solid #06c5d2;");
    $(".ImgHover>ul>li").children(".muhuImg_posi").eq(index).css({"display":"block"});
    $(".ImgHover>ul>li").children("span").eq(index).css({"margin-top":"-10px"});
    $(".ImgHover>ul>li").children(".border_bottom").eq(index).css({"display":"none"});
},function(){
    var index=$(this).index();
    $(".ImgHover>ul>li").eq(index).removeAttr("style","border: 4px solid #06c5d2;");
    $(".ImgHover>ul>li").children(".muhuImg_posi").eq(index).css({"display":"none"});
    $(".ImgHover>ul>li").children("span").eq(index).css({"margin-top":"0"});
    $(".ImgHover>ul>li").children(".border_bottom").eq(index).css({"display":"block"});
})
// 首页登陆验证
// $(".dengl").click(function(){
//   var Email=$(".dengluemail").val();
//   var PassWord=$(".denglumima").val();
//   if(Email=="" && PassWord==""){
//       $(".wzw_dltk").stop(true,true).fadeOut();
//       $(".header_nav_li5").html("<div class='btn-group geren'>"
//       +"<button class='btn btn-default btn-lg dropdown-toggle touxaing' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>"
//     +"<img src='img/crown.png'/> <span class='caret'></span>"
// +"</button>"
// +"<ul class='dropdown-menu'>"
//     +"<li><a href='#'>投资记录</a></li>"
//     +"<li><a href='#'>我的项目</a></li>"
//     +"<li><a href='#'>投资记录</a></li>"
//     +"<li><a href='#'>帮助</a></li>"
// +"</ul>"
//       +"</div>");
//   };
//   $('body').css({ 
//         "overflow-x":"auto",
//         "overflow-y":"auto"       
//     });
// });

// footer点击语言切换
$(".yuyan li").click(function(ev){
    ev.preventDefault();
    $(".yuyan li").parent("ul").prev("button").html($(this).html()+"<span class='caret'></span>")
})
// 头部语言切换
$('.header_nav_li6 #parent1 select').change(function(){
    window.location.href=$(this).val();
})
$('.enyuyan #parent2lang select').change(function(){
    window.location.href=$(this).val();
})
//ie8兼容placeholder
$(function() {
if(!('placeholder' in document.createElement('input'))) {
    $('input[placeholder],textarea[placeholder]').each(function() {
        var that = $(this),
            text = that.attr('placeholder');
        if(that.val() === "") {
            that.val(text).addClass('placeholder');
        }
        that.focus(function() {
                if(that.val() === text) {
                    that.val("").removeClass('placeholder');
                }
            })
            .blur(function() {
                if(that.val() === "") {
                    that.val(text).addClass('placeholder');
                }
            })
            .closest('form').submit(function() {
                if(that.val() === text) {
                    that.val('');
                }
            });
    });
}
});

function loading(msg=''){
    if(msg)
    {
        $('.wzw_shadow_loading').find('p').html(msg);
        $('.wzw_shadow_posiab').attr('style','display:block');
    }
    $(".wzw_shadowBox").css("height",10+$("html").height()+"px");
    $(".wzw_shadowBox").css("z-index","99999 !important");
    $(".wzw_shadow_loading").fadeIn(300);
}
function clearloading(){
    
    $(".wzw_shadowBox").css("height","0px");
    $(".wzw_shadow_loading").fadeOut(300);
}

var xhrOnProgress=function(fun) {
    xhrOnProgress.onprogress = fun; //绑定监听
    //使用闭包实现监听绑
    return function() {
      //通过$.ajaxSettings.xhr();获得XMLHttpRequest对象
      var xhr = $.ajaxSettings.xhr();
      //判断监听函数是否为函数
      if (typeof xhrOnProgress.onprogress !== 'function')
        return xhr;
      //如果有监听函数并且xhr对象支持绑定时就把监听函数绑定上去
      if (xhrOnProgress.onprogress && xhr.upload) {
        xhr.upload.onprogress = xhrOnProgress.onprogress;
      }
      return xhr;
    }
}
$(".wzw_shadow_posiab").click(function(){
	$(this).parent().parent().fadeOut(300);
	$(this).parent().parent().parent().css({"height":"0px"});
})
//去除cnzz
$("#box span#cnzz_stat_icon_1273987547").css("display","none");
