<?php
class kvkValidation {
	const ServiceURL = "https://overheid.io/api/kvk/";
  private $APIKEY;
  private $data;
  private $KvKvalid;
  private $KeyValid;

  public function __construct($APIKEY) { 
    $this->APIKEY=$APIKEY;
  }

  public function reset() {
    $this->data  = NULL;
    $this->KvKvalid = NULL;
    $this->KeyError = false;
  }

  public function getData() {
    if (!(isset($this->data))) return null;

    $data = array();
    $data['kvk_nummer']        = $this->data['dossiernummer'];
    $data['organisation_name'] = $this->data['handelsnaam'];
    $data['adress_street']     = $this->data['straat'];

    // Contatenating number and suffix
    $data['adress_number']     = $this->data['huisnummer'] .$this->data['huisnummertoevoeging'];

    // formatting postal code, as the data provided by OpenKVK.io appears to 
    // leave the space between the numbers and letters out.
    if (strlen($this->data['postcode'])==6) {
      $data['adress_postalcode'] = substr($this->data['postcode'],0,4) . " " . substr($this->data['postcode'],4,2);
    } else {
      $data['adress_postalcode'] = $this->data['postcode'];
    }

    $data['adress_city']       = $this->data['plaats'];
    return $data;
  }

  public function check($KvKnummer) {
    $this->reset();
    if (!(strlen($KvKnummer)==8)) {
      $this->KvKvalid = false;
    }

    $response = $this->QueryOverheidIO($KvKnummer);

    //DEBUG    
    //echo "<pre>Raw Responde:\n".var_export($response,true)."</pre>";
    //DEBUG

    // Note: Responses are in JSON HAL format
    // http://stateless.co/hal_specification.html
    // TODO: Research if a HAL library adds anything for our purposes
    // But I suppose, for this simple task at hand, it does not.
    $this->data = json_decode($response,true); 

    //DEBUG
    //echo "<pre>JSON Decoded Response:\n".var_export($this->data, true)."</pre>";
    //DEBUG

    //echo "<pre>Company Info:\n".var_export($this->data['_embedded']['rechtspersoon'][0],true)."</pre>";
    if (isset($this->data['_embedded']['rechtspersoon'][0])) { echo "OK!!!";
       $this->data = $this->data['_embedded']['rechtspersoon'][0];
       $this->KvKvalid = true;
       $this->KeyError = false;
    }
    if (isset($this->data['error'])) {  echo "ERROR!!!";
      if (strstr($this->data['error'], "niet gevonden")) {
       $this->KvKvalid = false;
       $this->KeyError = false;
     } 
      if (isset($this->data['error'])) {
        if (strstr($this->data['error'], "Geen geldige API key")) {
         $this->KeyError = true;
       }       
      }
    }
    return $this->KvKvalid;
  }

  public function __get($name) {
    return $this->$name;
  }

  private function QueryOverheidIO($KvKnummer){
    $ch = curl_init();

//DEBUG
//curl_setopt($handle, CURLOPT_VERBOSE, true);
//$verbose = fopen('php://temp', 'w+');
//curl_setopt($handle, CURLOPT_STDERR, $verbose);
//DEBUG



    curl_setopt($ch, CURLOPT_URL, self::ServiceURL . $KvKnummer);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, 
                                      array( 'ovio-api-key: ' . $this->APIKEY));
    $result = curl_exec($ch);


//DEBUG
//    if ($result === FALSE) {
//        printf("cUrl error (#%d): %s<br>\n", curl_errno($handle),
//               htmlspecialchars(curl_error($handle)));
//    }
//
//    rewind($verbose);
//    $verboseLog = stream_get_contents($verbose);
//
//    echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
//DEBUG


    curl_close($ch);
    return $result;
  }

}


