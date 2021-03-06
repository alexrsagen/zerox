<?php namespace ZeroX;
use \ZeroX\Vars\VarCollection;
use \ZeroX\Encoding\Json;
use \ZeroX\Encoding\Querystring;

class Router {
	// Constants

	// HTTP 1xx - Informational
	const HTTP_CONTINUE                               = 100;
	const HTTP_SWITCHING_PROTOCOLS                    = 101;
	const HTTP_PROCESSING                             = 102;

	// HTTP 2xx - Success
	const HTTP_OK                                     = 200;
	const HTTP_CREATED                                = 201;
	const HTTP_ACCEPTED                               = 202;
	const HTTP_NON_AUTHORITATIVE_INFORMATION          = 203;
	const HTTP_NO_CONTENT                             = 204;
	const HTTP_RESET_CONTENT                          = 205;
	const HTTP_PARTIAL_CONTENT                        = 206;
	const HTTP_MULTI_STATUS                           = 207;
	const HTTP_ALREADY_REPORTED                       = 208;
	const HTTP_IM_USED                                = 226;

	// HTTP 3xx - Redirection
	const HTTP_MULTIPLE_CHOICES                       = 300;
	const HTTP_MOVED_PERMANENTLY                      = 301;
	const HTTP_FOUND                                  = 302;
	const HTTP_SEE_OTHER                              = 303;
	const HTTP_NOT_MODIFIED                           = 304;
	const HTTP_USE_PROXY                              = 305;
	const HTTP_TEMPORARY_REDIRECT                     = 307;
	const HTTP_PERMANENT_REDIRECT                     = 308;

	// HTTP 4xx - Client Error
	const HTTP_BAD_REQUEST                            = 400;
	const HTTP_UNAUTHORIZED                           = 401;
	const HTTP_PAYMENT_REQUIRED                       = 402;
	const HTTP_FORBIDDEN                              = 403;
	const HTTP_NOT_FOUND                              = 404;
	const HTTP_METHOD_NOT_ALLOWED                     = 405;
	const HTTP_NOT_ACCEPTABLE                         = 406;
	const HTTP_PROXY_AUTHENTICATION_REQUIRED          = 407;
	const HTTP_REQUEST_TIMEOUT                        = 408;
	const HTTP_CONFLICT                               = 409;
	const HTTP_GONE                                   = 410;
	const HTTP_LENGTH_REQUIRED                        = 411;
	const HTTP_PRECONDITION_FAILED                    = 412;
	const HTTP_PAYLOAD_TOO_LARGE                      = 413;
	const HTTP_REQUEST_URI_TOO_LONG                   = 414;
	const HTTP_UNSUPPORTED_MEDIA_TYPE                 = 415;
	const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE        = 416;
	const HTTP_EXPECTATION_FAILED                     = 417;
	const HTTP_IM_A_TEAPOT                            = 418;
	const HTTP_MISDIRECTED_REQUEST                    = 421;
	const HTTP_UNPROCESSABLE_ENTITY                   = 422;
	const HTTP_LOCKED                                 = 423;
	const HTTP_FAILED_DEPENDENCY                      = 424;
	const HTTP_UPGRADE_REQUIRED                       = 426;
	const HTTP_PRECONDITION_REQUIRED                  = 428;
	const HTTP_TOO_MANY_REQUESTS                      = 429;
	const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE        = 431;
	const HTTP_CONNECTION_CLOSED_WITHOUT_RESPONSE     = 444;
	const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS          = 451;
	const HTTP_CLIENT_CLOSED_REQUEST                  = 499;

	// HTTP 5xx - Server Error
	const HTTP_INTERNAL_SERVER_ERROR                  = 500;
	const HTTP_NOT_IMPLEMENTED                        = 501;
	const HTTP_BAD_GATEWAY                            = 502;
	const HTTP_SERVICE_UNAVAILABLE                    = 503;
	const HTTP_GATEWAY_TIMEOUT                        = 504;
	const HTTP_HTTP_VERSION_NOT_SUPPORTED             = 505;
	const HTTP_VARIANT_ALSO_NEGOTIATES                = 506;
	const HTTP_INSUFFICIENT_STORAGE                   = 507;
	const HTTP_LOOP_DETECTED                          = 508;
	const HTTP_NOT_EXTENDED                           = 510;
	const HTTP_NETWORK_AUTHENTICATION_REQUIRED        = 511;
	const HTTP_NETWORK_CONNECT_TIMEOUT_ERROR          = 599;

