
$("#login-form").validate({
    errorPlacement: function(error, element) {
        error.appendTo(element.parent());
    },
    rules:{
        username:{
            required:true,
        },
        password:{
            required:true,
        },
    },
    messages:{
        username:{
            required: '请输入用户名',
            remote: '用户名或密码错误'
        },
        password:{
            required: '请输入密码',
        },
    }
});
$("input").focus(function(){
    $('.js-error').html('');
});

$('#login-btn').on('click', function (){
    let url =  $("#username").data('url')+'?password='+ $("#password").val()+'&nickname='+$("#username").val();
    $.post(url, function($result){
        if($result){
            $("#login-form").submit();
        }else{
            $('.js-error').html('用户名或密码错误！');
        }

    });
});

