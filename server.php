<?php
    require_once ("C:/xampp/htdocs/ET-01/Util/Funcoes_comandos.php");
    
    error_reporting(0);
    set_time_limit(0);
    ob_implicit_flush();
    $host = "192.168.25.216";
    $port = 54321;
    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create
    socket<br>");
    $result = socket_bind($socket,$host,54000) or die("Could not bind to
    socket<br>");
    $result = socket_listen($socket) or die("Could not set up socket
    listener<br>");
//    echo "Waiting for connections... <br>";
    $cont = 0;
    $qnt = 0;

    while(1)
    {
    $cont = $cont +1;
    $spawn = socket_accept($socket) or die("Could not accept incoming
    connection <br>");
//    echo "| _".$cont."_ | <br>";   
    echo "<p>Rastreador Enviou : ".$msg."</p>";
    while(@socket_recv($spawn,$buffer_resposta,1024,MSG_WAITALL)){
		// RECEBER SOLICITACAO DO CLIENTE
		echo "<br>Cliente diz : ".$buffer_resposta;
		//%sql = mysql_query("$buffer_resposta");
		// retorna consulta
                // 
                $en = "Seu registro foi guardado";
                
                echo "<br>enviou";
                socket_write($spawn,$en,strlen($en));
                socket_close($spawn);
       }   
    if($cont == "exit"){
            exit;
    }

    }
    socket_close($socket);
    $so = fsockopen('udp://pool.ntp.br', 123, $err_no, $err_str, 1);
    if ($so)
    {
    if (fwrite(so, chr(bindec('00'.sprintf('%03d', decbin(3)).'011')).str_repeat(chr(0x0), 39).pack('N', time()).pack("N", 0)))
    {
        stream_set_timeout($so, 1);
        $unpack0 = unpack("N12", fread($so, 48));
        echo date('Y-m-d H:i:s', $unpack0[7]);
    }

    fclose($so);
    }
    ?>		


