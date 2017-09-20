<?php
    require_once ("C:/xampp/htdocs/ET-01/Util/Funcoes.php");
    
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
    $spawn = socket_accept($socket) or die("Could not accept incoming connection <br>");
    while(socket_recv($spawn,$buffer_enviado_rastreador,1024,MSG_WAITALL))
    {
        $cont = $cont +1;
//    $spawn = socket_accept($socket) or die("Could not accept incoming connection <br>");
//    echo "| _".$cont."_ | <br>";
//    $input = socket_read($spawn,1024);
        $msg = $buffer_enviado_rastreador;  
        echo "<p>Rastreador Enviou : ".$msg."</p>";
    
        $resultadoComando = lerMensagem($msg);
        if ($resultadoComando == "atualizouNumeroPrincipal") {
            $parts[3]="Numero principal registrado na Plataforma"; 
            $msgConfigurada = implode(",", $parts);//convertendo Array para String
            $msgConfigurada .="#";
            echo "<br>$msgConfigurada<br>";
            socket_write($spawn,$msgConfigurada,strlen($msgConfigurada));
            socket_close($spawn);
            echo "<p>Resultado do contato : ".$resultadoComando."</p>";
        
        }elseif($resultadoComando == "MQ"){
        
            $msgConfigurada = implode(",", $parts);//convertendo Array para String
            //$msgConfigurada .="#";
            echo "<br>entrou MQ_$msgConfigurada_<br>";
            socket_write($spawn,$msgConfigurada,strlen($msgConfigurada));
            socket_close($spawn);
        }elseif ($resultadoComando=="JAVA") {
            $fala = "fala mano";
            echo "<br>Vai falar<br>";
            socket_write($spawn,$fala,strlen($fala));
        }else{
            $speed = "*ET,SN,OD,0078#";
            //$speed = "*ET,358155100088765,MQ#";
            socket_write($spawn,$speed,strlen($speed));       
        }     
        if($cont == 3){
            exit;
        }
    }   
    socket_close($socket);
?>
<?php
$socket = fsockopen('udp://pool.ntp.br', 123, $err_no, $err_str, 1);
if ($socket)
{
    if (fwrite($socket, chr(bindec('00'.sprintf('%03d', decbin(3)).'011')).str_repeat(chr(0x0), 39).pack('N', time()).pack("N", 0)))
    {
        stream_set_timeout($socket, 1);
        $unpack0 = unpack("N12", fread($socket, 48));
        echo date('Y-m-d H:i:s', $unpack0[7]);
    }

    fclose($socket);
}
?>