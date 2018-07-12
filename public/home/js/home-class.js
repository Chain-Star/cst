// 注册登录按钮点击
$('.z_tou .zlregister').click(function(){
    clickregister();
})
$('.z_tou .zllogin').click(function(){
    clicklogin();
})
$(".yuyan li").click(function(ev) {
    ev.preventDefault()
    $(this).parent("ul").prev("button").html($(this).html() + "<span class='caret'></span>")
})

function back() {
    var back = document.getElementsByClassName("back");
    var clientHeight = 500;
    var timer = null;

    window.onscroll = function() {
        var osTop = document.documentElement.scrollTop || document.body.scrollTop;

        if(osTop > clientHeight) {
            back[0].style.display = "block";
        } else {
            back[0].style.display = "none";
        }
    }
    back[0].onclick = function() {
        clearInterval(timer);
        timer = setInterval(function() {
            var osTop = document.documentElement.scrollTop || document.body.scrollTop;
            var spd = Math.floor((-osTop) / 50);

            document.documentElement.scrollTop = osTop + spd;
            document.body.scrollTop = osTop + spd;

            if(osTop == 0) {
                clearInterval(timer);
            }
        }, 1);
    }
}
back()


//投资专家，链课堂鼠标放上
$(function () {
    $(".nei").mouseenter(function () {
        $(this).css("backgroundColor", "#666");
        $(this).find(".z_zi").css("backgroundColor", "#666");
        $(this).find(".z_zi").css("border", "none");
        $(this).find(".hua").css("overflow-y", "scroll");
        $(this).find('span').css("color","#fff")
    }).mouseleave(function () {
        $(this).css("backgroundColor", "white");
        $(this).find(".z_zi").css("backgroundColor", "#f5f5f5");
        $(this).find(".z_zi").css("border", "1px solid #ddd");
        $(this).find(".hua").css("overflow-y", "hidden");
        $(this).find('span').css("color","black");
        $(this).find('.ple1').css("color","#003366");
    });
})
    
    //链课堂
    $(function () {
    $(".z_lian_wai").mouseenter(function () {
        $(this).css("backgroundColor", "#666");
        $(this).find(".z_top_foot").css("color", "#666");
        $(this).find('span').css("color","#fff");
    }).mouseleave(function () {
        $(this).css("backgroundColor", "white");
        $(this).find(".z_lian_day1").css("color", "#333");
        $(this).find(".z_lian_day2").css("color", "#666");
        $(this).find('#z_top_left_botton').css("color","#333");
        $(this).find('.z_top_foot').css("display","block");
    });
})

$('.carousel').carousel({
    interval: 3000,
})

//              轮播左右按钮实现页码显示
var bitem = $('.carousel-inner div.item').length;

function banner(bitem) {
    var i = 1;
    $(".right").click(function() {
        i++;
        $(".span1").html(i);
        if(i > bitem) {
            i = 1;
            $(".span1").html(i);
        }
    })
    $(".left").click(function() {
        i--;
        $(".span1").html(i);
        if(i < 1) {
            i = bitem;
            $(".span1").html(i);
        }
    })
}
banner(bitem);