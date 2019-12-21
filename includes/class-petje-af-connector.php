<?php

/**
 * Create connection with PetjeafApiClient.
 *
 * Create connection with the Petje.af API through
 * the PetjeafApiClient composer package
 *
 * @link       https://petje.af
 * @since      2.0.0
 *
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 */

/**
 * Create connection with PetjeafApiClient.
 *
 * @since      2.0.0
 * @package    Petje_Af
 * @subpackage Petje_Af/includes
 * @author     Stefan de Groot <stefan@petje.af>
 */
use Petjeaf\Api\PetjeafApiClient;

class Petje_Af_Connector
{
    
    /**
     * Instance of PetjeafApiClient.
     *
     * @since    2.0.0
     * @access   public
     * @var      Petjeaf\Api\PetjeafApiClient
     */
    public $client;

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
    public function __construct($userId = null, $accessToken)
    {
        if ($userId) {
            $this->userId = $userId;
        }

        $this->accessToken = $accessToken;

        $this->setClient();
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
     * Set client on init.
     *
     * @since   2.0.0
     * 
     */
    protected function setClient()
    {
        $this->client = new PetjeafApiClient();
        $this->client->setAccessToken($this->accessToken);
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
        $this->client->setAccessToken($this->accessToken);
    }
}