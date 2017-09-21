<?php
    require_once ("C:/xampp/htdocs/ET-01/Util/Funcoes_Comandos.php");
    
    error_reporting(0);
    set_time_limit(0);
    ob_implicit_flush();
    $host = "192.168.25.216";
    $port = 54321;
    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket<br>");
    $result = socket_bind($socket,$host,54000) or die("Could not bind to  socket<br>");
    $result = socket_listen($socket) or die("Could not set up socket  listener<br>");
    

    $cont = 0;
    $qnt = 0;
    
    
     while(1)
    {         
         $cont = $cont +1;
         
         $spawn = socket_accept($socket) or die("Could not accept incoming connection <br>");
        
         while(socket_recv($spawn,$buffer_enviado_rastreador,1024,MSG_WAITALL))
        {        
            $msg = trim($buffer_enviado_rastreador);
            if($msg == "exit"){exit;}
            echo "<p>Rastreador Enviou : ".$msg."</p>";
            $parts_msg = convertStringToArray($msg);
            /*
            -----------------VERIFICA SE O TRACKER PRECISA DE UM RETORNO PARA CARREGAR DADOS
            */
            if($parts_msg[2]=="MQ#"){
                socket_write($spawn,$msg,strlen($msg));
                socket_close($spawn);
            }
            /*
            -----------------VERIFICA SE NÃO A COMANDOS PARA O TRACKER
            */
            $checkComando = checkComando($parts_msg[1]);            
            if ($checkComando != false) {
                /*
                -----------------EXECUTA COMANDOS ENVIADOS PELO TRACKER 
                */
                socket_write($spawn,$checkComando,strlen($checkComando));
                socket_close($spawn);
                //echo "<p>Servidor Respondeu :".$checkComando."</p>";
            }else{
                /*-----------------------------------------
                    LE A MENSAGEM QUE OS TRACKER ENVIOU
                  ------------------------------------------ 
                          */
                $resultadoComando = lerMensagem($parts_msg);
                socket_write($spawn,$resultadoComando,strlen($resultadoComando));
                socket_close($spawn);
            }   
            /*-----------------------------------------
                    MOSTRA DATA E HORA DAQUI PARA BAIXO
                  ------------------------------------------ 
                          */
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
             
    }
    
?>