	const HTTP_STATUSTEXT = [
		// HTTP 1xx - Informational
		self::HTTP_CONTINUE                           => 'Continue',
		self::HTTP_SWITCHING_PROTOCOLS                => 'Switching protocols',
		self::HTTP_PROCESSING                         => 'Processing',

		// HTTP 2xx - Success
		self::HTTP_OK                                 => 'Ok',
		self::HTTP_CREATED                            => 'Created',
		self::HTTP_ACCEPTED                           => 'Accepted',
		self::HTTP_NON_AUTHORITATIVE_INFORMATION      => 'Non-authoritative information',
		self::HTTP_NO_CONTENT                         => 'No content',
		self::HTTP_RESET_CONTENT                      => 'Reset content',
		self::HTTP_PARTIAL_CONTENT                    => 'Partial content',
		self::HTTP_MULTI_STATUS                       => 'Multi status',
		self::HTTP_ALREADY_REPORTED                   => 'Already reported',
		self::HTTP_IM_USED                            => 'IM used',

		// HTTP 3xx - Redirection
		self::HTTP_MULTIPLE_CHOICES                   => 'Multiple choices',
		self::HTTP_MOVED_PERMANENTLY                  => 'Moved permanently',
		self::HTTP_FOUND                              => 'Found',
		self::HTTP_SEE_OTHER                          => 'See other',
		self::HTTP_NOT_MODIFIED                       => 'Not modified',
		self::HTTP_USE_PROXY                          => 'Use proxy',
		self::HTTP_TEMPORARY_REDIRECT                 => 'Temporary redirect',
		self::HTTP_PERMANENT_REDIRECT                 => 'Permanent redirect',

		// HTTP 4xx - Client Error
		self::HTTP_BAD_REQUEST                        => 'Bad request',
		self::HTTP_UNAUTHORIZED                       => 'Unauthorized',
		self::HTTP_PAYMENT_REQUIRED                   => 'Payment required',
		self::HTTP_FORBIDDEN                          => 'Forbidden',
		self::HTTP_NOT_FOUND                          => 'Not found',
		self::HTTP_METHOD_NOT_ALLOWED                 => 'Method not allowed',
		self::HTTP_NOT_ACCEPTABLE                     => 'Not acceptable',
		self::HTTP_PROXY_AUTHENTICATION_REQUIRED      => 'Proxy authentication required',
		self::HTTP_REQUEST_TIMEOUT                    => 'Request timeout',
		self::HTTP_CONFLICT                           => 'Conflict',
		self::HTTP_GONE                               => 'Gone',
		self::HTTP_LENGTH_REQUIRED                    => 'Length required',
		self::HTTP_PRECONDITION_FAILED                => 'Precondition failed',
		self::HTTP_PAYLOAD_TOO_LARGE                  => 'Payload too large',
		self::HTTP_REQUEST_URI_TOO_LONG               => 'Request URI too long',
		self::HTTP_UNSUPPORTED_MEDIA_TYPE             => 'Unsupported media type',
		self::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE    => 'Requested range not satisfiable',
		self::HTTP_EXPECTATION_FAILED                 => 'Expectation failed',
		self::HTTP_IM_A_TEAPOT                        => 'I\'m a teapot',
		self::HTTP_MISDIRECTED_REQUEST                => 'Misdirected request',
		self::HTTP_UNPROCESSABLE_ENTITY               => 'Unprocessable entity',
		self::HTTP_LOCKED                             => 'Locked',
		self::HTTP_FAILED_DEPENDENCY                  => 'Failed dependency',
		self::HTTP_UPGRADE_REQUIRED                   => 'Upgrade required',
		self::HTTP_PRECONDITION_REQUIRED              => 'Precondition required',
		self::HTTP_TOO_MANY_REQUESTS                  => 'Too many requests',
		self::HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE    => 'Request header fields too large',
		self::HTTP_CONNECTION_CLOSED_WITHOUT_RESPONSE => 'Connection closed without response',
		self::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS      => 'Unavailable for legal reasons',
		self::HTTP_CLIENT_CLOSED_REQUEST              => 'Client closed request',

		// HTTP 5xx - Server Error
		self::HTTP_INTERNAL_SERVER_ERROR              => 'Internal server error',
		self::HTTP_NOT_IMPLEMENTED                    => 'Not implemented',
		self::HTTP_BAD_GATEWAY                        => 'Bad gateway',
		self::HTTP_SERVICE_UNAVAILABLE                => 'Service unavailable',
		self::HTTP_GATEWAY_TIMEOUT                    => 'Gateway timeout',
		self::HTTP_HTTP_VERSION_NOT_SUPPORTED         => 'HTTP version not supported',
		self::HTTP_VARIANT_ALSO_NEGOTIATES            => 'Variant also negotiates',
		self::HTTP_INSUFFICIENT_STORAGE               => 'Insufficient storage',
		self::HTTP_LOOP_DETECTED                      => 'Loop detected',
		self::HTTP_NOT_EXTENDED                       => 'Not extended',
		self::HTTP_NETWORK_AUTHENTICATION_REQUIRED    => 'Network authentication required',
		self::HTTP_NETWORK_CONNECT_TIMEOUT_ERROR      => 'Network connect timeout error'
	];

