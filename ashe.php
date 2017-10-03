<pre>
<?php 


require_once("C:/xampp/htdocs/Master-yi/Util/Config.php");
require_once("C:/xampp/htdocs/Master-yi/dao/Connection.php");

$selectCmd = dbExecute("SELECT * FROM `cmd_trackers` WHERE `imei`='100' AND `tipo` = 'FD' AND `sucesso` = false");
            if ($selectCmd->num_rows > 0 ) {
                $row = $selectCmd->fetch_assoc();
                $cmd = $row;
                //$result = dbExecute("UPDATE `cmd_trackers` SET `sucesso`='0', `resposta`='Não foi possível ativar o alarme' WHERE `imei` = ´$cmd['imei']´ AND `tipo` = 'FD'");
                $result = $cmd['imei'];
                print_r($result);
            }else{
                echo false;
            }
 ?>
 </pre>