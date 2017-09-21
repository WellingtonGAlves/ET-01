<?php
    require_once ("C:/xampp/htdocs/ET-01/Util/Funcoes_Comandos.php");
    
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
         $spawn = socket_accept($socket) or die("Could not accept incoming connection <br>");
        while(socket_recv($spawn,$buffer_enviado_rastreador,1024,MSG_WAITALL))
        {
        
            $msg = trim($buffer_enviado_rastreador);
            if($msg == "exit"){
                exit;
            }
            echo "<p>Rastreador Enviou : ".$msg."</p>";
            
            
            $resultadoComando = lerMensagem($msg);
            $parts_msg = convertStringToArray($msg);
            
            //echo "<p>resultadoComando :$resultadoComando___</p>";
            
            if($resultadoComando=="fsdsf"){
            }elseif($resultadoComando == "sem tratamento"){// retirar-----------------------------------------------------------
                socket_write($spawn,"sem tratamento:"."$msg",strlen("sem tratamento:"."$msg"));//enviaa uma resposta ao tracker
                socket_close($spawn);
                echo "<p>Servidor Respondeu :sem tratamento:</p>";
            }elseif($resultadoComando=="ativarAntiRoubo") {
                $comando = "*ET,".$parts_msg[1].",FD,F1Y1#";
                echo "<br>__$comando__<br>";
                socket_write($spawn,$comando,strlen($comando));//enviaa uma resposta ao tracker
                //socket_close($spawn);//fecha conexão com o tracker
//                echo "<p>Resultado do contato :_$resultadoComando_</p>";
                echo "<p>Servidor Respondeu :".$comando."</p>";
            }elseif($resultadoComando === "atualizouNumeroPrincipal") {
                $parts_msg[3]="Numero Master registrado na Plataforma"; 
                $msgConfigurada = arrayToString($parts_msg);//convertendo Array para String
                $msgConfigurada = addHashtag($msgConfigurada);
                echo "<br>mensagem configurada: $msgConfigurada<br>";
                socket_write($spawn,$msgConfigurada,strlen($msgConfigurada));//enviaa uma resposta ao tracker
                socket_close($spawn);//fecha conexão com o tracker
//                echo "<p>Resultado do contato : ".$resultadoComando."</p>";
                echo "<p>Servidor Respondeu :".$msgConfigurada."</p>";
            }elseif($resultadoComando == "atualizouNumeroPrincipal"){
                $parts_msg[3]="Numero Master registrado na Plataformaaaaaa"; 
                $msgConfigurada = arrayToString($parts_msg);//convertendo Array para String
                $msgConfigurada = addHashtag($msgConfigurada);
                echo "<br>mensagem configurada: $msgConfigurada<br>";
                socket_write($spawn,$msgConfigurada,strlen($msgConfigurada));//enviaa uma resposta ao tracker
                socket_close($spawn);//fecha conexão com o tracker
//                echo "<p>Resultado do contato : ".$resultadoComando."</p>";   
                echo "<p>Servidor Respondeu :".$msgConfigurada."</p>";
            }elseif($resultadoComando === "MQ"){//MQ é o comando para o tracker saber se deve CARREGAR dados        
                socket_write($spawn,$msg,strlen($msg));
                socket_close($spawn);
                echo "<p>Servidor Respondeu :".$msg."</p>";
            }else{
                
                //$speed = "*ET,358155100088765,MQ#";
                socket_write($spawn,$resultadoComando,strlen($resultadoComando));
                socket_close($spawn);
                echo "<p>Servidor Respondeu :".$resultadoComando."</p>";                
            }            
        }        
        $date = fsockopen('udp://pool.ntp.br', 123, $err_no, $err_str, 1);
        if ($date)
        {
            if (fwrite($date, chr(bindec('00'.sprintf('%03d', decbin(3)).'011')).str_repeat(chr(0x0), 39).pack('N', time()).pack("N", 0)))
            {
               stream_set_timeout($date, 1);
               $unpack0 = unpack("N12", fread($date, 48));
               echo date('Y-m-d H:i:s', $unpack0[7]);
           }
           fclose($date);
        }       
    }
    
?>
<?php
    $date = fsockopen('udp://pool.ntp.br', 123, $err_no, $err_str, 1);
    if ($date)
    {
        if (fwrite($date, chr(bindec('00'.sprintf('%03d', decbin(3)).'011')).str_repeat(chr(0x0), 39).pack('N', time()).pack("N", 0)))
        {
            stream_set_timeout($date, 1);
            $unpack0 = unpack("N12", fread($date, 48));
            echo date('Y-m-d H:i:s', $unpack0[7]);
        }
        fclose($date);
    }
    print_r($date);
    socket_close($socket);//encerra servidor
?>