function charitablesub()
{

	var rform = $("#charitableform");
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
                window.location.href=info.url;
            }
			
		}
	});
	return false;
}