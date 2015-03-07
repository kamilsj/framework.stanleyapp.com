$('#addPhotos').click(function(){

	$('#startUploadPhoto').animate({opacity: 1, height: 'toggle'}, 200, function() {});

});

$('#addPost').click(function(){
	$.ajax({
		url: './ajax/addpost.php',
		type: 'POST',
		data: {body: $('#post').val(), cat: $('#cat').val()},
		success:function(res)
		{

			var obj = jQuery.parseJSON(res);
			
			if(typeof obj == 'object')
			{
				
				if(obj.stat == 'OK')
				{
                    //alert(res);
                    $('#startUploadPhoto').slideUp('fast');
					$('#post').val("");
					$('#postMain').prepend('<div id="love'+obj.pid+'"></div>');
					$('#love'+obj.pid).css('display', 'none').append(obj.div).slideDown('fast');
				}
				
					
			}
			
		}
	});
	
});

function addcomment(e, num)
{
	if(e.keyCode == 13)
	{
		
		var body = $('#commentBody'+num).val();
		
		if(body.length > 2)
		{		
			$.ajax({
				
				url: './ajax/addcomment.php',
				type: 'post',
				data: {pid: num, body: body},
				success: function(res)
				{
					
					var obj = jQuery.parseJSON(res);
					
					
					if(typeof obj == 'object')
					{
						if(obj.stat == 'OK')
						{
																						
							$('#commentBody'+num).val("");
							$('#newComment'+num).append('<div id="smile'+obj.cid+'"></div>');
							$('#smile'+obj.cid).css('display', 'none').append(obj.div).slideDown('slow');
							
							
						}
						
					}
					
				}
				
			});
		}
	}


}
/*

EVALUATION POSTS

 */

function evaluateComm(cat, pid, value)
{

    $.ajax({
        url: './ajax/evaluateComm.php',
        type: 'post',
        data: {cat: cat, pid: pid, value: value},
        success: function(req)
        {
            if(value==0)
            {
                $('#eMinus'+pid).attr('src', 'https://c730088.ssl.cf2.rackcdn.com/gfx/down.gif');
                $('#ePlus'+pid).attr('src', 'https://c730088.ssl.cf2.rackcdn.com/gfx/up2.gif');
            }
            else
            {
                $('#eMinus'+pid).attr('src', 'https://c730088.ssl.cf2.rackcdn.com/gfx/down2.gif');
                $('#ePlus'+pid).attr('src', 'https://c730088.ssl.cf2.rackcdn.com/gfx/up.gif');
            }

            $('#eCount'+pid).html(req);
            $('html,body').animate({scrollTop: $('#eCount'+cid).offset().top}, 1);
        }
    });

}

function deleteEvComm(cat, pid)
{
    $.ajax({
        url: './ajax/deleteEvComm.php',
        type: 'post',
        data: {cat: cat, pid: pid},
        success: function(req)
        {

            $('#eMinus'+pid).attr('src', 'https://c730088.ssl.cf2.rackcdn.com/gfx/down.gif');
            $('#ePlus'+pid).attr('src', 'https://c730088.ssl.cf2.rackcdn.com/gfx/up.gif');
            $('#eCount'+pid).html(req);
            $('html,body').animate({scrollTop: $('#eCount'+pid).offset().top}, 1);

        }
    });
}
/*

EVALUATION POSTS ENDS

 */
function deletePost(pid, cat)
{
	$.ajax({
		url: './ajax/deletepost.php',
		type: 'post',
		data: {pid: pid, cat: cat},
		success: function(res)
		{
			
			var obj = jQuery.parseJSON(res);
			
			if(typeof obj == 'object')
			{
				if(obj.stat == 'OK')
				{
					
					$('#post'+pid).fadeOut('slow');
				}
			}
		}
	});

}

function deleteComment(cid, pid)
{
    $.ajax({

        url: './ajax/deletecomment.php',
        type: 'post',
        data: {cid: cid, pid: pid},
        success: function(res)
        {
            var obj = jQuery.parseJSON(res);
            if(typeof obj == 'object')
            {
                if(obj.stat == 'OK')
                {
                    $('#comment'+cid).fadeOut('slow');
                }
            }
        }

    });
}

Dropbox.choose(options);

options = {
    success: function(files) {

        $.ajax({

            url: './ajax/addfiles.php',
            type: 'POST',
            data: {files: files},
            success: function(res)
            {
                var obj = jQuery.parseJSON(res);

                if(typeof obj == 'object')
                {
                    if(obj.stat == 'OK')
                    {
                        $('#msg').html(obj.msg);
                        $('#msg').fadeIn('slow');
                        setTimeout(
                            function()
                            {
                                $('#msg').fadeOut('slow');
                            },
                            3000
                        );
                    }
                }
            }
        });

    },
    cancel:  function() {

    },

    linkType: "preview",
    multiselect: true,
    extensions: ['.pdf', '.doc', '.docx']
}