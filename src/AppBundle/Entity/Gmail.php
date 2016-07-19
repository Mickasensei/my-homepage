<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Gmail
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GmailRepository")
 */
class Gmail extends Component
{

    const APPLICATION_NAME = 'Gmail API PHP Quickstart';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="clientId", type="string", length=255, unique=true)
     */
    private $clientId;

    /**
     * @var string
     *
     * @ORM\Column(name="secret", type="string", length=255, unique=true)
     */
    private $secret;



    public function parse($userId, $rootDir)
    {
        $items = array();
        $client = $this->getClient($userId, $rootDir);
        $gmailService = new \Google_Service_Gmail($client);
        VarDumper::dump($gmailService->users_messages->listUsersMessages('me')->getMessages());
        die;
        $this->setItems($items);
    }

    private function getClient($userId, $rootDir)
    {
        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

        $client = new \Google_Client();
        $client->addScope(\Google_Service_Gmail::GMAIL_READONLY);
        $client->setRedirectUri($redirect_uri);
        $client->setClientId($this->clientId);
        $client->setClientSecret($this->secret);

        if (isset($_GET['code'])) {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            $client->setAccessToken($token);
            // store in the session also
            $_SESSION['upload_token'] = $token;
            // redirect back to the example
            header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }
        // set the access token as part of the client
        if (!empty($_SESSION['upload_token'])) {
            $client->setAccessToken($_SESSION['upload_token']);
            if ($client->isAccessTokenExpired()) {
                unset($_SESSION['upload_token']);
            }
        } else {
            $authUrl = $client->createAuthUrl();
            VarDumper::dump($authUrl);
            die;
        }

        return $client;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set clientId
     *
     * @param string $clientId
     *
     * @return Gmail
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * Get clientId
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Set secret
     *
     * @param string $secret
     *
     * @return Gmail
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get secret
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }
}

