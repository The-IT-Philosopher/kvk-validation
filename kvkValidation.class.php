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
    $this->KeyError = NULL;
  }

  public function getData() {
    if (!$this->data) return null;

    $data = array();
    $data['kvk_nummer']        = $this->data['dossiernummer'];
    $data['organisation_name'] = $this->data['handelsnaam'];
    $data['address_street']     = $this->data['straat'];

    // Contatenating number and suffix
    $data['address_number']     = $this->data['huisnummer'] .$this->data['huisnummertoevoeging'];

    // formatting postal code, as the data provided by OpenKVK.io appears to 
    // leave the space between the numbers and letters out.
    if (strlen($this->data['postcode'])==6) {
      $data['address_postalcode'] = substr($this->data['postcode'],0,4) . " " . substr($this->data['postcode'],4,2);
    } else {
      $data['address_postalcode'] = $this->data['postcode'];
    }

    $data['address_city']       = $this->data['plaats'];
    return $data;
  }

  public function check($KvKnummer) {
    $this->reset();
    //KvK nummers kunnen met 0 beginnen
    //Niet geverifieerde bron: https://www.higherlevel.nl/forum/index.php?board=50;action=display;threadid=28479
    //Response wijkt af met langere nummers, een array met een entry met het
    //falende nummer
    if (!$KvKnummer || $KvKnummer > 99999999) {
      $this->KvKvalid = false;
      return false;
    }
    $KvKnummer = sprintf("%08d",$KvKnummer);

    $response = $this->QueryOverheidIO($KvKnummer);

    //DEBUG    
    //echo "<pre>Raw Responde:\n".var_export($response,true)."</pre>";
    //DEBUG

    // Note: Responses are in JSON HAL format
    // http://stateless.co/hal_specification.html
    // TODO: Research if a HAL library adds anything for our purposes
    // But I suppose, for this simple task at hand, it does not.
    $data = json_decode($response,true); 

    //DEBUG
    //echo "<pre>JSON Decoded Response:\n".var_export($data, true)."</pre>";
    //DEBUG

    //echo "<pre>Company Info:\n".var_export($this->data['_embedded']['rechtspersoon'][0],true)."</pre>";
    if (isset($data['_embedded']['rechtspersoon'][0])) { 
       $this->data = $data['_embedded']['rechtspersoon'][0];
       $this->KvKvalid = true;
       $this->KeyError = false;
    } else if (isset($data['error'])) {  
      if (strstr($data['error'], "niet gevonden")) {
       $this->KvKvalid = false;
       $this->KeyError = false;
      } else if (strstr($data['error'], "Geen geldige API")) {
       $this->KeyError = true;
       
      } else {
        //echo "unknown error!"; // TODO : keep error state flag
      }
    } else {
      //echo "unkown response!"; // TODO : keep error state flag
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


