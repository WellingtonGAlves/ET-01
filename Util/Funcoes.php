<?php
    require_once("C:/xampp/htdocs/ET-01/Util/Connection.php");
    
    function cadastrarIMEI($imei) {
//        echo '<br>entrou para cadastrar';
        //cadastrando com informações default
        $query_insert = dbExecute("INSERT INTO `config_et`(`IMEI`,`TOLERANCIA`, `RASTREAMENTO_AO_VIVO`, `INTERVALO`, `CONTAGEM`, `SERVER_IP`, `UDP_PORTA`, `CORRE`, `CONTROLE_ALARME_POTENCIA`, `COMANDOS_ALARME_POTENCIA`, `CERCA`, `LIMITE_VELOCIDADE`, `SAIR_SUSPENDER`, `ENTRAR_SUSPENDER`, `SENSIBILIDADE`, `INTERVALO_TRANS_DIRIGINDO`, `INTERVALO_TRANS_PARADO`, `MODO_ECONOMICO`, `NUMERO_PRINCIPAL`, `FUSO_HORARIO`, `LINGUAGEM`, `SMS`, `RESTART`, `STATUS_DISPOSITIVO`, `ATIVO`) "
                . "VALUES ($imei,null,'GZ','0005','0001','179.184.59.144','D431','1',TRUE,'F1Y2',FALSE,NULL,'00F0','00F0','00','05','0A','01',NULL,'3.0','01','01',FALSE,'Aguardando resposta',true)");
        
//        print_r("_".$query_insert."_");
        if(($query_insert)==1){
//            echo "<br>Cadastrou";
            return true;
        }else{
//            echo "falha ao cadastrar";            
            false;
        }
    }
    function salvarLocalAtual($msg){
        
        $posicaoUltimoCaracter = strlen ($msg[15]);        
        if(explode("#", $msg[15])){
            
            $msg[15] = substr($msg[15], 0, $posicaoUltimoCaracter-1);
//            print_r($msg[15]);
        }
        if(dbExecute("INSERT INTO `local_atual_et`( `IMEI`, `COMANDO`, `VALIDADE_DADOS`, `YYMMDD`, `HHMMSS`, `LATITUDE`, `LONGITUDE`, `VELOCIDADE`, `CURSO`, `STATUS`, `SINAL`, `POWER`, `OLEO`, `ALTITUDE`, `QUILOMETRAGEM`) "
                . "VALUES ('$msg[1]','$msg[2]','$msg[3]','$msg[4]','$msg[5]','$msg[6]','$msg[7]','$msg[8]','$msg[9]','$msg[10]','$msg[11]','$msg[12]','$msg[13]','$msg[14]','$msg[15]')")==1){
            return true;//cadastrou
        }else{
            return false;//nao cadastrou
        }
    }
    function atualizarLocalAtual($msg){
        $posicaoUltimoCaracter = strlen ($msg[15]);        
        if(explode("#", $msg[15])){
            
            $msg[15] = substr($msg[15], 0, $posicaoUltimoCaracter-1);
//            print_r($msg[15]);
        }
        if (dbExecute("UPDATE `local_atual_et` SET `COMANDO`='$msg[2]',`VALIDADE_DADOS`='$msg[3]',`YYMMDD`='$msg[4]',`HHMMSS`='$msg[5]',`LATITUDE`='$msg[6]',`LONGITUDE`='$msg[7]',`VELOCIDADE`='$msg[8]',`CURSO`='$msg[9]',`STATUS`='$msg[10]',`SINAL`='$msg[11]',`POWER`='$msg[12]',`OLEO`='$msg[13]',`ALTITUDE`='$msg[14]',`QUILOMETRAGEM`='$msg[15]' WHERE IMEI = '$msg[1]'")) {
//            echo '<br>atualizou';        
            return true;//atualizou
        }else{
//            echo '<br>não atualizou';   
            return false;//nao atualizou
        }
    }    
    function lerMensagem($msg){
    //*ET,SN,DW,A/V,YYMMDD,HHMMSS,Latitude,Longitude,Speed,Course,Status,Signal,Power,oil,LC#   
    if ($msg=="JAVA") {
        return "JAVA";
    }
    $parts_msg = explode(',',$msg);
    $tot_parts = count($parts_msg);
    echo "<p>_$tot_parts</p>";
    if($tot_parts==16){
        //está mandando sua localização
        $link = dbConnect();
        if($link != null){//verifica se a conexão com o banco
            dbClose($link);//fecha conexao
            $query = "SELECT `IMEI` FROM `config_et` WHERE `IMEI` = $parts_msg[1]";
            $resultado_query = dbExecute($query);//verifica se o IMEI está cadastrado
            
            //print_r("<br>".$parts_msg[6]."<br>");
           
            
            if (mysqli_num_rows($resultado_query)==0) {//verifica se o IMEI está cadastrado
                echo 'entrou no if para cadastrar';
                $cadastrou = cadastrarIMEI($parts_msg[1]);//cadastra IMEI
                if ($cadastrou === true) {   //verifica se cadastrou IMEI
//                    echo 'vai salvar localização';
                    if (salvarLocalAtual($parts_msg)) {//salva localização atual do rastreador
//                        echo 'salvou local atual...fechamos a conexão com o socket';
                        return "cadastrou/atualizou";
                    }else{
//                        echo 'nao salvou a locallização';
                        return "cadastrou/naoAtualizou";
                    }
                    }else{
                        return "naoCadastrou";
                    }
            }else{                
//              echo '<br>vai atualizar localização do dispositivo existente<br>';
                if (atualizarLocalAtual($parts_msg)) {//atualiza o local atual do rastreador
//                  echo '<br>atualizou local atual...fechamos a conexão com o socket';                                        
                    return "atualizouLocalizacao";
                }else{
//                  echo '<br>nao atualizou a locallização';
                    return "naoAtualizouLocalizacao";
                }
            }  
            }else{
                return "dbConnect() falhou";
            }
        }elseif($parts_msg[2]=="RG") {
            $query = "SELECT `IMEI` FROM `config_et` WHERE `IMEI` = $parts_msg[1]";
            $resultado_query = dbExecute($query);//verifica se o IMEI está cadastrado
            
            if (mysqli_num_rows($resultado_query)==0) {//verifica se o IMEI está cadastrado
//                echo 'entrou no if para cadastrar';
                $cadastrou = cadastrarIMEI($parts_msg[1]);//cadastra IMEI
                if ($cadastrou === true) {   //verifica se cadastrou IMEI
                    echo "cadastrou";                    
                    dbExecute("UPDATE  `config_et` SET  `NUMERO_PRINCIPAL` =  '$msg[3]' WHERE  `IMEI` =  '$msg[1]]'");                    
                    return "atualizouNumeroPrincipal";                   
                }else{
                    return "naoCadastrou/naoRegistrouNumeroPrincipal";
                }
            }else{
                dbExecute("UPDATE  `config_et` SET  `NUMERO_PRINCIPAL` =  '$msg[3]' WHERE  `IMEI` =  '$msg[1]]'");
                return "atualizouNumeroPrincipal";
            }       
        }
        if(explode("#", $parts_msg[2])){
            echo "<p>_explode $parts_msg[2]</p>";
            $posicaoUltimoCaracter = strlen ($msg[2]); 
            $parts_msg[2] = substr($parts_msg[2], 0, $posicaoUltimoCaracter-1);
//            print_r($msg[15]);
            echo "<p>_explode Retirando $parts_msg[2]</p>";
            
        if ($parts_msg[2]=="MQ") {
            echo "<br>return MQ<br>";
            return "MQ";
        }
        }
    }
    
    function ouvindoRastreadores(string $host ,int $porta ){
        error_reporting(0);
        set_time_limit(0);
//        $host = "192.168.25.216";
//        $port = 54321;
        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create
        socket<br>");
        $result_bind = socket_bind($socket,$host,$porta) or die("Could not bind to  socket<br>");
        $result_listen = socket_listen($socket) or die("Could not set up socket
        listener<br>");
//      echo "Waiting for connections... <br>";
        $cont = 0;
        while(1)
        {
            $cont = $cont +1;
            $spawn[++$i] = socket_accept($socket) or die("Could not accept incoming connection <br>");
            $socket_read = socket_read($spawn[$i],1024);
            $msg_rastreador = $socket_read;
//          echo "<p>Rastreador Enviou : ".$msg_rastreador."</p>";
//          echo "<p>Rastreador Enviou : </p>";
            $retornoComando = lerMensagem($msg_rastreador);
            print_r("<p>".$retornoComando."</p>");
            socket_close($spawn[$i]);
//         if($cont == 10){
//             exit;
//         }
        }
        socket_close($socket);
        return "socketClose";
    }