<?php

namespace AppBundle\Entity;

use Debril\RssAtomBundle\Protocol\FeedReader;
use Debril\RssAtomBundle\Protocol\Parser\FeedContent;
use Doctrine\ORM\Mapping as ORM;

/**
 * Feed
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FeedRepository")
 */
class Feed extends Component
{
    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var int
     *
     * @ORM\Column(name="count", type="integer")
     */
    private $count;

    public function parse(FeedReader $reader)
    {
        /** @var FeedContent $feed */
        $feed = $reader->getFeedContent($this->getUrl(), $this->getCount());
        $items = $feed->getItems();
        usort($items, function($item1, $item2) {
            return $item2->getUpdated()->getTimestamp() - $item1->getUpdated()->getTimestamp();
        });
        $this->setItems($items);
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Component
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set count
     *
     * @param integer $count
     *
     * @return Feed
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
}

