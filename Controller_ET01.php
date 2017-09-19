<?php
    require_once ("C:/xampp/htdocs/ET-01/Util/Funcoes.php");
    
    error_reporting(0);
    set_time_limit(0);
    ob_implicit_flush();
    $host = "192.168.25.216";
    $port = 54321;
    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create
    socket<br>");
    $result = socket_bind($socket,$host,54321) or die("Could not bind to
    socket<br>");
    $result = socket_listen($socket) or die("Could not set up socket
    listener<br>");
//    echo "Waiting for connections... <br>";
    $cont = 0;
    $qnt = 0;

    while(1)
    {
    $cont = $cont +1;
    $spawn[++$i] = socket_accept($socket) or die("Could not accept incoming
    connection <br>");
//    echo "| _".$cont."_ | <br>";
    $input = socket_read($spawn[$i],1024);
    $msg = $input;  
    echo "<p>Rastreador Enviou : ".$msg."</p>";
    $resultadoComando = lerMensagem($msg);
    if ($resultadoComando == "atualizouNumeroPrincipal") {
        $parts[3]="Numero principal registrado na Plataforma"; 
        $msgConfigurada = implode(",", $parts);//convertendo Array para String
        $msgConfigurada .="#";
        echo "<br>$msgConfigurada<br>";
        socket_send($spawn,"$msgConfigurada",strlen($msgConfigurada));
        echo "<p>Resultado do contato : ".$resultadoComando."</p>";
        
    }
    

    socket_close($spawn[$i]);
    if($msg == "exit"){
            exit;
    }

    }
    socket_close($socket);
    ?>		
