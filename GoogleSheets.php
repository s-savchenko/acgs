<?php

/**
 * Created by PhpStorm.
 * User: savchenko
 * Date: 25.01.17
 * Time: 16:09
 */
class GoogleSheets
{
    protected $appName;
    protected $credentialsPath;
    protected $clientSecretPath;
    protected $scopes;
    protected $googleClient;

    public function __construct($appName, $credentialsPath, $clientSecretPath)
    {
        $this->scopes = implode(' ', [Google_Service_Sheets::SPREADSHEETS]);
        $this->appName = $appName;
        $this->credentialsPath = $credentialsPath;
        $this->clientSecretPath = $clientSecretPath;
    }

    /**
     * Returns an authorized API client.
     * @return mixed
     */
    public function getClient() {
        $accessToken = $this->getAccessToken();

        if ($accessToken === false) {
            return false;
        } else {
            $client = $this->getGoogleClient();
            $client->setAccessToken($accessToken);
            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                file_put_contents($this->credentialsPath, json_encode($client->getAccessToken()));
            }
            return $client;
        }
    }

    public function isReady()
    {
        return $this->getClient() !== false;
    }

    public function getAccessToken()
    {
        if (file_exists($this->credentialsPath)) {
            return json_decode(file_get_contents($this->credentialsPath), true);
        } else {
            return false;
        }
    }

    public function saveAccessToken($authCode)
    {
        $client = $this->getGoogleClient();
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        $result = file_put_contents($this->credentialsPath, json_encode($accessToken));
        if ($result !== false)
            return true;
        else
            return false;
    }

    public function getAuthUrl()
    {
        $client = $this->getGoogleClient();
        return $client->createAuthUrl();
    }

    protected function getGoogleClient()
    {
        if ($this->googleClient == null) {
            $client = new Google_Client();
            $client->setApplicationName($this->appName);
            $client->setScopes($this->scopes);
            $client->setAuthConfig($this->clientSecretPath);
            $client->setAccessType('offline');
            $this->googleClient = $client;
        }
        return $this->googleClient;
    }
}