<pre>
<?php 


require_once("C:/xampp/htdocs/Master-yi/Util/Config.php");
require_once("C:/xampp/htdocs/Master-yi/dao/Connection.php");

$timestamp = date("Y-m-d H:i:s");
print_r($timestamp);
$dataLocal = date('d/m/Y H:i:s', time());
echo "<br>";
print_r($dataLocal);
 ?>
 </pre>
