$('#logButton').click(function()
{
	
	$("#logButton").addClass("buttonAnim");
	
	$.ajax({
		type: 'POST',
		url: './ajax/login.php',
		data: { email: $('#email').val(), passwd: $('#passwd').val() },
		success: function(res)	
		{

			var obj = jQuery.parseJSON(res);			
			
			if(typeof obj == 'object')					
			{
				
				
				if(obj.stat == 'OK')
				{
					location.reload();
				}
				else
				{
					if(obj.email == 0) $('#email').addClass("err"); else $('#email').removeClass("err");
					if(obj.passwd == 0) $('#passwd').addClass("err"); else $('#passwd').removeClass("err");
                    if(obj.block == 1)
                    {
                        $('#msg').html(obj.block.msg);
                        $('#msg').fadeIn('slow');
                        setTimeout(
                            function()
                            {
                                $('#msg').fadeOut('slow');
                            },
                            3000
                        );
                    }
					
					$("#logButton").removeClass("buttonAnim");
				}
			
			}						
		}
	});
	
});

$('#logout').click(function()
{
	$.ajax({
		url: './ajax/logout.php',
		success: function(res)
		{
			location.reload();
		}
	});
});

/*

	SEARCH javascript part

*/

$('#search').focus(function()
{
	
	$('#searchArea').slideDown('fast');

});

$('#search').blur(function()
{
	
	$('#searchArea').slideUp('fast');
	$('#inputText').css('color', '#fff');
	
});

var query = '';
var spec  = '';

$('#search').keyup(function(event) {
	

	if($('#search').val() != query) {       
		$('#inputText').html($(this).val());
	}
      
	query = $('#search').val()
	
	if(query.length > 2)
	{
		
		$('#searchBody').slideDown('fast');
		
		if(query == 'category' || query == 'categories' || query == 'user' || query == 'users' || query == 'file' || query =='files')
		{
			spec = query;
			$('#inputText').css('color', '#b1afa2');			
		}
				
		
		$.ajax({
			url: './ajax/quicksearch.php',
			type: 'post',
			data: {q: query, spec: spec},
			success: function(res){
				
				
				
				
				if(typeof obj == 'object')
				{
					
					if(obj.stat == 'OK')
					{
						$('#searchBody').html(obj.div);
					}
					
				}
				
			}
		});


	}else
	{ 
		$('#searchBody').slideUp('fast');
		$('#inputText').css('color', '#fff');	
		
	}
	
	if(event.keyCode != 13)
	{
		
	}	
	

});
