// let navbar = new NavBar();
// console.log(navbar.element);


$('body').on('click', '#btnTest', function() {
    console.log(global);
});

let insx = qty => {
    if (qty == 1) return 0;
    if (qty == 2) return Number('<?php echo insx(2); ?>');
    if (qty == 3) return Number('<?php echo insx(3); ?>');
    if (qty == 4) return Number('<?php echo insx(4); ?>');
    if (qty == 5) return Number('<?php echo insx(5); ?>');
    if (qty == 6) return Number('<?php echo insx(6); ?>');
    if (qty == 7) return Number('<?php echo insx(7); ?>');
    if (qty == 8) return Number('<?php echo insx(8); ?>');
    if (qty == 9) return Number('<?php echo insx(9); ?>');
    if (qty == 10) return Number('<?php echo insx(10); ?>');
    if (qty == 11) return Number('<?php echo insx(11); ?>');
    if (qty == 12) return Number('<?php echo insx(12); ?>');
};

/**
 * Grafico de rosquinha
 */
function analyticsDoughnut(selector, set_data) {
	var $selector = selector ? $(selector) : $('.analytics-doughnut');
	$selector.each(function () {
		var $self = $(this),
			_self_id = $self.attr('id'),
			_get_data = typeof set_data === 'undefined' ? eval(_self_id) : set_data;

		var selectCanvas = document.getElementById(_self_id).getContext("2d");
		var chart_data = [];

		for (var i = 0; i < _get_data.datasets.length; i++) {
			chart_data.push({
				backgroundColor: _get_data.datasets[i].background,
				borderWidth: 2,
				borderColor: _get_data.datasets[i].borderColor,
				hoverBorderColor: _get_data.datasets[i].borderColor,
				data: _get_data.datasets[i].data
			});
		}

		var chart = new Chart(selectCanvas, {
			type: 'doughnut',
			data: {
				labels: _get_data.labels,
				datasets: chart_data
			},
			options: {
				legend: {
					display: _get_data.legend ? _get_data.legend : false,
					labels: {
						boxWidth: 12,
						padding: 20,
						fontColor: '#6783b8'
					}
				},
				rotation: -1.5,
				cutoutPercentage: 70,
				maintainAspectRatio: false,
				tooltips: {
					enabled: true,
					rtl: NioApp.State.isRTL,
					callbacks: {
						title: function title(tooltipItem, data) {
							return data['labels'][tooltipItem[0]['index']];
						},
						label: function label(tooltipItem, data) {
							return data.datasets[tooltipItem.datasetIndex]['data'][tooltipItem['index']] + ' ' + _get_data.dataUnit;
						}
					},
					backgroundColor: '#fff',
					borderColor: '#eff6ff',
					borderWidth: 2,
					titleFontSize: 13,
					titleFontColor: '#6783b8',
					titleMarginBottom: 6,
					bodyFontColor: '#9eaecf',
					bodyFontSize: 12,
					bodySpacing: 4,
					yPadding: 10,
					xPadding: 10,
					footerMarginTop: 0,
					displayColors: false
				}
			}
		});
	});
} 
/* COMO USAR:
var TrafficChannelDoughnutData = {
    labels: ["Organic Search", "Social Media", "Referrals", "Others"],
    dataUnit: 'People',
    legend: false,
    datasets: [{
        borderColor: "#fff",
        background: ["#798bff", "#b8acff", "#ffa9ce", "#f9db7b"],
        data: [1, 5, 10, 20]
    }]
};
analyticsDoughnut('#TrafficChannelDoughnutData', TrafficChannelDoughnutData);
*/

/**
 * Grafico de linha
 */
