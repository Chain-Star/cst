/**
 * 后台JS主入口
 */
var layer = layui.layer,
    element = layui.element,
    laydate = layui.laydate,
    form = layui.form;
    laypage=layui.laypage;

/**
 * AJAX全局设置
 */
$.ajaxSetup(
{
    type: "post",
    dataType: "json"
});

/**
 * 后台侧边菜单选中状态
 */
$('.layui-nav-item').find('a').removeClass('layui-this');
$('.layui-nav-tree').find('a[href*="' + GV.current_controller + '"]').parent().addClass('layui-this').parents('.layui-nav-item').addClass('layui-nav-itemed');
/**
 * 通用单图上传
 */
/**
 * 通用日期时间选择
 */
$('.datetime').on('click', function()
{
    laydate(
    {
        elem: this,
        istime: true,
        format: 'YYYY-MM-DD hh:mm:ss'
    })
});
/**
 * 通用表单提交(AJAX方式)
 */
form.on('submit(*)', function(data)
{
    $.ajax(
    {
        url: data.form.action,
        type: data.form.method,
        data: $(data.form).serialize(),
        success: function(info)
        {
			
            if (info.code === 1)
            {
                setTimeout(function()
                {
                    window.location.href=info.url;
                }, 2000);
				layer.msg(info.msg);
            }else{
				layer.msg(info.msg);
			}
            // console.log(info);
        }
    });
    return false;
});
// console.log(document.getElementsByTagName('iframe'));
/**
 * 通用批量处理（审核、取消审核、删除）
 */
$('.ajax-action').on('click', function()
{
    var _action = $(this).data('action');
    layer.open(
    {
        shade: false,
        content: '确定执行此操作？',
        btn: ['确定', '取消'],
        yes: function(index)
        {
            $.ajax(
            {
                url: _action,
                data: $('.ajax-form').serialize(),
                success: function(info)
                {
                    if (info.code === 1)
                    {
                        setTimeout(function()
                        {
                            location.href = info.url;
                        }, 1000);
                    }
                    layer.msg(info.msg);
                }
            });
            layer.close(index);
        }
    });
    return false;
});
function resize_base_call(){
	
	$(window).resize(function(){
		var ifrheight=0;
		if (document.documentElement.clientHeight==0&&document.documentElement.scrollHeight!=0) 
		{
			ifrheight =document.documentElement.scrollHeight+101;
		}
		else if(document.documentElement.scrollHeight==0)
		{
			ifrheight =parent.document.documentElement.scrollHeight-202;
		}
		else
		{
			ifrheight =document.documentElement.clientHeight-101;
		} 
		$('iframe').css("height",ifrheight);
	});
	
}
var list = new Array();

function jump_menu_parent(id){
    if (any_tpl_level==1){
        for(var i=0;i<allmenulist.length;i++){
            if (allmenulist[i].name.toLowerCase()==id.toLowerCase()){
                jump_menu(allmenulist[i].pid,allmenulist[i].name,allmenulist[i].title);
                return;
            }else if (allmenulist[i].children && allmenulist[i].children.length>0 ){
                for(var j=0;j<allmenulist[i].children.length;j++){
                    var jumpitem=allmenulist[i].children[j];
                    if (jumpitem.name.toLowerCase()==id.toLowerCase()){                        
                        jump_menu(jumpitem.pid,jumpitem.name,jumpitem.title);
                        return;
                    }
                }
            }
        }
        return ;
    }
    if (parent && parent.jump_menu_parent){
        parent.jump_menu_parent(id);
    }
}
function jump_menu(menuid,id,title){
	if (any_tpl_level==1){
		$('[lay-id='+menuid+']').trigger("click");
        var ifr=$('#left_'+menuid);
		//$('#left_'+menuid).first().contentWindow.jump_menu(menuid,id,title);
        if (ifr.length>0){
		  ifr[0].contentWindow.jump_menu(menuid,id,title);
        }
		return;
	}
    $("a").each(function(){
        if ($(this).attr('id').toLowerCase()==id.toLowerCase()){
            $(this).trigger("click")
        }
    });
    return;
	var num = list.indexOf(id);
    ///t.parentNode.classList.add("layui-this");
    if (num != -1)
    {
        // element.tabDelete("demo", id);
        // console.log(num);
    }
    else
    {
        var url = GV.root_url + "/" + id;
       
        
        element.tabAdd('demo',
        {
            title: title,
            content: '<iframe onload="changeFrameHeight(this)" width="100%" height="600" src="' + url + '" frameborder="0" scrolling="auto"></iframe> ' //支持传入html
                ,
            id: id,
        });
        list.push(id);
        // console.log(url);
    }
    element.tabChange('demo', id);
}
function closetab(id)
{
    var num = list.indexOf(id);
    if (num != -1) 
    {
        list.splice(num,1);
    }
    element.tabDelete("demo", id);
}
function jump1(id, reloadurl)
{ 

    var a=document.getElementsByTagName('iframe');
    var IframeSrc=$(a[0]).attr("src");
    $(a[0]).attr("src",IframeSrc);
    console.log(list);
    console.log(id);
    // a[0].location.reload();
    // var id=t.getAttribute('lay-id');
    var num = list.indexOf(id);
    if (num != -1) 
    {
        list.splice(num,1);
    }
    element.init();
    
	if (reloadurl){
	   console.log("tabChange:"+reloadurl);
	   element.tabChange('demo', reloadurl);
	}
	element.tabDelete("demo", id);
}
function jump(id,title,t,arr)
{
    var num = list.indexOf(id);
   // t.parentNode.classList.add("layui-this");
    var url = GV.root_url + "/" + id;
        if (arr) 
        {
            for (var k in arr) 
            {
                url+="/"+k+'/'+arr[k];
            }
        }
    if (num != -1)
    {
       
        if ($(t).parent().hasClass('layui-this') )
            $('iframe[ifrid="'+id+'"]').attr('src',url);
        // element.tabDelete("demo", id);
        // console.log(num);
    }
    else
    {
        
        
        element.tabAdd('demo',
        {
            title: title,
            content: '<iframe ifrid="'+id+'" onload="changeFrameHeight(this)" width="100%" height="600" src="' + url + '" frameborder="0" scrolling="auto"></iframe> ' //支持传入html
                ,
            id: id,
        });
        list.push(id);
        // console.log(url);
    }
    element.tabChange('demo', id);
    // element.render('tab', 'demo');
    // element.tabDelete("demo", id);
    // console.log(list);
}
var myinitis=false;
function left_init(){
    if (myinitis) return 
    myinitis=true;
    ;
    $('a').first().trigger("click");
}
element.on('tab(docDemoTabBrief)', function(data)
{
      var id=this.getAttribute('lay-id');
      $('#left_'+id)[0].contentWindow.left_init();
});
element.on('tab(demo)', function(data)
{
    // console.log(document.getElementsByTagName('iframe'));
    var id=this.getAttribute('lay-id');
    $('dd').removeClass('layui-this');
    $('.layui-tree').find('a[id*="' +id + '"]').parent().addClass('layui-this');
    // var Iframe=$(data.elem).find("iframe").eq(data.index);
    //         var IframeSrc=$(Iframe[0]).attr("src");
    //         $(Iframe[0]).attr("src",IframeSrc);
    // $('.layui-nav-tree').find('a[id*="' +id + '"]').parent().addClass('layui-this').parents('.layui-nav-item').addClass('layui-nav-itemed');
    // console.log(this); //当前Tab标题所在的原始DOM元素
    // console.log(data.index); //得到当前Tab的所在下标
    // console.log(data.elem); //得到当前的Tab大容器
});
element.on('tabDelete(demo)', function(data)
{
    var id=this.parentNode.getAttribute('lay-id');
    var num = list.indexOf(id);
    if (num != -1) 
    {
        list.splice(num,1);
    }
    // console.log(list);
    // console.log("asss");
});

