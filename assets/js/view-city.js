
var ViewCity = (function() {


	var _endpoint = '';

	const _editIcon 	= 'fa-edit';
	const _removeIcon 	= 'fa-trash-alt';
	const _recoverIcon  = 'fa-trash-restore';


	var generateFaIcon = function(className) {
		return $('<i></i>').addClass('fa ' +  className);
	};


	var generateCityEditLink = function(href) {
		return $('<a></a>').attr('href', href).addClass('btn btn-primary').append(generateFaIcon(_editIcon)).append('Edit');
	};


	var generateDisabledEditButton = function() {
		return $('<button></button>').addClass('btn btn-primary').prop('disabled', true)
									 .append(generateFaIcon(_editIcon)).append('Edit');
	};


	var setCityStatus = function(cityEnabled) {

		$('#cityStatus').fadeOut(function() {
			cityEnabled? $(this).addClass('hidden').fadeIn() : $(this).removeClass('hidden').fadeIn();
		});

	};

	var enableCityView = function($cityView, newLocation) {

		//update the city id contained in the $cityView
		let id = parseInt(newLocation.split('/').pop());
		$cityView.data('id', id);

		//update the browser navigation bar 
		//https://gomakethings.com/how-to-update-a-url-without-reloading-the-page-using-vanilla-javascript/
		let queryString = window.location.search;
		console.log(queryString);

		history.pushState(
			{
    			id: 'city'
			}, 
			'', 
			newLocation
		);

		//create new edit link
		$cityView.find('#cityEditLinkContainer').empty().append(generateCityEditLink(newLocation));

		//change the recover button for the delete button (behaviour and aspect)
		$cityView.find('button.cityRecoverButton').removeClass('cityRecoverButton').addClass('cityDeleteButton')
												  .empty().append(generateFaIcon(_removeIcon)).append('Delete');

		//if sunrise-sunset view exists: enable its controls
		$sunriseSunsetView = $('#sunriseSunsetView');

		if($sunriseSunsetView.length > 0) {
			$sunriseSunsetView.find('#timezoneSelector').prop('disabled', false);
			$sunriseSunsetView.find('#dateSelector').prop('disabled', false);
			$sunriseSunsetView.find('#sunriseSunsetCalculateButton').prop('disabled', false);
			$sunriseSunsetView.find('#sunriseSunsetForm').attr('action', newLocation);
		}

		//un-mute the text and ready to go
		$cityView.removeClass('text-muted');
		return $cityView;

	};


	var disableCityView = function($cityView) {

		//disable edit link: substitute it with a disabled button
		$cityView.find('#cityEditLinkContainer').empty().append(generateDisabledEditButton());

		//change the delete button for the recover button (behaviour and aspect)
		$cityView.find('button.cityDeleteButton').removeClass('cityDeleteButton').addClass('cityRecoverButton')
												 .empty().append(generateFaIcon(_recoverIcon)).append('Recover');

		//if sunrise-sunset view exists: disable its controls
		$sunriseSunsetView = $('#sunriseSunsetView');

		if($sunriseSunsetView.length > 0) {
			$sunriseSunsetView.find('#timezoneSelector').prop('disabled', true);
			$sunriseSunsetView.find('#dateSelector').prop('disabled', true);
			$sunriseSunsetView.find('#sunriseSunsetCalculateButton').prop('disabled', true);
		}

		//mute the text and ready to go
		$cityView.addClass('text-muted');
		return $cityView;

	};



	var recoverCity = function() {

		let $cityView = $('#cityView');

		let city = {
			name: 		$cityView.data('name'),
			latitude: 	parseFloat($cityView.data('latitude')),
			longitude: 	parseFloat($cityView.data('longitude'))
		};

		$.when(Application.sendData('POST', _endpoint, city))

		.done(function(data, textStatus, jqXHR) {

			let newLocation = jqXHR.getResponseHeader('Location').replace('/api', '');	//we don't want no API links here

			$cityView.fadeOut(function() {

				$.when(setCityStatus(true))
				.then(enableCityView($cityView, newLocation).fadeIn());

			});

		})

		.fail(function(jqXHR, textStatus, errorThrown) {
			Application.debugError(jqXHR, textStatus, errorThrown);
		});

	};


	var deleteCity = function() {

		let $cityView = $('#cityView');
		let id = $('#cityView').data('id');

		$.when(Application.sendData('DELETE', _endpoint + '/' + id))

		.done(function(data, textStatus, jqXHR) {

			$cityView.fadeOut(function() {

				$.when(setCityStatus(false))
				.then(disableCityView($cityView).fadeIn());

			});

		})

		.fail(function(jqXHR, textStatus, errorThrown) {
			Application.debugError(jqXHR, textStatus, errorThrown);
		});

	};


	var init = function(endpoint) {

		//retrieve the connection data
		_endpoint = Application.getRemoteUrl() + endpoint;

		//http://www.daterangepicker.com/#examples
		$('#dateSelector').daterangepicker({
			startDate: $('#dateSelector').data('startDate'),
			singleDatePicker: true,
			locale: {
				format: 'YYYY-MM-DD'
			}
		});

		//connect the click event of the delete button to deleting a city with AJAX
		$(document).on('click', '.cityDeleteButton', deleteCity);

		//connect the click event of the delete button to creating a city with AJAX
		$(document).on('click', '.cityRecoverButton', recoverCity);

	};


	return {
		init: init
	};


})(); //autoexecute
