(function($) {

    //获取响应语言的数据并填充到模版中
    var lang = $('#i18n').val();
    $.ajax({
        type: "get",
        url: '/static/js/language/' + lang + '/login.json',
        dataType: 'json',
        success: function(data) {
            //成功获取数据
            if (data) {
                //获取模版
                var tpl = $('.form-holder').html(),
                    temp = Handlebars.compile(tpl);
                //填充数据到模版
                $('.form-holder').html(temp(data))
                    .css('display', 'block');
                //调用init
                init(data);
            }
        }
    });

    // Set name of hidden property and visibility change event
    // since some browsers only offer vendor-prefixed support
    var hidden, state, visibilityChange;
    if (typeof document.hidden !== "undefined") {
        hidden = "hidden";
        visibilityChange = "visibilitychange";
        state = "visibilityState";
    } else if (typeof document.mozHidden !== "undefined") {
        hidden = "mozHidden";
        visibilityChange = "mozvisibilitychange";
        state = "mozVisibilityState";
    } else if (typeof document.msHidden !== "undefined") {
        hidden = "msHidden";
        visibilityChange = "msvisibilitychange";
        state = "msVisibilityState";
    } else if (typeof document.webkitHidden !== "undefined") {
        hidden = "webkitHidden";
        visibilityChange = "webkitvisibilitychange";
        state = "webkitVisibilityState";
    }

    var pageState = true;
    document.addEventListener(visibilityChange, function() {
        if(document[state] === 'visible'){ //可见
            pageState = true;
        }else{ //不可见
            pageState = false;
        }
    }, false);


    function init(langData) {
        //form的验证事件
        $('#signinForm').html5Validate(function() {
            //通过验证，提交表单
            $(this).submit();
        });

        // Matter aliases
        var Engine = Matter.Engine,
            World = Matter.World,
            Bodies = Matter.Bodies,
            Composites = Matter.Composites,
            Common = Matter.Common,
            Constraint = Matter.Constraint,
            MouseConstraint = Matter.MouseConstraint;

        // coins
        var coins = ['circle-bt.png', 'circle-lt.png', 'circle-doge.png', 'circle-aur.png', 'circle-bc.png', 'circle-dark.png', 'circle-nmc.png', 'circle-nxt.png', 'circle-qrk.png', 'circle-xc.png', 'circle-zet.png'];

        var container = document.getElementById('canvas-container');


        // create a Matter engine
        // NOTE: this is actually Matter.Engine.create(), see the aliases at top of this file
        var _engine = Engine.create(container);

        // add a mouse controlled constraint
        var _mouseConstraint = MouseConstraint.create(_engine, {
            constraint: {
                render: {
                    visible: false //隐藏橡皮筋
                }
            }
        });
        World.add(_engine.world, _mouseConstraint);

        // run the engine
        Engine.run(_engine);

        sprites();


        function sprites() {
            var _world = _engine.world,
                offset = 10,
                options = {
                    isStatic: true,
                    render: {
                        visible: false,
                    }
                };

            _sceneWidth = document.documentElement.clientWidth;
            _sceneHeight = document.documentElement.clientHeight;

            var boundsMax = _engine.world.bounds.max,
                renderOptions = _engine.render.options,
                canvas = _engine.render.canvas;

            boundsMax.x = _sceneWidth;
            boundsMax.y = _sceneHeight;

            canvas.width = renderOptions.width = _sceneWidth;
            canvas.height = renderOptions.height = _sceneHeight;

            //renderOptions.background = 'rgba(255, 255, 255, 0)';
            renderOptions.background = '#eee';
            renderOptions.showAngleIndicator = false;
            renderOptions.wireframes = false;

            // these static walls will not be rendered in this sprites example, see options
            World.add(_world, [
                Bodies.rectangle(_sceneWidth * 0.5, -offset, _sceneWidth + 0.5, 25, options),
                Bodies.rectangle(_sceneWidth * 0.5, _sceneHeight + offset, _sceneWidth + 0.5, 25, options),
                Bodies.rectangle(_sceneWidth + offset, _sceneHeight * 0.5, 25, _sceneHeight + 25, options),
                Bodies.rectangle(-offset, _sceneHeight * 0.5, 25, _sceneHeight + 0.5, options),
                Bodies.rectangle(_sceneWidth - 70, _sceneHeight - 80, 200, 1, {
                    isStatic: true,
                    angle: -Math.PI * 0.28,
                    render: {
                        visible: false
                    }
                })
            ]);


            var sum = 0;

            //出币
            function addCoins(count) {
                // 自动出币最多50次
                if (sum == 50) {
                    clearTimeout(loop);
                }
                //如果没有传入数值，则随机出1-2颗币
                count = count ? count : parseInt(Math.random() * 2 + 1);
                for (var i = 0; i < count; i++) {
                    var coin = coins[parseInt(Math.random() * 11)], //随机获取币的图片
                        size = (parseInt(Math.random() * 5 + 3)) / 10, //币的大小 0.3~0.8
                        radios = 90 * size * 0.5;

                    World.add(_world, [
                        Bodies.circle(_sceneWidth - 120, _sceneHeight - 120, radios, {
                            render: {
                                sprite: {
                                    texture: 'static/assets/img/' + coin,
                                    xScale: size,
                                    yScale: size
                                }
                            },
                            density: 0.0009 // 密度
                        })
                    ]);
                };

            }

            //定时出币
            var loop = setInterval(function() {
                if(pageState){ //如果页面在可见状态下
                    addCoins();
                    sum++;
                }
            }, 5000);

            //点击doge，大量出币
            $('.doge').on('click', function() {
                //随机3～6颗
                addCoins(parseInt(Math.random() * 3 + 3));
            });
        }

        //doge-tip
        setTimeout(function() {
            $('.doge-tip').fadeOut('fast');
        }, 10000);

    }


})(window.jQuery);