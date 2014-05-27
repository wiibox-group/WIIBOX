var actions = {
    setting: {
        url_restart: $('#urlConfig').data('restart'),
        url_restarttarget: $('#urlConfig').data('restarttarget'),
        url_shutdown: $('#urlConfig').data('shutdown'),
        url_supermodel: $('#urlConfig').data('supermodel'),
        url_check: $('#urlConfig').data('check'),
        runstate: false
    },
    // restart all service
    restart: function() {
        this.sendPost('restart_success', this.setting.url_restart);
    },
    // restart target usb program
    restartTar: function(usb) {
        this.sendPost('restartTar_success', this.setting.url_restarttarget, {
            'usb': usb
        });
    },
    // shutdown all service
    shutdown: function() {
        this.sendPost('shutdown_success', this.setting.url_shutdown);
    },
    // check current run state
    check: function() {
        this.sendPost('check_success', this.setting.url_check);
    },
    // send post to server
    sendPost: function(callback, tourl, senddata, isouter) {
        // set default data
        if (typeof(senddata) == 'undefined') {
            senddata = {};
        }

        if (isouter != 1) {
            eval("actionSuccess." + callback + "( -1 )");
        }

        $.ajax({
            type: "GET",
            url: tourl + "&rand=" + Math.random(),
            dataType: 'json',
            data: senddata,
            success: function(r) {
                if (isouter === 1) {
                    eval(callback);
                } else {
                    eval("actionSuccess." + callback + "( r )");
                }
            },
            fail: function() {
                actions.setting.runstate = false;
            }
        });
    }
};

var actionSuccess = {
    templates: {
        // usb-port : /dev/ttyUSB0 , usb-text : 新USB挖矿设备，请选择挖矿模式。
        newusb: '<div id="newusb-{usb-port}" class="panel panel-primary"><div class="panel-heading"><h3 class="panel-title">' + basei18n.mess_equipment + '{usb-port}</h3></div><div class="panel-body">{usb-text}<br><br><button type="button" class="btn btn-sm btn-default btn-run-ltc" tar="{usb-port}">' + basei18n.mess_scrypt_running + '</button>&nbsp;<button type="button" class="btn btn-sm btn-default btn-run-btc" tar="{usb-port}">' + basei18n.mess_sha_running + '</button></div></div>',
        // usb-port : /dev/ttyUSB0 , usb-run-tip-type : default|waining , usb-text : 正在运行SHA [正常]|目标运行SHA [已停止] , usb-restart-text : 重启|立即启动
        btcstate: '<div id="btcstate-{usb-port}" class="panel panel-{usb-run-tip-type}"><div class="panel-heading"><h3 class="panel-title">' + basei18n.mess_equipment + ' {usb-port}</h3></div><div class="panel-body">{usb-text}<br></div></div>',
        // usb-port : /dev/ttyUSB0 , usb-run-tip-type : default|waining , usb-text : 正在运行SCRYPT [正常]|目标运行SCRYPT [已停止] , usb-restart-text : 重启|立即启动
        ltcstate: '<div id="ltcstate-{usb-port}" class="panel panel-{usb-run-tip-type}"><div class="panel-heading"><h3 class="panel-title">' + basei18n.mess_equipment + ' {usb-port}</h3></div><div class="panel-body">{usb-text}<br></div></div>',
        // data-tip : 还未发现新挖矿设备!|暂无设备运行!
        nulldata: '<div class="alert alert-success important-tip">{data-tip}</div>'
    },
    restart_success: function(data) {
        if (data === -1) {
            // 重启中
            NProgress.start();
            $('#statePill').removeClass('btn-success btn-danger')
                .addClass('btn-warning')
                .text(basei18n.mess_restart_ing);
            return;
        } else if (data === 200) {
            // 成功重启
            NProgress.done();
            $('#statePill').removeClass('btn-warning btn-danger')
                .addClass('btn-success')
                .text(basei18n.mess_running);
        } else {
            // 失败
            NProgress.done();
            alert(basei18n.mess_restart_faild);
        }

        actions.setting.runstate = false;
        resetTopBt.check();
        actions.check();
    },
    restartTar_success: function(data) {
        return true;
    },
    shutdown_success: function(data) {
        if (data === -1) {
            // 停止中
            NProgress.start();
            $('#statePill').removeClass('btn-success btn-danger')
                .addClass('btn-warning')
                .text(basei18n.mess_stop_ing);
            return;
        } else if (data === 200) {
            // 成功停止
            NProgress.done();
            $('#statePill').removeClass('btn-warning btn-success')
                .addClass('btn-default')
                .text(basei18n.mess_stopped);
        } else {
            // 失败
            NProgress.done();
            alert(basei18n.mess_stop_faild);
        }
        actions.setting.runstate = false;
        resetTopBt.check();
        actions.check();
    },
    check_success: function(data) {
        if (data) {
            var null_data = [null, undefined, '', [], {}];
            if (!in_array(data.alived, null_data) || !in_array(data.died, null_data)) {
                var tpl = '{{#each alived.BTC}}<tr><td>B:{{this}}</td><td>' + basei18n.mess_status_normal + '</td><td>SHA</td></tr>{{/each}}';
                tpl += '{{#each alived.LTC}}<tr><td>L:{{this}}</td><td>' + basei18n.mess_status_normal + '</td><td>SCRYPT</td></tr>{{/each}}';
                tpl += '{{#each died.BTC}}<tr><td>B:{{this}}</td><td>' + basei18n.mess_status_stopped + '</td><td>SHA</td></tr>{{/each}}';
                tpl += '{{#each died.LTC}}<tr><td>L:{{this}}</td><td>' + basei18n.mess_status_stopped + '</td><td>SCRYPT</td></tr>{{/each}}';
                tpl += 'test';
                var temp = Handlebars.compile(tpl);
                $('#status-table>tbody').html(temp(data));
            }
        }
    }
};

