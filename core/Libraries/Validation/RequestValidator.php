<?php declare(strict_types = 1);
namespace System\Libraries\Validation;


use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\HeaderBag as SymfonyHeaderBag;
use System\Libraries\ErrorHandling\Exceptions\Http\HttpExceptionFactory;


/*
	HOW TO USE THIS CLASS:

	Load the expected content and accept types with setContent([...]) and setAccept([...]), now we are ready to validate inputs.
	The validation methods first attempt to validate the request headers. On successive input validations on the same request cycle,
	the headers are not validated anymore (they are only validated on the first time).

	Validation methods:

	assertStrictJsonInput(): the input is a raw JSON body. Validation fails if only one of the fields is missing or with a wrong type

	assertFlexibleJsonInput(): the input is a raw JSON body. Validation passes if no errors are present, no matter if some/all parameters are missing

	assertFlexibleFormInput(): the input is a set of form urlencoded values. Doesn't care if extra values are present, and if requested values aren't correct typed simply ignores them. Only raises an error if incorrect content-type (when evaluated)

	assertFlexibleQueryParameters(): the input is a set from query params. Doesn't care if extra values are present, and if requested values aren't correct typed simply ignores them. Only raises an error if incorrect content-type (when evaluated)

*/


final class RequestValidator {


	private const DEFAULT_MIME_TYPE 	= '*/*';
	private const REQUIRED_CONTENT_TYPE = ['PUT', 'POST'];

	private $rules 			= [];		//validation rules for types
	private $contentTypes 	= [];		//valid content types (input)
	private $accept 		= [];		//valid accept types (output)

	private $correctCharset = FALSE;	//checks for charset first in accept-charset, later on in accept
	private $errors 		= [];		//validation errors
	private $validHeaders 	= FALSE;	//flag to validate headers only once in successive input validations


	public function __construct() {

		$this->rules = [

			'int'	=> function(&$field, bool $conversion = FALSE) : bool { 
				$field = $conversion? (int)$field : $field;
				return is_int($field); 
			},

			'float' => function(&$field, bool $conversion = FALSE) : bool { 
				$field = $conversion? (float)$field : $field;
				return is_float($field); 
			},

			'string'=> function($field, bool $conversion = FALSE) : bool { 
				return is_string($field); 
			}

		];

	}
	

	public function setContentType(array $contentTypes) : void {
		$this->contentTypes = $contentTypes;
	}


	public function setAccept(array $acceptTypes) : void {
		$this->accept = $acceptTypes;
	}


	//https://stackoverflow.com/questions/7055849/accept-and-accept-charset-which-is-superior
	private function assertAcceptCharset(SymfonyHeaderBag $headers) : void {

		$charset = $headers->get('accept-charset');

		if(empty($charset)) {
			return;
		}

		$charset = strtolower($charset);

		if(!in_array($charset, $this->accept)) {
			throw HttpExceptionFactory::invalidCharset($charset, $this->accept);
		}

		$this->correctCharset = TRUE;

	}


	private function assertContentType(SymfonyHeaderBag $headers, string $method) : void {

		$content_type = $headers->get('content-type');

		//https://stackoverflow.com/questions/5661596/do-i-need-a-content-type-for-http-get-requests
		//It means that the Content-Type HTTP header should be set only for PUT and POST requests.
		if(empty($content_type)) {

			//no content type, but not needed anyway
			if(!in_array($method, self::REQUIRED_CONTENT_TYPE)) {
				return;
			}

			//PUT or POST: sorry, but this fails
			throw HttpExceptionFactory::missingMediaType(array_keys($this->contentTypes));

		}

		$types = explode(',', $content_type);

		foreach($types as &$type) {

			if(strpos($type, ';charset=')) {

				list($type, $charset) = explode(';charset=', $type);

				if($this->correctCharset) {
					continue;
				}

				$charset = strtolower(str_replace(';', '', $charset));

				if(array_key_exists($type, $this->contentTypes) && $this->contentTypes[$type] !== $charset) {
					throw HttpExceptionFactory::unsupportedMediaTypeCharset($type, $charset, $this->contentTypes);
				}

			}

		}

		if(empty(array_intersect($types, array_keys($this->contentTypes)))) {
			throw HttpExceptionFactory::unsupportedMediaType($content_type, array_keys($this->contentTypes));
		}

	}


	private function assertAccept(SymfonyHeaderBag $headers) : void {

		//browser always fills with predefined */* if not specified. We assume if nothing is specified, they will expect anything
		$accept = $headers->get('accept') ?? '*/*'; 
		$types = explode(',', $accept);

		//default MIME type: requester accepts anything, so escape 
		if(in_array(self::DEFAULT_MIME_TYPE, $types)) {
			return;
		}

		foreach($types as &$type) {

			if(strpos($type, ';q=')) {
				list($type, $priority) = explode(';q=', $type);
				$priority = str_replace(';', '', $priority);
			}

			if(strpos($type, ';charset=')) {

				list($type, $charset) = explode(';charset=', $type);

				if($this->correctCharset) {
					continue;
				}

				$charset = strtolower(str_replace(';', '', $charset));

				if(array_key_exists($type, $this->contentTypes) && $this->contentTypes[$type] !== $charset) {
					throw HttpExceptionFactory::notAcceptableCharset($type, $charset, $this->accept);
				}

			}

		}

		if(empty(array_intersect($types, array_keys($this->accept)))) {
			throw HttpExceptionFactory::notAcceptable($accept, array_keys($this->accept));
		}

	}


