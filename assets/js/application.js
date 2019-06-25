
var Application = (function() {


	var _baseUrl = '';
	var _requestUri = '';


	var getBaseUrl = function() {
		return _baseUrl;
	};


	var getRequestUri = function() {
		return _requestUri;
	};


	var getScrollDirection = function() {

		var parts = requestUri.split('#');

		if(parts.length == 1) {
			return '';
		}
		return parts.pop();

	};


	var getData = function(requestUrl) {
		return $.get(requestUrl);
	};


	var sendData = function(method, requestUrl, data = null, headers = []) {

		let request = {
		    url: requestUrl,
		    type: method,
		    contentType: 'application/json;charset=utf-8',
		    dataType: 'json',	//automatically parse the response data for us
		    accepts: {
		    	json: 'application/json;charset=utf-8'
		    }
		};

		if(data !== null) {
			request['data'] = JSON.stringify(data);
		}

		if(headers.length > 0) {
			request['headers'] = headers
		}

		return $.ajax(request);

	};


	var getErrorMessage = function(jqXHR, textStatus, errorThrown) {

		if(jqXHR.status === 0) {
			return jqXHR.statusText;
		}

		let basicErrorMessage = 'Error ' + jqXHR.status + ': ' + errorThrown;

		if(jqXHR.responseText === undefined || jqXHR.responseText === null || jqXHR.responseText.length === 0) {
			return basicErrorMessage;
		}

		//additional error information passed on the response body
		let errors = JSON.parse(jqXHR.responseText).errors || [];
		let errorMessages = [];

		for(const error of errors) {

			let errorString = '(' + error.message;
			console.log(error);

			if(error.file !== undefined && error.file !== null) {
				errorString += ' on file: ' + error.file;
			}

			if(error.line !== undefined && error.line !== null) {
				errorString += ' at line: ' + error.line;
			}

			errorString += ')';
			errorMessages.push(errorString);

		}

		if(errorMessages.length > 0) {
			basicErrorMessage += ' ' + errorMessages.join(', ');
		}

		return basicErrorMessage;

	};


	var debugError = function(jqXHR, textStatus, errorThrown) {
		console.log(getErrorMessage(jqXHR, textStatus, errorThrown));
	};


	var init = function(baseUrl) {
		_baseUrl = baseUrl;
		_requestUri = window.location.href.replace(baseUrl, '');
	};


	return {

		//public functions
		init: 				init,

		getBaseUrl: 		getBaseUrl,
		getRequestUri: 		getRequestUri,
		getScrollDirection: getScrollDirection,

		getData: 			getData, 
		sendData: 			sendData,
		getErrorMessage: 	getErrorMessage,
		debugError: 		debugError,

		Modules: {
			InputCity: 		InputCity,	//allows to communicate with the API to create and edit cities
			ViewCity:   	ViewCity,	//allows to work with the daterangepicker element and communicate with the API to delete/recover
			ListCities: 	ListCities	//allows to communicate with the API to delete/recover cities
		}

	};

})(); //autoexecute


//at start:
$(function() {

	//setting up application
	let baseUrl = $('footer').data('baseUrl'); 
	Application.init(baseUrl);

	//routing
	switch(true) {

		// /cities/[i:id]/edit
		case Application.getRequestUri().match(/^(\/cities\/[0-9]+\/edit)$/) !== null: 

			let pathItems = Application.getRequestUri().split('/'); // /['', 'cities', '[i:id]', 'edit'] 
			let id = parseInt(pathItems[2]);

			console.log('/cities/[i:id]/edit');
			Application.Modules.InputCity.init('PUT', '/api/cities/' + id);
			break;

		// /cities(/page-[i:page])
		case Application.getRequestUri().match(/^(\/cities)$/) !== null:
		case Application.getRequestUri().match(/^(\/cities\/page-)[0-9]+/) !== null: 

			console.log('/cities');
			Application.Modules.ListCities.init('/api/cities');	//DELETE and POST (recover)
			break;

		// /cities/search(/page-[i:page])
		case Application.getRequestUri().match(/^(\/cities\/search)$/) !== null:
		case Application.getRequestUri().match(/^(\/cities\/search\/page-)[0-9]+/) !== null: 

			console.log('/cities/search');
			Application.Modules.ListCities.init('/api/cities');	//DELETE and POST (recover)
			break;

		// /cities/[i:id]
		case Application.getRequestUri().match(/^(\/cities\/)[0-9]+/) !== null:

			console.log('/cities/[i:id]');
			Application.Modules.ViewCity.init('/api/cities');	//DELETE and POST (recover)
			break;

		// /cities/new
		case Application.getRequestUri().match(/^(\/cities\/new)$/) !== null: 			

			console.log('/cities/new');
			Application.Modules.InputCity.init('POST', '/api/cities');
			break;

	}

	//Toggle the side navigation
	$("#sidebarToggle").on('click',function(e) {
		e.preventDefault();
		$("body").toggleClass("sidebar-toggled");
		$(".sidebar").toggleClass("toggled");
	});

	//Prevent the content wrapper from scrolling when the fixed side navigation hovered over
	$('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
		if($(window).width() > 768) {
			var e0 = e.originalEvent,
			delta = e0.wheelDelta || -e0.detail;
			this.scrollTop += (delta < 0 ? 1 : -1) * 30;
			e.preventDefault();
		}
	});

	//Scroll to top button appear
	$(document).on('scroll',function() {
		var scrollDistance = $(this).scrollTop();
		if(scrollDistance > 100) {
			$('.scroll-to-top').fadeIn();
		} 
		else {
			$('.scroll-to-top').fadeOut();
		}
	});

	//Smooth scrolling using jQuery easing
	$(document).on('click', 'a.scroll-to-top', function(event) {
		var $anchor = $(this);
		$('html, body').stop().animate({
			scrollTop: ($($anchor.attr('href')).offset().top)
		}, 1000, 'easeInOutExpo');
		event.preventDefault();
	});

});
