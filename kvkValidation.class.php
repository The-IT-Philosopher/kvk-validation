<?php
class kvkValidation {
	const ServiceURL = "https://overheid.io/api/kvk/";
  private $APIKEY;

  public function __construct($APIKEY) { 
    $this->APIKEY=$APIKEY;
  }

  public function check($KvKnummer) {
    $response = $this->GetData($KvKnummer);
    echo "<pre>Raw Responde:\n".var_export($response,true)."</pre>";
    // Note: Responses are in JSON HAL format
    // http://stateless.co/hal_specification.html
    // TODO: Research if a HAL library adds anything for our purposes
    // But I suppose, for this simple task at hand, it does not.
    $data = json_decode($response);
    echo "<pre>JSON Decoded Response:\n".var_export($data,true)."</pre>";
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


