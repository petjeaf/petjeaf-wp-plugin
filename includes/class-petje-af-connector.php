<?php

use Petjeaf\Api\PetjeafApiClient;

class Petje_Af_Connector
{
    public $client;

    protected $userId = null;

    protected $accessToken;

    public function __construct($userId = null, $accessToken)
    {
        if ($userId) {
            $this->userId = $userId;
        }

        $this->accessToken = $accessToken;

        $this->setClient();
    }

    public function setUser($userId)
    {
        $this->userId = $userId;
    }

    protected function setClient()
    {
        $this->client = new PetjeafApiClient();
        $this->client->setAccessToken($this->accessToken);
    }

    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;
        $this->client->setAccessToken($this->accessToken);
    }
}