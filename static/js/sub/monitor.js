(function($) {
    if ($('.page-monitor').length) {

        /*
         * 获取Hashrate的数据
         */
        $.ajax({
            type: "get",
            url: $('.hashrate').data('url'),
            dataType: 'json',
            timeout: 5000,
            success: function(data) {
                hashrate(data);
            },
            error: function() {
                //“加载失败，请重试”
                $('.hashrate').text(basei18n.loadFaild);
            }
        });


        /*
         * 关闭UTC
         */
        Highcharts.setOptions({
            global: {
                useUTC: false
            }
        });

        /*
         单位换算
         opt: {max, num, point}
        */

        function unitConversion(opt) {
            var pb = 1024 * 1024 * 1024 * 1024,
                tb = 1024 * 1024 * 1024,
                gb = 1024 * 1024,
                mb = 1024,
                value = '';

            if (opt.max > pb) {
                value = (opt.num / pb).toFixed(opt.point) + " PB";
            } else if (opt.max > tb) {
                value = (opt.num / tb).toFixed(opt.point) + " TB";
            } else if (opt.max > gb) {
                value = (opt.num / gb).toFixed(opt.point) + " GB";
            } else if (opt.max > mb) {
                value = (opt.num / mb).toFixed(opt.point) + " MB";
            } else {
                value = opt.num + " KB";
            }

            return value;
        }


        /*
         * Hashrate统计图
         */

        function hashrate(data) {
            $('.hashrate').highcharts({
                chart: {
                    type: 'spline'
                },
                title: {
                    text: ''
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    type: 'datetime'
                },
                yAxis: {
                    title: {
                        text: ''
                    },
                    min: 0,
                    labels: {
                        formatter: function() {
                            return unitConversion({
                                max: this.axis.max,
                                num: this.value,
                                point: 1
                            });
                        }
                    }
                },
                tooltip: {
                    formatter: function() {
                        var value = unitConversion({
                            max: this.y,
                            num: this.y,
                            point: 2
                        });
                        return Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) + '<br/><b>' + this.series.name + '</b><br><b>' + value + '</b>';
                    }
                },
                plotOptions: {
                    spline: {
                        lineWidth: 2,
                        states: {
                            hover: {
                                lineWidth: 3
                            }
                        },
                        marker: {
                            enabled: false
                        }
                    }
                },
                series: [{
                    name: 'SHA (khash/s)',
                    color: '#FF7F0E',
                    data: data.DATA.B
                }, {
                    name: 'SCRYPT (khash/s)',
                    data: data.DATA.L
                }],
                navigation: {
                    menuItemStyle: {
                        fontSize: '10px'
                    }
                }
            });
        }



        /*
         * 查询设备运行状态
         *
         */

        function deviceInfo() {
            $.ajax({
                type: 'get',
                url: $('#urlConfig').data('check'),
                dataType: 'json',
                timeout: 10000,
                success: function(data) {
                    if (data.alived.BTC.length || data.alived.LTC.length || data.died.BTC.length || data.died.LTC.length) {
                        var tpl = '{{#each alived.BTC}}<tr><td>B:{{this}}</td><td>' + basei18n.running + '</td><td>SHA</td></tr>{{/each}}';
                        tpl += '{{#each alived.LTC}}<tr><td>L:{{this}}</td><td>' + basei18n.running + '</td><td>SCRYPT</td></tr>{{/each}}';
                        tpl += '{{#each died.BTC}}<tr><td>B:{{this}}</td><td>' + basei18n.stopped + '</td><td>SHA</td></tr>{{/each}}';
                        tpl += '{{#each died.LTC}}<tr><td>L:{{this}}</td><td>' + basei18n.stopped + '</td><td>SCRYPT</td></tr>{{/each}}';
                        var temp = Handlebars.compile(tpl);
                        $('#statusTable>tbody').html(temp(data));
                    } else {
                        $('#statusTable>tbody').html('<tr><td colspan="3">-</td></tr>');
                    }
                },
                error: function() {
                    $('#statusTable>tbody').html('<tr><td colspan="3">' + basei18n.loadFaild + '</td></tr>');
                },
                complete: function() {
                    doAgain();
                }
            });

            /*
             * 定时执行查询
             * 10s
             */

            function doAgain() {
                setTimeout(function() {
                    deviceInfo();
                }, 10000);
            }
        }

        $(document).ready(function() {
            deviceInfo();
        });

    }
})(window.jQuery);