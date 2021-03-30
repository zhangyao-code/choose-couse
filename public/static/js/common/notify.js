class Notify {
    showMessage(type, message, duration) {
        let $exist = $('.bootstrap-notify-bar');
        if ($exist.length > 0) {
            $exist.remove();
        }

        var html = '<div class="alert alert-' + type + ' bootstrap-notify-bar" style="display:none;">'
        html += '<button type="button" class="close" data-dismiss="alert">×</button>';
        html += message;
        html += '</div>';

        var $html = $(html);
        $html.appendTo('body');

        $html.slideDown(100, function(){
            duration = $.type(duration) == 'undefined' ? 3 :  duration;
            if (duration > 0) {
                setTimeout(function(){
                    $html.remove();
                }, duration * 1000);
            }
        });

    }


   primary(message, duration) {
            this.showMessage('primary', message, duration);
        };

    success(message, duration) {
        this.showMessage('success', message, duration);
        };
    warning(message, duration) {
        this.showMessage('warning', message, duration);
    };
   danger(message, duration) {
       this.showMessage('danger', message, duration);
   };

   info(message, duration) {
       this.showMessage('info', message, duration);
   };
};

export default Notify;