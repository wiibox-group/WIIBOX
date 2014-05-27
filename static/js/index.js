(function($) {

    setTimeout(function() {
        $('#action-tip').hide();
    }, 5000);

    $('#speed-select-options li a').on('click', function() {
    	var value = $(this).data('value');
        $('#speed-cur').html(value);
        $('#run_speed').val(value);
    });

    $('.runmodel-bt').on('click', function() {
        $('.runmodel-bt').removeClass('active');
        $(this).addClass('active');
        $('#runmodel-input').val($(this).attr('tar'));
    });

})(window.jQuery);