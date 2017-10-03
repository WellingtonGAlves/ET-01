<?php
    require_once ("C:/xampp/htdocs/Master-yi/Util/Funcoes_Comandos.php");
    
    error_reporting(0);
    set_time_limit(0);
    ob_implicit_flush();
    $host = "192.168.25.216";
    $port = 54321;
    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket<br>");
    $result = socket_bind($socket,$host,54321) or die("Could not bind to  socket<br>");
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
            if($msg == "exit"){
                exit;
            }
            echo "<p>Rastreador Enviou : ".$msg."</p>";
            $parts_msg = convertStringToArray($msg);
            
            /*
            -----------------VERIFICA SE NÃO A COMANDOS PARA O TRACKER
            */
            $checkResultado = explode("|",checkComando($parts_msg[1]));  //setarNumeroMaster|342432423
            
            if($checkResultado[0] == "cadastrarNumeroMaster"){// ----CADASTRAR O NUMERO MASTER DE MONITORAMENTO - ZH
                $comando = "*ET,$checkResultado[1]],ZH,$checkResultado[2]#";
                socket_write($spawn,$comando,strlen($comando));
                socket_close($spawn);
                echo "<p>Servidor Respondeu :".$comando." --CADASTRAR O NUMERO MASTER DE MONITORAMENTO - ZH</p>";
            }elseif($checkResultado[0] == "cadastrarNumeroSecundario"){// ----CADASTRAR NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH
                $comando = "*ET,$checkResultado[1],FH,$checkResultado[3],$checkResultado[2]#";                
                socket_write($spawn,$comando,strlen($comando));
                socket_close($spawn);
                echo "<p>Servidor Respondeu :".$comando." --CADASTRAR NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH</p>";
            }elseif($checkResultado[0] == "descadastrarNumeroSecundario"){// ----DESCADASTRAR NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH
                $comando = "*ET,$checkResultado[1],FH,$checkResultado[3],$checkResultado[2]#";                
                socket_write($spawn,$comando,strlen($comando));
                socket_close($spawn);
                echo "<p>Servidor Respondeu :".$comando." --DESCADASTRAR NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH</p>";
            }elseif($checkResultado[0] == "descadastrarTodosNumeroSecundario"){// ----DESCADASTRAR TODOS OS NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH
                $comando = "*ET,$checkResultado[1],FH,$checkResultado[3],$checkResultado[2]#";                
                socket_write($spawn,$comando,strlen($comando));
                socket_close($spawn);
                echo "<p>Servidor Respondeu :".$comando." --DESCADASTRAR TODOS OS NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH</p>";
            }elseif($checkResultado[0] == "ativarEconomiaEnergia"){// ----ATIVAR MODO DE ECONOMIA DE ENERGIA - PM
                $comando = "*ET,$checkResultado[1],PM,$checkResultado[2]#";                                
                socket_write($spawn,$comando,strlen($comando));
                socket_close($spawn);
                echo "<p>Servidor Respondeu :".$comando." --ATIVAR MODO DE ECONOMIA DE ENERGIA - PM</p>";
            }elseif($checkResultado[0] == "desativarEconomiaEnergia"){// ----DESATIVAR MODO DE ECONOMIA DE ENERGIA - PM
                $comando = "*ET,$checkResultado[1],PM,$checkResultado[2]#";                                
                socket_write($spawn,$comando,strlen($comando));
                socket_close($spawn);
                echo "<p>Servidor Respondeu :".$comando." --DESATIVAR MODO DE ECONOMIA DE ENERGIA - PM</p>";
            }
            
            /*
            -----------------EXECUTA COMANDOS ENVIADOS PELO TRACKER 
            */
            
            $resultadoComando = lerMensagem($msg);
            
            if($resultadoComando=="fsdsf"){ 
            }elseif($resultadoComando == "sem tratamento"){// retirar-----------------------------------------------------------
                socket_write($spawn,"sem tratamento:"."$msg",strlen("sem tratamento:"."$msg"));//enviaa uma resposta ao tracker
                socket_close($spawn);
                echo "<p>Servidor Respondeu :sem tratamento:</p>";
            }elseif($resultadoComando=="ativarAntiRoubo") { //--ANTI-ROUBO ATIVADO
                $comando = "*ET,".$parts_msg[1].",FD,F1#";                
                socket_write($spawn,$comando,strlen($comando));
                socket_close($spawn);//fecha conexão com o tracker
                echo "<p>Servidor Respondeu :".$comando." --ANTI-ROUBO ATIVADO</p>";
            }elseif($resultadoComando=="desativarAntiRoubo") { //--ANTI-ROUBO DESATIVADO
                $comando = "*ET,".$parts_msg[1].",FD,F2#";                
                socket_write($spawn,$comando,strlen($comando));
                socket_close($spawn);//fecha conexão com o tracker
//                echo "<p>Resultado do contato :_$resultadoComando_</p>";
                echo "<p>Servidor Respondeu :".$comando." --ANTI-ROUBO DESATIVADO</p>";
            }elseif($resultadoComando=="desativaCombustivel") { //--DESATIVA COMBUSTIVEL
                $comando = "*ET,".$parts_msg[1].",FD,Y1#";               
                socket_write($spawn,$comando,strlen($comando));
                socket_close($spawn);//                
                echo "<p>Servidor Respondeu :".$comando." --DESATIVA COMBUSTIVEL</p>";
            }elseif($resultadoComando=="ativaCombustivel") { //--ATIVA COMBUSTIVEL
                $comando = "*ET,".$parts_msg[1].",FD,Y2#";                
                socket_write($spawn,$comando,strlen($comando));
                socket_close($spawn);//                
                echo "<p>Servidor Respondeu :".$comando." --ATIVA COMBUSTIVEL</p>";
            }elseif($resultadoComando=="ativaAlarmeCombustivel") { //--ATIVA ALARME E COMBUSTIVEL
                $comando = "*ET,".$parts_msg[1].",FD,F1Y2#";                
                socket_write($spawn,$comando,strlen($comando));
                socket_close($spawn);//                
                echo "<p>Servidor Respondeu :".$comando." --ATIVA ALARME E COMBUSTIVEL</p>";
            }elseif($resultadoComando=="desativaAlarmeCombustivel") { //--DESATIVA ALARME E COMBUSTIVEL
                $comando = "*ET,".$parts_msg[1].",FD,F2Y1#";                
                socket_write($spawn,$comando,strlen($comando));
                socket_close($spawn);//                
                echo "<p>Servidor Respondeu :".$comando." --DESATIVA ALARME E COMBUSTIVEL</p>";
            }elseif($resultadoComando=="desativaAlarmeAtivaCombustivel") { //--DESATIVA ALARME E ATIVA COMBUSTIVEL
                $comando = "*ET,".$parts_msg[1].",FD,F2Y2#";                
                socket_write($spawn,$comando,strlen($comando));
                socket_close($spawn);//                
                echo "<p>Servidor Respondeu :".$comando." --DESATIVA ALARME E ATIVA COMBUSTIVEL</p>";
            }elseif($resultadoComando=="ativaAlarmeDesativaCombustivel") { //--ATIVA ALARME E DESATIVA COMBUSTIVEL
                $comando = "*ET,".$parts_msg[1].",FD,F1Y1#";                
                socket_write($spawn,$comando,strlen($comando));
                socket_close($spawn);//                
                echo "<p>Servidor Respondeu :".$comando." --ATIVA ALARME E DESATIVA COMBUSTIVEL</p>";
            }elseif($resultadoComando == "atualizouNumeroPrincipal"){ //---ATUALIZOU NUMERO PRINCIPAL
                $parts_msg[3]="Numero Master registrado na Plataforma"; 
                $msgConfigurada = arrayToString($parts_msg);
                $msgConfigurada = addHashtag($msgConfigurada);                
                socket_write($spawn,$msgConfigurada,strlen($msgConfigurada));
                socket_close($spawn);//fecha conexão com o tracker//               
                echo "<p>Servidor Respondeu :".$msgConfigurada." ---ATUALIZOU NUMERO PRINCIPAL</p>";
            }elseif($resultadoComando == "naoCadastrou/naoRegistrouNumeroPrincipal"){ //---NÃO CADASTROU IMEI E NEM NUMERO PRINCIPAL
                $parts_msg[3]="Numero Master não registrado na Plataforma e nem o IMEI"; 
                $msgConfigurada = arrayToString($parts_msg);
                $msgConfigurada = addHashtag($msgConfigurada);                
                socket_write($spawn,$msgConfigurada,strlen($msgConfigurada));
                socket_close($spawn);
                echo "<p>Servidor Respondeu :".$msgConfigurada." ---NÃO CADASTROU IMEI E NEM NUMERO PRINCIPAL</p>";
            }elseif($resultadoComando == "naoAtualizouNumeroPrincipal"){ //---NÃO ATUALIZOU NUMERO PRINCIPAL 
                $parts_msg[3]="Numero Master não registrado na Plataforma"; 
                $msgConfigurada = arrayToString($parts_msg);
                $msgConfigurada = addHashtag($msgConfigurada);                
                socket_write($spawn,$msgConfigurada,strlen($msgConfigurada));
                socket_close($spawn);//fecha conexão com o tracker
                echo "<p>Servidor Respondeu :".$msgConfigurada." --NÃO ATUALIZOU NUMERO PRINCIPAL</p>";
            }elseif($resultadoComando === "MQ"){//MQ é o comando para o tracker saber se deve CARREGAR dados (MQ)       
                socket_write($spawn,$msg,strlen($msg));
                socket_close($spawn);
                echo "<p>Servidor Respondeu :".$msg." --CARREGAR dados (MQ) </p>";
            }else{//                                            NÃO FAZ NADA
                
                //$speed = "*ET,358155100088765,MQ#";
                socket_write($spawn,$resultadoComando,strlen($resultadoComando));
                socket_close($spawn);
                echo "<p>Servidor Respondeu :".$resultadoComando."-- não caiu em nenhum if</p>";                
            }


            /*-----------------------------------------
                    MOSTRA DATA DAQUI PARA BAIXO
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