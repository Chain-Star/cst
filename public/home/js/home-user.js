// 注册
function forrsa()
{
	var passwd = $(".regpa").val();
	var rpasswd = $('.regrpa').val();
	var time = $(".rtime").val();
	var rsapubkey = $(".rpubkey").val();
	var rsapass = passwd;
	var rsarpass = rpasswd;

	$(".regpa").val(rsapass);
	$(".regrpa").val(rsarpass);
	var rform = $("#registerForm");
	$.ajax({
		url: rform.attr('action'),
		type: 'POST',
		data: rform.serialize(),
		success: function (info) 
		{
			if(info.code == 1)
			{
                alert(info.msg);
				window.location.href=info.url;
            }
            else if(info.code == 0)
            {
                alert(info.msg);
                $(".regpa").val('');
                $(".regrpa").val('');
                $('.regtok').val(info.data.token);
            }
			
		}
	});
	return false;
}

// // 登录
function loginbtn()
{
	var passwd = $(".loginpas").val();
	var time = $(".ltime").val();
	var rsapubkey = $(".lpubkey").val();
    var rsapass = passwd;
    
	$(".loginpas").val(rsapass);

	var lform = $("#signupForm");
	$.ajax({
		url: lform.attr('action'),
		type: 'POST',
		data: lform.serialize(),
		success: function (info) 
		{
			console.log(info);
			if(info.code == 1)
			{
                alert(info.msg);
				window.location.href=info.url;
            }
            else if(info.code == 0)
            {
                $('#signupForm .logtok').val(info.data.token);
                $(".loginpas").val('');
                alert(info.msg);
            }
			
		}
	});
	return false;
}

function userlogout()
{
   var url = $('.header_nav .logout').attr('href');
   var zllang = $('.header_nav .logout').attr('data-lang');
   $.ajax({url:url,type:'POST',data:{lang:zllang},success:function(info){
        alert(info.msg);        
        // alert(info.url+zllang);
        window.location.href=info.url+zllang;
   }});
    return false;
}
window.onload=function(){
	pingHeight=$("html").height();
}

//				首页点击登陆弹框
function clicklogin()
{
    $(".wzw_dltk").fadeToggle();
	$(".wzw_dltk").css("height",pingHeight+"px");	
     inp=$(".inp input");
    for(var j=0;j<inp.length;j++){
        inp.eq(j).css({"color":"gray"});
        inp.eq(j).focus(function(){
            if($(this).val()=="Email" || $(this).val()=="Password"){
                $(this).val("");
            };
        });
    };
}
$(".header_nav_li5_btn1").click(function(){
    clicklogin();
});
//首页点击    登录弹框内的注册按钮
$(".zhuc").click(function(){
    $(this).parent().parent().parent().parent().parent().parent().fadeOut();
    $(".wzw_zctk").fadeToggle();
    $(".wzw_zctk").css("height",pingHeight+"px");
    return false;
})
//首页点击   注册 弹框内的登录按钮
$(".other2 .a2").click(function(){
$(this).parent().parent().parent().parent().fadeOut();
$(".wzw_dltk").fadeToggle();
$(".wzw_dltk").css("height",pingHeight+"px");
})
//注册框点击x号关闭
$(".Close").click(function(){
    var url = $(this).attr('data-url');
    $.ajax({url:url,type:'POST',data:{},success:function(info){
   }});
 $(this).parent().parent().fadeOut();
});

function clickregister()
{
    $(".wzw_zctk").fadeToggle();
    $(".wzw_zctk").css("height",pingHeight+"px");
    var inP=$(".zc_inp>input");
    for(var n=0;n<inP.length;n++){
        inP.eq(n).css({"color":"gray"});
        inP.eq(n).focus(function(){
            if($(this).val()=="Email" || $(this).val()=="Password" || $(this).val()=="First Name" || $(this).val()=="Last Name"){
                $(this).val("");
            }
        })
    }
}
//点击注册弹框
$(".header_nav_li5_btn2").click(function(){
    clickregister();
});

// 返回顶部js代码
window.onkeydown=function(ev){
	if(ev.keyCode==116){
		$("body,html").animate({
            scrollTop:0,
        },50);
	}
}
$("body,html").animate({
    scrollTop:0,
},50);
function back(){
    $(".back").click(function(){
        $("body,html").animate({
            scrollTop:0,
        },500);
    })
};
back();
var inter=setInterval(function(){
        var htmlTop=$("body,html").scrollTop();
    if(htmlTop>500){
        $(".back").fadeIn();
    }else{
        $(".back").fadeOut();
    };
},20);

// 发送验证码
$('.wzw_sryzm .zlmobilebtn').click(function(){
    var mobile = $('#wzw_telyzm').val();
    if(mobile == '')
    {
        alert($('.zl_mobile_empty').val());
    }
    else
    {
        var code = $('#wzw_yzmparent1').find("input[name='code']").val();
        sendMobileCode(code,mobile);
    }
    return false;
});


function sendMobileCode(code,mobile)
{
    var url = $('.zl_mobile_url').val();
    console.log(url);
    $.ajax({url:url,type:'POST',data:{code:code,mobile:mobile},success:function(info){
        alert(info.msg);
        if(info.code == 0)
        {
            $('.regtok').val(info.data.token);
        }
    }});
}
