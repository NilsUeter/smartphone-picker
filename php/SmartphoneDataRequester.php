<?php
  ignore_user_abort(true);
  set_time_limit(0);

  require_once "logger.php";

  class SmartphoneDataRequester {

    private $endpoint;
    private $uri = "/onca/xml";
    private $secret_key;
    private $params;

    function __construct($endpoint) {
      $jsonKeys = json_decode(file_get_contents("../scripts/keys.js"),TRUE);

      // Your Secret Key corresponding to the above ID, as taken from the Your Account page
      $this->secret_key = $jsonKeys['secret_key'];

      // Amazon marketplace/region
      $this->endpoint = $endpoint;

      $this->params = array(
        "Service" => "AWSECommerceService",
        "Operation" => "ItemLookup",
        "AWSAccessKeyId" => $jsonKeys['access_key_id'],
        "AssociateTag" => "smartphonep08-21",
        "ItemId" => "B07CHQHFDZ",
        "IdType" => "ASIN",
        "ResponseGroup" => "Medium"
      );
    }

    public function getSmartPhoneData($ASIN) {
      $smartphoneData = [];

      $this->params["ItemId"] = $ASIN;

      //Only 1 request per second to amazon API, sleep before setting actual timestamp
      sleep(1);
      // Set current timestamp
      $this->params["Timestamp"] = gmdate('Y-m-d\TH:i:s\Z');

      // Sort the parameters by key
      ksort($this->params);

      $pairs = array();
      foreach ($this->params as $key => $value) {
        array_push($pairs, rawurlencode($key)."=".rawurlencode($value));
      }

      // Generate the canonical query
      $canonical_query_string = join("&", $pairs);

      // Generate the string to be signed
      $string_to_sign = "GET\n".$this->endpoint."\n".$this->uri."\n".$canonical_query_string;

      // Generate the signature required by the Product Advertising API
      $signature = base64_encode(hash_hmac("sha256", $string_to_sign, $this->secret_key, true));

      // Generate the signed URL
      $request_url = 'http://'.$this->endpoint.$this->uri.'?'.$canonical_query_string.'&Signature='.rawurlencode($signature);

      echo "Request URL: " . $request_url . "<br>";
      logToFile("amazonSlave_de", "Request URL: " . $request_url);

      $response = file_get_contents($request_url);

      if($response === FALSE)
      {
        $smartphoneData[0] = TRUE;
        $smartphoneData[1] = "";
        $smartphoneData[2] = "";
      }else {
        $smartphoneData[0] = FALSE;
        $xml = new DOMDocument();
        $xml->loadXML($response);
        $item = $xml->getElementsByTagName("Item")[0];
        $smartphoneData[1] = $item->getElementsByTagName("DetailPageURL")[0]->nodeValue . PHP_EOL;
        $smartphoneData[2] = (int)substr($item->getElementsByTagName("LowestNewPrice")[0]->getElementsByTagName("Amount")[0]->nodeValue.PHP_EOL, 0, -3);
      }

      //Array mit 3 Objekten, 0 = Failed?, 1 = Link, 2 = Preis
      return $smartphoneData;
    }
  }
 ?>