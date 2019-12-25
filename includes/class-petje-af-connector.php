<?php

/**
 * Create connection with Petje.af API.
 *
 * Create connection with the Petje.af API through
 * the endpoints
 *
 * @link       https://petje.af
 * @since      2.0.0
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 */

/**
 * Create connection with Petje.af API.
 *
 * @since      2.0.0
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 * @author     Stefan de Groot <stefan@petje.af>
 */

class Petje_Af_Connector
{
    /**
     * Api base url.
     *
     * @since    2.0.2
     * @access   protected
     * @var      string
     */
    protected $apiBaseUrl = 'https://api.petje.af/v1/';

    /**
     * Headers.
     *
     * @since    2.0.2
     * @access   protected
     * @var      array
     */
    protected $headers = [];

    /**
     * The User Id
     *
     * @since    2.0.0
     * @access   protected
     * @var      integer
     */
    protected $userId = null;

    /**
     * Access Token.
     *
     * @since    2.0.0
     * @access   protected
     * @var      string
     */
    protected $accessToken;

    /**
     * Initialize class.
     *
     * @since   2.0.0
     * @param   $userId
     * @param   $accessToken
     * 
     */
    public function __construct($userId = null, $accessToken = null)
    {
        if ($userId) {
            $this->userId = $userId;
        }

        $this->setAccessToken($accessToken);
    }

    /**
     * Set user if needed.
     *
     * @since   2.0.0
     * @param   $userId
     * 
     */
    public function setUser($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Set access token if needed.
     *
     * @since   2.0.0
     * @param   $accessToken
     * 
     */
    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;
        $this->setAccessTokenInHeader();
    }

    /**
     * Set access token in authorization header.
     *
     * @since   2.0.2
     * 
     */
    protected function setAccessTokenInHeader() {
        if ($this->accessToken) {
            $this->headers['Authorization'] = 'Bearer ' . $this->accessToken;
        } else {
            unset($this->headers['Authorization']);
        }
    }

    /**
     * Set user if needed.
     *
     * @since   2.0.2
     * @param   $request    Request string
     * @param   $method     HTTP Method
     * @param   $args       Request arguments
     * 
     * @return object|string          Response object or fail message
     * 
     */
	public function make_request($path, $method = 'GET', $args = [], $body = []) {
        $url = $this->build_request_url($path, $args);

        $request = ['headers' => $this->headers, 'method' => $method];
        
        if ($method = 'POST') {
            $request['body'] = $body;
        }

        $results = wp_remote_request( $url, $request );

        $this->last_response = $results;
        
		if( ! is_wp_error( $results ) ) {
			if( 200 == wp_remote_retrieve_response_code( $results ) ) {
				$results = wp_remote_retrieve_body( $results );
				return json_decode( $results );
			}else{
				$body = wp_remote_retrieve_body( $results );
				if( is_string( $body ) && is_object( $json = json_decode( $body ) ) ){
					$body = (array) $json;
				}

				if( isset( $body['detail'] ) && ! empty( $body[ 'detail' ] ) ){
					return $body[ 'detail' ];
				}elseif( isset( $body['message'] ) && ! empty( $body[ 'message' ] ) ){
					return $body[ 'message' ];
				}else{
					return wp_remote_retrieve_response_code( $results );
				}

			}
		}
    }
    
    /**
     * Get request
     * 
     * @since   2.0.2
     * @param   $request    Request string
     * @param   $args       Request arguments
     * 
     * @return object|string          Response object or fail message
     * 
     */
    public function get($request, $args = []) {
        return $this->make_request($request, 'GET', $args);
    }

    /**
     * Delete request
     * 
     * @since   2.0.2
     * @param   $request    Request string
     * @param   $args       Request arguments
     * 
     * @return object|string          Response object or fail message
     * 
     */
    public function delete($request, $args = []) {
        return $this->make_request($request, 'DELETE', $args);
    }

    /**
     * Post request
     * 
     * @since   2.0.2
     * @param   $request    Request string
     * @param   $args       Request arguments
     * 
     * @return object|string          Response object or fail message
     * 
     */
    public function post($request, $body = []) {
        return $this->make_request($request, 'POST', [], $body);
    }

	/**
	 * Merge default request arguments with those of this request.
	 *
	 * @since 2.0.2
	 *
	 * @param  array  $args Request arguments
	 * @return array        Request arguments
	 */
	public function filter_request_arguments($args = array()) {
		return $args;
	}

	/**
	 * Build the full request URL.
	 *
	 * @since 2.0.2
	 *
	 * @param  string $request Request path
	 * @param  array  $args    Request arguments
	 * @return string          Request URL
	 */
	public function build_request_url($request, array $args) {
		return $this->apiBaseUrl . $request . '?' . http_build_query($this->filter_request_arguments($args));
	}

	/**
	 * Search in response object by name.
	 *
	 * @since 2.0.2
	 *
	 * @param $list
	 * @param $name
	 * @param $property
	 *
	 * @return bool
	 */
	protected function find_by_name( $list, $name, $property ){
		if( is_object( $list ) && property_exists( $list, $property ) ){
			foreach( $list->$property as $item ){
				if( $name == $item->name ) {
					return $item;
				}
			}
		}

		return false;

	}

	/**
	 * Get the last response returned by WordPress HTTP API
	 *
	 * @since 2.0.2
	 *
	 * @return array|\WP_Error
	 */
	public function get_last_response(){
		return $this->last_response;
	}
}