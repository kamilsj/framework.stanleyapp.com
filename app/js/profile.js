$('#addNewProfile').click(function()
{

        $('#mainInfo').fadeOut('fast');
        $('#uploadProfilePicture').fadeIn('fast');

});

function changeAvatar(num)
{
    $.ajax({
       url: './ajax/changeAvatar.php',
       type: 'post',
       data: {id: num},
       success: function(req)
       {
           $('#addNewProfile').attr('src',obj.url);
           $('#banerPhoto').attr('src', obj.url);
       }
    });
}

function changePassword()
{

    var old  = $('#oldPwd').val();
    var pwd1 = $('#newPwd1').val();
    var pwd2 = $('#newPwd2').val();

    if(old.length > 4 && pwd1.length >4 && pwd2.length > 0)
    {

        $.ajax({

            url: './ajax/changePwd.php',
            type: 'post',
            data: {old: old, pwd1: pwd1, pwd2: pwd2},
            success: function(res)
            {
                //alert(res);

                var obj = jQuery.parseJSON(res);

                if(typeof obj == 'object')
                {
                    if(obj.stat == 'OK')
                    {
                        $('#msg').html('OK');
                        $('#msg').fadeIn('slow');
                        setTimeout(
                            function()
                            {
                                $('#msg').fadeOut('slow');
                            },
                            3000
                        );

                        $('#oldPwd').removeClass("err").val('');
                        $('#newPwd1').removeClass("err").val('');
                        $('#newPwd2').removeClass("err").val('');

                    }
                    else
                    {
                        if(obj.nold == 1) $('#oldPwd').addClass("err"); else $('#oldPwd').removeClass("err");
                        if(obj.oldsame == 1)
                        {
                            $('#oldPwd').addClass("err");
                            $('newPwd1').addClass("err");
                        }
                        else
                        {
                            $('#oldPwd').removeClass("err");
                            $('#newPwd1').removeClass("err");
                        }
                        if(obj.nsame == 1)
                        {
                            $('#newPwd1').addClass("err");
                            $('#newPwd2').addClass("err");
                        }
                        else
                        {
                            $('#newPwd1').removeClass("err");
                            $('#newPwd2').removeClass("err");
                        }
                    }
                }
            }

        });

    }else
    {
        $('#oldPwd').addClass("err");
        $('#newPwd1').addClass("err");
        $('#newPwd2').addClass("err");
    }

}