
<?php 


require_once("C:/xampp/htdocs/Master-yi/Util/Config.php");
require_once("C:/xampp/htdocs/Master-yi/dao/Connection.php");

$endereço = "https://maps.googleapis.com/maps/api/geocode/json?latlng=-25.474350,-49.246792&key=AIzaSyAE7rpJUJVM9h0skjf495EYANp_ohb0rNc";
            //print_r($url);
    		$json_endereco = json_decode(file_get_contents($endereço), true);
    		print_r($json_endereco);
 ?>
 
