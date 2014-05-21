(function($) {

	//当前浏览器的语言
	var userLang = navigator.language || navigator.userLanguage; 

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
                success: function(r) {
                    r = eval('(' + r + ')');
                    eval("actionSuccess." + callback + "( r )");
                },
                fail: function() {
                    eval("actionFail." + callback + "( r )");
                }
            });
        }
    };

    var actionSuccess = {
        findResult: function(data) {
            // run find success
            $('#button-find').html($('#button-find').attr('tt') + ' (成功)');
            $('#button-find-stop').html($('#button-find-stop').attr('tt'));
        },
        findStopResult: function(data) {
            // run find stop success
            $('#button-find').html($('#button-find').attr('tt'));
            $('#button-find-stop').html($('#button-find-stop').attr('tt') + ' (成功)');
        },
        lsusbResult: function(data) {
            $('#check-result-lsusb-1,#check-result-lsusb-2').removeClass('waiting-check');
            checkOptionFinish('lsusb');
            if (data == null) {
                actionFail.lsusbResult(data);
                return;
            }

            if (data.COMMAND === 1) {
                $('#check-result-lsusb-1 span').html('正常');
            } else {
                $('#check-result-lsusb-1').addClass('check-error');
                $('#check-result-lsusb-1 span').html('异常');
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

            $('#check-result-program span').html('正常');

        },
        timerResult: function(data) {
            $('#check-result-timer').removeClass('waiting-check');
            checkOptionFinish('timer');
            if (data == null) {
                actionFail.timerResult(data);
                return;
            }

            if (data.COMMAND === 1 && data.FILE === 1) {
                $('#check-result-timer span').html('正常');
            } else {
                $('#check-result-timer').addClass('check-error');
                $('#check-result-timer span').html('异常');
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
                $('#check-result-date-1 span').html('正常');
            } else {
                $('#check-result-date-1').addClass('check-error');
                $('#check-result-date-1 span').html('异常');
            }

            var d = new Date();
            cur = d.getTime() / 1000;
            offset = d.getTimezoneOffset() * 60;
            cur = offset + cur + 8 * 3600;
            if (data.TIME > 0 && cur - data.TIME < 30 && cur - data.TIME > -30) {
                $('#check-result-date-2 span').html('正常');
            } else {
                $('#check-result-date-2').addClass('check-error');
                $('#check-result-date-2 span').html('异常');
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
                $('#check-result-network-1 span').html('正常');
            } else {
                $('#check-result-network-1').addClass('check-error');
                $('#check-result-network-1 span').html('异常');
            }

            if (data.WIIBOX === 1) {
                $('#check-result-network-2 span').html('正常');
            } else {
                $('#check-result-network-2').addClass('check-error');
                $('#check-result-network-2 span').html('异常');
            }

            if (data.WIIBOX_DELAY != '') {
                $('#check-result-network-3 span').html(data.WIIBOX_DELAY);
            } else {
                $('#check-result-network-3').addClass('check-error');
                $('#check-result-network-3 span').html('异常');
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
                $('#check-result-ip-1 span').html('异常');
            }

            if (data.MAC != '') {
                $('#check-result-ip-2 span').html(data.MAC);
            } else {
                $('#check-result-ip-2').addClass('check-error');
                $('#check-result-ip-2 span').html('异常');
            }
        }
    };

    var actionFail = {
        findResult: function(data) {
            // run find fail
            $('#button-find').html($('#button-find').attr('tt') + ' (失败)');
            $('#button-find-stop').html($('#button-find-stop').attr('tt'));
        },
        findStopResult: function(data) {
            // run find stop fail
            $('#button-find').html($('#button-find').attr('tt'));
            $('#button-find-stop').html($('#button-find-stop').attr('tt') + ' (失败)');
        },
        lsusbResult: function(data) {
            checkOptionFinish('lsusb');
            $('#check-result-lsusb-1,#check-result-lsusb-2').addClass('check-error');
            $('#check-result-lsusb-1 span,#check-result-lsusb-2 span').html('异常');
        },
        checkResult: function(data) {
            checkOptionFinish('check');
            $('#check-result-program').addClass('check-error');
            $('#check-result-program span').html('异常');
        },
        timerResult: function(data) {
            checkOptionFinish('time');
            $('#check-result-timer').addClass('check-error');
            $('#check-result-timer span').html('异常');
        },
        dateResult: function(data) {
            checkOptionFinish('date');
            $('#check-result-date-1,#check-result-date-2').addClass('check-error');
            $('#check-result-date-1 span,#check-result-date-2 span').html('异常');
        },
        versionResult: function(data) {
            checkOptionFinish('version');
            $('#check-result-version').addClass('check-error');
            $('#check-result-version span').html('异常');
        },
        networkResult: function(data) {
            checkOptionFinish('network');
            $('#check-result-network-1,#check-result-network-2,#check-result-network-3').addClass('check-error');
            $('#check-result-network-1 span,#check-result-network-2 span,#check-result-network-3 span').html('异常');
        },
        ipResult: function(data) {
            checkOptionFinish('ip');
            $('#check-result-ip-1,#check-result-ip-2').addClass('check-error');
            $('#check-result-ip-1 span,#check-result-ip-2 span').html('异常');
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
        $('#button-check').html('重新检测');
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
        if (ischecking === true)
            return;

        $('.check-item').removeClass('check-error').removeClass('waiting-check').addClass('waiting-check');

        ischecking = true;
        for (var option in check_options)
            eval("check_options." + option + " = 0;");

        $(this).html('正在努力检测...');
        $('.check-item span').html('检测中...');

        actions.sendPost('lsusbResult', actions.setting.url_lsusb, {});
        actions.sendPost('checkResult', actions.setting.url_check, {});
        actions.sendPost('timerResult', actions.setting.url_timer, {});
        actions.sendPost('dateResult', actions.setting.url_date, {});
        actions.sendPost('versionResult', actions.setting.url_version, {});
        actions.sendPost('networkResult', actions.setting.url_network, {});
        actions.sendPost('ipResult', actions.setting.url_ip, {});
    });

})(window.jQuery);