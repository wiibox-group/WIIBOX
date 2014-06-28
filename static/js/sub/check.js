(function($) {

    var cookieLang = $.cookie('wiiboxLanguage'),
        userLang = navigator.language || navigator.userLanguage, //当前浏览器的语言
        lang = '',
        fullName = {
            'zh': '简体中文',
            'en': 'English'
        };
    if (cookieLang) {
        lang = (cookieLang.indexOf('zh') !== -1) ? 'zh' : 'en';
    } else {
        lang = (userLang.indexOf('zh') !== -1) ? 'zh' : 'en';
        $.cookie('wiiboxLanguage', lang, {path: '/'});
    }

    //修改页面title
    if (lang === 'zh') {
        document.title = '控制板自检';
    } else {
        document.title = 'Controller Self-test';
    }

    /*
     * 获取相应语言的数据并填充到模版中
     */
    $.ajax({
        type: "get",
        url: '/static/js/language/' + lang + '/check.json',
        dataType: 'json',
        success: function(data) {
            if (data) {
                var tpl = $('.page-check').html(),
                    temp = Handlebars.compile(tpl);
                $('.page-check').html(temp(data))
                    .css('display', 'block');

                init(data);
            }
        }
    });


    function init(langData) {

        //当前所用语言
        $('#langNow').text(fullName[lang]);

        /*
         * 切换语言按钮
         */
        $('#languageMenu').on('click', '>li>a', function() {
            $.cookie('wiiboxLanguage', $(this).data('lang'), {path: '/'});
            window.location.reload();
        });

        /*
         * 控制器闪灯按钮
         */
        $('#btnFind').on('click', function() {
            $(this).attr('disabled', true);
            controlFlash(+$(this).data('state'));
        });


        /*
         * 控制器闪灯/停止闪灯
         * type: 1闪灯 0停止闪灯
         */

        function controlFlash(type) {
            var url = '/index.php?r=check/find',
                clas = 'btn-danger',
                text = langData.btn2, //“停止闪灯”
                state = 0;

            if (type === 0) {
                url = '/index.php?r=check/stopFind';
                clas = 'btn-primary';
                text = langData.btn1; //“控制器闪灯”
                state = 1;
            }

            $.ajax({
                type: 'get',
                url: url,
                dataType: 'json',
                beforeSend: function() {
                    NProgress.start();
                },
                success: function(data) {
                    $('#btnFind').text(text)
                        .removeClass('btn-danger')
                        .addClass(clas);
                },
                error: function() {
                    $('#btnFind').text(text + '(' + langData.fail + ')');
                },
                complete: function() {
                    NProgress.done();
                    $('#btnFind').attr('disabled', false)
                        .data('state', state);

                }
            });

        }


        /*
         * 自检按钮
         */
        $('#btnCheck').on('click', function() {
            $(this).attr('disabled', true)
                .addClass('btn-warning')
                .text(langData.btn3Ing);
            selfTest();
        });


        /*
         * 开始自检
         */

        function selfTest() {
            var testNum = 0,
                options = [{
                    key: 'lsusb',
                    url: '/index.php?r=check/lsusb'
                }, {
                    key: 'check',
                    url: '/index.php?r=index/check'
                }, {
                    key: 'timer',
                    url: '/index.php?r=check/timer'
                }, {
                    key: 'date',
                    url: '/index.php?r=check/date'
                }, {
                    key: 'version',
                    url: '/index.php?r=check/version'
                }, {
                    key: 'network',
                    url: '/index.php?r=check/network'
                }, {
                    key: 'ip',
                    url: '/index.php?r=check/ip'
                }];

            NProgress.start();
            $('.check-item').removeClass('check-error check-warning');
            $('.check-item>span').text(langData.state1);
            
            $.each(options, function(index, item) {
                $.ajax({
                    type: 'get',
                    url: item.url,
                    dataType: 'json',
                    success: function(data) {
                        console.log(item.key);
                        console.log(data);
                        result(item.key, data);
                    },
                    error: function() {
                        result(item.key);
                    },
                    complete: function() {
                        testNum++;
                        if (testNum === 6) {
                            NProgress.done();
                            $('#btnCheck').attr('disabled', false)
                                .removeClass('btn-warning')
                                .text(langData.btn3Retry);
                        }
                    }
                });
            });


            /*
             * 结果处理
             */

            function result(key, data) {

                switch (key) {
                    case 'lsusb': //矿机检测 & 矿机数量
                        if (data) {
                            if (data.COMMAND === 1) {
                                $('#resultLsusb1>span').text(langData.normal);
                            } else {
                                abnormal('resultLsusb1');
                            }

                            if (data.MILL > 0) {
                                $('#resultLsusb2>span').text(data.MILL);
                            } else {
                                $('#resultLsusb2').addClass('check-error')
                                    .find('>span')
                                    .text(0);
                            }
                        } else {
                            abnormal('resultLsusb1');
                            abnormal('resultLsusb2');
                        }
                        break;

                    case 'check': //wiibox程序
                        if (data) {
                            $('#resultProgram>span').text(langData.normal);
                        } else {
                            abnormal('resultProgram');
                        }
                        break;

                    case 'timer': //定时器
                        if (data) {
                            if (data.COMMAND === 1 && data.FILE === 1) {
                                $('#resultTimer>span').text(langData.normal);
                            } else {
                                abnormal('resultTimer');
                            }
                        } else {
                            abnormal('resultTimer');
                        }
                        break;

                    case 'date': //系统市区
                        if (data) {
                            if (data.ZONE === 1) {
                                $('#resultDate>span').text(langData.normal);
                            } else {
                                abnormal('resultDate');
                            }
                        } else {
                            abnormal('resultDate');
                        }
                        break;

                    case 'network': //到全网网络环境 & 同步网络环境 & 网络延时
                        if (data) {
                            if (data.NET === 1) {
                                $('#resultNetwork1>span').text(langData.normal);
                            } else {
                                abnormal('resultNetwork1');
                            }

                            if (data.WIIBOX === 1) {
                                $('#resultNetwork>span').text(langData.normal);
                            } else {
                                abnormal('resultNetwork2');
                            }

                            if (data.WIIBOX_DELAY !== '') {
                                $('#resultNetwork3>span').text(data.WIIBOX_DELAY);
                            } else {
                                abnormal('resultNetwork3');
                            }
                        } else {
                            abnormal('resultNetwork1');
                            abnormal('resultNetwork2');
                            abnormal('resultNetwork3');
                        }
                        break;

                    case 'ip': //IP地址检测 & MAC地址检测
                        if (data) {
                            if (data.IP != '') {
                                $('#resultIp>span').text(data.IP);
                            } else {
                                abnormal('resultIp');
                            }

                            if (data.MAC != '') {
                                $('#resultMac>span').text(data.MAC);
                            } else {
                                abnormal('resultMac');
                            }
                        } else {
                            abnormal('resultIp');
                            abnormal('resultMac');
                        }
                        break;

                    case 'version': //WIIBOX版本
                        if (data) {
                            $('#resultVersion>span').text(data.VERSION);
                        } else {
                            abnormal('resultVersion');
                        }
                        break;
                }


                /*
                 * 异常消息处理
                 */

                function abnormal(id) {
                    $('#' + id).addClass('check-error')
                        .find('>span')
                        .text(langData.abnormal);
                }


            }

        }



    }
})(window.jQuery);