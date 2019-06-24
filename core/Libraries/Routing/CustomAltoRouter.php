<?php declare(strict_types = 1);
namespace System\Libraries\Routing;


use AltoRouter;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;


use System\Libraries\Routing\Route\RouteInterface;
use System\Libraries\Routing\Route\RouteFactory;


final class CustomAltoRouter extends AltoRouter {


	private const DEFAULT_URI 		= '/';
	private const DEFAULT_METHOD 	= 'GET';

	private $routeFactory = NULL; // \System\Libraries\Routing\Route\RouteFactory


	public function __construct(RouteFactory $routeFactory, string $basePath, array $routes) {
		parent::__construct($routes, $basePath);
		$this->routeFactory = $routeFactory;
	}


	public function getMatch(SymfonyRequest $request) : ?RouteInterface {

		$match = $this->match(
			$request->server->get('REQUEST_URI') ?? DEFAULT_URI, 
			$request->server->get('REQUEST_METHOD') ?? DEFAULT_METHOD
		);

		if($match === FALSE) {
			return NULL;
		}

		return $this->routeFactory->create($request, $match);

	}


	/**
	 * Match a given Request Url against stored routes
	 * @param string $requestUrl
	 * @param string $requestMethod
	 * @return array|boolean Array with route information on success, array with allowed methods when 405, FALSE on failure (404).
	 */
	public function match($requestUrl = NULL, $requestMethod = NULL) {

		$allowed_methods = [];
		$params = [];
		$match = FALSE;

		//strip base path from request url
		$requestUrl = substr($requestUrl, strlen($this->basePath));

		//strip query string (?a=b) from Request Url, BUT capture the query string
		if(($strpos = strpos($requestUrl, '?')) !== FALSE) {
			$requestUrl = substr($requestUrl, 0, $strpos);
		}
		
		//strip last slash unless / is requested
		if(strlen($requestUrl) > 1 && substr($requestUrl, -1) === '/') {
			$requestUrl = substr($requestUrl, 0, strlen($requestUrl) -1);
		}

		//iterate over the routes to find matches
		foreach($this->routes as $index=>$routeData) {

			list($method, $_route, $target, $group) = $routeData;

			//check for a wildcard (matches all)
			if($_route === '*') {
				$match = TRUE;
			} 

			//@ regex delimiter
			elseif(isset($_route[0]) && $_route[0] === '@') {
				$pattern = '`' . substr($_route, 1) . '`u';
				$match = (bool)preg_match($pattern, $requestUrl, $params); // FALSE, 0, 1 == match
			} 

			else {

				$route = NULL;
				$regex = FALSE;
				$j = 0;
				$n = $_route[0] ?? NULL;
				$i = 0;

				//find the longest non-regex substring and match it against the URI
				while(TRUE) {

					if(!isset($_route[$i])) {
						break;
					} 

					elseif($regex === FALSE) {

						$c = $n;
						$regex = $c === '[' || $c === '(' || $c === '.';

						if ($regex === FALSE && isset($_route[$i+1]) !== FALSE) {
							$n = $_route[$i + 1];
							$regex = $n === '?' || $n === '+' || $n === '*' || $n === '{';
						}

						if($regex === FALSE && $c !== '/' && (!isset($requestUrl[$j]) || $c !== $requestUrl[$j])) {
							continue 2;
						}

						$j++;
					}

					$route .= $_route[$i++];

				}

				$regex = $this->compileRoute($route);
				$match = (bool)preg_match($regex, $requestUrl, $params); // FALSE, 0, 1 == match

			}

			//NO MATCH OR ERROR: next route
			if(!$match) {
				continue;
			}

			$methods = explode('|', $method);
			$method_match = FALSE;

			//check for method matching. If match, abandon early. (CHEAP)
			foreach($methods as $method) {

				if(strcasecmp($requestMethod, $method) === 0) {
					$method_match = TRUE;
					break;
				}

				else {
					$allowed_methods[] = $method;
					continue 2;	//next iteration
				}

			}

			//https://stackoverflow.com/questions/8174749/how-can-i-get-only-named-captures-from-preg-match
			//preg_match will always return the numeric indexes regardless of named capturing groups
			if($params) {
				foreach($params as $key => $value) {
					if(is_numeric($key)) unset($params[$key]);
				}
			}

			return [
				'group' 	=> $group,
				'target' 	=> $target,
				'params' 	=> $params
			];

		}

		//ROUTE MATCH, BUT WITHOUT METHOD MATCH
		if(!empty($allowed_methods)) {

			return [
				'allowed_methods' => $allowed_methods
			];	

		}

		//NO MATCH AT ALL
		return FALSE;

	}


	/**
	 * Compile the regex for a given route (EXPENSIVE)
	 */
	private function compileRoute($route) {

		if(preg_match_all('`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`', $route, $matches, PREG_SET_ORDER)) {

			$matchTypes = $this->matchTypes;

			foreach($matches as $match) {

				list($block, $pre, $type, $param, $optional) = $match;

				if (isset($matchTypes[$type])) {
					$type = $matchTypes[$type];
				}

				if ($pre === '.') {
					$pre = '\.';
				}

				$optional = $optional !== '' ? '?' : NULL;
				
				//Older versions of PCRE require the 'P' in (?P<named>)
				$pattern = '(?:'
						. ($pre !== '' ? $pre : NULL)
						. '('
						. ($param !== '' ? "?P<$param>" : NULL)
						. $type
						. ')'
						. $optional
						. ')'
						. $optional;

				$route = str_replace($block, $pattern, $route);

			}

		}

		return "`^$route$`u";

	}


}
