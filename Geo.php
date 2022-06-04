<?php

/** A class for working with delivery cities */
class Geo {
    /** @var City[] List of available cities */
    static private $cities;

    /**
     * Returns the distance in degrees.
     * Warning: For test purposes only.
     * @param string $a First city
     * @param string $b Second city
     * @return float Distance (returns -1 in case of error)
     */
    static function distance(string $a, string $b): float {
        $cityA = self::getByName($a);
        $cityB = self::getByName($b);
        if (!$cityA || !$cityB) {
            return -1;
        }

        return sqrt(($cityA->lon - $cityB->lon) ** 2.0 + ($cityA->lat - $cityB->lat) ** 2.0);
    }

    static private function init() {
        if (Geo::$cities !== null) {
            return;
        }

        Geo::$cities = [
            new City('Астрахань',46.35,48.04),
            new City('Брянск',53.25,34.37),
            new City('Владивосток',43.11,131.87),
            new City('Екатеринбург',56.85,60.61),
            new City('Иркутск',52.3,104.3),
            new City('Казань',55.79,49.12),
            new City('Москва',55.75, 37.62),
            new City('Ростов-на-Дону', 47.23,39.72),
            new City('Санкт-Петербург',59.94, 30.31),
            new City('Сочи',43.6, 39.73)
        ];
    }

    /**
     * @param string $name
     * @return City|null
     */
    private static function getByName(string $name)
    {
        foreach (self::$cities as $city) {
            if ($city->name === $name) {
                return $city;
            }
        }
        return null;
    }

    /**
     * Returns a random city
     * @return City
     */
    public static function getRandom(): City
    {
        Geo::init();
        return self::$cities[rand(0, count(self::$cities) - 1)];
    }
}

/** The entity of the city - the name and coordinates */
class City {
    /** @var string City name */
    public $name;
    /** @var float Latitude of the city */
    public $lat;
    /** @var float Longitude of the city */
    public $lon;

    public function __construct(string $name, float $lat, float $lon)
    {
        $this->name = $name;
        $this->lat = $lat;
        $this->lon = $lon;
    }
}
