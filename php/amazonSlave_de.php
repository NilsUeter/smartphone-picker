<?php if($_SERVER['QUERY_STRING'] != "Schulze-Beckendorf"){ exit(); }
  require_once "logger.php";
$fileKeys = "../scripts/keys.js";
$jsonKeys = json_decode(file_get_contents($fileKeys),TRUE);

// Your Access Key ID, as taken from the Your Account page
$access_key_id = $jsonKeys['access_key_id'];

// Your Secret Key corresponding to the above ID, as taken from the Your Account page
$secret_key = $jsonKeys['secret_key'];

// Amazon marketplace/region
$endpoint = "webservices.amazon.de";

$uri = "/onca/xml";

$file = "../scripts/smartphones.js";
$json = json_decode(file_get_contents($file),TRUE);

for ($x = 0; $x <= count($json['smartphones']); $x++) {
    logToFile("amazonSlave_de", "The number is: $x");
    echo "The number is: $x <br>";

    $params = array(
    "Service" => "AWSECommerceService",
    "Operation" => "ItemLookup",
    "AWSAccessKeyId" => $access_key_id,
    "AssociateTag" => "smartphonep08-21",
    "ItemId" => "B07CHQHFDZ",
    "IdType" => "ASIN",
    "ResponseGroup" => "Medium"
    );

  if ($json['smartphones'][$x]['asin_de'] != '') {
      $params["ItemId"] = $json['smartphones'][$x]['asin_de'];
      logToFile("amazonSlave_de", $params["ItemId"]);
      echo $params["ItemId"];

      // Set current timestamp if not set
      if (!isset($params["Timestamp"])) {
        $params["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z');
      }

      // Sort the parameters by key
      ksort($params);

      $pairs = array();
      foreach ($params as $key => $value) {
        array_push($pairs, rawurlencode($key)."=".rawurlencode($value));
      }

      // Generate the canonical query
      $canonical_query_string = join("&", $pairs);

      // Generate the string to be signed
      $string_to_sign = "GET\n".$endpoint."\n".$uri."\n".$canonical_query_string;

      // Generate the signature required by the Product Advertising API
      $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $secret_key, true));

      // Generate the signed URL
      $request_url = 'http://'.$endpoint.$uri.'?'.$canonical_query_string.'&Signature='.rawurlencode($signature);

      logToFile("amazonSlave_de", "Signed URL: \"".$request_url."\"");
  echo "Signed URL: \"".$request_url."\"";

  $response = file_get_contents($request_url);

  if($response === FALSE)
  {
    logToFile("amazonSlave_de", "Amazon blocked");
    echo "Amazon blocked";
  }else {
      $xml = new DOMDocument();
      $xml->loadXML($response);
      $items = $xml->getElementsByTagName("Items")[0];
      foreach ( $items->getElementsByTagName("Item") as $item )    {
        $json['smartphones'][$x]['amazon_de'] = $item->getElementsByTagName("DetailPageURL")[0]->nodeValue.PHP_EOL;
        $json['smartphones'][$x]['price_de'] = (int)substr($item->getElementsByTagName("LowestNewPrice")[0]->getElementsByTagName("Amount")[0]->nodeValue.PHP_EOL, 0, -3);
echo "apple";
      logToFile("amazonSlave_de", substr($item->getElementsByTagName("LowestNewPrice")[0]->getElementsByTagName("Amount")[0]->nodeValue.PHP_EOL, 0, -3));
echo substr($item->getElementsByTagName("LowestNewPrice")[0]->getElementsByTagName("Amount")[0]->nodeValue.PHP_EOL, 0, -3);
echo "dragonsbehere";
      }
      sleep(2);
    }
  }
}

file_put_contents("../scripts/smartphones1.js", json_encode($json));
logToFile("amazonSlave_de", "Prices should be updated");
echo "Files should be updated";
?>
