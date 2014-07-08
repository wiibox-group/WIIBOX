(function($) {

	/*
	 切换语言按钮
	 */
	$('#languageMenu').on('click', '>li>a', function() {
		$.cookie('wiiboxLanguage', $(this).data('lang'), {
			path: '/'
		});
		window.location.reload();
	});


	/*
	 state: true可查询运行状态；false不查询
	 url: api url
	 */
	var options = {
		state: true,
		url: {
			restart: $('#urlConfig').data('restart'),
			check: $('#urlConfig').data('check')
		}
	};


	/*
	 重启按钮
	 */
	$('#actionRestart').on('click', function() {
		$(this).attr('disabled', true);

		//重启期间停止状态查询
		options.state = false;

		restart();
	});


	/*
	 重启
	 */

	function restart() {
		$.ajax({
			type: 'get',
			url: options.url.restart,
			dataType: 'json',
			beforeSend: function() {
				NProgress.start();
				//"正在重启"
				$('#statePill').removeClass('btn-success btn-danger')
					.addClass('btn-warning')
					.text(basei18n.restartIng);
			},
			success: function(data) {
				if (data === -1) {
					//重启中
					restart();
				} else if (data === 200) {
					// 成功重启，“正在运行”
					$('#statePill').removeClass('btn-warning btn-danger')
						.addClass('btn-success')
						.text(basei18n.running);
					//恢复状态查询
					options.state = true;
				} else {
					// 失败
					$('#statePill').removeClass('btn-success btn-warning')
						.addClass('btn-danger')
						.text(basei18n.restartFaild);
					//恢复状态查询
					options.state = true;
				}
			},
			error: function() {
				$('#statePill').removeClass('btn-success btn-warning')
					.addClass('btn-danger')
					.text(basei18n.restartFaild);
				//恢复状态查询
				options.state = true;
			},
			complete: function() {
				$('#actionRestart').attr('disabled', false);
				NProgress.done();
			}
		});
	}


	/*
	 查询运行状态
	 */

	function checkState() {
		if (options.state) {
			$.ajax({
				type: 'get',
				url: options.url.check,
				dataType: 'json',
				timeout: 10000,
				beforeSend: function() {
					options.state = false;
				},
				success: function(data) {
					if ((data.alived.BTC.length > 0 || data.alived.LTC.length) && data.super === true) {
						//有矿机，正在运行
						$('#statePill').removeClass('btn-warning btn-danger')
							.addClass('btn-success')
							.text(basei18n.running);
					} else {
						//已停止
						$('#statePill').removeClass('btn-warning btn-success')
							.addClass('btn-danger')
							.text(basei18n.stopped);
					}
				},
				error: function() {
					$('#statePill').removeClass('btn-warning btn-success')
						.addClass('btn-danger')
						.text(basei18n.stopped);
				},
				complete: function() {
					options.state = true;
					checkAgain();
				}
			});
		} else {
			checkAgain();
		}

		/*
		 定时执行查询
		 10s
		 */

		function checkAgain() {
			setTimeout(function() {
				checkState();
			}, 10000);
		}

	}


	$(document).ready(function() {
		checkState();
	});

})(window.jQuery);