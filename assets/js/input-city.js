
var InputCity = (function() {


	var _method 	= '';
	var _endpoint 	= '';


	var getSuccessMessage = function(jqXHR) {

		//edition mode
		if(_method === 'PUT') {
			return 'City content successfully updated';
		}

		//creation mode
		let location = jqXHR.getResponseHeader('Location').replace('/api', '');	//we don't want no API links here
		return 'City successfully processed and available in <a href="' + location + '">' + location + '</a>';

	};


	var changeResultContainer = function(content, success) {

		let $resultContainer = $('#result');
		let className = success? 'success' : 'danger';

		$resultContainer.fadeOut(function() {
			$(this).removeClass().addClass('alert alert-' + className).html(content).fadeIn();
		});

	};


	var changeBreadcrumbsCityName = function(newCityName) {

		let $breadcrumbsNav = $('ol.breadcrumb');

		$breadcrumbsNav.fadeOut(function() {
			$('#breadcrumbCityName').find('a').text(newCityName);
			$breadcrumbsNav.fadeIn();
		})

	};


	var submitCity = function() {

		//can't send anything if the form isn't valid (perform the native browswer HTML5 validation)
		if(!$('#cityForm')[0].checkValidity()) {
			return;
		}

		var cityData = {
			name: $('#inputName').val(),
			latitude: parseFloat($('#inputLatitude').val()),
			longitude: parseFloat($('#inputLongitude').val())
		};

		$.when(Application.sendData(_method, _endpoint, cityData))

		.done(function(data, textStatus, jqXHR) {

			let content = getSuccessMessage(jqXHR);
			changeResultContainer(content, true);

			if(_method === 'PUT') {

				if(data.name !== undefined) {
					changeBreadcrumbsCityName(data.name);
				}

			}

		})

		.fail(function(jqXHR, textStatus, errorThrown) {

			let content = Application.getErrorMessage(jqXHR, textStatus, errorThrown);
			changeResultContainer(content, false);
			Application.debugError(jqXHR, textStatus, errorThrown);

		});

	};


	var init = function(method, endpoint) {

		//retrieve the connection data
		_method = method;
		_endpoint = Application.getBaseUrl() + endpoint;

		//disable traditional form submission
		$('#cityForm').submit(function(e) {
			e.preventDefault();
		});

		//connect the click event of the submit button to sending the form with AJAX
		$('#citySubmitButton').click(submitCity);

	};


	return {
		init: init
	};


})(); //autoexecute
