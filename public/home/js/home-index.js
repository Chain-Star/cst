
//	首页轮播
    
//              轮播左右按钮实现页码显示
function banner(){
    $('.carousel').carousel({
        interval: 3000,
    });
    
};

function banner(){
        var i=1;
         inter=setTimeout(function(){
            i++;
            $(".span1").html(i);
            if(i>5){
                i=1;
                $(".span1").html(i);
            }
            if(i<1){
                i=5;
                $(".span1").html(i);
            }
        },3000)
    $(".right").click(function(){
        clearTimeout(inter);
        i++;
        $(".span1").html(i);
        if(i>5){
            i=1;
            $(".span1").html(i);
        }
    })
    $(".left").click(function(){
        clearTimeout(inter);
        i--;
        $(".span1").html(i);
        if(i<1){
            i=5;
            $(".span1").html(i);
        }
    })
}
banner();

//投资项目投资机构鼠标经过效果
   $(".box_three>ul>li").hover(function(){
    $(this).children(".posi_ab").stop(true,true).fadeIn();
},function(){
    $(this).children(".posi_ab").stop(true,true).fadeOut();
});
    //首页第三部分    投资项目  投资机构  切换部分
$(".xiangmu").click(function(){
    $(this).toggleClass("borthree");
    $(".jigou").removeClass("borthree");
    $(".box_three").eq(0).stop(true,true).fadeToggle(500);
    $(".box_three").eq(1).stop(true,true).fadeOut(20);
});
$(".jigou").click(function(){
     $(this).toggleClass("borthree");
     $(".xiangmu").removeClass("borthree");
    $(".box_three").eq(1).stop(true,true).fadeToggle(500);
    $(".box_three").eq(0).stop(true,true).fadeOut(20);
});
$(".wzw_clickT").click(function(){
    $(".xiangmu").addClass("borthree");
    $(".jigou").removeClass("borthree");
    $(".box_three").eq(0).stop(true,true).fadeIn(500);
    $(".box_three").eq(1).stop(true,true).fadeOut(20);
});

//              发起项目发起投资
$(".box_three_two>ul>li").hover(function(){
    $(this).children("div").attr("style","border: 1px solid white;");
    $(this).attr("style","cursor: pointer;");
    $(this).children(".box_three_two_posiimg").attr("style","z-index: 4;");
    var index=$(this).index();
    if(index>1){
    	text_none1=$(this).children("div").children("h3").html();
    	text_none2=$(this).children("div").children("p").html();
    	var none_text_one=$(".wzw_none_one").eq(0).text();
    	$(this).children("div").html("<h3>"+none_text_one+"</h3><p></p>");
    }
},function(){
    $(this).children("div").removeAttr("style","border: 1px solid white;");
    $(this).removeAttr("style","cursor: pointer;");
    $(this).children(".box_three_two_posiimg").removeAttr("style","z-index: 4;");
     var index=$(this).index();
    if(index>1){
    	$(this).children("div").html("<h3>"+text_none1+"</h3><p>"+text_none2+"</p>");
    }
});
