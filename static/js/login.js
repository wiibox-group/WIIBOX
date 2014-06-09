(function($) {

    //获取响应语言的数据并填充到模版中
    function makePage() {
        var lang = $('#i18n').val();
        $.ajax({
            type: "get",
            url: '/static/js/language/' + lang + '/login.json',
            dataType: 'json',
            success: function(data) {
                if (data) {
                    var tpl = $('.page-login').html(),
                        temp = Handlebars.compile(tpl);
                    $('.page-login').html(temp(data))
                        .css('display', 'block');
                    init(data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert(textStatus + '||' + errorThrown);
            }
        });
    }

    makePage();

    function init(langData) {
        //form的验证事件
        $('#signinForm').html5Validate(function() {
            //通过验证，提交表单
            $(this).submit();
        });
    }


})(window.jQuery);