<?php


header('Content-Type: application/json');
getOperation();



//GET Function
function getOperation()
{
  error_reporting(E_ALL ^ E_WARNING);

  // ================================ //
  //       Change Data In Here        //
  // ================================ //
  $page_start = 1;
  $page_end = 2;
  $fp = fopen('./amulet-pharmaceuticals-ltd.csv', 'w');
  $site_url = 'https://medex.com.bd/companies/7/amulet-pharmaceuticals-ltd?page=';
  // ================================ //
  // ================================ //


  while ($page_start <= $page_end) {
    $sUrl = $site_url . $page_start;
    $page_start++;
    $html = file_get_contents($sUrl);


    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DomXPath($dom);
    $a = [];



    $elements = $xpath->query('//*[@class="hoverable-block"]');
    foreach ($elements as $e) {

      // $a['Pharmaceuticals'] = trim($e->childNodes[1]->childNodes[1]->childNodes[0]->nodeValue);
      // $a['medicine_form'] = $e->childNodes[1]->childNodes[1]->childNodes[1]->nodeValue;
      // $a['mg_ml'] = trim($e->childNodes[1]->childNodes[3]->nodeValue);
      // $a['generic_name'] = trim($e->childNodes[1]->childNodes[5]->nodeValue);

      // $unit_n_price = trim($e->childNodes[1]->childNodes[7]->nodeValue);
      // $unit_n_price_arr = explode(':',trim($e->childNodes[1]->childNodes[7]->nodeValue));

      // $a['price_unit'] = $unit_n_price_arr[0];
      // $a['unit_price'] = $unit_n_price_arr[1];
      // $a['prodcut_url'] = $e->attributes['href']->value;

      echo trim($e->childNodes[1]->childNodes[1]->childNodes[0]->nodeValue), PHP_EOL;

      $unit_n_price_arr = explode(':', trim($e->childNodes[1]->childNodes[7]->nodeValue));

      $b = [
        trim($e->childNodes[1]->childNodes[1]->childNodes[0]->nodeValue),
        $e->childNodes[1]->childNodes[1]->childNodes[1]->nodeValue,
        trim($e->childNodes[1]->childNodes[3]->nodeValue),
        trim($e->childNodes[1]->childNodes[5]->nodeValue),
        $unit_n_price_arr[0],
        $unit_n_price_arr[1],
        $e->attributes['href']->value
      ];

      fputcsv($fp, $b);
      // $len = $e->childNodes->length;



      // $data =  json_encode($a);
      // echo json_encode($data);

    }
  }

  fclose($fp);
}
