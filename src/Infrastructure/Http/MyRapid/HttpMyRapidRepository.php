<?php
declare(strict_types=1);

namespace App\Infrastructure\Http\MyRapid;

use App\Domain\MyRapid\MyRapidNotFoundException;
use App\Domain\MyRapid\MyRapidRepository;
use GuzzleHttp;
use Symfony\Component\DomCrawler\Crawler;

class HttpMyRapidRepository implements MyRapidRepository
{

    public function ListServiceStatus(array $query): array
    {
        $client = new GuzzleHttp\Client(['base_uri' => 'https://www.myrapid.com.my']);
        $res = $client->get('/');
        if ($res->getStatusCode() !== 200) {
          throw new MyRapidNotFoundException();
        }
        $status = [];
        $crawler = new Crawler((string) $res->getBody());
        $tables = $crawler->filter('tbody')->children();
        foreach($tables as $index => $value) {
          $temp = [];
          foreach ($value->childNodes as $k => $v) {
            if ($k === 1) {
              array_push($temp, $v->textContent);
            } else if ($k === 3) {
              array_push($temp, $v->textContent);
            }
          }
          if (COUNT($temp) === 2) {
            $status[$temp[0]] = $temp[1];
          }
        }
        return $status;
    }

    public function ListStreetAutocomplete(array $query): array
    {
        // https://jp.myrapid.com.my/endpoint/geoservice/geocode?scope=WMcentral&agency=rapidkl&input=alam%20megah
        $query['scope'] = 'WMcentral';
        $query['agency'] = 'rapidkl';
        $query['input'] = $query['term'] ?? '';
        $client = new GuzzleHttp\Client(['base_uri' => 'https://jp.myrapid.com.my']);
        $res = $client->get('/endpoint/geoservice/geocode', ['query' => $query]);
        if ($res->getStatusCode() !== 200) {
          throw new MyRapidNotFoundException();
        }
        return json_decode((string) $res->getBody(), true) ?? [];
    }

    public function ListPlanner(array $query): array
    {
        // https://jp.myrapid.com.my/endpoint/geoservice/transit?agency=rapidkl&flng=101.69556427001953&flat=3.1422929763793945&tlng=101.57202911376953&tlat=3.02315092086792&mode=mix&type=fastest&departure_datetime=2022-05-07%2014%3A27%3A00
        $query['agency'] = 'rapidkl';
        $query['flng'] = $query['flng'] ?? '';
        $query['flat'] = $query['flat'] ?? '';
        $query['tlng'] = $query['tlng'] ?? '';
        $query['tlat'] = $query['tlat'] ?? '';
        $query['mode'] = $query['mode'] ?? ''; // mix|bus|rail
        $query['type'] = $query['type'] ?? ''; // fastest|leasttransit
        $query['departure_datetime'] = $query['departure_datetime'] ?? ''; // escape()
        $client = new GuzzleHttp\Client(['base_uri' => 'https://jp.myrapid.com.my']);
        $res = $client->get('/endpoint/geoservice/transit', ['query' => $query]);
        if ($res->getStatusCode() !== 200) {
          throw new MyRapidNotFoundException();
        }
        return json_decode((string) $res->getBody(), true) ?? [];
    }
}
