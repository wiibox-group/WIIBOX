(function($) {

    if ($('.page-index').length) {
        /*
         * 5s后关闭actionTip
         */
        setTimeout(function() {
            $('#actionTip').hide();
        }, 5000);

        //选择运行频率
        $('#selectSpeed>li>a').on('click', function() {
            var value = $(this).data('value');
            $('#speed-cur').html(value);
            $('#run_speed').val(value);
        });
    }

})(window.jQuery);