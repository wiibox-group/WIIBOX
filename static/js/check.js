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
        $.cookie('wiiboxLanguage', userLang);
        lang = (userLang.indexOf('zh') !== -1) ? 'zh' : 'en';
    }

    makePage(lang);

    //获取响应语言的数据并填充到模版中

    function makePage(langFile) {

        //修改页面title
        if(langFile === 'zh'){
            document.title = '控制板自检';
        }else{
            document.title = 'Controller Self-test';
        }

        $.ajax({
            type: "get",
            url: '/static/js/language/' + langFile + '/check.json',
            dataType: 'json',
            success: function(data) {
                if(data){
                    var tpl = $('.page-check').html(),
                        temp = Handlebars.compile(tpl);
                    $('.page-check').html(temp(data))
                                    .css('display', 'block');
                    init(data);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert(textStatus + '||' + errorThrown);
            }
        });
    }

    function init(langData) {

        //设置当前所用的语言
        $('#langNow').text(fullName[lang]);

        //切换语言
        $('#languageMenu').on('click', '>li>a', function() {
            $.cookie('wiiboxLanguage', $(this).data('lang'));
            window.location.reload();
        });


        var actions = {
            setting: {
                url_find: '/index.php?r=check/find',
                url_find_stop: '/index.php?r=check/stopFind',
                url_lsusb: '/index.php?r=check/lsusb',
                url_check: '/index.php?r=index/check',
                url_timer: '/index.php?r=check/timer',
                url_date: '/index.php?r=check/date',
                url_version: '/index.php?r=check/version',
                url_network: '/index.php?r=check/network',
                url_ip: '/index.php?r=check/ip'
            },
            // send post to server
            sendPost: function(callback, tourl, senddata) {
                // set default data
                if (typeof(senddata) == 'undefined') senddata = {};

                $.ajax({
                    type: "GET",
                    url: tourl + "&rand=" + Math.random(),
                    data: senddata,
                    dataType: 'json',
                    success: function(data) {
                        eval("actionSuccess." + callback + "(data)");
                    },
                    fail: function() {
                        eval("actionFail." + callback + "(data)");
                    }
                });
            }
        };

        var actionSuccess = {
            findResult: function(data) {
                // run find success
                $('#button-find').html($('#button-find').data('name') + ' (' + langData.success + ')');
                $('#button-find-stop').html($('#button-find-stop').data('name'));
            },
            findStopResult: function(data) {
                // run find stop success
                $('#button-find').html($('#button-find').data('name'));
                $('#button-find-stop').html($('#button-find-stop').data('name') + ' (' + langData.success + ')');
            },
            lsusbResult: function(data) {
                $('#check-result-lsusb-1,#check-result-lsusb-2').removeClass('waiting-check');
                checkOptionFinish('lsusb');
                if (data == null) {
                    actionFail.lsusbResult(data);
                    return;
                }

                if (data.COMMAND === 1) {
                    $('#check-result-lsusb-1 span').html(langData.normal);
                } else {
                    $('#check-result-lsusb-1').addClass('check-error');
                    $('#check-result-lsusb-1 span').html(langData.abnormal);
                }

                if (data.MILL > 0) {
                    $('#check-result-lsusb-2 span').html(data.MILL);
                } else {
                    $('#check-result-lsusb-2').addClass('check-error');
                    $('#check-result-lsusb-2 span').html('0');
                }
            },
            checkResult: function(data) {
                $('#check-result-program').removeClass('waiting-check');
                checkOptionFinish('check');
                if (data == null) {
                    actionFail.checkResult(data);
                    return;
                }

                $('#check-result-program span').html(langData.normal);

            },
            timerResult: function(data) {
                $('#check-result-timer').removeClass('waiting-check');
                checkOptionFinish('timer');
                if (data == null) {
                    actionFail.timerResult(data);
                    return;
                }

                if (data.COMMAND === 1 && data.FILE === 1) {
                    $('#check-result-timer span').html(langData.normal);
                } else {
                    $('#check-result-timer').addClass('check-error');
                    $('#check-result-timer span').html(langData.abnormal);
                }
            },
            dateResult: function(data) {
                $('#check-result-date-1,#check-result-date-2').removeClass('waiting-check');
                checkOptionFinish('date');
                if (data == null) {
                    actionFail.dateResult(data);
                    return;
                }

                if (data.ZONE === 1) {
                    $('#check-result-date-1 span').html(langData.normal);
                } else {
                    $('#check-result-date-1').addClass('check-error');
                    $('#check-result-date-1 span').html(langData.abnormal);
                }

                var d = new Date();
                cur = d.getTime() / 1000;
                offset = d.getTimezoneOffset() * 60;
                cur = offset + cur + 8 * 3600;
                if (data.TIME > 0 && cur - data.TIME < 30 && cur - data.TIME > -30) {
                    $('#check-result-date-2 span').html(langData.normal);
                } else {
                    $('#check-result-date-2').addClass('check-error');
                    $('#check-result-date-2 span').html(langData.abnormal);
                }
            },
            versionResult: function(data) {
                $('#check-result-version').removeClass('waiting-check');
                checkOptionFinish('version');
                if (data == null) {
                    actionFail.versionResult(data);
                    return;
                }

                $('#check-result-version span').html(data.VERSION);
            },
            networkResult: function(data) {
                $('#check-result-network-1,#check-result-network-2,#check-result-network-3').removeClass('waiting-check');
                checkOptionFinish('network');
                if (data == null) {
                    actionFail.networkResult(data);
                    return;
                }

                if (data.NET === 1) {
                    $('#check-result-network-1 span').html(langData.normal);
                } else {
                    $('#check-result-network-1').addClass('check-error');
                    $('#check-result-network-1 span').html(langData.abnormal);
                }

                if (data.WIIBOX === 1) {
                    $('#check-result-network-2 span').html(langData.normal);
                } else {
                    $('#check-result-network-2').addClass('check-error');
                    $('#check-result-network-2 span').html(langData.abnormal);
                }

                if (data.WIIBOX_DELAY != '') {
                    $('#check-result-network-3 span').html(data.WIIBOX_DELAY);
                } else {
                    $('#check-result-network-3').addClass('check-error');
                    $('#check-result-network-3 span').html(langData.abnormal);
                }
            },
            ipResult: function(data) {
                $('#check-result-ip-1,#check-result-ip-2').removeClass('waiting-check');
                checkOptionFinish('ip');
                if (data == null) {
                    actionFail.ipResult(data);
                    return;
                }

                if (data.IP != '') {
                    $('#check-result-ip-1 span').html(data.IP);
                } else {
                    $('#check-result-ip-1').addClass('check-error');
                    $('#check-result-ip-1 span').html(langData.abnormal);
                }

                if (data.MAC != '') {
                    $('#check-result-ip-2 span').html(data.MAC);
                } else {
                    $('#check-result-ip-2').addClass('check-error');
                    $('#check-result-ip-2 span').html(langData.abnormal);
                }
            }
        };

        var actionFail = {
            findResult: function(data) {
                // run find fail
                $('#button-find').html($('#button-find').data('name') + '(' + langData.fail + ')');
                $('#button-find-stop').html($('#button-find-stop').data('name'));
            },
            findStopResult: function(data) {
                // run find stop fail
                $('#button-find').html($('#button-find').data('name'));
                $('#button-find-stop').html($('#button-find-stop').data('name') + '(' + langData.fail + ')');
            },
            lsusbResult: function(data) {
                checkOptionFinish('lsusb');
                $('#check-result-lsusb-1,#check-result-lsusb-2').addClass('check-error');
                $('#check-result-lsusb-1 span,#check-result-lsusb-2 span').html(langData.abnormal);
            },
            checkResult: function(data) {
                checkOptionFinish('check');
                $('#check-result-program').addClass('check-error');
                $('#check-result-program span').html(langData.abnormal);
            },
            timerResult: function(data) {
                checkOptionFinish('time');
                $('#check-result-timer').addClass('check-error');
                $('#check-result-timer span').html(langData.abnormal);
            },
            dateResult: function(data) {
                checkOptionFinish('date');
                $('#check-result-date-1,#check-result-date-2').addClass('check-error');
                $('#check-result-date-1 span,#check-result-date-2 span').html(langData.abnormal);
            },
            versionResult: function(data) {
                checkOptionFinish('version');
                $('#check-result-version').addClass('check-error');
                $('#check-result-version span').html(langData.abnormal);
            },
            networkResult: function(data) {
                checkOptionFinish('network');
                $('#check-result-network-1,#check-result-network-2,#check-result-network-3').addClass('check-error');
                $('#check-result-network-1 span,#check-result-network-2 span,#check-result-network-3 span').html(langData.abnormal);
            },
            ipResult: function(data) {
                checkOptionFinish('ip');
                $('#check-result-ip-1,#check-result-ip-2').addClass('check-error');
                $('#check-result-ip-1 span,#check-result-ip-2 span').html(langData.abnormal);
            }
        };

        function checkOptionFinish(tar) {
            eval("check_options." + tar + " = 1;");
            for (var option in check_options) {
                eval("var tmp_val = check_options." + option + ";");
                if (tmp_val === 0)
                    return;
            }

            ischecking = false;
            $('#button-check').html(langData.btn3Retry);
        }

        $('#button-find').on('click', function() {
            actions.sendPost('findResult', actions.setting.url_find, {});
        });

        $('#button-find-stop').on('click', function() {
            actions.sendPost('findStopResult', actions.setting.url_find_stop, {});
        });

        var ischecking = false;
        var check_options = {
            lsusb: 0,
            check: 0,
            timer: 0,
            date: 0,
            network: 0,
            ip: 0,
            version: 0
        };

        $('#button-check').on('click', function() {
            if (ischecking === true){
                return;
            }

            $('.check-item').removeClass('check-error').removeClass('waiting-check').addClass('waiting-check');

            ischecking = true;
            for (var option in check_options){
                eval("check_options." + option + " = 0;");
            }

            $(this).html(langData.btn3Ing);
            $('.check-item span').html(langData.state1);

            actions.sendPost('lsusbResult', actions.setting.url_lsusb, {});
            actions.sendPost('checkResult', actions.setting.url_check, {});
            actions.sendPost('timerResult', actions.setting.url_timer, {});
            actions.sendPost('dateResult', actions.setting.url_date, {});
            actions.sendPost('versionResult', actions.setting.url_version, {});
            actions.sendPost('networkResult', actions.setting.url_network, {});
            actions.sendPost('ipResult', actions.setting.url_ip, {});
        });

    }
})(window.jQuery);