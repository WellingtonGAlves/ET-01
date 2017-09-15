<?php
error_reporting(0);
set_time_limit(0);
$host = "192.168.1.6";
$port = 54321;
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create
socket<br>");
$result = socket_bind($socket, "192.168.25.216",54321) or die("Could not bind to
socket<br>");
$result = socket_listen($socket) or die("Could not set up socket
listener<br>");
echo "Waiting for connections... <br>";
$cont = 0;

$mensagens = array();
while(1)
{
	$cont = $cont +1;
	$spawn[++$i] = socket_accept($socket) or die("Não foi possivel conectar ao socket <br>");
	echo "| _".$cont."_ | <br>";
	$input = socket_read($spawn[$i],1024);
	$client = $input;
	if($client == "2"){		
			echo "Cliente 2 conectou" ."<br>";	
		
	}elseif($client == "3"){
		echo "Cliente 3 conectou"."<br>"."3 Conectou";	
		//$cont2 = 0;
                exit;
	}else{
		echo "<br><br><br>Cliente Desconhecido<br><br><br>";
		
	}
	echo "<br><br><br>Cliente".$client." Desconhecido<br><br><br>";
	socket_close($spawn[$i]);
        
        
        $mensagens[$cont] = $client;
	
        if( isset($mensagens) ){
            $cont_Mensagens = count($mensagens);
            if($cont_Mensagens >= 1 ){
                exit;
            }
        
        }
        
//echo "_______________________________________________________<br>";
}
socket_close($socket);
?>

<html>
	<body>
		<p>  
			fdsfs
		</p>
	</body>
</html>