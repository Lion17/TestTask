<?php

/** A class for emulating responses from delivery services */
class Emulator
{
    /**
     * @param string $url
     * @return string|false
     */
    public static function api(string $url)
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) {
            return false;
        }

        $query = parse_url($url, PHP_URL_QUERY);
        $path = parse_url($url, PHP_URL_PATH);
        switch ($host) {
            case 'api.fastdelivery.com':
                return self::apiFastDelivery($path, $query);
            case 'slowship.ru':
                return self::apiSlowDelivery($path, $query);
            default:
                return false;
        }
    }

    private static function apiFastDelivery(string $path,string $query)
    {
        try {
            if ($path !== '/calc') {
                throw new Exception('Invalid endpoint');
            }

            parse_str($query, $params);
            $from = $params['from'];
            $to = $params['to'];
            if (!$from || !$to) {
                throw new Exception('Bad request');
            }

            $cargo = floatval($params['cargo']);
            if ($cargo == 0 || $cargo > 1000.0) {
                throw new Exception('Invalid cargo weight');
            }

            $distance = Geo::distance($from, $to);
            if ($distance == -1) {
                throw new Exception('Invalid route');
            }

            $price = round(($distance + 1) * 220 * (1 + $cargo / 70.0), 2);
            $period = (int) ($distance / 15 + 1);
            return json_encode(new FastDeliveryResponse($price, $period));
        } catch (Exception $e) {
            return json_encode(FastDeliveryResponse::error($e->getMessage()));
        }
    }

    private static function apiSlowDelivery(string $path, string $query)
    {
        try {
            if ($path !== '/price') {
                return false;
            }

            parse_str($query, $params);
            $from = $params['s'];
            $to = $params['t'];
            if (!$from || !$to) {
                throw new Exception('Bad route params');
            }

            $kg = floatval($params['w']) / 1000.0;
            if ($kg == 0.0 || $kg > 500.0) {
                throw new Exception('Unacceptable weight');
            }

            $distance = Geo::distance($from, $to);
            if ($distance == -1) {
                throw new Exception('Can\'t calculate the route');
            }

            $coefficient = round(($distance + 1) * 0.3 * (1 + $kg / 100.0),2);
            $now = new DateTime();
            $days = (int) ($distance / 6 + 3);
            $date = $now->add(new DateInterval(sprintf('P%dD', $days)))->format('Y-m-d');
            return json_encode(new SlowDeliveryResponse($coefficient, $date));
        } catch (Exception $e) {
            return json_encode(SlowDeliveryResponse::error($e->getMessage()));
        }
    }
}
