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
        unset($query['action']);
        $query = ['action' => 'list_service_status'];
        return $this->_prasaranaSetting($query);
    }

    public function ListServiceMsg(array $query): array
    {
        unset($query['action']);
        $query['action'] = 'list_service_msg';
        return $this->_prasaranaSetting($query);
    }

    public function ListStreetAutocomplete(array $query): array
    {
        unset($query['action']);
        $query['action'] = 'list_street_autocomplete';
        $query['term'] = $query['term'] ?? '';
        return $this->_prasaranaPlanner($query);
    }

    public function ListPlanner(array $query): array
    {
        unset($query['action']);
        $query['action'] = 'list_planner';
        $query['flng'] = $query['flng'] ?? '';
        $query['flat'] = $query['flat'] ?? '';
        $query['tlng'] = $query['tlng'] ?? '';
        $query['tlat'] = $query['tlat'] ?? '';
        $query['time'] = $query['time'] ?? '';
        $query['mode'] = $query['mode'] ?? '';
        $query['type'] = $query['type'] ?? '';
        $result = $this->_prasaranaPlanner($query);
        if (COUNT($result) > 0) {
          foreach($result[0]['a'] as $index => $value) {
            $result[0]['a'][$index]['t_fare_price'] = $this->parseFarePrice($value['t_fare_price']);
            $result[0]['a'][$index]['t_transport'] = $this->parseTransport($value['t_transport']);
            $result[0]['a'][$index]['t_detail'] = $this->parsedDetail($value['t_detail']);
            $result[0]['a'][$index]['t_geometry'] = json_decode($value['t_geometry']);
            $result[0]['a'][$index]['t_geometry_point'] = json_decode($value['t_geometry_point']);
          }
        }
        return $result;
    }

    private function _prasaranaSetting(array $query): array
    {
        $client = new GuzzleHttp\Client(['base_uri' => 'https://www.myrapid.com.my']);
        $res = $client->get('/clients/Myrapid_Prasarana_37CB56E7-2301-4302-9B98-DFC127DD17E9/api/prasarana_setting.ashx', ['query' => $query]);
        if ($res->getStatusCode() !== 200) {
          throw new MyRapidNotFoundException();
        }
        return json_decode((string) $res->getBody(), true) ?? [];
    }

    private function _prasaranaPlanner(array $query): array
    {
        $client = new GuzzleHttp\Client(['base_uri' => 'https://www.myrapid.com.my']);
        $res = $client->get('/clients/Myrapid_Prasarana_37CB56E7-2301-4302-9B98-DFC127DD17E9/api/prasarana_planner.ashx', ['query' => $query]);
        if ($res->getStatusCode() !== 200) {
          throw new MyRapidNotFoundException();
        }
        return json_decode((string) $res->getBody(), true) ?? [];
    }

    private function parseFarePrice($fare)
    {
      $html = '<!DOCTYPE html><html><body>'.$fare.'</body></html>';
      $crawler = new Crawler($html);
      $lis = $crawler->filterXPath('descendant-or-self::body/ul')->children();
      $fares = [];
      foreach($lis as $index => $value) {
        $values = explode(':', $value->textContent);
        $fares[$values[0]] = str_replace(' ', '', $values[1]);
      }
      return $fares;
    }

    private function parseTransport($transport)
    {
      $html = '<!DOCTYPE html><html><body>'.$transport.'</body></html>';
      $crawler = new Crawler($html);
      $transports = [];
      foreach($crawler->filterXPath('descendant-or-self::body')->children() as $index => $child) {
        array_push($transports, str_replace(' ', '', $child->textContent));
      }
      return $transports;
    }

    private function parsedDetail($detail)
    {
      $html = '<!DOCTYPE html><html><body>'.str_replace('<br/>', '-', $detail).'</body></html>';
      $crawler = new Crawler($html);
      $detail = [];
      foreach($crawler->filterXPath('descendant-or-self::body')->children() as $index => $child) {
        foreach ($child->childNodes as $index1 => $child1) {
          $temp = [];
          foreach ($child1->childNodes as $index2 => $child2) {
            if ($index2 === 0) {
              $temp['time'] = $child2->textContent;
            } else if ($index2 === 1) {
              $temp['line'] = $child2->textContent;
            } else if ($index2 === 2) {
              $temp['place'] = $child2->textContent;
            }
          }
          array_push($detail, $temp);
        }
      }
      return $detail;
    }
}
