<?php

   $mypass = "12345"; 

   // Encoding
   $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_RAND)), '+', '.');
   $salt_sha512 = '$6$rounds=5000$'.$salt.'$';
   $sha512md = crypt($mypass, $salt_sha512);

   echo "Password Encoding.<br />";
   echo "Salt for SHA512: ".$salt_sha512."<br />";
   echo "Result for SHA512: ".$sha512md."<br />";

   // Verify
   $db_pass = '$6$rounds=5000$YgKuS2nndoNNSuy8$SI25Y4nZ5aCm0qn4.Q1meQtt/BjZO1vKMV0N/LVEXtuvmYX3Z12Ll1qZf7Wi.m1rNi9KUf54dTQ9Tq77HQNOE1';
   $verify_hash = crypt($mypass, '$6$rounds=5000$YgKuS2nndoNNSuy8$SI25Y4nZ5aCm0qn4.Q1meQtt/BjZO1vKMV0N/LVEXtuvmYX3Z12Ll1qZf7Wi.m1rNi9KUf54dTQ9Tq77HQNOE1');
   echo strlen($verify_hash).'<br />';
   if ($verify_hash == $db_pass)
      echo "<br />Verify OK.<br />";
   else
      echo "<br />Verify Error.<br />";
?> 
