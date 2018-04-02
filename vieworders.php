<!DOCTYPE html>
<html>
  <head>
    <title>Bob's Auto Parts - Order Results</title>
  </head>
  <body>
    <h1>Bob's Auto Parts</h1>
    <h2>Order Results</h2> 
    <?php
    $fh = fopen('orders.txt','r');
    if(!$fh){
          echo "<p style='color:red'>Cannot open input file 'orders.txt'</p>";
    } else {
       if(flock($fh,LOCK_EX | LOCK_NB)){
          while(!feof($fh)){
             $line = fgets($fh);
             if(feof($fh)){
                 break;
             }
             $line = trim($line);
             $param_arr = explode("\t",$line);
             list($orderdate,$tireqty,$oilqty,$sparkqty,$find,$notes) = $param_arr;
             $totalqty = $tireqty + $oilqty + $sparkqty;

             echo '<p>Your order is as follows: </p>';

             echo '<p>';
             echo "Order date: ".$orderdate."<br/>";
             echo htmlspecialchars($tireqty).' tires<br />';
             echo htmlspecialchars($oilqty).' bottles of oil<br />';
             echo htmlspecialchars($sparkqty).' spark plugs<br />';
             echo 'Customer notes: '.htmlspecialchars($notes).'<br />';
             echo 'How did you find Bob\'s: ';
             if($find == 'a'){
                        echo 'I\'m a regular customer';
             }elseif ($find == 'b'){
                        echo 'TV advertising';
             }elseif ($find == 'c'){
                        echo 'Phone directory';
             }else{
                        echo 'Word of mouth';
             }
             echo '<br/>';
             echo '</p>';
             echo "<p>Items ordered: " . $totalqty . "<br />";
             $totalamount = 0.00;

             define('TIREPRICE', 100);
             define('OILPRICE', 10);
             define('SPARKPRICE', 4);

             $totalamount = $tireqty * TIREPRICE
               + $oilqty * OILPRICE
               + $sparkqty * SPARKPRICE;

             echo "Subtotal: $" . number_format($totalamount, 2) . "<br />";

             $taxrate = 0.10;  // local sales tax is 10%
             $totalamount = $totalamount * (1 + $taxrate);
             echo "Total including tax: $" . number_format($totalamount, 2) . "</p>";
          }
          flock($fh,LOCK_UN);
          fclose($fh);
       } else {
          echo "<p style='color:red'>Cannot get exclusive lock</p>";
       }
    }
    ?>
  </body>
</html>
