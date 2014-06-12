(function($) {
	
	$.ajax({
        type: "get",
        url: $('.hashrate').data('url'),
        dataType: 'json',
        success: function(data) {
        	
        	dataSCRYPT = data['DATA']['L'];
        	dataSHA = data['DATA']['B'];
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
        	            type: 'datetime',
        	            
        	        },
        	        yAxis: {
        	            title: {
        	                text: ''
        	            },
        	            min: 0
        	        },
        	        tooltip: {
        	            formatter: function() {
        	                return Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) + '<br/><b>' + this.series.name + '</b><br><b>' + this.y + '</b>';
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
        	            data: dataSHA
        	        },{
        	            name: 'SCRYPT (khash/s)',
        	            data: dataSCRYPT
        	        }],
        	        navigation: {
        	            menuItemStyle: {
        	                fontSize: '10px'
        	            }
        	        }
        	    });
        }
    });

	function refreshState()
	{
		var need_show_check_result = true;
		setTimeout(function(){
			refreshState();
		},10000);
	}

	
	
})(window.jQuery);