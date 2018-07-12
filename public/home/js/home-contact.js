$('#box div.footer').addClass('footer1');
// 修改底部分享图标为白色
var zlf = $('#box div.footer .footFour img');
zlf.eq(0).attr('src','/home/img/a.png');
zlf.eq(1).attr('src','/home/img/b.png');
zlf.eq(2).attr('src','/home/img/c.png');
zlf.eq(3).attr('src','/home/img/d.png');

$('.header_nav_li1 img').attr('src','/home/img/logo-white.png');
$('.header_nav_li2 p label img').attr('src','/home/img/news2-b.png');
$('.header_nav_li2').css('width','90px');
$('.header_nav_li2').css('margin-left','10px');
$('.header_nav_li4').css('width','300px');
$('.header_nav_li4').css('margin-left','10px');
$('.header_nav_li4').css('text-align','left');
$('.header_nav_li4 a').css('margin-left','10px');
$('.header_nav_li5').css('margin-left','0px');
$('.header_nav_li5 button').css('margin-left','0px');
$('.header_nav_li5 button').css('text-align','left');
$('#parent1 select').css('color','#fff');
$('.header').css('height','60px');
//$('.footer .foot2 .foot2_zhong').css('display','none');
//$('.footer .p5').css('color','#fff');
function contactsub()
{
	var rform = $("#contactform");
	$.ajax({
		url: rform.attr('action'),
		type: 'POST',
		data: rform.serialize(),
		success: function (info) 
		{			
			$('.toast span').html(info.msg);
			$('.toast').stop().fadeToggle();
			setTimeout(function(){
				$('.toast').stop().fadeToggle();
			}, info.wait*1000);
			if(info.code == 1)
			{	
				$('#contactform input').val('')
				$('#contactform textarea').html('')
			}
		}
	});
	return false;
}

$(".foot2").css({"background":"transparent"});
$(".foot_right .p5").css({"color":"#fff"});
$('#parent2lang select').css('color','#fff');
$('.foot_right .p1 a img').attr('src','/home/img/logo-white.png');
$("html").css("height","1215px");
$(".footFour p img").css("height","19px");
