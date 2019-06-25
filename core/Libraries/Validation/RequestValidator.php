<?php declare(strict_types = 1);
namespace System\Libraries\Validation;


use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\HeaderBag as SymfonyHeaderBag;
use Symfony\Component\HttpFoundation\AcceptHeader as SymfonyAcceptHeader;

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
	private $contentType 	= [];		//valid content type (input) in canonical MIME form
	private $responseTypes	= [];		//valid accept types (output)

	private $requireContentCharset 	= FALSE;	//requires a charset specified for the Content-Type: application/json; charset=utf-8
	private $acceptCharsetIsSet 	= FALSE;	//checks for charset first in accept-charset, later on in accept
	private $errors 				= [];		//validation errors
	private $validHeaders 			= FALSE;	//flag to validate headers only once in successive input validations


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
	

	public function setRequestContentType(array $contentType, bool $requireContentCharset = FALSE) : void {
		$this->contentType = $contentType;
		$this->setRequiredContentCharset($requireContentCharset);
	}


	public function setResponseAcceptTypes(array $acceptTypes) : void {
		$this->responseTypes = $acceptTypes;
	}


	public function setRequiredContentCharset(bool $requireContentCharset) : void {
		$this->requireContentCharset = $requireContentCharset;
	}


	//https://stackoverflow.com/questions/7055849/accept-and-accept-charset-which-is-superior
	private function assertAcceptCharset(SymfonyHeaderBag $headers) : void {

		$charset = $headers->get('accept-charset');

		if(empty($charset)) {
			return;
		}

		$charset = strtolower($charset);

		if(!in_array($charset, $this->responseTypes)) {
			throw HttpExceptionFactory::invalidCharset($charset, $this->responseTypes);
		}

		$this->acceptCharsetIsSet = TRUE;

	}


	private function assertContentType(SymfonyRequest $request) : void {

		//https://stackoverflow.com/questions/5661596/do-i-need-a-content-type-for-http-get-requests
		//It means that the Content-Type HTTP header should be set only for PUT and POST requests.
		if(!in_array($request->getMethod(), self::REQUIRED_CONTENT_TYPE)) {
			return;
		}

		/*
		//this gives the Content-Type in the canonical MIME form (application/json => json), and no context about the charset
		$contentType = $request->get(ContentType();

		if(!in_array($contentType, $this->contentType)) {
			throw HttpExceptionFactory::unsupportedMediaType($content_type, array_keys($this->contentType));
		}
		*/

		//request with body, but content type not set
		$contentType = $request->headers->get('content-type');

		if(empty($contentType)) {
			throw HttpExceptionFactory::missingMediaType($this->contentType);
		}

		//check if charset is specified
		$charset = NULL;

		if(strpos($contentType, 'charset=') !== FALSE) {

			list($contentType, $charset) = explode(';', $contentType);
			list(, $charset) =  explode('charset=', trim($charset));
			$charset = strtolower(str_replace(';', '', $charset));

		}

		//Content-Type is invalid
		if($contentType !== key($this->contentType)) {
			throw HttpExceptionFactory::unsupportedMediaType($contentType, $this->contentType);
		}

		//Content-Type is valid, if the content charset is not required we are done here
		if(!$this->requireContentCharset) {
			return;
		}

		//charset not specified
		if($this->requireContentCharset && is_null($charset)) {
			throw HttpExceptionFactory::unspecifiedMediaTypeCharset($contentType, $this->contentType);
		}

		//invalid charset
		if($this->requireContentCharset && $charset !== $this->contentType[$contentType]) {
			throw HttpExceptionFactory::unsupportedMediaTypeCharset($contentType, $charset, $this->contentType);
		}

	}


	private function assertAccept(SymfonyRequest $request) : void {

		$acceptHeader = SymfonyAcceptHeader::fromString($request->headers->get('accept'));

		//default MIME type present, or void Accept values: we asume requester accepts anything, so escape 
		if($acceptHeader->has(self::DEFAULT_MIME_TYPE) || empty($acceptHeader->all())) {
			return;
		}

		foreach($acceptHeader->all() as $acceptType => $headerItem) {

			//type doesn't match
			if(!array_key_exists($acceptType, $this->responseTypes)) {
				continue;
			}

			//type matches and no need to check the charset, has been provided before with an Accept-Charset header (which has priority)
			if($this->acceptCharsetIsSet) {
				return;
			}

			$charset = $headerItem->getAttribute('charset');

			//charset not set implicitly
			if(is_null($charset)) {
				throw HttpExceptionFactory::missingAcceptCharset($acceptType, $this->responseTypes);
			}

			$charset = strtolower($charset);

			//charset is invalid
			if($this->responseTypes[$acceptType] !== $charset) {
				throw HttpExceptionFactory::notAcceptableCharset($acceptType, $charset, $this->responseTypes);
			}

			//success, so return
			return;

		}

		//no matches found
		throw HttpExceptionFactory::notAcceptable($request->headers->get('accept'), array_keys($this->responseTypes));

	}


	//can be called from the outside, for endpoints which doesn't require input (for example, /api/cities/[i:id])
	public function validateHeaders(SymfonyRequest $request) : void {

		if(!$this->validHeaders) {
			$this->assertAcceptCharset($request->headers);
			$this->assertContentType($request);
			$this->assertAccept($request);
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
			throw HttpExceptionFactory::badRequest('Malformed request body');
		}

		if(empty($processedInput) && empty($this->errors)) {
			throw HttpExceptionFactory::unprocessableEntityErrorFields($expectedFields);	
		}

		if(empty($processedInput) && !empty($this->errors)) {
			throw HttpExceptionFactory::unprocessableEntityUnexpectedFields($this->errors);
		}

		if(!empty($processedInput) && !empty($this->errors)) {
			throw HttpExceptionFactory::unprocessableEntityErrorFields($this->errors);
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
			throw HttpExceptionFactory::badRequest('Malformed request body');
		}

		if(empty($processedInput) && !empty($this->errors)) {
			throw HttpExceptionFactory::unprocessableEntityUnexpectedFields($this->errors);
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