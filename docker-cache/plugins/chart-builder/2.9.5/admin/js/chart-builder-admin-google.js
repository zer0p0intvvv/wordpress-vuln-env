(function($) {
    'use strict';
    function ChartBuilderGoogleCharts(element, options){
        this.el = element;
        this.$el = $(element);
        this.ajaxAction = 'ays_chart_admin_ajax';
        this.htmlClassPrefix = 'ays-chart-';
        this.htmlNamePrefix = 'ays_';
        this.dbOptions = undefined;
        this.chartSourceData = undefined;
		this.chartObj = undefined;
		this.chartOptions = null;
		this.chartData = null;
		this.chartTempData = null;
		this.chartType = 'pie_chart';
		this.chartSourceType = 'google-charts';

		this.chartSources = {
			'line_chart'   : 'Line Chart',
			'bar_chart'    : 'Bar Chart',
			'pie_chart'    : 'Pie Chart',
			'column_chart' : 'Column Chart',
			'org_chart'    : 'Org Chart',
			'donut_chart'  : 'Donut Chart',
		}

        this.init();

        return this;
    }

    ChartBuilderGoogleCharts.prototype.init = function() {
        var _this = this;
		_this.chartSourceData = window.ChartBuilderSourceData;
        
        _this.setEvents();
        _this.initLibraries();
        _this.setAccordionEvents();

		_this.initDbImport();
		_this.initQuizDBImport();
    };

	// Set events
    ChartBuilderGoogleCharts.prototype.setEvents = function(e){
        var _this = this;

        _this.chartType = _this.chartSourceData.chartType;
        _this.chartSourceType = _this.chartSourceData.chartSourceType;
        _this.loadChartBySource();
        _this.configureOptions();
            
		/* == Tabulation == */
			_this.$el.on('click', '.nav-tab-wrapper a.nav-tab' , _this.changeTabs);
		/* */

		/* == Notifications dismiss button == */
			_this.$el.on('click', '.notice-dismiss', function (e) {
				_this.changeCurrentUrl('status');
			});

			_this.$el.on('click', '.toggle_ddmenu' , _this.toggleDDmenu);
		/* */

		/* Add Manual data */
			_this.$el.on("click"  , '.'+_this.htmlClassPrefix+'add-new-row-box', function(){
				_this.addChartDataRow();
			});

			_this.$el.on("click"  , '.'+_this.htmlClassPrefix+'add-new-column-box', function(){
				_this.addChartDataColumn();
			});

			_this.$el.on('click', '.'+_this.htmlClassPrefix+'show-on-chart-bttn', function(e){		
				e.preventDefault();
				if(_this.chartType != "org_chart" ) {
					_this.showOnChart();
				} else {
					_this.orgTypeShowOnChart();
				}
			});

		/* */

		/* Delete data */ 
			_this.$el.on("click"  , '.'+_this.htmlClassPrefix+'chart-source-data-remove-row', function(){
				_this.deleteChartDataRow($(this));
				_this.detectManualChange();
			});
			_this.$el.on("click"  , '.'+_this.htmlClassPrefix+'chart-source-data-remove-col', function(){
				_this.deleteChartDataColumn($(this));
				_this.detectManualChange();
			});
			_this.$el.on('mouseenter', '.'+_this.htmlClassPrefix+'chart-source-data-remove-block', function() {
				$(this).find('path').css('fill', '#ff0000');
			});
			_this.$el.on('mouseleave', '.'+_this.htmlClassPrefix+'chart-source-data-remove-block', function() {
				$(this).find('path').css('fill', '#b8b8b8');
			});
		/* */

		/* Save with Ctrl + S */
		_this.$el.on('keydown', $(document), _this.quickSaveHotKeys);
		/* */

		// Submit buttons disabling
		_this.$el.on('click', '.'+_this.htmlClassPrefix+'loader-banner', _this.submitOnce);
		/* */
		
		// Disabling submit when press enter button on inputing
		$(document).on("keypress", '.ays-text-input', _this.keyBoardConfig.bind(_this));
		/* */

		// Modal close
		$(document).on('click', '.ays-close', function () {
			$(this).parents('.ays-modal').aysModal('hide');
		});

		// Changing source type to manual
		_this.$el.on('input', '.'+_this.htmlClassPrefix+'chart-source-data-content input', function () {
			_this.detectManualChange();
		});

		// Shortcode text for editor tooltip
		$(document).find('strong.ays-chart-shortcode-box').on('mouseleave', function(){
			var _this = $(this);
	
			_this.attr('data-original-title', aysChartBuilderAdmin.clickForCopy);
			_this.attr("data-bs-original-title", aysChartBuilderAdmin.clickForCopy);
			_this.attr("title", aysChartBuilderAdmin.clickForCopy);
		});

		// Quiz maker integration
		$(document).find('#'+_this.htmlClassPrefix+'select-quiz-maker-data-query').on('change', function () {
			var option = $(this).find('option:selected');
			if (option.attr('is-pro') == "true") {
				$(this).find('option[value=""]').prop('selected', true);
				window.open('https://ays-pro.com/wordpress/chart-builder/', '_blank');
			} else {
				_this.$el.find('.ays-chart-quiz-query-tooltip').attr('title', _this.chartSourceData.quiz_query_tooltips[this.value]);
				_this.$el.find('.ays-chart-quiz-query-tooltip').attr('data-bs-original-title', _this.chartSourceData.quiz_query_tooltips[this.value]);
				_this.quizMakerIntegrationConfig();
				_this.toggleQuizSelect(this);
			}
		});

		$(document).find('#'+_this.htmlClassPrefix+'select-quiz-maker-quiz').on('change', function () {
			_this.quizMakerIntegrationConfig();
		});

		$(window).on('load', function () {
			_this.toggleQuizSelect();
		});

		// Options configuration
		$(document).find('#'+_this.htmlClassPrefix+'option-haxis-slanted-text').on('change', function () {
			var angleOption = $(document).find('#'+_this.htmlClassPrefix+'option-haxis-slanted-text-angle').parents('.'+_this.htmlClassPrefix+'options-section');
			if ($(this).val() !== 'false') {
				angleOption.removeClass('display_none');
			} else {
				angleOption.addClass('display_none');
			}
		});

		_this.$el.find('.'+_this.htmlClassPrefix+'toggle-hidden-option').on('change', function () {
			// var animationOptions = _this.$el.find('.'+_this.htmlClassPrefix+'hidden-options-section');
			var currentSettings = $(this).parents('.' + _this.htmlClassPrefix + 'settings-data-main-wrap')
			var hiddenSection = currentSettings.find('.' + _this.htmlClassPrefix + 'hidden-options-section');
			var notHiddenSection = currentSettings.find('.' + _this.htmlClassPrefix + 'not-hidden-options-section');

			if ($(this).is(':checked')) {
				hiddenSection.removeClass('display_none');
				notHiddenSection.addClass('display_none');
			} else {
				hiddenSection.addClass('display_none');
				notHiddenSection.removeClass('display_none');
			}
		});

		_this.$el.on("click", '.'+_this.htmlClassPrefix+'chart-source-data-sort', function(){
			_this.sortDataByColumn($(this));
			_this.detectManualChange();
		});

		_this.$el.on("click", '.'+_this.htmlClassPrefix+'charts-change-type-type', function(){
			_this.chartType = $(this).attr('data-type');
			_this.changeChartType();
			_this.$el.find('.'+_this.htmlClassPrefix+'charts-change-type-type').css('backgroundColor', '#e7e7e7');
			$(this).css('backgroundColor', '#008cff63');
		});

		// Live preview
		_this.liveSettingsPreview();
    }

	ChartBuilderGoogleCharts.prototype.liveSettingsPreview = function () {
		var _this = this;

		// general settings
			_this.$el.find('#'+_this.htmlClassPrefix+'option-width').on('input', function () {
				var format = _this.$el.find('#'+_this.htmlClassPrefix+'option-width-format').val() == '%' ? '%' : '';
				_this.$el.find('.'+_this.htmlClassPrefix+'charts-main-container').css('width', $(this).val() + format);
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-width-format').on('change', function () {
				var format = '%';
				if ($(this).val() == 'px') {
					format = '';
				}
				var value = _this.$el.find('#'+_this.htmlClassPrefix+'option-width').val();
				_this.$el.find('.'+_this.htmlClassPrefix+'charts-main-container').css('width', value + format);
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-position').on('change', function () {
				var value = $(this).val();
				var val = '';
				switch (value) {
					case 'center':
						val = 'auto';
						break;
					case 'right':
						val = 'auto 0 auto auto';
						break;
					case 'left':
						val = 'auto auto auto 0';
						break;
					default:
						break;
				}
				_this.$el.find('.'+_this.htmlClassPrefix+'charts-main-container').css('margin', val);
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-height').on('input', function () {
				var format = _this.$el.find('#'+_this.htmlClassPrefix+'option-height-format').val() == '%' ? '%' : '';
				_this.$el.find('.'+_this.htmlClassPrefix+'charts-main-container').css('height', $(this).val() + format);
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-height-format').on('change', function () {
				var format = '';
				if ($(this).val() == '%') {
					format = '%';
				}
				var value = _this.$el.find('#'+_this.htmlClassPrefix+'option-height').val();
				_this.$el.find('.'+_this.htmlClassPrefix+'charts-main-container').css('height', value + format);
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-font-size').on('input', function () {
				_this.chartOptions.fontSize = $(this).val();
				_this.chartSourceData.settings.font_size = _this.chartOptions.fontSize;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-background-color').on('input', function () {
				var isTransparent = _this.$el.find('#'+_this.htmlClassPrefix+'option-transparent-background').is(':checked');

				if (!isTransparent) {
					_this.chartOptions.backgroundColor.fill = $(this).val();
					_this.chartSourceData.settings.background_color = _this.chartOptions.backgroundColor.fill;
					_this.drawChartFunction(_this.chartData, _this.chartOptions);
				}
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-transparent-background').on('change', function () {
				var bgColor = _this.$el.find('#'+_this.htmlClassPrefix+'option-background-color').val();
				var chartBgColor = _this.$el.find('#'+_this.htmlClassPrefix+'option-chart-background-color').val();

				_this.chartSourceData.settings.transparent_background = $(this).is(':checked') ? 'checked' : '';
				_this.chartOptions.backgroundColor.fill = $(this).is(':checked') ? 'transparent' : bgColor;
				if (typeof _this.chartOptions.chartArea.backgroundColor !== 'undefined') {
					_this.chartOptions.chartArea.backgroundColor.fill = $(this).is(':checked') ? 'transparent' : chartBgColor;
				}
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-chart-background-color').on('input', function () {
				var isTransparent = _this.$el.find('#'+_this.htmlClassPrefix+'option-transparent-background').is(':checked');

				if (!isTransparent) {
					_this.chartOptions.chartArea.backgroundColor.fill = $(this).val();
					_this.chartSourceData.settings.chart_background_color = _this.chartOptions.chartArea.backgroundColor.fill;
					_this.drawChartFunction(_this.chartData, _this.chartOptions);
				}
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-border-width').on('input', function () {
				_this.chartOptions.backgroundColor.strokeWidth = $(this).val();
				_this.chartSourceData.settings.border_width = _this.chartOptions.backgroundColor.strokeWidth;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-border-radius').on('input', function () {
				_this.$el.find('.'+_this.htmlClassPrefix+'charts-main-container').css('border-radius', $(this).val() + 'px');
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-chart-border-width').on('input', function () {
				_this.chartOptions.chartArea.backgroundColor.strokeWidth = $(this).val();
				_this.chartSourceData.settings.chart_border_width = _this.chartOptions.chartArea.backgroundColor.strokeWidth;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-border-color').on('input', function () {
				_this.chartOptions.backgroundColor.stroke = $(this).val();
				_this.chartSourceData.settings.border_color = _this.chartOptions.backgroundColor.stroke;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-chart-border-color').on('input', function () {
				_this.chartOptions.chartArea.backgroundColor.stroke = $(this).val();
				_this.chartSourceData.settings.chart_border_color = _this.chartOptions.chartArea.backgroundColor.stroke;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-chart-left-margin').on('input', function () {
				_this.chartOptions.chartArea.left = $(this).val() === '' ? 'auto' : +$(this).val();
				_this.chartSourceData.settings.chart_left_margin = _this.chartOptions.chartArea.left;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-chart-right-margin').on('input', function () {
				_this.chartOptions.chartArea.right = $(this).val() === '' ? 'auto' : +$(this).val();
				_this.chartSourceData.settings.chart_right_margin = _this.chartOptions.chartArea.right;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-chart-top-margin').on('input', function () {
				_this.chartOptions.chartArea.top = $(this).val() === '' ? 'auto' : +$(this).val();
				_this.chartSourceData.settings.chart_top_margin = _this.chartOptions.chartArea.top;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-chart-bottom-margin').on('input', function () {
				_this.chartOptions.chartArea.bottom = $(this).val() === '' ? 'auto' : +$(this).val();
				_this.chartSourceData.settings.chart_bottom_margin = _this.chartOptions.chartArea.bottom;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-enable-interactivity').on('change', function () {
				_this.chartOptions.enableInteractivity = $(this).is(':checked');
				_this.chartSourceData.settings.enable_interactivity = _this.chartOptions.enableInteractivity ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-maximized-view').on('change', function () {
				_this.chartOptions.theme = $(this).is(':checked') ? 'maximized' : null;
				_this.chartSourceData.settings.maximized_view = $(this).is(':checked') ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

		// tooltip settings
			_this.$el.find('#'+_this.htmlClassPrefix+'option-tooltip-trigger').on('change', function () {
				_this.chartOptions.tooltip.trigger = $(this).val();
				_this.chartSourceData.settings.tooltip_trigger = _this.chartOptions.tooltip.trigger;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-show-color-code').on('change', function () {
				_this.chartOptions.tooltip.showColorCode = $(this).is(':checked');
				_this.chartSourceData.settings.show_color_code = _this.chartOptions.tooltip.showColorCode ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-tooltip-text-color').on('input', function () {
				_this.chartOptions.tooltip.textStyle.color = $(this).val();
				_this.chartSourceData.settings.tooltip_text_color = _this.chartOptions.tooltip.textStyle.color;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-tooltip-font-size').on('input', function () {
				_this.chartOptions.tooltip.textStyle.fontSize = $(this).val();
				_this.chartSourceData.settings.tooltip_font_size = _this.chartOptions.tooltip.textStyle.fontSize;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-tooltip-italic').on('change', function () {
				_this.chartOptions.tooltip.textStyle.italic = $(this).is(':checked');
				_this.chartSourceData.settings.tooltip_italic = _this.chartOptions.tooltip.textStyle.italic ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-tooltip-bold').on('change', function () {
				_this.chartOptions.tooltip.textStyle.bold = $(this).val();
				_this.chartSourceData.settings.tooltip_bold = _this.chartOptions.tooltip.textStyle.bold ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

		// legend settings
			_this.$el.find('#'+_this.htmlClassPrefix+'option-legend-position').on('change', function () {
				_this.chartOptions.legend.position = $(this).val();
				_this.chartSourceData.settings.legend_position = _this.chartOptions.legend.position;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-legend-alignment').on('change', function () {
				_this.chartOptions.legend.alignment = $(this).val();
				_this.chartSourceData.settings.legend_alignment = _this.chartOptions.legend.alignment;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-legend-font-color').on('input', function () {
				_this.chartOptions.legend.textStyle.color = $(this).val();
				_this.chartSourceData.settings.legend_font_color = _this.chartOptions.legend.textStyle.color;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-legend-font-size').on('input', function () {
				_this.chartOptions.legend.textStyle.fontSize = $(this).val();
				_this.chartSourceData.settings.legend_font_size = _this.chartOptions.legend.textStyle.fontSize;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-legend-italic').on('change', function () {
				_this.chartOptions.legend.textStyle.italic = $(this).is(':checked');
				_this.chartSourceData.settings.legend_italic = _this.chartOptions.legend.textStyle.italic ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-legend-bold').on('change', function () {
				_this.chartOptions.legend.textStyle.bold = $(this).is(':checked');
				_this.chartSourceData.settings.legend_bold = _this.chartOptions.legend.textStyle.bold ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

		// horizontal axis settings
			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-title').on('input', function () {
				_this.chartOptions.hAxis.title = $(this).val();
				_this.chartSourceData.settings.haxis_title = _this.chartOptions.hAxis.title;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-text-position').on('change', function () {
				_this.chartOptions.hAxis.textPosition = $(this).val();
				_this.chartSourceData.settings.haxis_text_position = _this.chartOptions.hAxis.textPosition;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-text-direction').on('change', function () {
				_this.chartOptions.hAxis.direction = $(this).is(':checked') ? -1 : 1;
				_this.chartSourceData.settings.haxis_text_direction = _this.chartOptions.hAxis.direction;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-text-color').on('input', function () {
				_this.chartOptions.hAxis.textStyle.color = $(this).val();
				_this.chartSourceData.settings.haxis_text_color = _this.chartOptions.hAxis.textStyle.color;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-baseline-color').on('input', function () {
				_this.chartOptions.hAxis.baselineColor = $(this).val();
				_this.chartSourceData.settings.haxis_baseline_color = _this.chartOptions.hAxis.baselineColor;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-text-font-size').on('input', function () {
				_this.chartOptions.hAxis.textStyle.fontSize = $(this).val();
				_this.chartSourceData.settings.haxis_text_font_size = _this.chartOptions.hAxis.textStyle.fontSize;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-slanted-text').on('change', function () {
				_this.chartOptions.hAxis.slantedText = $(this).val();
				_this.chartSourceData.settings.haxis_slanted_text = _this.chartOptions.hAxis.slantedText;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-slanted-text-angle').on('input', function () {
				_this.chartOptions.hAxis.slantedTextAngle = $(this).val() != 0 ? $(this).val() : 30;
				_this.chartSourceData.settings.haxis_slanted_text_angle = _this.chartOptions.hAxis.slantedTextAngle;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-show-text-every').on('input', function () {
				_this.chartOptions.hAxis.showTextEvery = $(this).val() != 0 ? $(this).val() : 'automatic';
				_this.chartSourceData.settings.haxis_show_text_every = $(this).val();
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-format').on('change', function () {
				_this.chartOptions.hAxis.format = $(this).val();
				_this.chartSourceData.settings.haxis_format = _this.chartOptions.hAxis.format;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-label-font-size').on('input', function () {
				_this.chartOptions.hAxis.titleTextStyle.fontSize = $(this).val();
				_this.chartSourceData.settings.haxis_label_font_size = _this.chartOptions.hAxis.titleTextStyle.fontSize;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-italic').on('change', function () {
				_this.chartOptions.hAxis.textStyle.italic = $(this).is(':checked');
				_this.chartSourceData.settings.haxis_italic = _this.chartOptions.hAxis.textStyle.italic ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-bold').on('change', function () {
				_this.chartOptions.hAxis.textStyle.bold = $(this).is(':checked');
				_this.chartSourceData.settings.haxis_bold = _this.chartOptions.hAxis.textStyle.bold ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-title-italic').on('change', function () {
				_this.chartOptions.hAxis.titleTextStyle.italic = $(this).is(':checked');
				_this.chartSourceData.settings.haxis_title_italic = _this.chartOptions.hAxis.titleTextStyle.italic ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-title-bold').on('change', function () {
				_this.chartOptions.hAxis.titleTextStyle.bold = $(this).is(':checked');
				_this.chartSourceData.settings.haxis_title_bold = _this.chartOptions.hAxis.titleTextStyle.bold ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-label-color').on('input', function () {
				_this.chartOptions.hAxis.titleTextStyle.color = $(this).val();
				_this.chartSourceData.settings.haxis_label_color = _this.chartOptions.hAxis.titleTextStyle.color;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-max-value').on('input', function () {
				_this.chartOptions.hAxis.viewWindow.max = $(this).val();
				_this.chartSourceData.settings.haxis_max_value = _this.chartOptions.hAxis.viewWindow.max;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-min-value').on('input', function () {
				_this.chartOptions.hAxis.viewWindow.min = $(this).val();
				_this.chartSourceData.settings.haxis_min_value = _this.chartOptions.hAxis.viewWindow.min;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-gridlines-count').on('input', function () {
				_this.chartOptions.hAxis.gridlines.count = $(this).val();
				_this.chartSourceData.settings.haxis_gridlines_count = _this.chartOptions.hAxis.gridlines.count;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-gridlines-color').on('input', function () {
				_this.chartOptions.hAxis.gridlines.color = $(this).val();
				_this.chartSourceData.settings.haxis_gridlines_color = _this.chartOptions.hAxis.gridlines.color;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-haxis-minor-gridlines-color').on('input', function () {
				_this.chartOptions.hAxis.minorGridlines.color = $(this).val();
				_this.chartSourceData.settings.haxis_minor_gridlines_color = _this.chartOptions.hAxis.minorGridlines.color;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

		// vertical axis settings
			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-title').on('input', function () {
				_this.chartOptions.vAxis.title = $(this).val();
				_this.chartSourceData.settings.vaxis_title = _this.chartOptions.vAxis.title;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-text-position').on('change', function () {
				_this.chartOptions.vAxis.textPosition = $(this).val();
				_this.chartSourceData.settings.vaxis_text_position = _this.chartOptions.vAxis.textPosition;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-text-direction').on('change', function () {
				_this.chartOptions.vAxis.direction = $(this).is(':checked') ? -1 : 1;
				_this.chartSourceData.settings.vaxis_text_direction = _this.chartOptions.vAxis.direction;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-text-color').on('input', function () {
				_this.chartOptions.vAxis.textStyle.color = $(this).val();
				_this.chartSourceData.settings.vaxis_text_color = _this.chartOptions.vAxis.textStyle.color;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-baseline-color').on('input', function () {
				_this.chartOptions.vAxis.baselineColor = $(this).val();
				_this.chartSourceData.settings.vaxis_baseline_color = _this.chartOptions.vAxis.baselineColor;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-text-font-size').on('input', function () {
				_this.chartOptions.vAxis.textStyle.fontSize = $(this).val();
				_this.chartSourceData.settings.vaxis_text_font_size = _this.chartOptions.vAxis.textStyle.fontSize;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-format').on('change', function () {
				_this.chartOptions.vAxis.format = $(this).val();
				_this.chartSourceData.settings.vaxis_format = _this.chartOptions.vAxis.format;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-label-font-size').on('input', function () {
				_this.chartOptions.vAxis.titleTextStyle.fontSize = $(this).val();
				_this.chartSourceData.settings.vaxis_label_font_size = _this.chartOptions.vAxis.titleTextStyle.fontSize;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-italic').on('change', function () {
				_this.chartOptions.vAxis.textStyle.italic = $(this).is(':checked');
				_this.chartSourceData.settings.vaxis_italic = _this.chartOptions.vAxis.textStyle.italic ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-bold').on('change', function () {
				_this.chartOptions.vAxis.textStyle.bold = $(this).is(':checked');
				_this.chartSourceData.settings.vaxis_bold = _this.chartOptions.vAxis.textStyle.bold ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-title-italic').on('change', function () {
				_this.chartOptions.vAxis.titleTextStyle.italic = $(this).is(':checked');
				_this.chartSourceData.settings.vaxis_title_italic = _this.chartOptions.vAxis.titleTextStyle.italic ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-title-bold').on('change', function () {
				_this.chartOptions.vAxis.titleTextStyle.bold = $(this).is(':checked');
				_this.chartSourceData.settings.vaxis_title_bold = _this.chartOptions.vAxis.titleTextStyle.bold ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-label-color').on('input', function () {
				_this.chartOptions.vAxis.titleTextStyle.color = $(this).val();
				_this.chartSourceData.settings.vaxis_label_color = _this.chartOptions.vAxis.titleTextStyle.color;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-max-value').on('input', function () {
				_this.chartOptions.vAxis.viewWindow.max = $(this).val();
				_this.chartSourceData.settings.vaxis_max_value = _this.chartOptions.vAxis.viewWindow.max;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-min-value').on('input', function () {
				_this.chartOptions.vAxis.viewWindow.min = $(this).val();
				_this.chartSourceData.settings.vaxis_min_value = _this.chartOptions.vAxis.viewWindow.min;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-gridlines-count').on('input', function () {
				_this.chartOptions.vAxis.gridlines.count = $(this).val();
				_this.chartSourceData.settings.vaxis_gridlines_count = _this.chartOptions.vAxis.gridlines.count;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-gridlines-color').on('input', function () {
				_this.chartOptions.vAxis.gridlines.color = $(this).val();
				_this.chartSourceData.settings.vaxis_gridlines_color = _this.chartOptions.vAxis.gridlines.color;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-vaxis-minor-gridlines-color').on('input', function () {
				_this.chartOptions.vAxis.minorGridlines.color = $(this).val();
				_this.chartSourceData.settings.vaxis_minor_gridlines_color = _this.chartOptions.vAxis.minorGridlines.color;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

		// animation settings
			_this.$el.find('#'+_this.htmlClassPrefix+'option-enable-animation').on('change', function () {
				if ($(this).is(':checked')) {
					_this.chartOptions.animation = {
						startup: _this.$el.find('#'+_this.htmlClassPrefix+'option-animation-startup').is(':checked'),
						duration: _this.$el.find('#'+_this.htmlClassPrefix+'option-animation-duration').val(),
						easing: _this.$el.find('#'+_this.htmlClassPrefix+'option-animation-easing').val(),
					}
					_this.chartSourceData.settings.enable_animation = 'checked';
				} else {
					delete _this.chartOptions.animation;
					_this.chartSourceData.settings.enable_animation = '';
				}
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-animation-duration').on('input', function () {
				if (_this.$el.find('#'+_this.htmlClassPrefix+'option-enable-animation').is(':checked')) {
					_this.chartOptions.animation.duration = $(this).val();
					_this.chartSourceData.settings.animation_duration = _this.chartOptions.animation.duration;
					_this.drawChartFunction(_this.chartData, _this.chartOptions);
				}
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-animation-startup').on('input', function () {
				if (_this.$el.find('#'+_this.htmlClassPrefix+'option-enable-animation').is(':checked')) {
					_this.chartOptions.animation.startup = $(this).is(':checked');
					_this.chartSourceData.settings.animation_startup = _this.chartOptions.animation.startup ? 'checked' : '';
					_this.drawChartFunction(_this.chartData, _this.chartOptions);
				}
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-animation-easing').on('input', function () {
				if (_this.$el.find('#'+_this.htmlClassPrefix+'option-enable-animation').is(':checked')) {
					_this.chartOptions.animation.easing = $(this).val();
					_this.chartSourceData.settings.animation_easing = _this.chartOptions.animation.easing;
					_this.drawChartFunction(_this.chartData, _this.chartOptions);
				}
			});

		// advanced settings
			_this.$el.find('#'+_this.htmlClassPrefix+'option-rotation-degree').on('input', function () {
				_this.chartOptions.pieStartAngle = $(this).val();
				_this.chartSourceData.settings.rotation_degree = _this.chartOptions.pieStartAngle;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-reverse-categories').on('change', function () {
				_this.chartOptions.reverseCategories = $(this).is(':checked');
				_this.chartSourceData.settings.reverse_categories = _this.chartOptions.reverseCategories ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-slice-border-color').on('input', function () {
				_this.chartOptions.pieSliceBorderColor = $(this).val();
				_this.chartSourceData.settings.slice_border_color = _this.chartOptions.pieSliceBorderColor;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-slice-text').on('change', function () {
				_this.chartOptions.pieSliceText = $(this).val();
				_this.chartSourceData.settings.slice_text = _this.chartOptions.pieSliceText;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-is-stacked').on('change', function () {
				_this.chartOptions.isStacked = $(this).is(':checked');
				_this.chartSourceData.settings.is_stacked = _this.chartOptions.isStacked ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-focus-target').on('change', function () {
				_this.chartOptions.focusTarget = $(this).val();
				_this.chartSourceData.settings.focus_target = _this.chartOptions.focusTarget;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-opacity').on('input', function () {
				_this.chartOptions.dataOpacity = $(this).val();
				_this.chartSourceData.settings.opacity = _this.chartOptions.dataOpacity;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-tooltip-text').on('change', function () {
				_this.chartOptions.tooltip.text = $(this).val();
				_this.chartSourceData.settings.tooltip_text = _this.chartOptions.tooltip.text;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-line-width').on('input', function () {
				_this.chartOptions.lineWidth = $(this).val();
				_this.chartSourceData.settings.line_width = _this.chartOptions.lineWidth;

				_this.chartSourceData.settings.series_line_width.forEach((value, id) => {
					_this.chartOptions.series[id]["lineWidth"] = $(this).val();
					_this.chartSourceData.settings.series_line_width[id] = $(this).val();
					_this.$el.find('#'+_this.htmlClassPrefix+'option-series-line-width-'+id).val($(this).val());
				});

				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-data-grouping-limit').on('input', function () {
				_this.chartOptions.sliceVisibilityThreshold = $(this).val()/100;
				_this.chartSourceData.settings.data_grouping_limit = $(this).val();
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-data-grouping-label').on('input', function () {
				_this.chartOptions.pieResidueSliceLabel = $(this).val();
				_this.chartSourceData.settings.data_grouping_label = _this.chartOptions.pieResidueSliceLabel;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-data-grouping-color').on('input', function () {
				_this.chartOptions.pieResidueSliceColor = $(this).val();
				_this.chartSourceData.settings.data_grouping_color = _this.chartOptions.pieResidueSliceColor;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-multiple-selection').on('change', function () {
				_this.chartOptions.selectionMode = $(this).is(':checked') ? 'multiple' : 'single';
				_this.chartSourceData.settings.multiple_selection = $(this).is(':checked') ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-multiple-data-format').on('change', function () {
				_this.chartOptions.aggregationTarget = $(this).val();
				_this.chartSourceData.settings.multiple_data_format = _this.chartOptions.aggregationTarget;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-point-shape').on('change', function () {
				_this.chartOptions.pointShape = $(this).val();
				_this.chartSourceData.settings.point_shape = _this.chartOptions.pointShape;

				_this.chartSourceData.settings.series_point_shape.forEach((value, id) => {
					_this.chartOptions.series[id]["pointShape"] = $(this).val();
					_this.chartSourceData.settings.series_point_shape[id] = $(this).val();
					_this.$el.find('#'+_this.htmlClassPrefix+'option-series-point-shape-'+id).val($(this).val());
				});

				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-point-size').on('input', function () {
				_this.chartOptions.pointSize = $(this).val();
				_this.chartSourceData.settings.point_size = _this.chartOptions.pointSize;

				_this.chartSourceData.settings.series_point_size.forEach((value, id) => {
					_this.chartOptions.series[id]["pointSize"] = $(this).val();
					_this.chartSourceData.settings.series_point_size[id] = $(this).val();
					_this.$el.find('#'+_this.htmlClassPrefix+'option-series-point-size-'+id).val($(this).val());
				});
				
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-crosshair-trigger').on('change', function () {
				_this.chartOptions.crosshair.trigger = $(this).val();
				_this.chartSourceData.settings.crosshair_trigger = _this.chartOptions.crosshair.trigger;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-crosshair-orientation').on('change', function () {
				_this.chartOptions.crosshair.orientation = $(this).val();
				_this.chartSourceData.settings.crosshair_orientation = _this.chartOptions.crosshair.orientation;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-crosshair-opacity').on('input', function () {
				_this.chartOptions.crosshair.opacity = $(this).val();
				_this.chartSourceData.settings.crosshair_opacity = _this.chartOptions.crosshair.opacity;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-dash-style').on('input', function () {
				_this.chartOptions.lineDashStyle = $(this).val() ? $(this).val().split(',') : null;
				_this.chartSourceData.settings.dash_style = _this.chartOptions.lineDashStyle.join(',');
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-group-width').on('input', function () {
				var format = _this.$el.find('#'+_this.htmlClassPrefix+'option-group-width-format').val() == '%' ? '%' : '';
				_this.chartOptions.bar.groupWidth = $(this).val() + format;
				_this.chartSourceData.settings.group_width = $(this).val();
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-group-width-format').on('change', function () {
				var format = '%';
				if ($(this).val() == 'px') {
					format = '';
				}
				var value = _this.$el.find('#'+_this.htmlClassPrefix+'option-group-width').val();
				_this.chartOptions.bar.groupWidth = value + format;
				_this.chartSourceData.settings.group_width_format = format;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-orientation').on('change', function () {
				_this.chartOptions.orientation = $(this).is(':checked') ? 'vertical' : 'horizontal';
				_this.chartSourceData.settings.orientation = $(this).is(':checked') ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-fill-nulls').on('change', function () {
				_this.chartOptions.interpolateNulls = $(this).is(':checked') ? 'true' : 'false';
				_this.chartSourceData.settings.full_nulls = $(this).is(':checked') ? 'checked' : '';
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-donut-hole-size').on('input', function () {
				_this.chartOptions.pieHole = $(this).val();
				_this.chartSourceData.settings.donut_hole_size = _this.chartOptions.pieHole;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-org-chart-font-size').on('change', function () {
				_this.chartOptions.size = $(this).val();
				_this.chartSourceData.settings.org_chart_font_size = _this.chartOptions.size;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-allow-collapse').on('change', function () {
				_this.chartOptions.allowCollapse = $(this).is(':checked');
				_this.chartSourceData.settings.allow_collapse = _this.chartOptions.allowCollapse;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-org-classname').on('input', function () {
				_this.chartOptions.nodeClass = $(this).val();
				_this.chartSourceData.settings.org_classname = _this.chartOptions.nodeClass;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-org-node-background-color').on('input', function () {
				var orgClassname = _this.chartSourceData.settings.org_classname;
				_this.chartSourceData.settings.org_node_background_color = $(this).val();
				if (orgClassname != '') {
					_this.setOrgChartCustomStyles();
				}
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-org-node-padding').on('input', function () {
				var orgClassname = _this.chartSourceData.settings.org_classname;
				_this.chartSourceData.settings.org_node_padding = $(this).val();
				if (orgClassname != '') {
					_this.setOrgChartCustomStyles();
				}
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-org-node-border-radius').on('input', function () {
				var orgClassname = _this.chartSourceData.settings.org_classname;
				_this.chartSourceData.settings.org_node_border_radius = $(this).val();
				if (orgClassname != '') {
					_this.setOrgChartCustomStyles();
				}
			});
			
			_this.$el.find('#'+_this.htmlClassPrefix+'option-org-node-border-width').on('input', function () {
				var orgClassname = _this.chartSourceData.settings.org_classname;
				_this.chartSourceData.settings.org_node_border_width = $(this).val();
				if (orgClassname != '') {
					_this.setOrgChartCustomStyles();
				}
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-org-node-border-color').on('input', function () {
				var orgClassname = _this.chartSourceData.settings.org_classname;
				_this.chartSourceData.settings.org_node_border_color = $(this).val();
				if (orgClassname != '') {
					_this.setOrgChartCustomStyles();
				}
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-org-node-text-color').on('input', function () {
				var orgClassname = _this.chartSourceData.settings.org_classname;
				_this.chartSourceData.settings.org_node_text_color = $(this).val();
				if (orgClassname != '') {
					_this.setOrgChartCustomStyles();
				}
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-org-node-text-font-size').on('input', function () {
				var orgClassname = _this.chartSourceData.settings.org_classname;
				_this.chartSourceData.settings.org_node_text_font_size = $(this).val();
				if (orgClassname != '') {
					_this.setOrgChartCustomStyles();
				}
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-org-seslected-classname').on('input', function () {
				_this.chartOptions.selectedNodeClass = $(this).val();
				_this.chartSourceData.settings.org_selected_classname = _this.chartOptions.selectedNodeClass;
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-org-selected-node-background-color').on('input', function () {
				var orgClassname = _this.chartSourceData.settings.org_selected_classname;
				_this.chartSourceData.settings.org_selected_node_background_color = $(this).val();
				if (orgClassname != '') {
					_this.setOrgChartCustomStyles();
				}
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-org-selected-node-text-color').on('input', function () {
				var orgClassname = _this.chartSourceData.settings.org_selected_classname;
				_this.chartSourceData.settings.org_selected_node_text_color = $(this).val();
				if (orgClassname != '') {
					_this.setOrgChartCustomStyles();
				}
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-org-node-description-font-color').on('input', function () {
				_this.chartSourceData.settings.org_node_description_font_color = $(this).val();
				_this.setOrgChartCustomStyles();
			});

			_this.$el.find('#'+_this.htmlClassPrefix+'option-org-node-description-font-size').on('input', function () {
				_this.chartSourceData.settings.org_node_description_font_size = $(this).val();
				_this.setOrgChartCustomStyles();
			});

		// Slices settings
			_this.$el.find('.'+_this.htmlClassPrefix+'option-slice-color').on('input', function () {
				var id = $(this).attr('data-slice-id');
				_this.chartOptions.slices[id].color = $(this).val();
				_this.chartSourceData.settings.slice_color[id] = $(this).val();
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('.'+_this.htmlClassPrefix+'option-slice-offset').on('input', function () {
				var id = $(this).attr('data-slice-id');
				_this.chartOptions.slices[id].offset = $(this).val();
				_this.chartSourceData.settings.slice_offset[id] = $(this).val();
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('.'+_this.htmlClassPrefix+'option-slice-text-color').on('input', function () {
				var id = $(this).attr('data-slice-id');
				_this.chartOptions.slices[id].textStyle.color = $(this).val();
				_this.chartSourceData.settings.slice_text_color[id] = $(this).val();
				_this.drawChartFunction(_this.chartData, _this.chartOptions);
			});
			
		// Series settings
			_this.$el.find('.'+_this.htmlClassPrefix+'option-series-color').on('input', function () {
				if (!_this.$el.find('#'+_this.htmlClassPrefix+'option-enable-row-settings').is(':checked')) {
					var id = $(this).attr('data-series-id');
					_this.chartOptions.series[id].color = $(this).val();
					_this.chartSourceData.settings.series_color[id] = $(this).val();
					_this.drawChartFunction(_this.chartData, _this.chartOptions);
				}
			});
			
			_this.$el.find('.'+_this.htmlClassPrefix+'option-series-visible-in-legend').on('input', function () {
				if (_this.chartSourceData.source[0].length > 2) {
					var id = $(this).attr('data-series-id');
					_this.chartOptions.series[id].visibleInLegend = $(this).is(':checked');
					_this.chartSourceData.settings.series_visible_in_legend[id] = $(this).val();
					_this.drawChartFunction(_this.chartData, _this.chartOptions);
				}
			});
			
			_this.$el.find('.'+_this.htmlClassPrefix+'option-series-line-width').on('input', function () {
				if (!_this.$el.find('#'+_this.htmlClassPrefix+'option-enable-row-settings').is(':checked')) {
					var id = $(this).attr('data-series-id');
					_this.chartOptions.series[id].lineWidth  = $(this).val();
					_this.chartSourceData.settings.series_line_width[id] = $(this).val();
					_this.drawChartFunction(_this.chartData, _this.chartOptions);
				}
			});
			
			_this.$el.find('.'+_this.htmlClassPrefix+'option-series-point-size').on('input', function () {
				if (!_this.$el.find('#'+_this.htmlClassPrefix+'option-enable-row-settings').is(':checked')) {
					var id = $(this).attr('data-series-id');
					_this.chartOptions.series[id].pointSize  = $(this).val();
					_this.chartSourceData.settings.series_point_size[id] = $(this).val();
					_this.drawChartFunction(_this.chartData, _this.chartOptions);
				}
			});
			
			_this.$el.find('.'+_this.htmlClassPrefix+'option-series-point-shape').on('change', function () {
				if (!_this.$el.find('#'+_this.htmlClassPrefix+'option-enable-row-settings').is(':checked')) {
					var id = $(this).attr('data-series-id');
					_this.chartOptions.series[id].pointShape  = $(this).val();
					_this.chartSourceData.settings.series_point_shape[id] = $(this).val();
					_this.drawChartFunction(_this.chartData, _this.chartOptions);
				}
			});

		// Rows settings
			_this.$el.find('#'+_this.htmlClassPrefix+'option-enable-row-settings').on('change', function () {
				var getChartSource = _this.chartSourceData.source;
				var dataTypes = _this.multiColumnChartConvertData( getChartSource );
					
				if ($(this).is(':checked')) {
					dataTypes = _this.setRowOptions(dataTypes, _this.chartSourceData.settings, false);
					_this.chartSourceData.settings.enable_row_settings = 'checked';
				} else {
					dataTypes = _this.removeRowOptions(dataTypes);
					_this.chartSourceData.settings.enable_row_settings = '';
				}

				_this.chartData = google.visualization.arrayToDataTable(dataTypes);
				_this.chartObj.draw(_this.chartData, _this.chartOptions);
			});

			_this.$el.find('.'+_this.htmlClassPrefix+'option-rows-color').on('input', function () {
				if (_this.$el.find('#'+_this.htmlClassPrefix+'option-enable-row-settings').is(':checked')) {
					var getChartSource = _this.chartSourceData.source;
					var dataTypes = _this.multiColumnChartConvertData( getChartSource );
					
					var id = $(this).attr('data-rows-id');
					_this.chartSourceData.settings.rows_color[id] = $(this).val();
	
					dataTypes = _this.setRowOptions(dataTypes, _this.chartSourceData.settings, false);
		
					_this.chartData = google.visualization.arrayToDataTable(dataTypes);
	
					_this.chartObj.draw(_this.chartData, _this.chartOptions);
				}
			});

			_this.$el.find('.'+_this.htmlClassPrefix+'option-rows-opacity').on('input', function () {
				if (_this.$el.find('#'+_this.htmlClassPrefix+'option-enable-row-settings').is(':checked')) {
					var getChartSource = _this.chartSourceData.source;
					var dataTypes = _this.multiColumnChartConvertData( getChartSource );
					
					var id = $(this).attr('data-rows-id');
					_this.chartSourceData.settings.rows_opacity[id] = $(this).val();
	
					dataTypes = _this.setRowOptions(dataTypes, _this.chartSourceData.settings, false);
		
					_this.chartData = google.visualization.arrayToDataTable(dataTypes);
	
					_this.chartObj.draw(_this.chartData, _this.chartOptions);
				}
			});
	}

	ChartBuilderGoogleCharts.prototype.toggleQuizSelect = function (e) {
		var _this = this;

		var select = $(document).find('#'+_this.htmlClassPrefix+'select-quiz-maker-data-query');
		var option = $(select).find('option:selected').val();
		var selectQuizSelect = $(document).find('.'+_this.htmlClassPrefix+'select-quiz-maker-quiz-container');

		if (option == 'q1' || option == 'q3' || option == 'q6') {
			selectQuizSelect.slideDown();
		} else {
			selectQuizSelect.slideUp();
		}
	}
	
	ChartBuilderGoogleCharts.prototype.quizMakerIntegrationConfig = function (e) {
		var _this = this;

		var input = $(document).find('input[name="ays_source_type"]');
		if (input.val() !== 'quiz_maker') input.val('quiz_maker');

		_this.$el.find('#ays-chart-quiz-maker-success').empty();
		_this.$el.find('#ays-chart-quiz-maker-error').empty();
	}

	ChartBuilderGoogleCharts.prototype.detectManualChange = function (e) {
		var input = $(document).find('input[name="ays_source_type"]');
		if (input.val() !== 'manual') input.val('manual'); 	
	}

	ChartBuilderGoogleCharts.prototype.keyBoardConfig = function(e) {
		if (e.which == 13) {
			if ($(document).find("#ays-charts-form-google-charts").length !== 0 || $(document).find("#ays-settings-form").length !== 0) {
				var parent = $(e.target).parents('.ays-chart-chart-source-data-edit-block');
				var index = $(e.target).parents('.ays-chart-chart-source-data-input-box').index();
				index = parent.index() == 0 ? index - 1 : index - 2;
	
				if (e.shiftKey) {
					var prevRow;
					if (parent.prev('.ays-chart-chart-source-data-edit-block').length === 0) {
						return false;
					}
					prevRow = parent.prev('.ays-chart-chart-source-data-edit-block');
	
					prevRow.children('.ays-chart-chart-source-data-input-box').eq(index).find('.ays-text-input').focus();
				} else {
					var nextRow;
					if (parent.next('.ays-chart-chart-source-data-edit-block').length === 0) {
						this.addChartDataRow();
					}
					nextRow = parent.next('.ays-chart-chart-source-data-edit-block');
	
					nextRow.children('.ays-chart-chart-source-data-input-box').eq(index).find('.ays-text-input').focus();
				}
	
				return false;
			}
		}
	}

	ChartBuilderGoogleCharts.prototype.changeTabs = function(e){
		if(! $(this).hasClass('no-js')){
			var elemenetID = $(this).attr('href');
			var active_tab = $(this).attr('data-tab');
			$(document).find('.nav-tab-wrapper a.nav-tab').each(function () {
				if ($(this).hasClass('nav-tab-active')) {
					$(this).removeClass('nav-tab-active');
				}
			});
			$(this).addClass('nav-tab-active');
			$(document).find('.ays-tab-content').each(function () {
				$(this).removeClass('ays-tab-content-active');
			});
			$(document).find("[name='ays_chart_tab']").val(active_tab);
			$('.ays-tab-content' + elemenetID).addClass('ays-tab-content-active');
			e.preventDefault();
		}
	}

	ChartBuilderGoogleCharts.prototype.changeCurrentUrl = function(key){
		var linkModified = location.href.split('?')[1].split('&');
		for(var i = 0; i < linkModified.length; i++){
			if(linkModified[i].split("=")[0] == key){
				linkModified.splice(i, 1);
			}
		}
		linkModified = linkModified.join('&');
		window.history.replaceState({}, document.title, '?'+linkModified);
	}

	ChartBuilderGoogleCharts.prototype.toggleDDmenu = function(e){
		var ddmenu = $(this).next();
		var state = ddmenu.attr('data-expanded');
		switch (state) {
			case 'true':
				$(this).find('.ays_fa').css({
					transform: 'rotate(0deg)'
				});
				ddmenu.attr('data-expanded', 'false');
				break;
			case 'false':
				$(this).find('.ays_fa').css({
					transform: 'rotate(90deg)'
				});
				ddmenu.attr('data-expanded', 'true');
				break;
		}
	}

	ChartBuilderGoogleCharts.prototype.submitOnce = function(el) {
        setTimeout(function() {
			$(document).find('.ays-chart-loader-banner').attr('disabled', true);
        }, 50);

        setTimeout(function() {
            $(document).find('.ays-chart-loader-banner').attr('disabled', false);
        }, 5000);
	}

	// Load charts by given type main function
	ChartBuilderGoogleCharts.prototype.initLibraries = function (){
		var _this = this;
		
		var googleSelect = _this.$el.find('.' + _this.htmlClassPrefix + 'google-sheet-select');
		googleSelect.select2();
		var fileTypeSelect =  _this.$el.find('#' + _this.htmlClassPrefix + 'import-files-file-type');
		fileTypeSelect.select2();
		var quizQuerySelect =  _this.$el.find('#' + _this.htmlClassPrefix + 'select-quiz-maker-data-query');
		quizQuerySelect.select2();
		var quizQuizSelect =  _this.$el.find('#' + _this.htmlClassPrefix + 'select-quiz-maker-quiz');
		quizQuizSelect.select2();
		var woocommerceSelect =  _this.$el.find('#' + _this.htmlClassPrefix + 'woocommerce-datas-select');
		woocommerceSelect.select2();

		_this.$el.find('#ays-chart-option-create-author').select2({
			placeholder: aysChartBuilderAdmin.selectUser,
			minimumInputLength: 1,
			allowClear: true,
			language: {
				searching: function() {
					return aysChartBuilderAdmin.searching;
				},
				inputTooShort: function () {
					return aysChartBuilderAdmin.pleaseEnterMore;
				}
			},
			ajax: {
				url: ajaxurl,
				dataType: 'json',
				data: function (response) {
					var checkedUsers = _this.$el.find('#ays-chart-option-create-author').val();
					return {
						action: _this.ajaxAction,
						function: window.aysChartBuilderChartSettings.ajax['actions']['author_user_search'],
						security: window.aysChartBuilderChartSettings.ajax['nonces']['author_user_search'],
						params: {
							search: response.term,
							val: checkedUsers
						}
					};
				}
			}
		});

		_this.$el.find('.' + _this.htmlClassPrefix + 'chart-source-data-content').sortable({
			items: "> div." + _this.htmlClassPrefix + "chart-source-data-edit-block:not(:first)",
			handle: "." + _this.htmlClassPrefix + "chart-source-data-move-row",
            update: function (event, ui) {
                _this.$el.find('div.' + _this.htmlClassPrefix + 'chart-source-data-edit-block:not(:first)').each(function (index) {
					$(this).attr('data-source-id', index + 1);
					$(this).find("input.ays-text-input").attr('name', 'ays_chart_source_data[' + (index + 1) + '][]')
                });
            }
		});
	}

	// Load charts by given type main function
	ChartBuilderGoogleCharts.prototype.loadChartBySource = function(isChangedType = false){
		var _this = this;

		if( ! _this.chartType ){
			_this.chartType = _this.chartSourceData.chartType;
		}

		if(typeof _this.chartType !== undefined && _this.chartType){
			switch (_this.chartType) {
				case 'pie_chart':
					_this.pieChartView(isChangedType);
					break;
				case 'bar_chart':
					_this.barChartView(isChangedType);
					break;
				case 'column_chart':
					_this.columnChartView(isChangedType);
					break;
				case 'line_chart':
					_this.lineChartView(isChangedType);
					break;
				case 'org_chart':
					_this.orgChartView(isChangedType);
					break;
				case 'donut_chart':
					_this.donutChartView(isChangedType);
					break;
				default:
					_this.pieChartView(isChangedType);
					break;
			}
		}		
	}

	// Load chart by pie chart
	ChartBuilderGoogleCharts.prototype.pieChartView = function(isChangedType){
		var _this = this;
		var getChartSource = _this.chartSourceData.source;

		var dataTypes = _this.chartConvertData( getChartSource );

		var settings = _this.chartSourceData.settings;
		var nSettings =  _this.configOptionsForCharts(settings);
		
		/* == Google part == */
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			_this.chartData = isChangedType ? _this.chartData : google.visualization.arrayToDataTable( dataTypes );

			_this.chartOptions = {
				fontSize: nSettings.chartFontSize,
				chartArea: {
					left: nSettings.chartLeftMargin,
					right: nSettings.chartRightMargin,
					top: nSettings.chartTopMargin,
					bottom: nSettings.chartBottomMargin,
				},
				backgroundColor: {
					fill: nSettings.backgroundColor,
					strokeWidth: nSettings.borderWidth,
					stroke: nSettings.borderColor
				},
				enableInteractivity: nSettings.enableInteractivity,
				legend: {
					position: nSettings.legendPosition,
					alignment: nSettings.legendAlignment,
					textStyle: {
						color: nSettings.legendColor,
						fontSize: nSettings.legendFontSize,
						italic: nSettings.legendItalicText,
						bold: nSettings.legendBoldText,
					}
				},
				tooltip: { 
					trigger: nSettings.tooltipTrigger,
					showColorCode: nSettings.showColorCode,
					text: nSettings.tooltipText,
					textStyle: {
						color: nSettings.tooltipTextColor,
						fontSize: nSettings.tooltipFontSize,
						italic: nSettings.tooltipItalicText,
						bold: nSettings.tooltipBoldText,
					}
				},
				pieStartAngle: nSettings.rotationDegree,
				pieSliceBorderColor: nSettings.sliceBorderColor,
				reverseCategories: nSettings.reverseCategories,
				pieSliceText: nSettings.sliceText,
				sliceVisibilityThreshold: nSettings.dataGroupingLimit,
				pieResidueSliceLabel: nSettings.dataGroupingLabel,
				pieResidueSliceColor: nSettings.dataGroupingColor,
				slices: {}
			};

			for (var i = 0; i < dataTypes.length - 1; i++) {
				_this.chartOptions.slices[i] = {
					color: nSettings.sliceColor[i],
					offset: typeof nSettings.sliceOffset[i] !== 'undefined' ? nSettings.sliceOffset[i] : 0,
					textStyle: {
						color: nSettings.sliceTextColor[i],
					},
				}
			}

			_this.chartObj = new google.visualization.PieChart( document.getElementById(_this.htmlClassPrefix + _this.chartType) );

			_this.chartObj.draw( _this.chartData, _this.chartOptions );
			_this.resizeChart();
		}
		/* */
	}

	// Load chart by bar chart
	ChartBuilderGoogleCharts.prototype.barChartView = function(isChangedType){
		var _this = this;
		var getChartSource = _this.chartSourceData.source;
		var dataTypes = _this.multiColumnChartConvertData( getChartSource );

		var settings = _this.chartSourceData.settings;
		var nSettings =  _this.configOptionsForCharts(settings);
		
		/* == Google part == */
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			dataTypes = nSettings.enableRowSettings ? _this.setRowOptions(dataTypes, nSettings, true) : dataTypes;

			_this.chartData = isChangedType ? _this.chartData : google.visualization.arrayToDataTable(dataTypes);

			_this.chartOptions = {
				fontSize: nSettings.chartFontSize,
				backgroundColor: {
					fill: nSettings.backgroundColor,
					strokeWidth: nSettings.borderWidth,
					stroke: nSettings.borderColor
				},
				chartArea: {
					backgroundColor: {
						fill: nSettings.chartBackgroundColor,
						stroke: nSettings.chartBorderColor,
						strokeWidth: nSettings.chartBorderWidth
					},
					left: nSettings.chartLeftMargin,
					right: nSettings.chartRightMargin,
					top: nSettings.chartTopMargin,
					bottom: nSettings.chartBottomMargin,
				},
				enableInteractivity: nSettings.enableInteractivity,
				theme: nSettings.maximizedView,
				legend: {
					position: nSettings.legendPosition,
					alignment: nSettings.legendAlignment,
					textStyle: {
						color: nSettings.legendColor,
						fontSize: nSettings.legendFontSize,
						italic: nSettings.legendItalicText,
						bold: nSettings.legendBoldText,
					}
				},
				tooltip: { 
					trigger: nSettings.tooltipTrigger,
					showColorCode: nSettings.showColorCode,
					textStyle: {
						color: nSettings.tooltipTextColor,
						fontSize: nSettings.tooltipFontSize,
						italic: nSettings.tooltipItalicText,
						bold: nSettings.tooltipBoldText,
					}
				},
				hAxis: {
					title: nSettings.hAxisTitle,
					textPosition: nSettings.hAxisTextPosition,
					direction: nSettings.hAxisDirection,
					baselineColor: nSettings.hAxisBaselineColor,
					textStyle: {
						color: nSettings.hAxisTextColor,
						fontSize: nSettings.hAxisTextFontSize,
						italic: nSettings.hAxisItalicText,
						bold: nSettings.hAxisBoldText,
					},
					slantedText: nSettings.hAxisSlantedText,
					slantedTextAngle: nSettings.hAxisSlantedTextAngle,
					format: nSettings.hAxisFormat,
					titleTextStyle: {
						fontSize: nSettings.hAxisLabelFontSize,
						color: nSettings.hAxisLabelColor,
						italic: nSettings.hAxisItalicTitle,
						bold: nSettings.hAxisBoldTitle,
					},
					viewWindow: {
						min: nSettings.hAxisMinValue,
						max: nSettings.hAxisMaxValue,
					},
					gridlines: {
						count: nSettings.hAxisGridlinesCount,
						color: nSettings.hAxisGridlinesColor,
					},
					minorGridlines: {
						color: nSettings.hAxisMinorGridlinesColor,
					},
				},
				vAxis: {
					title: nSettings.vAxisTitle,
					textPosition: nSettings.vAxisTextPosition,
					direction: nSettings.vAxisDirection,
					baselineColor: nSettings.vAxisBaselineColor,
					textStyle: {
						color: nSettings.vAxisTextColor,
						fontSize: nSettings.vAxisTextFontSize,
						italic: nSettings.vAxisItalicText,
						bold: nSettings.vAxisBoldText,
					},
					format: nSettings.vAxisFormat,
					titleTextStyle: {
						fontSize: nSettings.vAxisLabelFontSize,
						color: nSettings.vAxisLabelColor,
						italic: nSettings.vAxisItalicTitle,
						bold: nSettings.vAxisBoldTitle,
					},
					viewWindow: {
						min: nSettings.vAxisMinValue,
						max: nSettings.vAxisMaxValue,
					},
					gridlines: {
						count: nSettings.vAxisGridlinesCount,
						color: nSettings.vAxisGridlinesColor,
					},
					minorGridlines: {
						color: nSettings.vAxisMinorGridlinesColor,
					},
				},
				focusTarget: nSettings.focusTarget,
				isStacked: nSettings.isStacked,
				dataOpacity: nSettings.opacity,
				bar: {
					groupWidth: nSettings.groupWidth
				},
				series: {},
			};

			var seriesRows = dataTypes[0].filter(item => typeof item === 'string');
			for (var i = 0; i < seriesRows.length - 1; i++) {
				_this.chartOptions.series[i] = {
					color: nSettings.seriesColor[i],
					visibleInLegend: nSettings.seriesVisibleInLegend[i] == 'on' ? true : (typeof nSettings.seriesColor[i] !== 'undefined' ? false : true),
				}
			}

			if (nSettings.enableAnimation) {
				_this.chartOptions.animation = {
					startup: nSettings.animationStartup,
					duration: nSettings.animationDuration,
					easing: nSettings.animationEasing,
				}
			}

			_this.chartObj = new google.visualization.BarChart(document.getElementById(_this.htmlClassPrefix + _this.chartType));

			_this.chartObj.draw( _this.chartData, _this.chartOptions );
			_this.resizeChart();
		}
		/* */
	}

	// Load chart by column chart
	ChartBuilderGoogleCharts.prototype.columnChartView = function(isChangedType){
		var _this = this;
		var getChartSource = _this.chartSourceData.source;

		var dataTypes = _this.multiColumnChartConvertData( getChartSource );

		var settings = _this.chartSourceData.settings;
		var nSettings =  _this.configOptionsForCharts(settings);

		/* == Google part == */
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			dataTypes = nSettings.enableRowSettings ? _this.setRowOptions(dataTypes, nSettings, true) : dataTypes;

			_this.chartData = isChangedType ? _this.chartData : google.visualization.arrayToDataTable(dataTypes);

			_this.chartOptions = {
				fontSize: nSettings.chartFontSize,
				backgroundColor: {
					fill: nSettings.backgroundColor,
					strokeWidth: nSettings.borderWidth,
					stroke: nSettings.borderColor
				},
				chartArea: {
					backgroundColor: {
						fill: nSettings.chartBackgroundColor,
						stroke: nSettings.chartBorderColor,
						strokeWidth: nSettings.chartBorderWidth
					},
					left: nSettings.chartLeftMargin,
					right: nSettings.chartRightMargin,
					top: nSettings.chartTopMargin,
					bottom: nSettings.chartBottomMargin,
				},
				enableInteractivity: nSettings.enableInteractivity,
				theme: nSettings.maximizedView,
				legend: {
					position: nSettings.legendPosition,
					alignment: nSettings.legendAlignment,
					textStyle: {
						color: nSettings.legendColor,
						fontSize: nSettings.legendFontSize,
						italic: nSettings.legendItalicText,
						bold: nSettings.legendBoldText,
					}
				},
				tooltip: { 
					trigger: nSettings.tooltipTrigger,
					showColorCode: nSettings.showColorCode,
					textStyle: {
						color: nSettings.tooltipTextColor,
						fontSize: nSettings.tooltipFontSize,
						italic: nSettings.tooltipItalicText,
						bold: nSettings.tooltipBoldText,
					}
				},
				hAxis: {
					title: nSettings.hAxisTitle,
					textPosition: nSettings.hAxisTextPosition,
					direction: nSettings.hAxisDirection,
					baselineColor: nSettings.hAxisBaselineColor,
					textStyle: {
						color: nSettings.hAxisTextColor,
						fontSize: nSettings.hAxisTextFontSize,
						italic: nSettings.hAxisItalicText,
						bold: nSettings.hAxisBoldText,
					},
					slantedText: nSettings.hAxisSlantedText,
					slantedTextAngle: nSettings.hAxisSlantedTextAngle,
					format: nSettings.hAxisFormat,
					titleTextStyle: {
						fontSize: nSettings.hAxisLabelFontSize,
						color: nSettings.hAxisLabelColor,
						italic: nSettings.hAxisItalicTitle,
						bold: nSettings.hAxisBoldTitle,
					},
					viewWindow: {
						min: nSettings.hAxisMinValue,
						max: nSettings.hAxisMaxValue,
					},
					gridlines: {
						count: nSettings.hAxisGridlinesCount,
						color: nSettings.hAxisGridlinesColor,
					},
					minorGridlines: {
						color: nSettings.hAxisMinorGridlinesColor,
					},
					showTextEvery: nSettings.hAxisShowTextEvery
				},
				vAxis: {
					title: nSettings.vAxisTitle,
					textPosition: nSettings.vAxisTextPosition,
					direction: nSettings.vAxisDirection,
					baselineColor: nSettings.vAxisBaselineColor,
					textStyle: {
						color: nSettings.vAxisTextColor,
						fontSize: nSettings.vAxisTextFontSize,
						italic: nSettings.vAxisItalicText,
						bold: nSettings.vAxisBoldText,
					},
					format: nSettings.vAxisFormat,
					titleTextStyle: {
						fontSize: nSettings.vAxisLabelFontSize,
						color: nSettings.vAxisLabelColor,
						italic: nSettings.vAxisItalicTitle,
						bold: nSettings.vAxisBoldTitle,
					},
					viewWindow: {
						min: nSettings.vAxisMinValue,
						max: nSettings.vAxisMaxValue,
					},
					gridlines: {
						count: nSettings.vAxisGridlinesCount,
						color: nSettings.vAxisGridlinesColor,
					},
					minorGridlines: {
						color: nSettings.vAxisMinorGridlinesColor,
					},
				},
				focusTarget: nSettings.focusTarget,
				isStacked: nSettings.isStacked,
				dataOpacity: nSettings.opacity,
				bar: {
					groupWidth: nSettings.groupWidth
				},
				series: {},
			};

			var seriesRows = dataTypes[0].filter(item => typeof item === 'string');
			for (var i = 0; i < seriesRows.length - 1; i++) {
				_this.chartOptions.series[i] = {
					color: nSettings.seriesColor[i],
					visibleInLegend: nSettings.seriesVisibleInLegend[i] == 'on' ? true : (typeof nSettings.seriesColor[i] !== 'undefined' ? false : true),
				}
			}

			if (nSettings.enableAnimation) {
				_this.chartOptions.animation = {
					startup: nSettings.animationStartup,
					duration: nSettings.animationDuration,
        			easing: nSettings.animationEasing,
				}
			}

			_this.chartObj = new google.visualization.ColumnChart(document.getElementById(_this.htmlClassPrefix + _this.chartType));

			_this.chartObj.draw( _this.chartData, _this.chartOptions );
			_this.resizeChart();
		}
		/* */
	}

	// Load chart by line chart
	ChartBuilderGoogleCharts.prototype.lineChartView = function(isChangedType){
		var _this = this;
		var getChartSource = _this.chartSourceData.source;
		var dataTypes = _this.multiColumnChartConvertData(getChartSource);

		var settings = _this.chartSourceData.settings;
		var nSettings =  _this.configOptionsForCharts(settings);

		/* == Google part == */
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			dataTypes = nSettings.enableRowSettings ? _this.setRowOptions(dataTypes, nSettings, true) : dataTypes;

			_this.chartData = isChangedType ? _this.chartData : google.visualization.arrayToDataTable(dataTypes);

			_this.chartOptions = {
				fontSize: nSettings.chartFontSize,
				backgroundColor: {
					fill: nSettings.backgroundColor,
					strokeWidth: nSettings.borderWidth,
					stroke: nSettings.borderColor
				},
				chartArea: {
					backgroundColor: {
						fill: nSettings.chartBackgroundColor,
						stroke: nSettings.chartBorderColor,
						strokeWidth: nSettings.chartBorderWidth
					},
					left: nSettings.chartLeftMargin,
					right: nSettings.chartRightMargin,
					top: nSettings.chartTopMargin,
					bottom: nSettings.chartBottomMargin,
				},
				enableInteractivity: nSettings.enableInteractivity,
				theme: nSettings.maximizedView,
				legend: {
					position: nSettings.legendPosition,
					alignment: nSettings.legendAlignment,
					textStyle: {
						color: nSettings.legendColor,
						fontSize: nSettings.legendFontSize,
						italic: nSettings.legendItalicText,
						bold: nSettings.legendBoldText,
					}
				},
				tooltip: { 
					trigger: nSettings.tooltipTrigger,
					showColorCode: nSettings.showColorCode,
					textStyle: {
						color: nSettings.tooltipTextColor,
						fontSize: nSettings.tooltipFontSize,
						italic: nSettings.tooltipItalicText,
						bold: nSettings.tooltipBoldText,
					}
				},
				hAxis: {
					title: nSettings.hAxisTitle,
					textPosition: nSettings.hAxisTextPosition,
					direction: nSettings.hAxisDirection,
					baselineColor: nSettings.hAxisBaselineColor,
					textStyle: {
						color: nSettings.hAxisTextColor,
						fontSize: nSettings.hAxisTextFontSize,
						italic: nSettings.hAxisItalicText,
						bold: nSettings.hAxisBoldText,
					},
					slantedText: nSettings.hAxisSlantedText,
					slantedTextAngle: nSettings.hAxisSlantedTextAngle,
					format: nSettings.hAxisFormat,
					titleTextStyle: {
						fontSize: nSettings.hAxisLabelFontSize,
						color: nSettings.hAxisLabelColor,
						italic: nSettings.hAxisItalicTitle,
						bold: nSettings.hAxisBoldTitle,
					},
					viewWindow: {
						min: nSettings.hAxisMinValue,
						max: nSettings.hAxisMaxValue,
					},
					gridlines: {
						count: nSettings.hAxisGridlinesCount,
						color: nSettings.hAxisGridlinesColor,
					},
					minorGridlines: {
						color: nSettings.hAxisMinorGridlinesColor,
					},
					showTextEvery: nSettings.hAxisShowTextEvery
				},
				vAxis: {
					title: nSettings.vAxisTitle,
					textPosition: nSettings.vAxisTextPosition,
					direction: nSettings.vAxisDirection,
					baselineColor: nSettings.vAxisBaselineColor,
					textStyle: {
						color: nSettings.vAxisTextColor,
						fontSize: nSettings.vAxisTextFontSize,
						italic: nSettings.vAxisItalicText,
						bold: nSettings.vAxisBoldText,
					},
					format: nSettings.vAxisFormat,
					titleTextStyle: {
						fontSize: nSettings.vAxisLabelFontSize,
						color: nSettings.vAxisLabelColor,
						italic: nSettings.vAxisItalicTitle,
						bold: nSettings.vAxisBoldTitle,
					},
					viewWindow: {
						min: nSettings.vAxisMinValue,
						max: nSettings.vAxisMaxValue,
					},
					gridlines: {
						count: nSettings.vAxisGridlinesCount,
						color: nSettings.vAxisGridlinesColor,
					},
					minorGridlines: {
						color: nSettings.vAxisMinorGridlinesColor,
					},
				},
				crosshair: {
					opacity: nSettings.crosshairOpacity,
					orientation: nSettings.crosshairOrientation,
					trigger: nSettings.crosshairTrigger,
				},
				focusTarget: nSettings.focusTarget,
				dataOpacity: nSettings.opacity,
				lineWidth: nSettings.lineWidth,
				selectionMode: nSettings.multipleSelection,
				aggregationTarget: nSettings.multipleDataFormat,
				pointShape: nSettings.pointShape,
				pointSize: nSettings.pointSize,
				orientation: nSettings.orientation,
				interpolateNulls: nSettings.fillNulls,
				lineDashStyle: nSettings.dashStyle,
				series: {},
			};

			var seriesRows = dataTypes[0].filter(item => typeof item === 'string');
			for (var i = 0; i < seriesRows.length - 1; i++) {
				_this.chartOptions.series[i] = {
					color: nSettings.seriesColor[i],
					visibleInLegend: nSettings.seriesVisibleInLegend[i] == 'on' ? true : (typeof nSettings.seriesColor[i] !== 'undefined' ? false : true),
					lineWidth: nSettings.seriesLineWidth[i],
					pointSize: nSettings.seriesPointSize[i],
					pointShape: nSettings.seriesPointShape[i],
				}
			}

			if (nSettings.enableAnimation) {
				_this.chartOptions.animation = {
					startup: nSettings.animationStartup,
					duration: nSettings.animationDuration,
        			easing: nSettings.animationEasing,
				}
			}

			_this.chartObj = new google.visualization.LineChart(document.getElementById(_this.htmlClassPrefix + _this.chartType));

			_this.chartObj.draw( _this.chartData, _this.chartOptions );
			_this.resizeChart();
		}
		/* */
	}

	// Load chart by donut chart
	ChartBuilderGoogleCharts.prototype.donutChartView = function(isChangedType){
		var _this = this;
		var getChartSource = _this.chartSourceData.source;
		var dataTypes = _this.chartConvertData( getChartSource );

		var settings = _this.chartSourceData.settings;
		var nSettings =  _this.configOptionsForCharts(settings);

		/* == Google part == */
		google.charts.load('current', {'packages':['corechart']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			_this.chartData = isChangedType ? _this.chartData : google.visualization.arrayToDataTable( dataTypes );

			_this.chartOptions = {
				fontSize: nSettings.chartFontSize,
				chartArea: {
					left: nSettings.chartLeftMargin,
					right: nSettings.chartRightMargin,
					top: nSettings.chartTopMargin,
					bottom: nSettings.chartBottomMargin,
				},
				backgroundColor: {
					fill: nSettings.backgroundColor,
					strokeWidth: nSettings.borderWidth,
					stroke: nSettings.borderColor
				},
				enableInteractivity: nSettings.enableInteractivity,
				legend: {
					position: nSettings.legendPosition,
					alignment: nSettings.legendAlignment,
					textStyle: {
						color: nSettings.legendColor,
						fontSize: nSettings.legendFontSize,
						italic: nSettings.legendItalicText,
						bold: nSettings.legendBoldText,
					}
				},
				tooltip: { 
					trigger: nSettings.tooltipTrigger,
					showColorCode: nSettings.showColorCode,
					text: nSettings.tooltipText,
					textStyle: {
						color: nSettings.tooltipTextColor,
						fontSize: nSettings.tooltipFontSize,
						italic: nSettings.tooltipItalicText,
						bold: nSettings.tooltipBoldText,
					}
				},
				pieStartAngle: nSettings.rotationDegree,
				pieSliceBorderColor: nSettings.sliceBorderColor,
				reverseCategories: nSettings.reverseCategories,
				pieSliceText: nSettings.sliceText,
				sliceVisibilityThreshold: nSettings.dataGroupingLimit,
				pieResidueSliceLabel: nSettings.dataGroupingLabel,
				pieResidueSliceColor: nSettings.dataGroupingColor,
				pieHole: nSettings.holeSize,
				slices: {}
			};

			for (var i = 0; i < dataTypes.length - 1; i++) {
				_this.chartOptions.slices[i] = {
					color: nSettings.sliceColor[i],
					offset: typeof nSettings.sliceOffset[i] !== 'undefined' ? nSettings.sliceOffset[i] : 0,
					textStyle: {
						color: nSettings.sliceTextColor[i],
					},
				}
			}

			_this.chartObj = new google.visualization.PieChart( document.getElementById(_this.htmlClassPrefix + _this.chartType) );

			_this.chartObj.draw( _this.chartData, _this.chartOptions );
			_this.resizeChart();
		}
		/* */
	}
	
	// Load chart by org chart
	ChartBuilderGoogleCharts.prototype.orgChartView = function(isChangedType){
		var _this = this;
		var getChartSource = _this.chartSourceData.source;
		var dataTypes = _this.orgChartConvertData(getChartSource);
		var treeData = _this.orgChartConvertDataForTreeManual(getChartSource);

		var settings = _this.chartSourceData.settings;
		var nSettings =  _this.configOptionsForCharts(settings);

		/* == Google part == */
		google.charts.load('current', {'packages':['orgchart']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			_this.chartDataArray = isChangedType ? _this.chartDataArray : dataTypes;
			_this.chartData = google.visualization.arrayToDataTable(_this.chartDataArray);

			var view = new google.visualization.DataView(_this.chartData);
    		view.setColumns([0, 1, 2]);

			_this.chartOptions = {
				allowHtml: true,
				size: nSettings.orgChartFontSize,
				allowCollapse: nSettings.allowCollapse,
				compactRows: true,
				nodeClass: nSettings.orgClassname,
				selectedNodeClass: nSettings.orgSelectedClassname,
			};

			_this.chartObj = new google.visualization.OrgChart(document.getElementById(_this.htmlClassPrefix + _this.chartType));

			google.visualization.events.addListener(_this.chartObj, 'select', function () {
				var selection = _this.chartObj.getSelection();
				if (selection.length > 0) {
					if (_this.chartData.getValue(selection[0].row, 3) != '') {
						window.open(_this.chartData.getValue(selection[0].row, 3), '_blank');
					}
				}
			});
			
			google.visualization.events.addListener(_this.chartObj, 'collapse', function () {
				_this.setOrgChartCustomStyles();
			});

			_this.chartObj.draw( view, _this.chartOptions );
			_this.resizeChart();
			_this.setOrgChartCustomStyles();
		}

		_this.$el.on('click', '.google-visualization-orgchart-node-small, .google-visualization-orgchart-node-medium, .google-visualization-orgchart-node-large', function(e) {
			if (nSettings.orgSelectedClassname !== '') {
				_this.setOrgChartCustomStyles();
			}
		});

		var treeManualId = '#ays-chart-chart-source-data-edit-tree-content';
		var sortable = new TreeSortable({
			treeSelector: treeManualId,
		});
		var $treeManual = $(treeManualId);
		var $content = treeData.map(sortable.createBranch);
		$treeManual.html($content);
		sortable.run();

		sortable.addListener('click', '.add-child', function (event, instance) {
			instance.addChildBranch($(event.target), treeData);
		});
		sortable.addListener('click', '.add-sibling', function (event, instance) {
			instance.addSiblingBranch($(event.target), treeData);
		});

		sortable.addListener('click', '.remove-branch', function (event, instance) {
			event.preventDefault();
			var confirm = window.confirm('Are you sure you want to delete this branch?');
			if (!confirm) {
				return;
			}
			instance.removeBranch($(event.target));
		});

		sortable.addListener('click', '.open-branch-options', function (event, instance) {
			instance.openBranchOptions($(event.target));
		})

		sortable.addListener('click', '.close-branch-options', function (event, instance) {
			instance.closeBranchOptions($(event.target));
		})

		sortable.addListener('click', '.change-branch-options', function (event, instance) {
			instance.changeBranchOptions($(event.target));
		})
		
		tippy('[data-tippy-content]');
	}

	/* 
	  Configure all settings for all chart types
	  Getting settings for each chart type in respective function 
	*/
	ChartBuilderGoogleCharts.prototype.configOptionsForCharts = function (settings) {
		var newSettings = {};

		newSettings.chartFontSize = settings['font_size'];
		newSettings.backgroundColor = settings['transparent_background'] && settings['transparent_background'] === 'checked' ? 'transparent' : settings['background_color'];
		newSettings.borderWidth = settings['border_width'];
		newSettings.borderColor = settings['border_color'];
		newSettings.tooltipTrigger = settings['tooltip_trigger'];
		newSettings.tooltipText = settings['tooltip_text'];
		newSettings.showColorCode = (settings['show_color_code'] == 'checked') ? true : false;
		newSettings.tooltipItalicText = (settings['tooltip_italic'] == 'checked') ? true : false;
		newSettings.tooltipBoldText = settings['tooltip_bold'];
		newSettings.legendItalicText = (settings['legend_italic'] == 'checked') ? true : false;
		newSettings.legendBoldText = (settings['legend_bold'] == 'checked') ? true : false;
		newSettings.tooltipTextColor = settings['tooltip_text_color'];
		newSettings.tooltipFontSize = settings['tooltip_font_size'];
		newSettings.legendPosition = settings['legend_position'];
		newSettings.legendAlignment = settings['legend_alignment'];
		newSettings.legendFontSize = settings['legend_font_size'];
		newSettings.rotationDegree = settings['rotation_degree'];
		newSettings.sliceBorderColor = settings['slice_border_color'];
		newSettings.reverseCategories = (settings['reverse_categories'] == 'checked') ? true : false;
		newSettings.sliceText = settings['slice_text'];
		newSettings.legendColor = settings['legend_color'];
		newSettings.dataGroupingLimit = settings['data_grouping_limit']/100;
		newSettings.dataGroupingLabel = settings['data_grouping_label'];
		newSettings.dataGroupingColor = settings['data_grouping_color'];
		newSettings.sliceColor = settings['slice_color'];
		newSettings.sliceOffset = settings['slice_offset'];
		newSettings.sliceTextColor = settings['slice_text_color'];
		newSettings.chartBackgroundColor = settings['transparent_background'] && settings['transparent_background'] === 'checked' ? 'transparent' : settings['chart_background_color'];
		newSettings.chartBorderWidth = settings['chart_border_width'];
		newSettings.chartBorderColor = settings['chart_border_color'];
		newSettings.chartLeftMargin = settings['chart_left_margin_for_js'];
		newSettings.chartRightMargin = settings['chart_right_margin_for_js'];
		newSettings.chartTopMargin = settings['chart_top_margin_for_js'];
		newSettings.chartBottomMargin = settings['chart_bottom_margin_for_js'];
		newSettings.isStacked = (settings['is_stacked'] == 'checked') ? true : false;
		newSettings.focusTarget = settings['focus_target'];
		newSettings.groupWidthFormat = settings['group_width_format'] == '%' ? '%' : '';
		newSettings.groupWidth = settings['group_width'] + newSettings.groupWidthFormat;
		newSettings.hAxisTitle = settings['haxis_title'];
		newSettings.vAxisTitle = settings['vaxis_title'];
		newSettings.hAxisLabelFontSize = settings['haxis_label_font_size'];
		newSettings.vAxisLabelFontSize = settings['vaxis_label_font_size'];
		newSettings.hAxisLabelColor = settings['haxis_label_color'];
		newSettings.vAxisLabelColor = settings['vaxis_label_color'];
		newSettings.hAxisTextPosition = settings['haxis_text_position'];
		newSettings.vAxisTextPosition = settings['vaxis_text_position'];
		newSettings.vAxisDirection = (settings['vaxis_direction'] == 'checked') ? -1 : 1;
		newSettings.hAxisDirection = (settings['haxis_direction'] == 'checked') ? -1 : 1;
		newSettings.hAxisTextColor = settings['haxis_text_color'];
		newSettings.vAxisTextColor = settings['vaxis_text_color'];
		newSettings.hAxisBaselineColor = settings['haxis_baseline_color'];
		newSettings.vAxisBaselineColor = settings['vaxis_baseline_color'];
		newSettings.hAxisTextFontSize = settings['haxis_text_font_size'];
		newSettings.vAxisTextFontSize = settings['vaxis_text_font_size'];
		newSettings.hAxisSlantedText = settings['haxis_slanted'];
		newSettings.hAxisSlantedTextAngle = settings['haxis_slanted_text_angle'];
		newSettings.hAxisShowTextEvery = settings['haxis_show_text_every'];
		newSettings.vAxisFormat = settings['vaxis_format'];
		newSettings.hAxisFormat = settings['haxis_format'];
		newSettings.hAxisMinValue = settings['haxis_min_value'];
		newSettings.hAxisMaxValue = settings['haxis_max_value'];
		newSettings.vAxisMinValue = settings['vaxis_min_value'];
		newSettings.vAxisMaxValue = settings['vaxis_max_value'];
		newSettings.hAxisGridlinesCount = settings['haxis_gridlines_count'];
		newSettings.hAxisGridlinesColor = settings['haxis_gridlines_color'];
		newSettings.hAxisMinorGridlinesColor = settings['haxis_minor_gridlines_color'];
		newSettings.vAxisGridlinesCount = settings['vaxis_gridlines_count'];
		newSettings.vAxisGridlinesColor = settings['vaxis_gridlines_color'];
		newSettings.vAxisMinorGridlinesColor = settings['vaxis_minor_gridlines_color'];
		newSettings.hAxisItalicText = (settings['haxis_italic'] == 'checked') ? true : false;
		newSettings.hAxisBoldText = (settings['haxis_bold'] == 'checked') ? true : false;
		newSettings.vAxisItalicText = (settings['vaxis_italic'] == 'checked') ? true : false;
		newSettings.vAxisBoldText = (settings['vaxis_bold'] == 'checked') ? true : false;
		newSettings.hAxisItalicTitle = (settings['haxis_title_italic'] == 'checked') ? true : false;
		newSettings.hAxisBoldTitle = (settings['haxis_title_bold'] == 'checked') ? true : false;
		newSettings.vAxisItalicTitle = (settings['vaxis_title_italic'] == 'checked') ? true : false;
		newSettings.vAxisBoldTitle = (settings['vaxis_title_bold'] == 'checked') ? true : false;
		newSettings.opacity = settings['opacity'];
		newSettings.enableInteractivity = (settings['enable_interactivity'] == 'checked') ? true : false;
		newSettings.maximizedView = (settings['maximized_view'] == 'checked') ? 'maximized' : null;
		newSettings.enableAnimation = (settings['enable_animation'] == 'checked') ? true : false;
		newSettings.animationDuration = settings['animation_duration'];
		newSettings.animationStartup = (settings['animation_startup'] == 'checked') ? true : false;
		newSettings.animationEasing = settings['animation_easing'];
		newSettings.seriesColor = settings['series_color'];
		newSettings.seriesVisibleInLegend = settings['series_visible_in_legend'];
		newSettings.seriesLineWidth = settings['series_line_width'];
		newSettings.seriesPointSize = settings['series_point_size'];
		newSettings.seriesPointShape = settings['series_point_shape'];
		newSettings.enableRowSettings = (settings['enable_row_settings'] == 'checked') ? true : false;
		newSettings.rowsColor = settings['rows_color'];
		newSettings.rowsOpacity = settings['rows_opacity'];
		newSettings.multipleSelection = (settings['multiple_selection'] == 'checked') ? 'multiple' : 'single';
		newSettings.multipleDataFormat = settings['multiple_data_format'];
		newSettings.pointShape = settings['point_shape'];
		newSettings.pointSize = settings['point_size'];
		newSettings.lineWidth = settings['line_width'];
		newSettings.crosshairTrigger = settings['crosshair_trigger'];
		newSettings.crosshairOrientation = settings['crosshair_orientation'];
		newSettings.crosshairOpacity = settings['crosshair_opacity'];
		newSettings.dashStyle = settings['dash_style'] ? settings['dash_style'].split(',') : null;
		newSettings.orientation = (settings['orientation'] == 'checked') ? 'vertical' : 'horizontal';
		newSettings.fillNulls = (settings['fill_nulls'] == 'checked') ? true : false;
		newSettings.holeSize = settings['donut_hole_size'];
		newSettings.orgChartFontSize = settings['org_chart_font_size'];
		newSettings.allowCollapse = (settings['allow_collapse'] == 'checked') ? true : false;
		newSettings.orgClassname = settings['org_classname'];
		newSettings.orgSelectedClassname = settings['org_selected_classname'];

		return newSettings;
	}

	ChartBuilderGoogleCharts.prototype.setRowOptions = function (data, options, isJS = true) {
		var _this = this;

		if (data && data.length > 0) {
			if (data[0] && data[0].length == 2) {
				data[0].push({ role: 'style' });
				for (var i = 1; i < data.length; i++) {
					var opts = [];

					var color = isJS ? options.rowsColor[i - 1] : options.rows_color[i - 1];
					opts.push(color ? 'color:'+color : '');

					var opacity = isJS ? options.rowsOpacity[i - 1] : options.rows_opacity[i - 1];
					opts.push(opacity ? 'opacity:'+opacity : '');


					data[i].push(opts.join(';'));
				}
			}
		}

		return data;
	}
	
	ChartBuilderGoogleCharts.prototype.removeRowOptions = function (data) {
		var _this = this;

		if (data && data.length > 0) {
			if (data[0] && data[0].length == 2) {
				for (var i = 0; i < data.length; i++) {
					data[i].splice(2, 1);
				}
			}
		}

		return data;
	}

	// Detect window resize moment to draw charts responsively 
	ChartBuilderGoogleCharts.prototype.resizeChart = function(){
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
			_this.drawChartFunction( _this.chartData, _this.chartOptions );
		});
		
	}

	ChartBuilderGoogleCharts.prototype.setOrgChartCustomStyles = function () {
		var _this = this;

		var orgClassname = _this.chartSourceData.settings.org_classname;
		var bgColor = _this.chartSourceData.settings.org_node_background_color;
		var padding = _this.chartSourceData.settings.org_node_padding;
		var borderRadius = _this.chartSourceData.settings.org_node_border_radius;
		var borderWidth = _this.chartSourceData.settings.org_node_border_width;
		var borderColor = _this.chartSourceData.settings.org_node_border_color;
		var textColor = _this.chartSourceData.settings.org_node_text_color;
		var textSize = _this.chartSourceData.settings.org_node_text_font_size;
		var descriptionColor = _this.chartSourceData.settings.org_node_description_font_color;
		var descriptionSize = _this.chartSourceData.settings.org_node_description_font_size;
		
		var orgSelectedClassname = _this.chartSourceData.settings.org_selected_classname;
		var selectedBgColor = _this.chartSourceData.settings.org_selected_node_background_color;
		var selectedTextColor = _this.chartSourceData.settings.org_selected_node_text_color;
		
		if (orgClassname != '') {
			var node = _this.$el.find('.' + orgClassname);
			node.css({
				'background-color' : bgColor,
				'padding' : padding + 'px',
				'border-radius' : borderRadius + 'px',
				'color' : textColor,
				'font-size' : textSize + 'px',
				'border' : 'none',
				'outline' : borderWidth + 'px solid ' + borderColor,
			});
		}

		if (orgSelectedClassname != '') {
			var selectedNode = _this.$el.find('.' + orgSelectedClassname);
			selectedNode.css({
				'background-color' : selectedBgColor,
				// 'padding' : padding + 'px',
				// 'border-radius' : borderRadius + 'px',
				'color' : selectedTextColor,
				// 'font-size' : textSize + 'px',
				// 'border' : 'none',
				// 'outline' : borderWidth + 'px solid ' + borderColor,
			});
		}

		var description = _this.$el.find('.' + _this.htmlClassPrefix + 'org-chart-tree-description');
		description.css({
			'color' : descriptionColor,
			'font-size' : descriptionSize + 'px',
		});
		
		// var image = _this.$el.find('.' + _this.htmlClassPrefix + 'org-chart-tree-image');
	}

	ChartBuilderGoogleCharts.prototype.chartConvertData = function( data ){
		var _this = this;
		var dataTypes = [];

		// Collect data in new array for chart rendering
		for ( var key in data ) {
			if ( data.hasOwnProperty( key ) ) {
				if (key == 0) {
					if (data[key][0] != '' && data[key][1] != '') {
						dataTypes.push([
							_this.htmlDecode(data[key][0]), _this.htmlDecode(data[key][1])
						]);
					}
				} else {
					if (data[key][0] != '' && data[key][1] != '') {
						dataTypes.push([
							_this.htmlDecode(data[key][0]), +(data[key][1])
						]);
					}
				}
			}
		}

		return dataTypes;
	}

	// Converting chart data for multicolumn chart
	ChartBuilderGoogleCharts.prototype.multiColumnChartConvertData = function( data ){
		var _this = this;
		var dataTypes = [];
		var titleRow = [];

		for (var key in data) {
			var dataRow = [];
			if (data.hasOwnProperty(key)) {
				if (key == 0) {
					for (var index in data[key]) {
						if (data[key][index] != '') {
							titleRow.push(_this.htmlDecode(data[key][index]));
						}
					}
					dataTypes.push(titleRow);
				} else {
					for (var index in data[key]) {
						if (data[key][index] != '') {
							if  (index == 0) {
								dataRow.push(_this.htmlDecode(data[key][index]));
							} else {
								dataRow.push(+data[key][index]);
							}
						}
					}
					dataTypes.push(dataRow);
				}
			}
		}

		return dataTypes;
	}

	// Converting chart data for org chart type
	ChartBuilderGoogleCharts.prototype.orgChartConvertData = function( data ){
		var _this = this;
		var dataTypes = [['Name', 'Manager', 'Tooltip', 'Url']];
		// var name = "";
		// Collect data in new array for chart rendering
		for ( var key in data ) {
			if ( data.hasOwnProperty( key ) ) {
				if (key != 0) {
					var name = data[key][0];
					var description = data[key][1];
					var image = (data[key].length > 6) ? data[key][2] : '';
					var parent_name = (data[key].length > 6) ? data[key][3] : data[key][2];
					var tooltip = (data[key].length > 6) ? data[key][4] : data[key][3];
					var url = (data[key].length > 6) ? data[key][5] : '';
					
					if (description) {
						name += _this.orgChartFormatName(description);
					}
					if (image && image != '') {
						name = _this.orgChartFormatImage(image) + name;
					}
					name = {'v': data[key][0], 'f': name};
					dataTypes.push([
						name, parent_name, tooltip, url
					]);
				}
			}
		}

		return dataTypes;
	}

	ChartBuilderGoogleCharts.prototype.orgChartFormatName = function( description ){
		return `<div class="${this.htmlClassPrefix}org-chart-tree-description">${description}</div>`;
	}
	
	ChartBuilderGoogleCharts.prototype.orgChartFormatImage = function( image ){
		return `<div class="${this.htmlClassPrefix}org-chart-tree-image"><img width="128px" src="${image}"></div>`;
	}

	ChartBuilderGoogleCharts.prototype.orgChartConvertDataForTreeManual = function( source ){
		var _this = this;
		var treeDataArr = [];
		var ordering = _this.chartSourceData.source_ordering;
		source = Object.assign({}, source);
		if ( !$.isArray(source) ) {
			for (var index in ordering) {
				var key = (ordering[index]);

				var id = key;
				var title = source[key][0];
				var description = source[key][1];
				var image = (source[key].length > 6) ? source[key][2] : '';
				var parent_name = (source[key].length > 6) ? source[key][3] : source[key][2];
				var tooltip = (source[key].length > 6) ? source[key][4] : source[key][3];
				var url = (source[key].length > 6) ? source[key][5] : '';
				var parent_id = (source[key].length > 6) ? source[key][6].toString() : source[key][4].toString();
				var level = (source[key].length > 6) ? source[key][7].toString() : source[key][5].toString();

				// var ID = (source[0] == undefined) ? key : key + 1;
				treeDataArr[index] = {
					id: id,
					parent_id: parent_id,
					title: title,
					level: level,
					description: description,
					image: image,
					tooltip: tooltip,
					url: url,
					parent_name: parent_name
				}
			}
		}

		return treeDataArr;
	}

	// Update chart data and display immediately
	ChartBuilderGoogleCharts.prototype.updateChartData =  function( newData ){
		var _this = this;
		_this.drawChartFunction( newData, _this.chartOptions );
	}

	ChartBuilderGoogleCharts.prototype.addChartDataRow = function (element){
        var _this = this;
        // var deleteImageUrl = ChartBuilderSourceData.removeManualDataRow;
        var content = '';

        var addedTermsandConds = this.$el.find("."+this.htmlClassPrefix+"chart-source-data-edit-block");
        var addedTermsandCondsId = this.$el.find("."+this.htmlClassPrefix+"chart-source-data-edit-block:last-child");
        var dataId = addedTermsandConds.length >= 1 ? addedTermsandCondsId.data("sourceId") + 1 : 1;
		var colCount = addedTermsandConds.first().children().length - 1;

        var termsCondsMessageAttrName = this.newTermsCondsMessageAttrName(  this.htmlNamePrefix + 'chart_source_data' ,  dataId );

		content += '<div class = "'+this.htmlClassPrefix+'chart-source-data-edit-block" data-source-id="' + dataId + '" >';
			content += '<div class="'+this.htmlClassPrefix+'chart-source-data-move-block '+this.htmlClassPrefix+'chart-source-data-move-row">';
				content += '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><path d="M278.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l9.4-9.4V224H109.3l9.4-9.4c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-9.4-9.4H224V402.7l-9.4-9.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-9.4 9.4V288H402.7l-9.4 9.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l9.4 9.4H288V109.3l9.4 9.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-64-64z" style="fill: #b8b8b8;" /></svg>';
			content += '</div>';
			content += '<div class="'+this.htmlClassPrefix+'icons-box '+this.htmlClassPrefix+'icons-remove-box">';
				content += '<svg class="'+this.htmlClassPrefix+'chart-source-data-remove-block '+this.htmlClassPrefix+'chart-source-data-remove-row" data-trigger="hover" data-bs-toggle="tooltip" title="Delete row" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z" style="fill: #b8b8b8;" /></svg>';
			content += '</div>';
			for (var i = 0; i < colCount; i++) {
				if (i == 0) {
					content += '<div class="'+this.htmlClassPrefix+'chart-source-data-input-box '+this.htmlClassPrefix+'chart-source-data-name-input-box" data-cell-id="'+i+'">';
						content += '<input type="text" class="ays-text-input form-control" name="' + termsCondsMessageAttrName + '">';
					content += '</div>';
				} else {
					content += '<div class="'+this.htmlClassPrefix+'chart-source-data-input-box '+this.htmlClassPrefix+'chart-source-data-input-number" data-cell-id="'+i+'">';
						content += '<input type="number" class="ays-text-input form-control" name="' + termsCondsMessageAttrName + '" step="any">';
					content += '</div>';
				}
			}
		content += '</div>';

		this.$el.find('.'+this.htmlClassPrefix+'chart-source-data-content').append(content);
		$('[data-bs-toggle="tooltip"]').tooltip();
	}

	ChartBuilderGoogleCharts.prototype.addChartDataColumn = function (e){
		var _this = this;

		var rows = _this.$el.find("."+_this.htmlClassPrefix+"chart-source-data-content").children();
		var lastColId = +$(rows[0]).find('.'+_this.htmlClassPrefix+'chart-source-data-input-box:last-child').attr('data-cell-id');
		rows.each(function(key, row){
			var dataIDEach = row.getAttribute('data-source-id');
			var content = '';

			if (key === 0) {
				content += '<div class="'+_this.htmlClassPrefix+'chart-source-data-input-box ' +_this.htmlClassPrefix+'chart-source-title-box" data-cell-id="'+(lastColId+1)+'">';
					content += '<svg class="'+_this.htmlClassPrefix+'chart-source-data-remove-block '+_this.htmlClassPrefix+'chart-source-data-remove-col" data-trigger="hover" data-bs-toggle="tooltip" title="Delete column" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="10px">';
						content += '<path d="M310.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L160 210.7 54.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L114.7 256 9.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 301.3 265.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L205.3 256 310.6 150.6z" style="fill: #b8b8b8;" />';
					content += '</svg>';

					content += '<div class="' + _this.htmlClassPrefix + 'chart-source-data-titles-box-item">';
						content += '<input type="text" class="ays-text-input form-control ' + _this.htmlClassPrefix+'chart-source-title-input" name="' + _this.newTermsCondsMessageAttrName(  _this.htmlNamePrefix + 'chart_source_data' ,  dataIDEach ) + '">';
						content += '<svg class="' + _this.htmlClassPrefix + 'chart-source-data-sort" data-sort-order="asc" xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 320 512">';
							content += '<path d="M137.4 41.4c12.5-12.5 32.8-12.5 45.3 0l128 128c9.2 9.2 11.9 22.9 6.9 34.9s-16.6 19.8-29.6 19.8H32c-12.9 0-24.6-7.8-29.6-19.8s-2.2-25.7 6.9-34.9l128-128zm0 429.3l-128-128c-9.2-9.2-11.9-22.9-6.9-34.9s16.6-19.8 29.6-19.8H288c12.9 0 24.6 7.8 29.6 19.8s2.2 25.7-6.9 34.9l-128 128c-12.5 12.5-32.8 12.5-45.3 0z" style="fill: #b8b8b8;" />';
						content += '</svg>';
					content += '</div>'
				content += '</div>';
			} else {
				content += '<div class="'+_this.htmlClassPrefix+'chart-source-data-input-box ' +_this.htmlClassPrefix+'chart-source-data-input-number" data-cell-id="'+(lastColId+1)+'">';
					content += '<input type="number" class="ays-text-input form-control" name="' + _this.newTermsCondsMessageAttrName(  _this.htmlNamePrefix + 'chart_source_data' ,  dataIDEach ) + '" step="any">';
				content += '</div>';
			}

			$(row).append(content);
			$('[data-bs-toggle="tooltip"]').tooltip();
		});
	}

	ChartBuilderGoogleCharts.prototype.deleteChartDataRow = function (element){
		var _this = this;

		var rows = _this.$el.find("."+_this.htmlClassPrefix+"chart-source-data-content").children();
		if ((rows.length - 1) >= 2) {
			var confirm = window.confirm(aysChartBuilderAdmin.confirmRowDelete);
			if (confirm) {
				var thisMainParent = element.parent().parent();
				thisMainParent.remove();
			}
		} else {
			alert(aysChartBuilderAdmin.minRowNotice);
		}

		element.blur();
		element.tooltip('hide');
	}

	ChartBuilderGoogleCharts.prototype.deleteChartDataColumn = function (element){
		var _this = this;

		var rows = _this.$el.find("."+_this.htmlClassPrefix+"chart-source-data-content").children();
		if (rows.eq(1).children('.ays-chart-chart-source-data-input-number').length >= 2) {
			var confirm = window.confirm(aysChartBuilderAdmin.confirmColDelete);
			if (confirm) {
				var parent = element.parents('.ays-chart-chart-source-title-box');
				var index = _this.returnIndexOfEl(parent);
				rows.each(function(key, row){
					var dataRow = $(row).find('.ays-chart-chart-source-data-input-box:not("ays-chart-chart-source-data-name-input-box")');
					$(dataRow).each(function(ind, cell){
						if (ind == index) {
							$(cell).remove();
						}
					});
				});
			}
		} else {
			alert(aysChartBuilderAdmin.minColNotice);
		}

		element.blur();
		element.tooltip('hide');
	}
	
	ChartBuilderGoogleCharts.prototype.sortDataByColumn = function (el) {
		var _this = this;

		var sortingOrder = el.attr('data-sort-order');
		var colFirst = el.parents('.'+_this.htmlClassPrefix+'chart-source-data-input-box');
		var colIndex = colFirst.attr('data-cell-id');
		var colList = colFirst.parents('.'+_this.htmlClassPrefix+'chart-source-data-content').find('.'+_this.htmlClassPrefix+'chart-source-data-input-box[data-cell-id="'+colIndex+'"] input.ays-text-input');

		var sorted = {};
		colList.each(function(key, input){
			if (key !== 0) {
				var rowIndex = $(input).parents('.'+_this.htmlClassPrefix+'chart-source-data-edit-block').attr('data-source-id');
				sorted[rowIndex] = +input.value
			}
		});
		
		if (sortingOrder === 'asc') {
			sorted = _this.sortDataAsc(sorted);
			el.attr('data-sort-order', 'desc');
		} else if (sortingOrder === 'desc') {
			sorted = _this.sortDataDesc(sorted);
			el.attr('data-sort-order', 'asc');
		} else {
			sorted = _this.sortDataAsc(sorted);
			el.attr('data-sort-order', 'desc');
		}
		
		var container = _this.$el.find('.'+_this.htmlClassPrefix+'chart-source-data-content');
		var newContainer = [];
		sorted.forEach((index, newIndex) => {
			var row = container.find("."+_this.htmlClassPrefix+"chart-source-data-edit-block[data-source-id='"+index+"']");
			var inputs = row.find('input.ays-text-input');
			var inputName =  _this.newTermsCondsMessageAttrName(_this.htmlNamePrefix + 'chart_source_data', newIndex + 1);
			
			$(inputs).each(function (inputIndex, input) {
				input.setAttribute('name', inputName);
			});

			newContainer.push(row);
		});
		sorted.forEach((index, newIndex) => {
			var row = container.find("."+_this.htmlClassPrefix+"chart-source-data-edit-block[data-source-id='"+index+"']");
			newContainer.push(row);
		});
		
		container.innerHTML = '';
		newContainer.forEach(row => {
			container.append(row);
		});

		var rows = container.find("."+_this.htmlClassPrefix+"chart-source-data-edit-block");
		for (var i = 0; i < rows.length; i++) {
			$(rows[i]).attr('data-source-id', i);
		}
	}

	ChartBuilderGoogleCharts.prototype.sortDataAsc = function (data) {
		return Object.keys(data).sort(function (a, b) {return data[a] - data[b]});
	}

	ChartBuilderGoogleCharts.prototype.sortDataDesc = function (data) {
		return Object.keys(data).sort(function (a, b) {return data[b] - data[a]});
	}
	
	ChartBuilderGoogleCharts.prototype.returnIndexOfEl = function (element){
		var i = 0;
		var elements = element.parent().find('.ays-chart-chart-source-title-box');
		elements.each(function(key, cell){
			if ($(cell).is(element)) {
				i = key;
			}
		});
		return i;
	}

	ChartBuilderGoogleCharts.prototype.configureOptions = function() {
        var _this = this;

		_this.$el.find('.ays-chart-chart-source-title-box').eq(0).find('.ays-chart-chart-source-data-remove-block').css('visibility', 'hidden');

		var removeCols = _this.$el.find('.ays-chart-chart-source-title-box').find('.ays-chart-chart-source-data-remove-block');
		if (_this.chartType === 'pie_chart') {
			removeCols.css('visibility', 'hidden');
		}
		
		var options = _this.$el.find('.cb-changable-opt');
		var typeOptions = _this.$el.find('.cb-'+_this.chartType+'-opt');
		options.addClass('display_none');
		typeOptions.removeClass('display_none');
		
		var manualTabs = _this.$el.find('.cb-changable-manual:not(.cb-'+_this.chartType+'-manual)');
		var typeManualTab = _this.$el.find('.cb-'+_this.chartType+'-manual');
		manualTabs.remove();
		typeManualTab.removeClass('display_none');
		
		var tabs = _this.$el.find('.cb-changable-tab:not(.cb-'+_this.chartType+'-tab)').parents('fieldset.ays-accordion-options-container');
		var currentTabs = _this.$el.find('.cb-'+_this.chartType+'-tab').parents('fieldset.ays-accordion-options-container');
		tabs.addClass('display_none');
		currentTabs.removeClass('display_none');
	}
	
	ChartBuilderGoogleCharts.prototype.changeChartType = function () {
		var _this = this;

		_this.$el.find('#'+_this.htmlClassPrefix+'option-chart-type').val(_this.chartType);
		_this.$el.find('.'+_this.htmlClassPrefix+'type-info-box-text-changeable').text(_this.chartSources[_this.chartType]);
		_this.$el.find('.'+_this.htmlClassPrefix+'nav-tab-chart.nav-tab[data-tab="tab4"]').text(_this.chartSourceData.chartTypesNames[_this.chartType]+" settings");
		_this.$el.find('.'+_this.htmlClassPrefix+'advanced-settings .ays-accordion-options-container:first-child legend.ays-accordion-options-header span').text(_this.chartSourceData.chartTypesNames[_this.chartType]+" settings");
		_this.$el.find('.'+_this.htmlClassPrefix+'charts-main-container').attr('id' , _this.htmlClassPrefix+_this.chartType);

		_this.configureOptions();
		_this.loadChartBySource(true);
	}

	ChartBuilderGoogleCharts.prototype.newTermsCondsMessageAttrName = function (termCondName, termCondId){
		var _this = this;
		return termCondName + '['+ termCondId +'][]';	
	}
    
    ChartBuilderGoogleCharts.prototype.setAccordionEvents = function(e){
        var _this = this;

        _this.$el.on('click', '.ays-accordion-options-header', function(e){
			_this.openCloseAccordion(e, _this);
		});

		_this.$el.on('click', '.ays-slices-accordion-options-header', function(e){
			_this.openCloseAccordion(e, _this, '-slices');
		});
		
		_this.$el.on('click', '.ays-series-accordion-options-header', function(e){
			_this.openCloseAccordion(e, _this, '-series');
		});
		
		_this.$el.on('click', '.ays-rows-accordion-options-header', function(e){
			_this.openCloseAccordion(e, _this, '-rows');
		});
    }
	
	ChartBuilderGoogleCharts.prototype.openCloseAccordion = function(e, _this, contType = ""){
		var container = $(e.target).parents('.ays' + contType + '-accordion-options-container');
		var parent = (contType != "") ? container.parents('.ays-chart' + contType + '-settings') : _this.$el.find('#' + container.parents('.ays-tab-content').attr('id') + ' .ays-accordions-container').eq(0);
		var index = (contType != "") ? container.index() : -1;

        if( container.attr('data-collapsed') === 'true' ){
			_this.closeAllAccordions( parent, contType, index );
			setTimeout(() => {
				container.find('.ays' + contType + '-accordion-options-content').slideDown();
				container.attr('data-collapsed', 'false');
				container.find('.ays' + contType + '-accordion-options-header .ays' + contType + '-accordion-arrow').find('path').css('fill', '#008cff');
				container.find('.ays' + contType + '-accordion-options-header .ays' + contType + '-accordion-arrow').removeClass('ays' + contType + '-accordion-arrow-right').addClass('ays' + contType + '-accordion-arrow-down');
			}, 150);
        }else{
			setTimeout(() => {
				container.find('.ays' + contType + '-accordion-options-content').slideUp();
				container.attr('data-collapsed', 'true');
				container.find('.ays' + contType + '-accordion-options-header .ays' + contType + '-accordion-arrow').find('path').css('fill', '#c4c4c4');
				container.find('.ays' + contType + '-accordion-options-header .ays' + contType + '-accordion-arrow').removeClass('ays' + contType + '-accordion-arrow-down').addClass('ays' + contType + '-accordion-arrow-right');
			}, 150);
        }
    }
    
    ChartBuilderGoogleCharts.prototype.closeAllAccordions = function( container, contType, index ){
		var _this = this;

		container.find('.ays' + contType + '-accordion-options-container').each(function (i){
			var $this = $(this);
			if (i != index) {
				setTimeout(() => {
					$this.find('.ays' + contType + '-accordion-options-content').slideUp();
					$this.attr('data-collapsed', 'true');
					$this.find('.ays' + contType + '-accordion-options-header .ays' + contType + '-accordion-arrow').find('path').css('fill', '#c4c4c4');
					$this.find('.ays' + contType + '-accordion-options-header .ays' + contType + '-accordion-arrow').removeClass('ays' + contType + '-accordion-arrow-down').addClass('ays' + contType + '-accordion-arrow-right');
				}, 150);
			}
		});
    }

	ChartBuilderGoogleCharts.prototype.initDbImportComponent = function(){
		var _this = this;
		var table_columns = window.aysChartBuilderChartSettings.db_query.tables;
		var code_mirror = wp.CodeMirror || CodeMirror;
		var cm = code_mirror.fromTextArea(_this.$el.find('.ays-chart-db-query').get(0), {
			value: _this.$el.find('.ays-chart-db-query').val(),
			autofocus: false,
			mode: 'text/x-mysql',
			lineWrapping: true,
			dragDrop: false,
			matchBrackets: true,
			autoCloseBrackets: true,
			extraKeys: {"Ctrl-Space": "autocomplete"},
			hintOptions: { tables: table_columns }
		});
		cm = code_mirror.fromTextArea(_this.$el.find('.ays-chart-external-db-query').get(0), {
			value: _this.$el.find('.ays-chart-external-db-query').val(),
			autofocus: false,
			mode: 'text/x-mysql',
			lineWrapping: true,
			dragDrop: false,
			matchBrackets: true,
			autoCloseBrackets: true,
			extraKeys: {"Ctrl-Space": "autocomplete"}
		});


		// force refresh so that the query shows on first time load. Otherwise you have to click on the editor for it to show.
		_this.$el.on('ays-chart:db:query:focus', function(event, data){
			cm.refresh();
		});

		// cm.focus();

		// update text area.
		cm.on('inputRead', function(x, y){
			cm.save();
		});

		// backspace and delete do not register so the text box does not get empty if the entire query is deleted
		// from the editor. Let's force this.
		_this.$el.on('ays-chart:db:query:update', function(event, data){
			cm.save();
		});

		// clear the editor.
		_this.$el.on('ays-chart:db:query:setvalue', function(event, data){
			cm.setValue(data.value);
			cm.clearHistory();
			cm.refresh();
		});

		// set an option at runtime?
		_this.$el.on('ays-chart:db:query:changeoption', function(event, data){
			cm.setOption(data.name, data.value);
		});
	}

	ChartBuilderGoogleCharts.prototype.initDbImport = function(){
		var _this = this;
		_this.initDbImportComponent();
	}

	ChartBuilderGoogleCharts.prototype.initQuizDBImport = function(){
		var _this = this;

		_this.$el.find('#ays-chart-quiz-maker-fetch').on('click', function(e){
			e.preventDefault();

			var select_query = $(document).find('#'+_this.htmlClassPrefix+'select-quiz-maker-data-query').val();
			if (select_query == "") {
				return;
			}
			
			_this.fetchQuizData( true );
		});

		_this.$el.find('#ays-chart-quiz-maker-show-on-chart').on( 'click', function(e){
			e.preventDefault();

			var select_query = $(document).find('#'+_this.htmlClassPrefix+'select-quiz-maker-data-query').val();
			if (select_query == "") {
				return;
			}

			if( _this.chartTempData ){
				_this.$el.find('#ays-chart-quiz-maker-success').empty();
				_this.$el.find('#ays-chart-quiz-maker-error').empty();
				if (_this.chartType == 'line_chart' || _this.chartType == 'bar_chart' || _this.chartType == 'column_chart') {
					_this.chartTempData = _this.multiColumnChartConvertData( _this.chartTempData );
				} else if (_this.chartType == 'org_chart') {
					_this.chartTempData = _this.orgChartConvertData( _this.chartTempData );
				} else {
					_this.chartTempData = _this.chartConvertData( _this.chartTempData );
				}
				_this.chartData = window.google.visualization.arrayToDataTable( _this.chartTempData );

				_this.updateChartData( _this.chartData );

				_this.chartTempData = null;
			}else{
				_this.fetchQuizData( false, true );
			}

		});

		_this.$el.find('#ays-chart-quiz-maker-save').on( 'click', function(e){
			e.preventDefault();

			var select_query = $(document).find('#'+_this.htmlClassPrefix+'select-quiz-maker-data-query').val();
			if (select_query == "") {
				return;
			}

			_this.$el.find('#ays-chart-quiz-maker-success').empty();
			_this.$el.find('#ays-chart-quiz-maker-error').empty();

			var chart_id = $(document).find('.' + _this.htmlClassPrefix + 'chart-id').val();
			var query = $(document).find('#'+_this.htmlClassPrefix+'select-quiz-maker-data-query').find('option:selected').val();
			var selectQuizSelectDisplay = $(document).find('.'+_this.htmlClassPrefix+'select-quiz-maker-quiz-container').css('display');
			var quizID = null;
			if (selectQuizSelectDisplay != 'none') {
				quizID = $(document).find('#'+_this.htmlClassPrefix+'select-quiz-maker-quiz').find('option:selected').val();
				if (quizID == 0) {
					_this.$el.find('#ays-chart-quiz-maker-error').html( 'Please select a quiz.' );
					return;
				}
			}

			// submit only if a query has been provided.
			if( query.length > 0 ){
				_this.startAjax( _this.$el.find('#ays-chart-quiz-maker-form') );

				$.ajax({
					url: ajaxurl,
					method: 'post',
					dataType: 'json',
					data: {
						'action': _this.ajaxAction,
						'function': window.aysChartBuilderChartSettings.ajax['actions']['quiz_maker_save_data'],
						'security': window.aysChartBuilderChartSettings.ajax['nonces']['quiz_maker_save_data'],
						'params': {
							query: query,
							quizID: quizID,
							chart_id: chart_id,
						}
					},
					success: function( response ){
						if( response.success ){
							_this.$el.find('#ays-chart-quiz-maker-success').html( response.data.msg );
							_this.$el.find('input[name="' + _this.htmlNamePrefix + 'source_type"]').val('quiz_maker');
						}else{
							_this.$el.find('#ays-chart-quiz-maker-error').html( response.data.msg );
							_this.$el.find('input[name="' + _this.htmlNamePrefix + 'source_type"]').val('manual');
						}
					},
					complete: function(){
						_this.endAjax( _this.$el.find('#ays-chart-quiz-maker-form') );
					}
				});
			}else{
				_this.$el.find('#ays-chart-quiz-maker-form').unlock();
				_this.$el.find('input[name="' + _this.htmlNamePrefix + 'source_type"]').val('manual');
			}
		});

		$(document).on('ays-chart:modal:close', function (){
			_this.chartTempData = null;
		});
	}

	ChartBuilderGoogleCharts.prototype.fetchQuizData = function( openModal = false, showOnChart = false ){
		var _this = this;
		_this.$el.trigger('ays-chart:db:query:update', {});

		var chart_id = $(document).find('.' + _this.htmlClassPrefix + 'chart-id').val();
		var query = $(document).find('#'+_this.htmlClassPrefix+'select-quiz-maker-data-query').find('option:selected').val();
		var selectQuizSelectDisplay = $(document).find('.'+_this.htmlClassPrefix+'select-quiz-maker-quiz-container').css('display');
		var quizID = null;
		if (selectQuizSelectDisplay != 'none') {
			quizID = $(document).find('#'+_this.htmlClassPrefix+'select-quiz-maker-quiz').find('option:selected').val();
			if (quizID == 0) {
				_this.$el.find('#ays-chart-quiz-maker-error').html( 'Please select a quiz.' );
				return;
			}
		}

		if (query.length == 0) {
			return;
		}

		_this.startAjax( _this.$el.find('#ays-chart-quiz-maker-form') );

		var modal = $(document).find('#ays-chart-db-query-results');

		_this.$el.find('.db-wizard-results').empty();
		_this.$el.find('#ays-chart-quiz-maker-success').empty();
		_this.$el.find('#ays-chart-quiz-maker-error').empty();

		$.ajax({
			url: ajaxurl,
			method: 'post',
			dataType: 'json',
			data: {
				'action': _this.ajaxAction,
				'function': window.aysChartBuilderChartSettings.ajax['actions']['quiz_maker_get_data'],
				'security': window.aysChartBuilderChartSettings.ajax['nonces']['quiz_maker_get_data'],
				'params': {
					query: query,
					quizID: quizID,
					chart_id: chart_id,
				}
			},
			success: function( response ){
				if( response.success ){
					modal.find('.db-wizard-results').html( response.data.table );
					modal.find('#results').DataTable({
						"paging": true
					});
					modal.find('#results').parent().addClass('ays-db-results-wrap');
					
					if( openModal === true ) {
						modal.aysModal('show');
					}

					_this.chartTempData = response.data.data ? response.data.data : null;

					if( showOnChart === true ) {
						if (_this.chartType == 'line_chart' || _this.chartType == 'bar_chart' || _this.chartType == 'column_chart') {
							_this.chartTempData = _this.multiColumnChartConvertData( _this.chartTempData );
						} else if (_this.chartType == 'org_chart') {
							_this.chartTempData = _this.orgChartConvertData( _this.chartTempData );
						} else {
							_this.chartTempData = _this.chartConvertData( _this.chartTempData );
						}
						_this.chartData = window.google.visualization.arrayToDataTable(_this.chartTempData);

						_this.updateChartData(_this.chartData);

						_this.chartTempData = null;
					}
				}else{
					modal.find('ays-close').trigger('click');
					_this.$el.find('#ays-chart-quiz-maker-error').html( response.data.msg );
					_this.$el.find('input[name="' + _this.htmlNamePrefix + 'source_type"]').val('manual');
				}
			},
			complete: function(){
				_this.endAjax( _this.$el.find('#ays-chart-quiz-maker-form') );
			}
		});
	}

	ChartBuilderGoogleCharts.prototype.startAjax = function( element ){
		element.lock();
	}

	ChartBuilderGoogleCharts.prototype.endAjax = function( element ){
		element.unlock();
	}

	ChartBuilderGoogleCharts.prototype.quickSaveHotKeys = function() {
		$(document).on('keydown', function(e){
			var saveButton = $(document).find('input#ays-button-apply');
			if ( saveButton.length > 0 ) {
                if (!(e.which == 83 && e.ctrlKey) && !(e.which == 19)){
                    return true;
                }
                saveButton.trigger("click");
                e.preventDefault();
                return false;
            }
		});
	}

	// Manual data preview button
	ChartBuilderGoogleCharts.prototype.showOnChart = function () {
		var _this = this;
		
		var lastId = $(document).find(".ays-chart-chart-source-data-edit-block:last-child").attr('data-source-id');
		var chartData = [];

		var rowTitles = $(document).find('.ays-chart-chart-source-data-name-input-box');
		rowTitles.each(function(key, el) {
			var value = $(el).find('input').val();
			if ( value == '' ) {
				$(el).find('input').val('Option');
			}
		});

		var form = $(document).find("#ays-charts-form-google-charts");
		var data = form.serializeFormJSON();

		for (var i = 0; i <= lastId; i++) {
			if (data['ays_chart_source_data['+i+'][]'] !== undefined) {
				var dataRow = data['ays_chart_source_data['+i+'][]'];
				var filteredRow = [];
				for (var key = 0; key < dataRow.length; key++) {
					var value = dataRow[key];
					if (value != '') {
						filteredRow.push(value);
					} else {
						if (i == 0) {
							filteredRow.push('Title'+key);
						} else {
							filteredRow.push('0');
						}
					}
				}
				if (filteredRow.length != 0) {
					chartData.push(filteredRow);
				}
			}
		}
		if (_this.chartType == 'line_chart' || _this.chartType == 'bar_chart' || _this.chartType == 'column_chart') {
			_this.chartTempData = _this.multiColumnChartConvertData( chartData );
		} else {
			_this.chartTempData = _this.chartConvertData( chartData );
		}

		_this.chartTempData = _this.chartSourceData.settings.enableRowSettings ? _this.setRowOptions(_this.chartTempData, _this.chartSourceData.settings, false) : _this.chartTempData;

		_this.chartData = window.google.visualization.arrayToDataTable( _this.chartTempData );

		_this.updateChartData( _this.chartData );

		_this.chartTempData = null;
	}

	ChartBuilderGoogleCharts.prototype.orgTypeShowOnChart = function () {
		var _this = this;

		var form = _this.$el.find("#ays-charts-form-google-charts");
		var data = form.serializeFormJSON();
		var allBranches = _this.$el.find(".tree-branch");
		var treeManual = _this.$el.find("#ays-chart-chart-source-data-edit-tree-content");

		var chartData = {};
		allBranches.each(function() {
			var branchId = $(this).attr("data-id") 
			var changedName = $(this).find(".ays-chart-org-chart-name-" + branchId).val();
			var changedDescription = $(this).find(".ays-chart-org-chart-description-" + branchId).val();
			var changedImage = $(this).find(".ays-chart-org-chart-image-" + branchId).val();
			var changedParentName = $(this).find(".ays-chart-org-chart-parent-name").val();
			var changedTooltip = $(this).find(".ays-chart-org-chart-tooltip-" + branchId).val();
			var changedUrl = $(this).find(".ays-chart-org-chart-url-" + branchId).val();
			var changedParentId = $(this).find(".ays-chart-org-chart-parent").val();

			var changedLevel = $(this).find(".ays-chart-org-chart-level").val();

			chartData[branchId] = [changedName, changedDescription, changedImage, changedParentName, changedTooltip, changedUrl, changedParentId, changedLevel]
		})

		_this.chartData = _this.orgChartConvertData( chartData );
		_this.chartTempData = _this.chartData;
		_this.chartData = window.google.visualization.arrayToDataTable( _this.chartData );
		_this.updateChartData( _this.chartData );
		chartData = null;
	}

	ChartBuilderGoogleCharts.prototype.htmlDecode = function (input) {
		if (!input) return input;

		var e = document.createElement('div');
		e.innerHTML = input;
		return e.childNodes[0].nodeValue;
	}

	ChartBuilderGoogleCharts.prototype.drawChartFunction = function (source, options) {
		var _this = this;

		var view = new google.visualization.DataView(source);
		_this.chartData = source;

		if (_this.chartType == 'org_chart') {
			view.setColumns([0, 1, 2]);
		}

		_this.chartObj.draw(view, options);

		if (_this.chartType == 'org_chart') {
			_this.setOrgChartCustomStyles();
		}
	}

	$.fn.ChartBuilderGoogleChartsMain = function(options) {
        return this.each(function() {
            if (!$.data(this, 'ChartBuilderGoogleChartsMain')) {
                $.data(this, 'ChartBuilderGoogleChartsMain', new ChartBuilderGoogleCharts(this, options));
            } else {
                try {
                    $(this).data('ChartBuilderGoogleChartsMain').init();
                } catch (err) {
                    console.error('ChartBuilderGoogleChartsMain has not initiated properly');
                }
            }
        });
    };
    $(document).find('#ays-charts-form-google-charts').ChartBuilderGoogleChartsMain();

})(jQuery);

(function ($) {

	$.fn.serializeFormJSON = function () {
		let o = {},
			a = this.serializeArray();
		$.each(a, function () {
			if (o[this.name]) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});
		return o;
	};

	$.fn.lock = function () {
		$(this).each(function () {
			var $this = $(this);
			var position = $this.css('position');

			if (!position) {
				position = 'static';
			}

			switch (position) {
				case 'absolute':
				case 'relative':
					break;
				default:
					$this.css('position', 'relative');
					break;
			}
			$this.data('position', position);

			var width = $this.width(),
				height = $this.height();

			var locker = $('<div class="locker"></div>');
			locker.width(width).height(height);

			var loader = $('<div class="locker-loader"></div>');
			loader.width(width).height(height);

			locker.append(loader);
			$this.append(locker);
			$(window).resize(function () {
				$this.find('.locker,.locker-loader').width($this.width()).height($this.height());
			});
		});

		return $(this);
	};

	$.fn.unlock = function () {
		$(this).each(function () {
			$(this).find('.locker').remove();
			$(this).css('position', $(this).data('position'));
		});

		return $(this);
	};
})(jQuery);