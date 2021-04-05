$('.js-delete').on('click',function (e){
    $.post($(e.currentTarget).data('url'), function ($result){
      if($result == true){
          $(e.currentTarget).parents('tr').remove();
      }
    });
});



