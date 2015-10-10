<?php
class kvkValidation {
	const ServiceURL = "https://overheid.io/api/kvk/";
  private $APIKEY;
  private $kvkInfo;
  private $KvKvalid;
  private $KeyValid;

  public function __construct($APIKEY) { 
    $this->APIKEY=$APIKEY;
  }

  public function reset() {
    $this->kvkInfo  = NULL;
    $this->KvKvalid = NULL;
    $this->KeyError = false;

  }

  public function check($KvKnummer) {
    $this->reset();
    if (!(strlen($KvKnummer)==8)) {
      $this->KvKvalid = false;
    }

    $response = $this->GetData($KvKnummer);

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

    //echo "<pre>Company Info:\n".var_export($data['_embedded']['rechtspersoon'][0],true)."</pre>";
    if (isset($data['_embedded']['rechtspersoon'][0])) { echo "OK!!!";
       $kvkInfo = $data['_embedded']['rechtspersoon'][0];
       $this->KvKvalid = true;
       $this->KeyError = false;
    }
    if (isset($data['error'])) {  echo "ERROR!!!";
      if (strstr($data['error'], "niet gevonden")) {
       $this->KvKvalid = false;
       $this->KeyError = false;
     } 
      if (isset($data['error'])) {
        if (strstr($data['error'], "Geen geldige API key")) {
         $this->KeyError = true;
       }       
      }
    }
    return $this->KvKvalid;
  }

  public function __get($name) {
    return $this->$name;
  }

  function GetData($KvKnummer){
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


