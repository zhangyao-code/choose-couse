$().ready(function() {
    $("#user-update").validate({
        errorPlacement: function(error, element) {
            error.appendTo(element.parent());
        },
        rules:{
            nickname:{
                required:true,
                remote: $("#nickname").data('url'),
            },
            email:{
                required:true,
                remote: $("#email").data('url'),
            },
            mobile:{
                required:true,
                remote: $("#mobile").data('url'),
            },
        },
        messages:{
            nickname:{
                required: '请输入用户名',
                remote: '用户名已存在',
            },
            email:{
                required: '请输入邮箱',
                remote: '邮箱已存在',
            },
            mobile:{
                required: '请输入手机号',
                remote: '手机号已存在',
            },
        }
    });
});
