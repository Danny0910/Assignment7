    <?php
    // tell the browser that we will be sending JSON as the output
    header("Content-Type: application/json; charset=UTF-8");
    // We want to read the raw HTTP POST value.  To do so, we use php://input.
    // See: http://php.net/manual/en/wrappers.php.php#wrappers.php.input
    $json_string = file_get_contents('php://input');
    // write out the POST input to a file so we can inspect it
    file_put_contents("json_input_to_php.txt", $json_string);

    // try to decode the JSON sent by JavaScript from the browser via the POST request
    $json_object = json_decode($json_string);

    // make a new empty object to hold our JSON response object
    $json_output_object = new stdClass();
    // if json_decode returns null, then there was an error decoding the JSON
    // See: http://php.net/manual/en/function.json-decode.php
    if ($json_object === null) {
        // create a new property named error_message and assign the last JSON decode error to it
        $json_output_object->error_message = json_last_error_msg();
        // encode the PHP JSON object into a JSON string and send it to the browser
        echo json_encode($json_output_object);
        // we can use exit here, since we sent our JSON response
        exit;
    }

    // write the decoded JSON PHP object to a file so we can inspect it
    file_put_contents("php_decoded_json.txt", print_r($json_object, true));
    $fh = fopen('orders.txt','r');
    if(!$fh){
        $json_output_object->error_message = "<p style='color:red'>Cannot open input file 'orders.txt'</p>";
    } else {
       if(flock($fh,LOCK_EX | LOCK_NB)){
          while(!feof($fh)){
             $line = fgets($fh);
             if(feof($fh)){
                 $json_output_object->error_message = "<p style='color:red'>Order not found</p>";
                 break;
             }
             $line = trim($line);
             $param_arr = explode("\t",$line);
             list($ordernumber,$orderdate,$tireqty,$oilqty,$sparkqty,$find,$notes) = $param_arr;
             if($ordernumber == $json_object->order_number) {
                 $json_output_object->order_number = $ordernumber;
                 $json_output_object->order_date = $orderdate;
                 $json_output_object->tireqty = $tireqty;
                 $json_output_object->oilqty = $oilqty;
                 $json_output_object->sparkqty = $sparkqty;
                 $json_output_object->notes = $notes;
                 if ($find == 'a') {
                     $json_output_object->howfind = 'I\'m a regular customer';
                 } elseif ($find == 'b') {
                     $json_output_object->howfind = 'TV advertising';
                 } elseif ($find == 'c') {
                     $json_output_object->howfind = 'Phone directory';
                 } else {
                     $json_output_object->howfind = 'Word of mouth';
                 }

                 $totalqty = $tireqty + $oilqty + $sparkqty;

                 $json_output_object->totalqty = $totalqty;
                 $totalamount = 0.00;

                 define('TIREPRICE', 100);
                 define('OILPRICE', 10);
                 define('SPARKPRICE', 4);

                 $totalamount = $tireqty * TIREPRICE
                     + $oilqty * OILPRICE
                     + $sparkqty * SPARKPRICE;

                 $json_output_object->subtotal = number_format($totalamount, 2);

                 $taxrate = 0.10;  // local sales tax is 10%
                 $totalamount = $totalamount * (1 + $taxrate);
                 $json_output_object->total = number_format($totalamount, 2);
                 break;
             }
          }
          flock($fh,LOCK_UN);
          fclose($fh);
       } else {
           $json_output_object->error_message = "<p style='color:red'>Cannot get exclusive lock</p>";
       }
    }
    echo json_encode($json_output_object);
    ?>
