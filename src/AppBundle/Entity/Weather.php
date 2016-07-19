<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;

/**
 * Weather
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WeatherRepository")
 */
class Weather extends Component
{
    private static $icons = array(
        '01d' => 'wi-day-sunny',
        '02d' => 'wi-day-cloudy',
        '03d' => 'wi-cloud',
        '04d' => 'wi-cloudy',
        '09d' => 'wi-showers',
        '10d' => 'wi-day-hail',
        '11d' => 'wi-thunderstorm',
        '13d' => 'wi-snow-wind',
        '50d' => 'wi-fog',
        '01n' => 'wi-night-clear',
        '02n' => 'wi-night-cloudy',
        '03n' => 'wi-cloud',
        '04n' => 'wi-cloudy',
        '09n' => 'wi-showers',
        '10n' => 'wi-night-hail',
        '11n' => 'wi-thunderstorm',
        '13n' => 'wi-snow-wind',
        '50n' => 'wi-fog'
    );

    /**
     * @var string
     *
     * @ORM\Column(name="apiKey", type="string", length=255)
     */
    private $apiKey;

    /**
     * @var string
     *
     * @ORM\Column(name="lang", type="string", length=5)
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="string", length=10)
     */
    private $unit;

    /**
     * @ORM\ManyToMany(targetEntity="City")
     * @ORM\JoinTable(name="weathers_cities")
     */
    private $cities;

    public function parse()
    {
        $items = array();
        $now = new \DateTime();
        $i = 0;

        foreach ($this->cities as $city) {
            $j = 0;
            $owm = new OpenWeatherMap($this->apiKey);
            try {
                $weather = $owm->getWeather($city->getName(), $this->unit, $this->lang);
                $items[$i]['city'] = $weather->city->name . ', ' . $weather->city->country;
                $items[$i]['current']['weather'] = self::$icons[$weather->weather->icon];
                $items[$i]['current']['temperature'] = round($weather->temperature->now->getValue()) . ' &deg;C';

                $forecasts = $owm->getWeatherForecast($city->getName(), $this->unit, $this->lang, $this->apiKey, 6);
                foreach ($forecasts as $weather) {
                    if ($weather->time->day->format('j') != $now->format('j') && $j < 4) {
                        $items[$i]['forecasts'][$weather->time->day->format('D')]['weather'] = self::$icons[$weather->weather->icon];
                        $items[$i]['forecasts'][$weather->time->day->format('D')]['temperature'] = array(
                            'min' => round($weather->temperature->min->getValue()) . '&deg;',
                            'max' => round($weather->temperature->max->getValue()) . '&deg;'
                        );
                    }
                    $j++;
                }
            } catch(OWMException $e) {
                echo 'OpenWeatherMap exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
            } catch(\Exception $e) {
                echo 'General exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
            }
            $i++;
        }
        $this->setItems($items);
    }

    public function __construct()
    {
        $this->cities = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return ArrayCollection
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * @param City $city
     */
    public function addCity(City $city)
    {
        $this->cities[] = $city;
    }

    /**
     * @param City $city
     */
    public function removeCity(City $city)
    {
        $this->cities->remove($city);
    }
}