	// Content types
	const CONTENT_TYPE_HTML = 'text/html';
	const CONTENT_TYPE_TEXT = 'text/plain';
	const CONTENT_TYPE_JSON = 'application/json';
	const CONTENT_TYPE_XML  = 'application/xml';

	// Character sets
	const CHARSET_UTF8 = 'utf-8';

	// Request methods
	const METHOD_GET     = 'GET';
	const METHOD_HEAD    = 'HEAD';
	const METHOD_POST    = 'POST';
	const METHOD_PUT     = 'PUT';
	const METHOD_DELETE  = 'DELETE';
	const METHOD_OPTIONS = 'OPTIONS';
	const METHOD_PATCH   = 'PATCH';

	// Special "methods" unique to this router which serve as catch-alls
	const METHOD_USE     = 'USE';
	const METHOD_ANY     = 'ANY';

	// Mapping of static methods of Router to RouterInstance methods

	protected static $global_instance = null;

	protected static function init() {
		if (self::$global_instance === null) {
			self::$global_instance = new RouterInstance(new VarCollection());
		}
	}

	public static function __callStatic(string $method_name, array $args) {
		self::init();
		if (is_callable([self::getInstance(), $method_name])) {
			return call_user_func_array([self::getInstance(), $method_name], $args);
		}
	}

	public static function getInstance() {
		self::init();
		return self::$global_instance->getInstance();
	}

	// Global utility methods

	public static $strict               = true;
	public static $html_suffix          = '';
	public static $html_minify_options  = [];
	protected static $headers           = [];
	protected static $response_code     = self::HTTP_OK;
	protected static $response_sent     = false;

	public static function stripTrailingSlash() {
		$path = static::getPath();
		$qs = static::getQueryString();
		if (substr($path, -1) === '/' && strlen($path) > 1) {
			static::redirect(rtrim($path, '/') . $qs, static::HTTP_TEMPORARY_REDIRECT);
			return true;
		}
		return false;
	}

	public static function getPost(?string $key = null) {
		static $data = null;
		if ($data === null) {
			$data = [];
			$request_method = static::getMethod();
			$content_type = static::parseRequestHeader('Content-Type');
			if (count($content_type) > 0) {
				switch ($request_method) {
				case 'POST': case 'PUT':
					switch ($content_type[0]) {
					case 'multipart/form-data':
						$data = Multipart::decodeFormData(file_get_contents('php://input'));
						break;
					case 'application/x-www-form-urlencoded':
						$data = Querystring::decode(file_get_contents('php://input'));
						break;
					case 'application/json':
						$data = Json::decode(file_get_contents('php://input'));
						break;
					}
					break;
				}
			}
		}
		if ($key === null) return $data;
		if (!is_array($data) || !array_key_exists($key, $data)) return null;
		return $data[$key];
	}

	public static function getPostBool(string $key) {
		$var = static::getPost($key);
		if ($var === '1' || is_string($var) && strtolower($var) === 'true') {
			return true;
		}
		return false;
	}

	public static function getQuery(?string $key = null) {
		static $data = null;
		if ($data === null) {
			$data = Querystring::decode(static::getQueryString());
		}
		if ($key === null) return $data;
		if (!is_array($data) || !array_key_exists($key, $data)) return null;
		return $data[$key];
	}

	public static function getQueryBool(string $key) {
		$var = static::getQuery($key);
		if ($var === '1' || is_string($var) && strtolower($var) === 'true') {
			return true;
		}
		return false;
	}

	public static function getRequestHeader(?string $key = null) {
		if ($key !== null) {
			$key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
			if (!array_key_exists($key, $_SERVER)) {
				return null;
			}
			return $_SERVER[$key];
		}

		$headers = [];
		foreach ($_SERVER as $k => $v) {
			if (substr($k, 0, 5) === 'HTTP_') {
				$headers[strtolower(str_replace('_', '-', substr($k, 5)))] = $v;
			}
		}
		return $headers;
	}

	public static function parseRequestHeader(string $key, string $delimiter = ',', string $endchar = ';') {
		$val = static::getRequestHeader($key);
		if ($val === null) return [];
		$end = strpos($val, $endchar);
		if ($end === false || empty($endchar)) $end = strlen($val);
		return array_map('trim', explode($delimiter, substr($val, 0, $end)));
	}

	public static function getMethod() {
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}

