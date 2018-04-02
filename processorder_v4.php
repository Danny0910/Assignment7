<?php
  // create short variable names
  $tireqty = $_POST['tireqty'];
  $oilqty = $_POST['oilqty'];
  $sparkqty = $_POST['sparkqty'];
  $notes = $_POST['notes'];
  $find = $_POST['find'];
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Bob's Auto Parts - Order Results</title>
  </head>
  <body>
    <h1>Bob's Auto Parts</h1>
    <h2>Order Results</h2> 
    <?php
    $orderdate = date('H:i, jS F Y');

    echo '<p>The type of each variable is:<br/>';
    echo 'tireqty => '.gettype($tireqty).'<br />';
    echo 'oilqty => '.gettype($oilqty).'<br />';
    echo 'sparkqty => '.gettype($sparkqty).'<br />';
    echo 'notes => '.gettype($notes).'<br />';
    echo '</p>';

    $tireqty = intval($tireqty);
    $oilqty = intval($oilqty);
    $sparkqty = intval($sparkqty);

    echo '<p>After converting </p>';
    echo 'tireqty => '.gettype($tireqty).'<br />';
    echo 'oilqty => '.gettype($oilqty).'<br />';
    echo 'sparkqty => '.gettype($sparkqty).'<br />';
    echo 'notes => '.gettype($notes).'<br />';

    $totalqty = 0;

    if($tireqty < 0 or $oilqty < 0 or $sparkqty < 0){
        echo '<p style="color: red">Error!<br/>';
        if($tireqty < 0){
            echo 'tire qty is less than zero<br/>';
        }
        if($oilqty < 0){
            echo 'oil qty is less than zero<br/>';
        }
        if($sparkqty < 0){
            echo 'spark qty is less than zero<br/>';
        }
        echo '</p>';
    } else {
        $totalqty = $tireqty + $oilqty + $sparkqty;
        if($totalqty > 0) {
            $fh = fopen('orders.txt','a');
            if(!$fh){
                echo "<p style='color:red'>Cannot open output file 'orders.txt'</p>";
            } else {
                if(flock($fh,LOCK_EX | LOCK_NB)){
                    $next_order_number_count_file = fopen("next_order_number.txt", "cb+");
                    // YOUR CODE HERE: you need to add error checking for fopen
                    if(!$next_order_number_count_file){
                        echo "<p style='color:red'>Cannot open output file 'next_order_number.txt'</p>";
                    } else {
                        // YOUR CODE HERE: you need to get an exclusive lock on $next_order_number_count_file
                        // YOUR CODE HERE: you need to add error checking for flock
                        if (flock($next_order_number_count_file, LOCK_EX | LOCK_NB)) {
                            // get the next order number from the file
                            // if the file is empty, which happens when it is created by fopen, then false will be returned
                            $next_order_number = fgets($next_order_number_count_file);
                            if ($next_order_number === false) {
                                // since there is no order number, we will assume we are order 0
                                $next_order_number = 0;
                            }
                            // Truncate the file to 0 size.  This deletes everything in the file.
                            ftruncate($next_order_number_count_file, 0);
                            rewind($next_order_number_count_file);
                            $order_number = $next_order_number;
                            // write out the next order number to use by incrementing the one we just read in
                            fputs($next_order_number_count_file, ++$next_order_number);
                            //unlock file
                            flock($next_order_number_count_file, LOCK_UN);
                            // close the file
                            fclose($next_order_number_count_file);
                            fputs($fh, $order_number . "\t");
                            fputs($fh, $orderdate . "\t");
                            fputs($fh, $tireqty . "\t");
                            fputs($fh, $oilqty . "\t");
                            fputs($fh, $sparkqty . "\t");
                            fputs($fh, $find . "\t");
                            fputs($fh, $notes . "\n");
                            flock($fh, LOCK_UN);
                            fclose($fh);
                            echo "<p>Order successfully placed</p>";
                            // display the order number NOTE: You should display the order number with the rest of the
                            // order details
                            echo "<p> Order number: ".$order_number."</p>";
                            echo "<p>Order processed at ";
                            echo $orderdate;
                            echo "</p>";

                            echo '<p>Your order is as follows: </p>';

                            echo '<p>';
                            echo htmlspecialchars($tireqty) . ' tires<br />';
                            echo htmlspecialchars($oilqty) . ' bottles of oil<br />';
                            echo htmlspecialchars($sparkqty) . ' spark plugs<br />';
                            echo 'Customer notes: ' . htmlspecialchars($notes) . '<br />';
                            echo 'How did you find Bob\'s: ';
                            if ($find == 'a') {
                                echo 'I\'m a regular customer';
                            } elseif ($find == 'b') {
                                echo 'TV advertising';
                            } elseif ($find == 'c') {
                                echo 'Phone directory';
                            } else {
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
                        } else {
                            echo "<p style='color:red'>Cannot get exclusive lock on 'next_order_number.txt'</p>";
                        }
                    }
                } else {
                    echo "<p style='color:red'>Cannot get exclusive lock</p>";
                }
            }
        }else{
            echo '<p style="color: red;">Error! Total qty is zero!</p>';
        }
    }
    ?>
  </body>
</html>
