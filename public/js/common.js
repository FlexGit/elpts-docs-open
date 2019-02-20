$(document).ready(function () {

	$('.phone').mask('8-000-000-00-00', {placeholder: "8-___-___-__-__"});
	$('.snils').mask('000-000-000-00', {placeholder: "___-___-___-__"});

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	/*-----------------------------------/
	/*	TOP NAVIGATION AND LAYOUT
	/*----------------------------------*/

	$('.btn-toggle-fullwidth').on('click', function () {
		if (!$('body').hasClass('layout-fullwidth')) {
			$('body').addClass('layout-fullwidth');

		} else {
			$('body').removeClass('layout-fullwidth');
			$('body').removeClass('layout-default'); // also remove default behaviour if set
		}

		$(this).find('.lnr').toggleClass('lnr-arrow-left-circle lnr-arrow-right-circle');

		if ($(window).innerWidth() < 1025) {
			if (!$('body').hasClass('offcanvas-active')) {
				$('body').addClass('offcanvas-active');
			} else {
				$('body').removeClass('offcanvas-active');
			}
		}
	});

	$(window).on('load', function () {
		if ($(window).innerWidth() < 1025) {
			$('.btn-toggle-fullwidth').find('.icon-arrows')
				.removeClass('icon-arrows-move-left')
				.addClass('icon-arrows-move-right');
		}

		// adjust right sidebar top position
		$('.right-sidebar').css('top', $('.navbar').innerHeight());

		// if page has content-menu, set top padding of main-content
		if ($('.has-content-menu').length > 0) {
			$('.navbar + .main-content').css('padding-top', $('.navbar').innerHeight());
		}

		// for shorter main content
		if ($('.main').height() < $('#sidebar-nav').height()) {
			$('.main').css('min-height', $('#sidebar-nav').height());
		}
	});


	/*-----------------------------------/
	/*	SIDEBAR NAVIGATION
	/*----------------------------------*/

	$('.sidebar a[data-toggle="collapse"]').on('click', function () {
		if ($(this).hasClass('collapsed')) {
			$(this).addClass('active');
		} else {
			$(this).removeClass('active');
		}
	});

	if ($('.sidebar-scroll').length > 0) {
		$('.sidebar-scroll').slimScroll({
			height: '95%',
			wheelStep: 2,
		});
	}


	/*-----------------------------------/
	/*	PANEL FUNCTIONS
	/*----------------------------------*/

	// panel remove
	$('.panel .btn-remove').click(function (e) {

		e.preventDefault();
		$(this).parents('.panel').fadeOut(300, function () {
			$(this).remove();
		});
	});

	// panel collapse/expand
	var affectedElement = $('.panel-body');

	$('.panel .btn-toggle-collapse').clickToggle(
		function (e) {
			e.preventDefault();

			// if has scroll
			if ($(this).parents('.panel').find('.slimScrollDiv').length > 0) {
				affectedElement = $('.slimScrollDiv');
			}

			$(this).parents('.panel').find(affectedElement).slideUp(300);
			$(this).find('i.lnr-chevron-up').toggleClass('lnr-chevron-down');
		},
		function (e) {
			e.preventDefault();

			// if has scroll
			if ($(this).parents('.panel').find('.slimScrollDiv').length > 0) {
				affectedElement = $('.slimScrollDiv');
			}

			$(this).parents('.panel').find(affectedElement).slideDown(300);
			$(this).find('i.lnr-chevron-up').toggleClass('lnr-chevron-down');
		}
	);


	/*-----------------------------------/
	/*	PANEL SCROLLING
	/*----------------------------------*/

	if ($('.panel-scrolling').length > 0) {
		$('.panel-scrolling .panel-body').slimScroll({
			height: '430px',
			wheelStep: 2,
		});
	}

	if ($('#panel-scrolling-demo').length > 0) {
		$('#panel-scrolling-demo .panel-body').slimScroll({
			height: '175px',
			wheelStep: 2,
		});
	}

	/*-----------------------------------/
	/*	TODO LIST
	/*----------------------------------*/

	$('.todo-list input').change(function () {
		if ($(this).prop('checked')) {
			$(this).parents('li').addClass('completed');
		} else {
			$(this).parents('li').removeClass('completed');
		}
	});


	/*-----------------------------------/
	/* TOASTR NOTIFICATION
	/*----------------------------------*/

	if ($('#toastr-demo').length > 0) {
		toastr.options.timeOut = "false";
		toastr.options.closeButton = true;
		toastr['info']('Hi there, this is notification demo with HTML support. So, you can add HTML elements like <a href="#">this link</a>');

		$('.btn-toastr').on('click', function () {
			$context = $(this).data('context');
			$message = $(this).data('message');
			$position = $(this).data('position');

			if ($context == '') {
				$context = 'info';
			}

			if ($position == '') {
				$positionClass = 'toast-left-top';
			} else {
				$positionClass = 'toast-' + $position;
			}

			toastr.remove();
			toastr[$context]($message, '', {positionClass: $positionClass});
		});

		$('#toastr-callback1').on('click', function () {
			$message = $(this).data('message');

			toastr.options = {
				"timeOut": "300",
				"onShown": function () {
					alert('onShown callback');
				},
				"onHidden": function () {
					alert('onHidden callback');
				}
			}

			toastr['info']($message);
		});

		$('#toastr-callback2').on('click', function () {
			$message = $(this).data('message');

			toastr.options = {
				"timeOut": "10000",
				"onclick": function () {
					alert('onclick callback');
				},
			}

			toastr['info']($message);

		});

		$('#toastr-callback3').on('click', function () {
			$message = $(this).data('message');

			toastr.options = {
				"timeOut": "10000",
				"closeButton": true,
				"onCloseClick": function () {
					alert('onCloseClick callback');
				}
			}

			toastr['info']($message);
		});
	}

	// Floating label
	$("body").on("input propertychange", ".floating", function (e) {
		$(this).toggleClass("floating-with-value", !!$(e.target).val());
	}).on("focus", ".floating", function () {
		$(this).addClass("floating-with-focus");
	}).on("blur", ".floating", function () {
		$(this).removeClass("floating-with-focus");
	});

	// Exception For OKOPF == 'Индивидуальные предприниматели'
	$('select[name="doc_field3"]').change(function () {
		if ($(this).val() == '50102') {
			$('.required_sign6').hide();
		} else {
			$('.required_sign6').show();
		}
	});

	// Send E-mail Confirmation Code
	$('#email_confirm_code_btn').on('click', function (e) {
		e.preventDefault();

		$('#ajaxResponse').html('');

		var email = $(this).prev('input[type="text"]').val().trim();
		var msg = '';

		var data = 'email=' + email + '&template_id=' + $('input[name="templates_id"]').val();
		$.ajax({
			url: '/ajaxEmailConfirmCode',
			type: 'POST',
			data: data,
			success: function (data) {
				//console.log(data);

				if (data.response.error.length) { // Error
					$.each(data.response.error, function (index, value) {
						msg += '<li>' + value + '</li>';
					});
					$('#ajaxResponse').append('<div class="alert alert-danger"><ul>' + msg + '</ul></div>');
				} else if (data.response.status == 'success') {
					$('#ajaxResponse').append('<div class="alert alert-success">' + data.response.msg + '</div>');
				}

				$('html, body').animate({scrollTop: 0}, 100);
			}
		});
	});

	// Check OGRN
	$('#ogrn_check_btn').on('click', function (e) {
		e.preventDefault();

		$('#ajaxResponse').html('');
		$('#doc_field21').prop('disabled', true);
		$('.form-group, #email_confirm_code_btn').show();
		$('#email_confirm_code_hidden').val(0);

		var ogrn = $(this).prev('input[type="text"]').val().trim();
		var msg = '';
		var is_error = 0;

		var data = 'ogrn=' + ogrn + '&templates_id=' + $('input[name="templates_id"]').val() + '&doctypes_id=' + $('input[name="doctypes_id"]').val();
		//console.log(data);
		$.ajax({
			url: '/ajaxOgrnCheck',
			type: 'POST',
			data: data,
			success: function (data) {
				//console.log(data);
				if (data.response.error.length) { // Error
					is_error = 1;
					$.each(data.response.error, function (index, value) {
						msg += '<li>' + value + '</li>';
					});
					$('#ajaxResponse').append('<div class="alert alert-danger"><ul>' + msg + '</ul></div>');
				} else if (data.response.status == 'success') {
					if (Object.size(data.response.default_values)) {
						$.each(data.response.default_values, function (index, value) {
							$('input[type="text"], input[type="checkbox"], input[type="file"], select').each(function () {
								if ($(this).attr('name') == 'doc_field' + index) {
									if ($(this).attr('type') == 'checkbox' && value.value == '1') {
										$(this).prop('checked', true);
									} else if ($(this).attr('type') == 'file') {
										$(this).val('');
										$(this).closest('div').find('.btn_file_remove').trigger('click');
										$(this).closest('div').append('<div class="form-control"><a href="/file/' + value.value + '">Скачать</a></div><div><button type="button" name="btn_doc_field' + index + '" class="btn btn-danger btn_file_remove">Удалить</button></div>');
										$(this).next().val(value.value);
										$(this).hide();
									} else {
										if ($.inArray(index, ['3', '14']) !== -1) { // 'Страна', 'ОКОПФ'
											$('option', this).filter(function () {
												return $.trim($(this).text()) == value.value;
											}).prop('selected', true);
										} else if ($.inArray(index, ['81']) !== -1) { // 'Номер акцепта Оферты'
											$this = $(this);
											if (Object.size(value.value)) {
												$.each(value.value, function (i, v) {
													$this.append($("<option>").val(v).text(v));
												});
											} else {
												is_error = 1;
												$('#ajaxResponse').append('<div class="alert alert-danger"><ul>По данному значению поля "ОГРН/ОГРНИП" не найдено ни одной Оферты с разрешенным префиксом. Подписание невозможно.</ul></div>');
											}
										} else {
											if ($.inArray(index, ['21']) !== -1) {
												$(this).prop('disabled', true);
												$(this).closest('.form-group').hide();
												$('#email_confirm_code_btn').hide();
												$('#email_confirm_code_hidden').val(1);
												//return true;
											}
											$(this).val(value.value);
										}
									}
									if ($.inArray(index, ['5', '20']) !== -1) {
										$(this).prop('readonly', true);
									}
								}
							});
						});
					}

					if (!is_error) {
						$('input[name="ogrn_check"]').val(1);
						$('#ajaxResponse').append('<div class="alert alert-success">' + data.response.msg + '</div>');
					}
				}

				$('html, body').animate({scrollTop: 0}, 100);
			}
		});
	});

	$(document).on('click', '.btn_file_remove', function () {
		$(this).closest('div').prev().prev().prev().show();
		$(this).closest('div').prev().prev().val('');
		$(this).closest('div').prev().remove();
		$(this).closest('div').remove();
	});

	// Sign Form Submit
	var options = {
		//clearForm: true,
		//resetForm: true,
		//beforeSubmit: showRequest,
		//success: showResponse,
		dataType: 'json',
		success: function (data) {
			//console.log(data);

			$('.doc_field').removeClass('red');

			if (data.response.error.length) { // Error
				var msg = '<div class="alert alert-danger"><ul>';
				$.each(data.response.error, function (index, value) {
					msg += '<li>' + value + '</li>';

					var n = value.indexOf("doc_field");
					var class_name = value.substring(n, (value.indexOf(">") - 1));
					if (class_name) {
						$('.' + class_name).addClass('red');
					}
				});
				msg += '</ul></div>';

				$('#ajaxResponse').append(msg);

				if ($('.g-recaptcha').length) {
					grecaptcha.reset();
				}

				$('.overlay').hide();

				$('html, body').animate({scrollTop: 0}, 100);
			} else if (parseInt(data.response.doc_id)) { // Saved Doc
				var certificates = data.response.certificates;
				var file = data.response.file;
				var doc_id = data.response.doc_id;
				var email_confirm_code_id = data.response.email_confirm_code_id;

				window.cspsignplugin.getCertificates().then(function (data) {
					var br = 0;
					for (var i in data) {
						if (i === certificates) {
							var inn, snils, position, lastname, firstname, ogrn = '';
							window.cspsignplugin.getCertificateProperty(data[i], 'subject').then(
								function (data) {
									//console.log('subject: ' + data);

									var data_arr = data.split(',');

									data_arr.forEach(function (item, i, data_arr) {
										//if(inn && snils && ogrn && position && lastname && firstname) return true;

										var param = item.trim();

										if (!inn && (param.indexOf('ИНН') === 0 || param.indexOf('INN') === 0)) {
											inn = param.substring(4);
										}
										if (!snils && (param.indexOf('СНИЛС') === 0 || param.indexOf('SNILS') === 0)) {
											snils = param.substring(6);
										}
										if (!ogrn && (param.indexOf('ОГРН') === 0 || param.indexOf('OGRN') === 0)) {
											ogrn = param.substring(5);
										}
										if (!position && (param.indexOf('Т=') === 0 || param.indexOf('T=') === 0)) {
											position = param.substring(2);
										}
										if (!lastname && param.indexOf('SN=') === 0) {
											lastname = param.substring(3);
										}
										if (!firstname && param.indexOf('G=') === 0) {
											firstname = param.substring(2);
										}
									});
								}, function (error) {
									//console.log("error: ", error.message);
									$('.overlay').hide();
								}
							);

							br = 1;
							window.cspsignplugin.sign(file, data[i]).then(function (data) { // Signed Doc
								//console.log(data);
								var fullname = lastname + ' ' + firstname;

								var data = 'inn=' + inn + '&snils=' + snils + '&ogrn=' + ogrn + '&position=' + position + '&fullname=' + fullname + '&doc_id=' + doc_id + '&file=' + encodeURIComponent(file) + '&signature=' + encodeURIComponent(data) + '&email_confirm_code_id=' + email_confirm_code_id;
								//console.log(data);

								$(function () {
									$.ajax({
										url: '/ajaxDocSign',
										type: 'POST',
										data: data,
										success: function (data) {
											//console.log(data);

											if (data.response.error.length) { // Error
												//console.log(data.response.error);
												if (data.response.error === 'OGRN Matching Error') {
													$('input[name="ogrn_check"]').val(0);
												}
												var msg = '<div class="alert alert-danger"><ul><li>' + data.response.msg + '</li></ul></div>';
												$('#ajaxResponse').append(msg);

												if ($('.g-recaptcha').length) {
													grecaptcha.reset();
												}

												$('.overlay').hide();
												$('html, body').animate({scrollTop: 0}, 100);
											} else if (data.response.status === 'success') { // Saved Signed Doc
												$('#sign_form')[0].reset();

												alert(data.response.msg);

												$('.overlay').hide();

												window.location.href = '/';
											}
										}
									});
								});
							});
							if (br) return false;
						}
					}
				});
			}
		}
	};

	$('#sign_form').on('submit', function () {
		$('#ajaxResponse').html('');

		$('.overlay').show();

		$(this).ajaxSubmit(options);

		return false;
	});
});