	public static function getRemoteAddress(bool $pack = false, bool $allow_x_forwarded_for = true) {
		$ip = $_SERVER['REMOTE_ADDR'];
		if ($allow_x_forwarded_for && array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		// fix IPv6-mapped IPv4 address
		if (substr($ip, 0, 7) === '::ffff:') {
			return $pack ? inet_pton($ip) : inet_ntop(inet_pton($ip));
		}
		return $pack ? inet_pton($ip) : $ip;
	}

	public static function getRemotePort(): int {
		return (int)$_SERVER['REMOTE_PORT'];
	}

	public static function getPath() {
		$path = $_SERVER['REQUEST_URI'];
		$qs_pos = strpos($path, '?');
		if ($qs_pos !== false) {
			$path = substr($path, 0, $qs_pos);
		}
		return $path;
	}

	public static function getQueryString() {
		$request_uri = $_SERVER['REQUEST_URI'];
		$qs_pos = strrpos($request_uri, '?');
		if ($qs_pos !== false) {
			return substr($request_uri, $qs_pos);
		}
		return '';
	}

	public static function getScheme() {
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' || !empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
			return 'https';
		}
		return 'http';
	}

	public static function getHost(bool $validate_strict = false) {
		$host = null;
		if (!empty($_SERVER['HTTP_HOST'])) {
			$host = $_SERVER['HTTP_HOST'];
		} elseif (!empty($_SERVER['SERVER_NAME'])) {
			$host = $_SERVER['SERVER_NAME'];
		} elseif (!empty($_SERVER['SERVER_ADDR'])) {
			$host = $_SERVER['SERVER_ADDR'];
		}
		if ($validate_strict && filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false) {
			return null;
		}
		return $host;
	}

	public static function sendResponse($response = null, string $content_type = self::CONTENT_TYPE_HTML, string $charset = 'utf-8', bool $minify = true) {
		if (self::$response_sent) return;

		// Prepare response
		if ($response !== null) {
			if ($content_type === self::CONTENT_TYPE_JSON) {
				$response = Json::encode($response, $minify ? 0 : JSON_PRETTY_PRINT);
			}
			if ($minify && $content_type === self::CONTENT_TYPE_HTML) {
				$response = Minify::HTML($response, static::$html_minify_options);
			}
			if ($content_type === self::CONTENT_TYPE_HTML) {
				$response .= self::$html_suffix;
			}
			static::setResponseHeader('Content-Type', $content_type . '; charset=' . $charset);
			static::setResponseHeader('Content-Length', (string)strlen($response));
		}

		// Run deferred functions from all router instances
		static::runDeferred();

		// Remove any buffered output
		if (ob_get_contents() !== false) ob_clean();

		// Flush response to client
		http_response_code(self::$response_code);
		foreach (self::$headers as $name => $value) {
			header(sprintf('%s: %s', $name, $value));
		}
		if ($response !== null) echo $response;
		flush();
		self::$response_sent = true;
	}

	public static function sendJSON($input) {
		static::sendResponse($input, self::CONTENT_TYPE_JSON);
	}

	public static function isResponseSent() {
		return self::$response_sent;
	}

	public static function redirectOut(string $location, int $code = self::HTTP_FOUND) {
		if (self::$response_sent) return;
		static::setResponseHeader('Location', str_replace(array(';', "\r", "\n"), '', $location));
		static::setResponseCode($code);
		static::sendResponse();
	}

	public static function redirect(string $location, int $code = self::HTTP_FOUND) {
		static::redirectOut(rtrim(App::getConfig()->get('url'), '/') . '/' . ltrim($location, '/'), $code);
	}

	public static function setResponseCode(int $response_code) {
		self::$response_code = $response_code;
	}

	public static function getResponseCode() {
		return self::$response_code;
	}

	public static function getResponseCodeText() {
		if (array_key_exists(self::$response_code, self::HTTP_STATUSTEXT)) {
			return self::HTTP_STATUSTEXT[self::$response_code];
		}
		return '';
	}

	public static function getResponseHeader(?string $name = null) {
		if ($name === null) return self::$headers;
		if (!array_key_exists($name, self::$headers)) return null;
		return self::$headers[$name];
	}

	public static function setResponseHeader(string $name, string $value) {
		self::$headers[$name] = $value;
	}

	public static function setResponseHeaders(array $headers) {
		foreach ($headers as $name => $value) {
			self::$headers[$name] = $value;
		}
	}

	public static function unsetResponseHeader(string $name) {
		unset(self::$headers[$name]);
	}

	public static function unsetResponseHeaders(array $names) {
		foreach ($names as $name) {
			unset(self::$headers[$name]);
		}
	}

	public static function vars(): VarCollection {
		return self::getInstance()->vars;
	}
}
