$('.js-delete').on('click',function (e){
    $.post($(e.currentTarget).data('url'), function ($result){
        if($result == true){
            $(e.currentTarget).parents('tr').remove();
        }
    });
});

$('.js-add-member').on('click',function (e){
   $('#add-member').submit();
});

$('#userIds').select2({
    placeholder: '输入用户名选择学生',
    ajax: {
        url: "/course/user_match/"+$('.js-add-member').data('id'),
        dataType: 'json',
        data: function (item, page) {
            return {
                name: item.term,
            };
        },
        processResults: function (data) {
            return {
                results: $.map(data, function(obj) {
                    return { id: obj.id, text: obj.name };
                })
            };
        },
    },
    multiple: true,
    minimumInputLength: 0
});