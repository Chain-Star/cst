$("#carousel-example-generic").addClass(' wzw_box');
$(".wzw_team li").hover(function(){
    $(this).children(".wzw_team_img").stop(true,true).animate({"left":"-228px"},100).siblings().stop(true,true);
},function(){
    $(this).children(".wzw_team_img").stop(true,true).animate({"left":"0"},100).siblings().stop(true,true);
});