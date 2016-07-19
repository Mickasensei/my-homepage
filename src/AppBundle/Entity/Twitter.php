<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Endroid\Twitter\Twitter as TwitterService;

/**
 * Twitter
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TwitterRepository")
 */
class Twitter extends Component
{
    /**
     * @var string
     *
     * @ORM\Column(name="consumerKey", type="string", length=255, unique=true)
     */
    private $consumerKey;

    /**
     * @var string
     *
     * @ORM\Column(name="consumerSecret", type="string", length=255, unique=true)
     */
    private $consumerSecret;

    /**
     * @var string
     *
     * @ORM\Column(name="accessToken", type="string", length=255, unique=true)
     */
    private $accessToken;

    /**
     * @var string
     *
     * @ORM\Column(name="accessTokenSecret", type="string", length=255, unique=true)
     */
    private $accessTokenSecret;

    public function parse()
    {
        $items = array();
        $now = new \DateTime();
        $twitter = new TwitterService($this->consumerKey, $this->consumerSecret, $this->accessToken, $this->accessTokenSecret);

        $response = $twitter->query('statuses/home_timeline', 'GET', 'json');
        $tweets = json_decode($response->getContent());

        foreach ($tweets as $tweet) {
            $createdAt = new \DateTime($tweet->created_at);
            $delay = $now->diff($createdAt);
            if ($delay->format('%i') == 0) {
                $intervalTime = $delay->format('%s').' sec';
            } else {
                $intervalTime = $delay->format('%i').' min';
            }

            $items[] = array(
                'tweet' => $tweet->text,
                'userUrl' => $tweet->user->url,
                'username' => $tweet->user->name,
                'screenName' => $tweet->user->screen_name,
                'profileImg' => $tweet->user->profile_image_url,
                'delay' => $intervalTime
            );
        }
        $this->setItems($items);
    }

    /**
     * Set consumerKey
     *
     * @param string $consumerKey
     *
     * @return Twitter
     */
    public function setConsumerKey($consumerKey)
    {
        $this->consumerKey = $consumerKey;

        return $this;
    }

    /**
     * Get consumerKey
     *
     * @return string
     */
    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

    /**
     * Set consumerSecret
     *
     * @param string $consumerSecret
     *
     * @return Twitter
     */
    public function setConsumerSecret($consumerSecret)
    {
        $this->consumerSecret = $consumerSecret;

        return $this;
    }

    /**
     * Get consumerSecret
     *
     * @return string
     */
    public function getConsumerSecret()
    {
        return $this->consumerSecret;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string
     */
    public function getAccessTokenSecret()
    {
        return $this->accessTokenSecret;
    }

    /**
     * @param string $accessTokenSecret
     */
    public function setAccessTokenSecret($accessTokenSecret)
    {
        $this->accessTokenSecret = $accessTokenSecret;
    }

}

