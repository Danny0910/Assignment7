<?php
  // create short variable names
  $alphainput = $_POST['alphainput'];
  $alphanumericinput = $_POST['alphanumericinput'];
  $nameinput = $_POST['nameinput'];
  $emailinput = $_POST['emailinput'];
  $phonenumberinput = $_POST['phonenumberinput'];
?>
<!DOCTYPE html>
<html>
  <head>
    <title>String Work Processor</title>
  </head>
  <body>
    <h1>String Work Processor</h1>
    <h2>Process results</h2> 
    <?php

    $alpharesult = preg_replace("/[^a-zA-Z]/","",$alphainput);
    $alphanumericresult = preg_replace("/\W/","",$alphanumericinput);
    $nameresult = preg_replace("/[^a-zA-Z0-9\,\.]/","",$nameinput);
    $emailresult = preg_match("/^[a-zA-Z0-9\.\!\#\$\%\&\’\*\+\/\=\?\^\_\`\{\|\}\~\-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/",$emailinput);
    $phonenumberresult = preg_match("/^\d{3}\.\d{3}\.\d{4}$/",$phonenumberinput);

    echo "<p>";
    echo "Only alpha ".$alphainput." => ".$alpharesult."<br/>";
    echo "Alpha numeric ".$alphanumericinput." => ".$alphanumericresult."<br/>";
    echo "Name ".$nameinput." => ".$nameresult."<br/>";
    echo "E-Mail ".$emailinput." => ";
    if($emailresult){
       echo "correct";
    } else {
       echo "incorrect";
    }
    echo "<br/>";
    echo "Phone Number ".$phonenumberinput." => ";
    if($phonenumberresult){
       echo "correct";
    } else {
       echo "incorrect";
    }
    echo "<br/>";
    echo "</p>";
    ?>
  </body>
</html>
