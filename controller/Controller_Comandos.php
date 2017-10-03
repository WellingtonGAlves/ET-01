<?php
    require_once ("C:/xampp/htdocs/Master-yi/Util/Funcoes_Comandos.php");
    
    error_reporting(0);
    set_time_limit(0);
    ob_implicit_flush();
    $host = "192.168.25.215";
    $port = 54321;
    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket<br>");
    $result = socket_bind($socket,$host,44444) or die("Could not bind to  socket<br>");
    $result = socket_listen($socket) or die("Could not set up socket  listener<br>");
    

    $cont = 0;
    $qnt = 0;
    
    
     while(1)
    {         
         $cont = $cont +1;
         
         $spawn = socket_accept($socket) or die("Could not accept incoming connection <br>");
        
         while($buffer_enviado_rastreador = socket_read($spawn,1024))
        {        
            $msg = trim($buffer_enviado_rastreador);//LIMPA ESPAÇO EM BRANCO
            if($msg == "exit"){
                $msg = "exitou";
                socket_write($spawn,$msg,strlen($msg));
                socket_close($spawn);                
                exit;
                
            }
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
            -----------------VERIFICA SE EXECUTOU ALGUM COMANDO
            */
            $checkResposta = checkResposta($parts_msg);
            print_r($checkResposta);
            if ($checkResposta != false) {
                socket_write($spawn,$checkResposta,strlen($checkResposta));
                socket_close($spawn);
            }
            /*
            -----------------VERIFICA SE NÃO A COMANDOS PARA O TRACKER
            */
            $checkComando = checkComando($parts_msg[1]);            
            if ($checkComando != false) {
                //echo "<br>checkComando == TRUE";
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
                //echo "<br>vamos ler";
                $resultadoComando = lerMensagem($parts_msg);
                if ($parts_msg[1]=="358155100088765") {                    
                    insertMsgTracker("INSERT INTO `mensagens` (`msg`) VALUES('$msg')");
                    socket_close($spawn);
                }else{
                    socket_write($spawn,$resultadoComando,strlen($resultadoComando));
                    socket_close($spawn);
                }
            }   
            /*-----------------------------------------
                    MOSTRA DATA E HORA DAQUI PARA BAIXO
                  ------------------------------------------ 
                          */
         
        } 
        
    }
    socket_close($socket);
    
?>