// check navigation state
var resetTopBt = {
    check: function() {
        actions.sendPost("resetTopBt.checkResult(r)", actions.setting.url_check, {}, 1);
    },
    checkResult: function(r) {

        actionSuccess.check_success(r);

        if ((r.alived.BTC.length > 0 || r.alived.LTC.length) && r.super === true) {
            //有矿机，正在运行
            $('#statePill').removeClass('btn-warning btn-default')
                .addClass('btn-success')
                .text(basei18n.mess_running);
        } else {
            //已停止
            $('#statePill').removeClass('btn-warning btn-success')
                .addClass('btn-default')
                .text(basei18n.mess_stopped);
        }
    }
};

// navigation button active method
var headerBt = {
    init: function() {
        this.restartBt();
        this.stopBt();
    },
    restartBt: function() {
        $('#actionRestart').on('click', function() {
            headerBt.publicClick(this);
            actions.restart();
        });
    },
    stopBt: function() {
        $('#actionStop').on('click', function() {
            headerBt.publicClick(this);
            actions.shutdown();
        });
    },
    publicClick: function(ele) {
        // if action is running
        if (actions.setting.runstate === true) {
            return;
        }
        actions.setting.runstate = true;

    }
};

// timeout reset top navigation

function timerResetTopBt() {
    if (actions.setting.runstate === false) {
        resetTopBt.check();
    }
    setTimeout(function() {
        timerResetTopBt();
    }, 10000);
}

// replace all matched string

function replaceAll(find, replace, str) {
    return str.replace(new RegExp(find, 'g'), replace);
}

// object is or not in array

function in_array(needle, haystack, argStrict) {
    var key = '',
        strict = !! argStrict;

    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle)
                return true;
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle)
                return true;
        }
    }

    return false;
}

$(document).ready(function() {
    headerBt.init();
    timerResetTopBt();
});