function showRequest(formData, jqForm, options) {
	var queryString = $.param(formData);

	alert('About to submit: \n\n' + queryString);

	return true;
}

function showResponse(responseText, statusText, xhr, $form) {
	alert('status: ' + statusText + '\n\nresponseText: \n' + responseText +
		'\n\nThe output div should have already been updated with the responseText.');
}

// toggle function
$.fn.clickToggle = function (f1, f2) {
	return this.each(function () {
		var clicked = false;
		$(this).bind('click', function () {
			if (clicked) {
				clicked = false;
				return f2.apply(this, arguments);
			}

			clicked = true;
			return f1.apply(this, arguments);
		});
	});
}

/*function isValidEmail(s){
	var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(String(s).toLowerCase());
}*/

Object.size = function (obj) {
	var size = 0, key;
	for (key in obj) {
		if (obj.hasOwnProperty(key)) size++;
	}
	return size;
};

var gListCertificates;

function loadCertificates() {
	gListCertificates = [];
	var element = window.document.getElementById("certificates");

	var _error = function (error) {
		var msg = error.message;
		if (typeof msg == "undefined") {
			msg = error;
		}
		//console.log("Error: ", msg);
		alert(msg);
	};
	var _add = function (data) {
		var data_arr = data.split(',');
		var ogrn = '';
		data_arr.forEach(function (item, i, data_arr) {
			var param = item.trim();

			if (param.indexOf('ОГРН') !== -1 || param.indexOf('OGRN') !== -1) {
				ogrn = param.substring(5);
			}
		});

		if (ogrn.length) {
			var option = document.createElement("Option");
			option.text = data;
			option.value = this.index;
			element.add(option);
		}
	};
	var _load = function (data) {
		for (var i in data) {
			var obj = {};
			obj.index = i;
			obj.certificate = data[i];
			obj.func = _add;
			var _success = obj.func.bind(obj);
			window.cspsignplugin.getCertificateProperty(data[i], 'subject').then(_success, _error);
			gListCertificates.push(obj);
		}
	};

	if (element) {
		if (typeof window.cspsignplugin !== "undefined") {
			for (var i = (element.options.length - 1); i >= 0; i--) {
				element.options.remove(i);
			}
			window.cspsignplugin.getCertificates().then(_load, _error);
		}
	}
}

function _loadPage() {
	if (typeof window.cspsignplugin !== "undefined") {
		loadCertificates();
	} else {
		alert('Внимание! Доступ в приложение ограничен.\nНе удалось инициализировать плагин "Signal-COM CSP Plugin".');
		//window.location.href = '/';
	}
}

setTimeout(_loadPage, 2000);