function lineSalesOverview(selector, set_data) {
    var $selector = selector ? $(selector) : $('.sales-overview-chart');
    $selector.each(function () {
      var $self = $(this),
          _self_id = $self.attr('id'),
          _get_data = typeof set_data === 'undefined' ? eval(_self_id) : set_data;

      var selectCanvas = document.getElementById(_self_id).getContext("2d");
      var chart_data = [];

      for (var i = 0; i < _get_data.datasets.length; i++) {
        chart_data.push({
          label: _get_data.datasets[i].label,
          tension: _get_data.lineTension,
          backgroundColor: _get_data.datasets[i].background,
          borderWidth: 2,
          borderColor: _get_data.datasets[i].color,
          pointBorderColor: "transparent",
          pointBackgroundColor: "transparent",
          pointHoverBackgroundColor: "#fff",
          pointHoverBorderColor: _get_data.datasets[i].color,
          pointBorderWidth: 2,
          pointHoverRadius: 3,
          pointHoverBorderWidth: 2,
          pointRadius: 3,
          pointHitRadius: 3,
          data: _get_data.datasets[i].data
        });
      }

      var chart = new Chart(selectCanvas, {
        type: 'line',
        data: {
          labels: _get_data.labels,
          datasets: chart_data
        },
        options: {
          legend: {
            display: _get_data.legend ? _get_data.legend : false,
            labels: {
              boxWidth: 30,
              padding: 20,
              fontColor: '#6783b8'
            }
          },
          maintainAspectRatio: false,
          tooltips: {
            enabled: true,
            rtl: NioApp.State.isRTL,
            callbacks: {
              title: function title(tooltipItem, data) {
                return data['labels'][tooltipItem[0]['index']];
              },
              label: function label(tooltipItem, data) {
                return data.datasets[tooltipItem.datasetIndex]['data'][tooltipItem['index']] + ' ' + _get_data.dataUnit;
              }
            },
            backgroundColor: '#eff6ff',
            titleFontSize: 13,
            titleFontColor: '#6783b8',
            titleMarginBottom: 6,
            bodyFontColor: '#9eaecf',
            bodyFontSize: 12,
            bodySpacing: 4,
            yPadding: 10,
            xPadding: 10,
            footerMarginTop: 0,
            displayColors: false
          },
          scales: {
            yAxes: [{
              display: true,
              stacked: _get_data.stacked ? _get_data.stacked : false,
              position: NioApp.State.isRTL ? "right" : "left",
              ticks: {
                beginAtZero: true,
                fontSize: 11,
                fontColor: '#9eaecf',
                padding: 10,
                callback: function callback(value, index, values) {
                  return '$ ' + value;
                },
                min: 100,
                stepSize: 3000
              },
              gridLines: {
                color: NioApp.hexRGB("#526484", .2),
                tickMarkLength: 0,
                zeroLineColor: NioApp.hexRGB("#526484", .2)
              }
            }],
            xAxes: [{
              display: true,
              stacked: _get_data.stacked ? _get_data.stacked : false,
              ticks: {
                fontSize: 9,
                fontColor: '#9eaecf',
                source: 'auto',
                padding: 10,
                reverse: NioApp.State.isRTL
              },
              gridLines: {
                color: "transparent",
                tickMarkLength: 0,
                zeroLineColor: 'transparent'
              }
            }]
          }
        }
      });
    });
}
/*
var salesOverview = {
    labels: ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30"],
    dataUnit: 'BTC',
    lineTension: 0.1,
    datasets: [{
        label: "Sales Overview",
        color: "#798bff",
        background: NioApp.hexRGB('#798bff', .3),
        data: [8200, 7800, 9500, 5500, 9200, 9690, 8200, 7800, 9500, 5500, 9200, 9690, 8200, 7800, 9500, 5500, 9200, 9690, 8200, 7800, 9500, 5500, 9200, 9690, 8200, 7800, 9500, 5500, 9200, 9690]
    }]
};
lineSalesOverview('#salesOverview', salesOverview);
*/
$('body').on('change', '[app-toggle="change"]', function() {
  let value = this.value;
  let class_ = 'hide';
  [].map.call(this.children, option => {
    let target = option.hasAttribute('app-target') ? option.getAttribute('app-target') : '';
    if (!target) return;
    let targetElement = document.querySelector(target);
    let valueAttr = option.value;
    if (value == valueAttr)
      targetElement.classList.remove(class_);
      // [...targetElement.classList].includes(class_) ? targetElement.classList.remove(class_) : targetElement.classList.add(class_);
    else 
      targetElement.classList.add(class_);
  });
});