	//can be called from the outside, for endpoints which doesn't require input (for example, /api/cities/[i:id])
	public function validateHeaders(SymfonyRequest $request) : void {

		if(!$this->validHeaders) {
			$this->assertAcceptCharset($request->headers);
			$this->assertContentType($request->headers, $request->getMethod());
			$this->assertAccept($request->headers);
		}

		$this->validHeaders = TRUE;

	}


	private function getValidJsonInputFields(SymfonyRequest $request, array $fields) : ?array {

		//$input = json_decode(file_get_contents('php://input'), TRUE);
		$input = json_decode($request->getContent(), TRUE);

		//malformed JSON
		if(is_null($input)) {
			return NULL;
		}

		//void input: error in all the fields (all the fields are missing)
		if(empty($input)) {
			return [];
		}

		//initialize the error array
		$this->errors = [];

		//unexpected fields on the input: return them, they count as errors, request must avoid them
		$unexpectedFields = array_diff(array_keys($input), array_keys($fields));

		if(!empty($unexpectedFields)) {
			$this->errors = $unexpectedFields;
			return [];
		}

		//check for missing or error fields
		$output = [];

		foreach($fields as $field=>$rule) {

			//if it exists: calls the callback rule attempting to convert the value to the correct type first. If it fails or 
			//if it is missing (or null), marks this as an error
			$exists = array_key_exists($field, $input);

			if($exists && !is_null($input[$field]) && $this->rules[$rule]($input[$field], TRUE)) { //call the callback in rules
				$output[$field] = $input[$field];
				continue;
			}

			$this->errors[$field] = $rule; 	

		}

		return $output;

	}


	//validation fails if only one of the fields is missing or with a wrong type
	public function assertStrictJsonInput(SymfonyRequest $request, array $expectedFields) : array {

		//validate the headers if needed
		$this->validateHeaders($request);

		//process the input
		$processedInput = $this->getValidJsonInputFields($request, $expectedFields);

		if(is_null($processedInput)) {
			throw HttpExceptionFactory::badRequestMalformedJson();
		}

		if(empty($processedInput) && empty($this->errors)) {
			throw HttpExceptionFactory::badRequestErrorFields($expectedFields);	
		}

		if(empty($processedInput) && !empty($this->errors)) {
			throw HttpExceptionFactory::badRequestUnexpectedFields($this->errors);
		}

		if(!empty($processedInput) && !empty($this->errors)) {
			throw HttpExceptionFactory::badRequestErrorFields($this->errors);
		}

		return $processedInput;

	}


	//validation passes if no errors are present, no matter if some/all parameters are missing
	public function assertFlexibleJsonInput(SymfonyRequest $request, array $expectedFields) : array {

		//validate the headers if needed
		$this->validateHeaders($request);

		//process the input
		$processedInput = $this->getValidJsonInputFields($request, $expectedFields);

		if(is_null($processedInput)) {
			throw HttpExceptionFactory::badRequestMalformedJson();
		}

		if(empty($processedInput) && !empty($this->errors)) {
			throw HttpExceptionFactory::badRequestUnexpectedFields($this->errors);
		}

		return $processedInput;

	}


	//retrieve form urlencoded values. Doesn't care if extra values are present, and if requested values aren't correct typed
	//simply ignores them. Only raises an error if incorrect content-type (when needed)
	public function assertFlexibleFormInput(SymfonyRequest $request, array $expectedFields) : array {

		//validate the headers if needed
		$this->validateHeaders($request);

		//process the input
		$input = $request->request->all(); //$_POST object

		if(empty($input)) {
			return [];
		}

		//get only the correct fields, ignore the ones missing ore failing
		$output = [];

		foreach($expectedFields as $field=>$rule) {

			//if it exists and is not empty: calls de callback rule attempting to convert the value to the correct type first
			$exists = array_key_exists($field, $input);

			if($exists && !empty($input[$field]) && $this->rules[$rule]($input[$field], TRUE)) { 
				$output[$field] = $input[$field];
				continue;
			}

		}

		return $output;

	}


	//retrieve url query params. Doesn't care if extra values are present, and if requested values aren't correct typed
	//simply ignores them. Only raises an error if incorrect content-type (when needed)
	public function assertFlexibleQueryParameters(SymfonyRequest $request, array $expectedParameters) : array {

		//validate the headers if needed
		$this->validateHeaders($request);

		//process the input
		$input = $request->query->all(); //$_GET

		if(empty($input)) {
			return [];
		}

		//get only the correct fields, ignore the ones missing ore failing
		$output = [];

		foreach($expectedParameters as $parameter=>$rule) {

			//if it exists and is not empty: calls de callback rule attempting to convert the value to the correct type first
			if(array_key_exists($parameter, $input) && !empty($input[$parameter]) && $this->rules[$rule]($input[$parameter], TRUE)) { 
				$output[$parameter] = $input[$parameter];
				continue;
			}

		}

		return $output;

	}


}