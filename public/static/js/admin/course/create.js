$('.js-teacher').select2({
    placeholder: '输入用户名选择讲师',
    ajax: {
        url: "/course/teacher_match",
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
    multiple: false,
    minimumInputLength: 0
});
if($('.js-teacher').data('teacher')){
    let data = $('.js-teacher').data('teacher');
    $('.js-teacher').append(new Option(data.name, data.id, false, true));
}
$().ready(function() {
    $("#course-create").validate({
        errorPlacement: function(error, element) {
            error.appendTo(element.parent());
        },
        rules:{
            name:{
                required:true,
            },
            teacherId:{
                required:true
            }

        },
        messages:{
            name:{
                required: '请输入课程名称',
            },
            teacherId:{
                required: '请选择讲师',
            },
        }
    });
});
