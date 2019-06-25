
var ListCities = (function() {


	var _endpoint 		= '';

	const _editIcon 	= 'fa-edit';
	const _removeIcon 	= 'fa-trash-alt';
	const _recoverIcon  = 'fa-trash-restore';


	var generateFaIcon = function(className) {
		return $('<i></i>').addClass('fa fa-2x ' +  className);
	};


	var generateCityLink = function(href) {
		return $('<a></a>').attr('href', href);
	};


	var enableCityRow = function($cityRow, newLocation) {

		//update the city id contained in the $cityRow
		let id = parseInt(newLocation.split('/').pop());
		$cityRow.data('id', id);

		//empty cells
		$cityRow.find('td.cityViewLinkContainer').empty();
		$cityRow.find('td.cityEditLinkContainer').empty();

		//substitute them by clickable info
		$cityRow.find('td.cityViewLinkContainer').append(generateCityLink(newLocation).text($cityRow.data('name')));
		$cityRow.find('td.cityEditLinkContainer').append(generateCityLink(newLocation).append(generateFaIcon(_editIcon)));

		//change the recover button for the delete button (behaviour and aspect)
		$cityRow.find('button.cityRecoverButton').removeClass('cityRecoverButton').addClass('cityDeleteButton')
												 .empty().append(generateFaIcon(_removeIcon));

		//strip color and ready to go
		$cityRow.removeClass('table-danger').removeClass('text-muted');
		return $cityRow;

	};


	var disableCityRow = function($cityRow) {

		//destroy links
		$cityRow.find('td.cityViewLinkContainer').empty();
		$cityRow.find('td.cityEditLinkContainer').empty();

		//substitute them by non-clickable info
		$cityRow.find('td.cityViewLinkContainer').text($cityRow.data('name'));
		$cityRow.find('td.cityEditLinkContainer').append(generateFaIcon(_editIcon));

		//change the delete button for the recover button (behaviour and aspect)
		$cityRow.find('button.cityDeleteButton').removeClass('cityDeleteButton').addClass('cityRecoverButton')
												.empty().append(generateFaIcon(_recoverIcon));

		//add color and ready to go
		$cityRow.addClass('table-danger text-muted');
		return $cityRow;

	};


	var recoverCity = function() {

		let $cityRow = $(this).parents('.cityRow');

		let city = {
			name: 		$cityRow.data('name'),
			latitude: 	parseFloat($cityRow.data('latitude')),
			longitude: 	parseFloat($cityRow.data('longitude'))
		};

		$.when(Application.sendData('POST', _endpoint, city))

		.done(function(data, textStatus, jqXHR) {

			let newLocation = jqXHR.getResponseHeader('Location').replace('/api', '');	//we don't want no API links here

			$cityRow.fadeOut(function() {
				enableCityRow($cityRow, newLocation).fadeIn();
			});

		})

		.fail(function(jqXHR, textStatus, errorThrown) {
			Application.debugError(jqXHR, textStatus, errorThrown);
		});

	};


	var deleteCity = function() {

		let $cityRow = $(this).parents('.cityRow');
		let id = $cityRow.data('id');

		$.when(Application.sendData('DELETE', _endpoint + '/' + id))

		.done(function(data, textStatus, jqXHR) {

			$cityRow.fadeOut(function() {
				disableCityRow($cityRow).fadeIn();
			});

		})

		.fail(function(jqXHR, textStatus, errorThrown) {
			Application.debugError(jqXHR, textStatus, errorThrown);
		});


	};


	var init = function(endpoint) {

		//retrieve the connection data
		_endpoint = Application.getRemoteUrl() + endpoint;

		//connect the click event of the delete button to deleting a city with AJAX
		$(document).on('click', '.cityDeleteButton', deleteCity);

		//connect the click event of the delete button to creating a city with AJAX
		$(document).on('click', '.cityRecoverButton', recoverCity);

	};


	return {
		init: init
	};


})(); //autoexecute
