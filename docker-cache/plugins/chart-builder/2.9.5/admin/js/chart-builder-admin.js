(function($) {
    'use strict';
    function AysChartBuilder(element, options){
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

        this.init();

        return this;
    }

    AysChartBuilder.prototype.init = function() {
        var _this = this;
		_this.chartSourceData = window.ChartBuilderSourceData;
        _this.setEvents();
        _this.initLibraries();

		_this.initDbImport();
		_this.initQuizDBImport();
    };

	// Set events
    AysChartBuilder.prototype.setEvents = function(e){
        var _this = this;

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
    }

	AysChartBuilder.prototype.toggleQuizSelect = function (e) {
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
	
	AysChartBuilder.prototype.quizMakerIntegrationConfig = function (e) {
		var _this = this;

		var input = $(document).find('input[name="ays_source_type"]');
		if (input.val() !== 'quiz_maker') input.val('quiz_maker');

		_this.$el.find('#ays-chart-quiz-maker-success').empty();
		_this.$el.find('#ays-chart-quiz-maker-error').empty();
	}

	AysChartBuilder.prototype.detectManualChange = function (e) {
		var input = $(document).find('input[name="ays_source_type"]');
		if (input.val() !== 'manual') input.val('manual'); 	
	}

	AysChartBuilder.prototype.initLibraries = function (){
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

	AysChartBuilder.prototype.initDbImportComponent = function(){
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

	AysChartBuilder.prototype.initDbImport = function(){
		var _this = this;
		_this.initDbImportComponent();
	}

	AysChartBuilder.prototype.initQuizDBImport = function(){
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

	AysChartBuilder.prototype.fetchQuizData = function( openModal = false, showOnChart = false ){
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

	AysChartBuilder.prototype.startAjax = function( element ){
		element.lock();
	}

	AysChartBuilder.prototype.endAjax = function( element ){
		element.unlock();
	}

	AysChartBuilder.prototype.quickSaveHotKeys = function() {
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

	AysChartBuilder.prototype.htmlDecode = function (input) {
		if (!input) return input;

		var e = document.createElement('div');
		e.innerHTML = input;
		return e.childNodes[0].nodeValue;
	}

	$.fn.AysChartBuilderMain = function(options) {
        return this.each(function() {
            if (!$.data(this, 'AysChartBuilderMain')) {
                $.data(this, 'AysChartBuilderMain', new AysChartBuilder(this, options));
            } else {
                try {
                    $(this).data('AysChartBuilderMain').init();
                } catch (err) {
                    console.error('AysChartBuilderMain has not initiated properly');
                }
            }
        });
    };
    $(document).find('#ays-charts-form').AysChartBuilderMain();

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