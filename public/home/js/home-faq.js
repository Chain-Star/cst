$('#carousel-example-generic').addClass('wzw_box');
$(".wzw_li_top").click(function(){
    var id = $(this).attr('data-id');
    // getfaq(id);
    $(this).siblings('div').stop(true,true).fadeToggle();
    	
    if($(this).parent().prev().children("img").hasClass("bor")==false){
        $(this).parent().prev().children("img").addClass("bor");
    }else{
        $(this).parent().prev().children("img").removeClass("bor");
        
    };
});

//获取回复信息
function getfaq(id)
{
    var url = $('.faqurl').val();
    
    return $.ajax({
        url:url,
        data:{id:id},
        dataType:'json',
        type:'post',
        success: function(data){
            $('#faqzl'+id).siblings('div').remove();
            if(data.code==1)
            {                
                var list = data.data;
                for(var i=0;i<list.length;i++)
                {
                    var html = '<div class="wzw_li_bottom">'+list[i].faq_reply+'</div>';
                    $('#faqzl'+id).parent().append(html);
                }                
            }
            else
            {
                var html = '<div class="wzw_li_bottom">'+data.msg+'</div>';
                $('#faqzl'+id).parent().append(html);
            }

        }
    });
}