function changeFrameHeight(e)
{
    // console.log(document.documentElement.scrollHeight);
    // console.log(document.documentElement.clientHeight);
    // console.log(document.documentElement.offsetHeight);
    if (document.documentElement.clientHeight==0&&document.documentElement.scrollHeight!=0) 
    {
        e.height =document.documentElement.scrollHeight+101;
    }
    else if(document.documentElement.scrollHeight==0)
    {
        e.height =parent.document.documentElement.scrollHeight-202;
    }
    else
    {
        e.height =document.documentElement.clientHeight-101;
    } 
}
/**
 * 通用全选
 */
$('.check-all').on('click', function()
{
    $(this).parents('table').find('input[type="checkbox"]').prop('checked', $(this).prop('checked'));
});
/**
 * 通用删除
 */
$('.ajax-delete').on('click', function()
{
    var _href = $(this).attr('href');
    layer.open(
    {
        shade: false,
        content: '确定删除？',
        btn: ['确定', '取消'],
        yes: function(index)
        {
            $.ajax(
            {
                url: _href,
                type: "get",
                success: function(info)
                {
                    if (info.code === 1)
                    {
                        // console.log(info);
                        setTimeout(function()
                        {
                            location.href = info.url;
                        }, 1000);
                    }
                    layer.msg(info.msg);
                }
            });
            layer.close(index);
        }
    });
    return false;
});
/*
var _action = $(this).data('action');
    layer.open(
    {
        shade: false,
        content: '确定执行此操作？',
        btn: ['确定', '取消'],
        yes: function(index)
        {
            $.ajax(
            {
                url: _action,
                data: $('.ajax-form').serialize(),
                success: function(info)
                {
                    if (info.code === 1)
                    {
                        setTimeout(function()
                        {
                            location.href = info.url;
                        }, 1000);
                    }
                    layer.msg(info.msg);
                }
            });
            layer.close(index);
        }
    });
    return false;
*/

$('.ajax-deletes').on('click', function()
{
    var _action = $(this).data('action');
    layer.open(
    {
        shade: false,
        content: '确定删除？',
        btn: ['确定', '取消'],
        yes: function(index)
        {
           $.ajax(
            {
                url: _action,
                data: $('.ajax-form').serialize(),
                success: function(info)
                {
                    if (info.code === 1)
                    {
                        setTimeout(function()
                        {
                            location.href = info.url;
                        }, 1000);
                    }
                    layer.msg(info.msg);
                }
            });
            layer.close(index);
        }
    });
    return false;
});
/**
 * 清除缓存
 */
$('#clear-cache').on('click', function()
{
    var _url = $(this).data('url');
    if (_url !== 'undefined')
    {
        $.ajax(
        {
            url: _url,
            success: function(data)
            {
                if (data.code === 1)
                {
                    setTimeout(function()
                    {
                        location.href = location.pathname;
                    }, 1000);
                }
                layer.msg(data.msg);
            }
        });
    }
    return false;
});

/**
 * 通用删除
 */
$('.ajax-status').on('click', function()
{
    var _href = $(this).attr('href');
    layer.open(
    {
        shade: false,
        content: '确定修改状态？',
        btn: ['确定', '取消'],
        yes: function(index)
        {
            $.ajax(
            {
                url: _href,
                type: "get",
                success: function(info)
                {
                    if (info.code === 1)
                    {
                        // console.log(info);
                        setTimeout(function()
                        {
                            location.href = info.url;
                        }, 1000);
                    }
                    layer.msg(info.msg);
                }
            });
            layer.close(index);
        }
    });
    return false;
});