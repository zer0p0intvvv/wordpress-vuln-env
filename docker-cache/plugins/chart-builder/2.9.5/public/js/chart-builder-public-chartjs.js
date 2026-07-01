(function($) {
	'use strict';

	function ChartBuilderChartJs(element, options) {
		this.el = element;
		this.$el = $(element);
		this.htmlClassPrefix = 'ays-chart-';
		this.htmlNamePrefix = 'ays_';
		this.uniqueId;
		this.dbData = undefined;
		this.chartSourceData = undefined;
		this.chartObj = undefined;
		this.chartOptions = null;
		this.chartData = null;
		this.chartTempData = null;
		this.chartType = 'pie_chart';
		this.chartId = null;
		this.chartSourceType = 'chart-js';
        this.chartObject = null;
	
		this.chartSources = {
			'pie_chart'    : 'Pie Chart',
			'bar_chart'    : 'Bar Chart',
			'line_chart'   : 'Line Chart',
		}
	
		this.init();
	
		return this;
	}
	
	ChartBuilderChartJs.prototype.init = function() {
		var _this = this;
		_this.uniqueId = _this.$el.data('id');

		if ( typeof window['aysChartOptions'+_this.uniqueId] != 'undefined' ) {
			_this.dbData = JSON.parse( atob( window['aysChartOptions'+_this.uniqueId]['aysChartOptions'] ) );
        }

		_this.setEvents();
	}
	
	ChartBuilderChartJs.prototype.setEvents = function(e){
		var _this = this;
		
		_this.chartId = _this.dbData.id;
		_this.chartType = _this.dbData.chart_type;
		_this.chartSourceType = _this.dbData.chartSourceType;
        _this.chartData = _this.dbData.source;

		_this.loadChartBySource();
		_this.resizeChart();

		$(document).on('click', '.elementor-tab-title, .e-n-tab-title, .ays-load-chart-source', function (e) {
			_this.loadChartBySource();
		});
		$(document).on('change', '.ays-load-chart-source', function (e) {
			_this.loadChartBySource();
		});
	}

	// Load charts by given type main function
	ChartBuilderChartJs.prototype.loadChartBySource = function(){
		var _this = this;

		if(typeof _this.chartType !== undefined && _this.chartType){
			switch (_this.chartType) {
				case 'pie_chart':
					_this.pieChartView();
					break;
				case 'bar_chart':
					_this.barChartView();
					break;
				case 'line_chart':
					_this.lineChartView();
					break;
				default:
					_this.pieChartView();
					break;
			}
		}
	}

	// Load chart by pie chart
	ChartBuilderChartJs.prototype.pieChartView = function(){
		var _this = this;
		var getChartSource = _this.dbData.source;

		var dataTypes = _this.chartConvertData( getChartSource );

        var settings = _this.dbData.options;
		var nSettings =  _this.configOptionsForCharts(settings);
        
		// var ctx = document.getElementById(_this.htmlClassPrefix + _this.uniqueId + '-canvas');
		var parentId = _this.htmlClassPrefix + _this.chartType + '-' + _this.uniqueId;
		var canvasId = parentId + '-canvas';

		var parentElement = document.getElementById(parentId);
		var canvasElement = document.getElementById(canvasId);

		if (!canvasElement) {
			canvasElement = document.createElement('canvas');
			canvasElement.id = canvasId;
			parentElement.appendChild(canvasElement);
		}

		var ctx = canvasElement;

		_this.chartObject = new Chart(ctx, {
		  type: 'pie',
		  data: {
			labels: dataTypes?.labels,
			datasets: dataTypes?.dataSets,
		  },
		  options: {
			radius: nSettings.outerRadius,
			spacing: nSettings.sliceSpacing,
			circumference: nSettings.circumference,
			rotation: nSettings.startAngle,
		  }
		});

        _this.resizeChart();
	}

	// Load chart by bar chart
	ChartBuilderChartJs.prototype.barChartView = function(){
		var _this = this;
		var getChartSource = _this.dbData.source;

		var dataTypes = _this.chartConvertData( getChartSource );

        var settings = _this.dbData.options;
		var nSettings =  _this.configOptionsForCharts(settings);
        
		// var ctx = document.getElementById(_this.htmlClassPrefix + _this.uniqueId + '-canvas');
		var parentId = _this.htmlClassPrefix + _this.chartType + '-' + _this.uniqueId;
		var canvasId = parentId + '-canvas';

		var parentElement = document.getElementById(parentId);
		var canvasElement = document.getElementById(canvasId);

		if (!canvasElement) {
			canvasElement = document.createElement('canvas');
			canvasElement.id = canvasId;
			parentElement.appendChild(canvasElement);
		}

		var ctx = canvasElement;
		
		_this.chartObject = new Chart(ctx, {
		  type: 'bar',
		  data: {
			labels: dataTypes?.labels,
			datasets: dataTypes?.dataSets,
		  },
		  options: {}
		});

        _this.resizeChart();
	}

	// Load chart by line chart
	ChartBuilderChartJs.prototype.lineChartView = function(){
		var _this = this;
		var getChartSource = _this.dbData.source;

		var dataTypes = _this.chartConvertData( getChartSource );

        var settings = _this.dbData.options;
		var nSettings =  _this.configOptionsForCharts(settings);
        
		// var ctx = document.getElementById(_this.htmlClassPrefix + _this.uniqueId + '-canvas');
		var parentId = _this.htmlClassPrefix + _this.chartType + '-' + _this.uniqueId;
		var canvasId = parentId + '-canvas';

		var parentElement = document.getElementById(parentId);
		var canvasElement = document.getElementById(canvasId);

		if (!canvasElement) {
			canvasElement = document.createElement('canvas');
			canvasElement.id = canvasId;
			parentElement.appendChild(canvasElement);
		}

		var ctx = canvasElement;

		_this.chartObject = new Chart(ctx, {
		  type: 'line',
		  data: {
			labels: dataTypes?.labels,
			datasets: dataTypes?.dataSets,
		  },
		  options: {}
		});

        _this.resizeChart();
	}

	/* 
	  Configure all settings for all chart types
	  Getting settings for each chart type in respective function 
	*/
	ChartBuilderChartJs.prototype.configOptionsForCharts = function (settings) {
		var newSettings = {};

		newSettings.outerRadius = settings['outer_radius'];
		newSettings.sliceSpacing = settings['slice_spacing'];
		newSettings.circumference = settings['circumference'];
		newSettings.startAngle = settings['start_angle'];

		return newSettings;
	}

	// Detect window resize moment to draw charts responsively
	ChartBuilderChartJs.prototype.resizeChart = function(){
		var _this = this;

		//create trigger to resizeEnd event     
		$(window).resize(function() {
			if(this.resizeTO) clearTimeout(this.resizeTO);
			this.resizeTO = setTimeout(function() {
				$(this).trigger('resizeEnd');
			}, 100);
		});
	
		//redraw graph when window resize is completed  
		$(window).on('resizeEnd', function() {
            _this.chartObject.resize();
		});
	}

	// Load chart by pie chart
	ChartBuilderChartJs.prototype.chartConvertData = function( data ){
		var _this = this;
        var dataTypes = [];
        
        var dataTypes = Array.from({ length: data[0].length }, () => []);
        for (var i = 0; i < data.length; i++) {
            for (var j = 0; j < data[i].length; j++) {
                if (i === 0) {
                    dataTypes[j].push(data[i][j]);
                } else {
                    dataTypes[j].push(i === 0 || j === 0 ? data[i][j] : +data[i][j]);
                }
            }
        }

        var labels = dataTypes.shift();
        labels.shift();
        var dataSets = dataTypes.map(item => ({
            label: item[0],
            data: item.slice(1).map(Number),
        }));

        return {labels, dataSets};
	}

	// Update chart data and display immediately
	ChartBuilderChartJs.prototype.updateChartData =  function( newData ){
		var _this = this;
        var newData = _this.chartConvertData( _this.chartData );

        _this.chartObject.data = {
            ..._this.chartObject.data,
			labels: newData?.labels,
			datasets: newData?.dataSets,
		  },
		_this.chartObject.update();
	}

	ChartBuilderChartJs.prototype.htmlDecode = function (input) {
		var e = document.createElement('div');
		e.innerHTML = input;
		return e.childNodes[0].nodeValue;
	}

	$.fn.ChartBuilderChartJsMain = function(options) {
		return this.each(function() {
			if (!$.data(this, 'ChartBuilderChartJsMain')) {
				$.data(this, 'ChartBuilderChartJsMain', new ChartBuilderChartJs(this, options));
			} else {
				try {
					$(this).data('ChartBuilderChartJsMain').init();
				} catch (err) {
					console.error('ChartBuilderChartJsMain has not initiated properly');
				}
			}
		});
	};

})(jQuery);
