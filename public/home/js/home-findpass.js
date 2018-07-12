function findpass()
{
	var rform = $("#findForm");
	$.ajax({
		url: rform.attr('action'),
		type: 'POST',
		data: rform.serialize(),
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
                alert(info.msg);
                window.location.href=info.url;
            }
			
		}
	});
	return false;
}

function updatepass()
{
	var passwd = $(".uppgpa").val();
	var rpasswd = $('.upprpa').val();
	var time = $(".fptime").val();
	var rsapubkey = $(".fppubkey").val();
	var rsapass = any_rsa_pass(rsapubkey,time+"_"+passwd);
	var rsarpass = any_rsa_pass(rsapubkey,time+"_"+rpasswd);

	$(".uppgpa").val(rsapass);
	$(".upprpa").val(rsarpass);
    var fform = $("#updatepForm");
    
	$.ajax({
		url: fform.attr('action'),
		type: 'POST',
		data: fform.serialize(),
		dataType:'json',
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
                window.location.href=info.url;
            }
			
		},
		error:function (error)
		{
			console.log(error)
		}
	});
	return false;
}