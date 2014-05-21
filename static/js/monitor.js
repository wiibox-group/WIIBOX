(function($) {
    //var need_show_check_result = true;
    function refreshState() {
        if ( actions.setting.runstate === false ) actions.usbstate();
        //if ( actions.setting.runstate === false ) actions.check();
        setTimeout(function(){
            refreshState();
        },10000);
    }
    $(document).ready(function() {
        refreshState();
    });
})(window.jQuery);