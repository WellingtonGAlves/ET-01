<?php
    require_once("C:/xampp/htdocs/ET-01/dao/Connection.php");
    
    
    
    function selectIMEI($imei) {       
        $query = dbExecute("SELECT `IMEI` FROM `config_et` WHERE `IMEI` = '$imei'");
        if ($query->num_rows >0){        
//            echo 'true';
            return true;
        }else{ 
//            echo 'false';
            return false;
        }    
    }
       
    function cadastrarIMEI($imei) {
//        echo '<br>entrou para cadastrar';
        //cadastrando com informações default
        $query_insert = dbExecute("INSERT INTO `config_et`(`IMEI`,`TOLERANCIA`, `RASTREAMENTO_AO_VIVO`, `INTERVALO`, `CONTAGEM`, `SERVER_IP`, `UDP_PORTA`, `CORRE`, `COMANDOS_ALARME_POTENCIA`, `CERCA`, `LIMITE_VELOCIDADE`, `SAIR_SUSPENDER`, `ENTRAR_SUSPENDER`, `SENSIBILIDADE`, `INTERVALO_TRANS_DIRIGINDO`, `INTERVALO_TRANS_PARADO`, `MODO_ECONOMICO`, `NUMERO_PRINCIPAL`, `FUSO_HORARIO`, `LINGUAGEM`, `SMS`, `RESTART`, `STATUS_DISPOSITIVO`, `ATIVO`) "
                . "VALUES ($imei,null,'GZ','0005','0001','179.184.59.144','D431','1','F1Y2',FALSE,NULL,'00F0','00F0','00','05','0A','01',NULL,'3.0','01','01',FALSE,'Aguardando resposta',true)");
        
//        print_r("_".$query_insert."_");
        if(($query_insert)==1){
//            echo "<br>Cadastrou";
            return true;
        }else{
//            echo "falha ao cadastrar";            
            return false;
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

    function updateNumeroPrincipal($parts_msg) {
        $result = dbExecute("UPDATE  `config_et` SET  `NUMERO_PRINCIPAL` =  '$parts_msg[3]' WHERE  `IMEI` =  '$parts_msg[1]'");
        if($result== 0){
            return false;
        }else{
            return true;
        }
    }
    
    function updateRastreador($parts_msg) {
        $result = dbExecute("UPDATE  `config_et` SET  `NUMERO_PRINCIPAL` =  '$parts_msg[3]' WHERE  `IMEI` =  '$parts_msg[1]'");
        if($result== 0){
            return false;
        }else{
            return true;
        }
    }
    
    function removeHashtag($param) {
        $posicaoUltimoCaracter = strlen ($param[2]); 
        $param[2] = substr($param[2], 0, $posicaoUltimoCaracter-1);
        return $param;
}
    
    function convertArrayToString($param) {//converte array para string
       $string = implode(",", $param); 
        return $string;
    }
    
    function convertStringToArray($param) {//converte string para array
       $string = explode(",", $param); 
       
       return $string;
    }
    
    function addHashtag($param) { //adiciona hashtag
       $param.="#";
        return $param;
    }
    
    function checkComando($imei) {
        
        $checkResultado = dbExecute("SELECT  `COMANDO` FROM  `COMANDOS_RASTREADOR` WHERE  `IMEI` =  '$imei' and `EXECUTADO` = 'false'");
        print_r($checkResultado);
        if($checkResultado->num_rows >0){ //VERIFICA SE VOLTOU ALGUM REGISTRO
            $checkResultado->data_seek(0);    
            while($row = $checkResultado->fetch_assoc()){    
                $comandoPlataforma = $row['COMANDO']; // COMANDO É A COLUNA QUE ESTÁ NA TABELA
                
                $comandoPlataforma = trim($comandoPlataforma); //limpando espaços em branco
                
                $checkResultado = convertStringToArray($comandoPlataforma);
                
                print_r($checkResultado);
                if($checkResultado[0] == "cadastrarNumeroMaster"){// ----CADASTRAR O NUMERO MASTER DE MONITORAMENTO - ZH
                $comando = "*ET,$checkResultado[1],ZH,$checkResultado[2]#";            
                echo "<p>Servidor Respondeu :".$comando." --CADASTRAR O NUMERO MASTER DE MONITORAMENTO - ZH</p>";
                return $comando;
                }elseif($checkResultado[0] == "cadastrarNumeroSecundario"){// ----CADASTRAR NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH
                    //$checkResultado[3] = str_replace(",", "", $checkResultado[3]);
                    $comando = "*ET,$checkResultado[1],FH,$checkResultado[3],$checkResultado[2]#";                
                    echo "<p>Servidor Respondeu :".$comando." --CADASTRAR NUMERO DE MONITORAMENTO SECUNDÁRIO - FH</p>";
                    return $comando;
                }elseif($checkResultado[0] == "descadastrarNumeroSecundario"){// ----DESCADASTRAR NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH
                    $comando = "*ET,$checkResultado[1],FH,$checkResultado[3],$checkResultado[2]#";                
                    echo "<p>Servidor Respondeu :".$comando." --DESCADASTRAR NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH</p>";
                    return $comando;
                }elseif($checkResultado[0] == "descadastrarTodosNumeroSecundario"){// ----DESCADASTRAR TODOS OS NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH
                    $comando = "*ET,$checkResultado[1],FH,$checkResultado[2]#";                
                    echo "<p>Servidor Respondeu :".$comando." --DESCADASTRAR TODOS OS NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH</p>";
                    return $comando;
                }elseif($checkResultado[0] == "ativarEconomiaEnergia"){// ----ATIVAR MODO DE ECONOMIA DE ENERGIA - PM
                    $comando = "*ET,$checkResultado[1],PM,$checkResultado[2]#";                                
                    echo "<p>Servidor Respondeu :".$comando." --ATIVAR MODO DE ECONOMIA DE ENERGIA - PM</p>";
                    return $comando;
                }elseif($checkResultado[0] == "desativarEconomiaEnergia"){// ----DESATIVAR MODO DE ECONOMIA DE ENERGIA - PM
                    $comando = "*ET,$checkResultado[1],PM,$checkResultado[2]#";                                
                    echo "<p>Servidor Respondeu :".$comando." --DESATIVAR MODO DE ECONOMIA DE ENERGIA - PM</p>";
                    return $comando;
                }elseif($checkResultado[0]=="ativarAntiRoubo") { //--ANTI-ROUBO ATIVADO
                    $comando = "*ET,".$checkResultado[1].",FD,F1#";                
                    echo "<p>Servidor Respondeu :".$comando." --ANTI-ROUBO ATIVADO</p>";
                    return $comando;
                }elseif($checkResultado[0]=="desativarAntiRoubo") { //--ANTI-ROUBO DESATIVADO
                    $comando = "*ET,".$checkResultado[1].",FD,F2#";                
                    echo "<p>Servidor Respondeu :".$comando." --ANTI-ROUBO DESATIVADO</p>";
                    return $comando;
                }elseif($checkResultado[0]=="desativaCombustivel") { //--DESATIVA COMBUSTIVEL
                    $comando = "*ET,".$checkResultado[1].",FD,Y1#";               
                    echo "<p>Servidor Respondeu :".$comando." --DESATIVA COMBUSTIVEL</p>";
                    return $comando;
                }elseif($checkResultado[0]=="ativaCombustivel") { //--ATIVA COMBUSTIVEL
                    $comando = "*ET,".$checkResultado[1].",FD,Y2#";                
                    echo "<p>Servidor Respondeu :".$comando." --ATIVA COMBUSTIVEL</p>";
                    return $comando;
                }elseif($checkResultado[0]=="ativaAlarmeCombustivel") { //--ATIVA ALARME E COMBUSTIVEL
                    $comando = "*ET,".$checkResultado[1].",FD,F1Y2#";                
                    echo "<p>Servidor Respondeu :".$comando." --ATIVA ALARME E COMBUSTIVEL</p>";
                    return $comando;
                }elseif($checkResultado[0]=="desativaAlarmeCombustivel") { //--DESATIVA ALARME E COMBUSTIVEL
                    $comando = "*ET,".$checkResultado[1].",FD,F2Y1#";                
                    echo "<p>Servidor Respondeu :".$comando." --DESATIVA ALARME E COMBUSTIVEL</p>";
                    return $comando;
                }elseif($checkResultado[0]=="desativaAlarmeAtivaCombustivel") { //--DESATIVA ALARME E ATIVA COMBUSTIVEL
                    $comando = "*ET,".$checkResultado[1].",FD,F2Y2#";                
                    echo "<p>Servidor Respondeu :".$comando." --DESATIVA ALARME E ATIVA COMBUSTIVEL</p>";
                    return $comando;
                }elseif($checkResultado[0]=="ativaAlarmeDesativaCombustivel") { //--ATIVA ALARME E DESATIVA COMBUSTIVEL
                    $comando = "*ET,".$checkResultado[1].",FD,F1Y1#";                
                    echo "<p>Servidor Respondeu :".$comando." --ATIVA ALARME E DESATIVA COMBUSTIVEL</p>";
                    return $comando;
                }else{//                                            NÃO FAZ NADA
                    echo "<p>Servidor Respondeu :".convertArrayToString($checkResultado)."-- não caiu em nenhum if do checkComando</p>";  
                    return false;
                }
            }   
        }else{
            return false;
        }
                   
        
    }
    
    function lerMensagem($parts_msg){
    //*ET,SN,DW,A/V,YYMMDD,HHMMSS,Latitude,Longitude,Speed,Course,Status,Signal,Power,oil,LC#   
    $tot_parts = count($parts_msg);
    echo "<p>_Total de partes $tot_parts <-- </p>";  
    
    if($tot_parts==16){   //TRACKER ENVIA LOACALIZAÇÃO  
        $link = dbConnect();
        if($link != null){//verifica se a conexão com o banco
            dbClose($link);//fecha conexao
            
            $resultado_query = selectIMEI($parts_msg[1]);//verifica se o imei está cadastrado
            //print_r("<br>".$parts_msg[6]."<br>");
            
            if ($resultado_query === false) {//verifica se o IMEI está cadastrado
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
                return "dbConnect() falhou LINHA 177 Funcoes_comandos.php";
            }
        }elseif($parts_msg[2]=="RG") { //TRACKER QUER REGISTRAR UM NUMERO PRINCIPAL
//            echo 'vai verificar';
            $resultado_query = selectIMEI($parts_msg[1]);        
            if ($resultado_query === false) {//verifica se o IMEI está cadastrado
//                echo 'entrou no if para cadastrar';
                $cadastrou = cadastrarIMEI($parts_msg[1]);//cadastra IMEI
                if ($cadastrou === true) {   //verifica se cadastrou IMEI                                        
                    
                    $result = updateNumeroMaster($part);
                    if ($result==true) {     //    ---ATUALIZOU NUMERO PRINCIPAL                                                
                        $parts_msg[3]="Numero Master registrado na Plataforma"; 
                        $msgConfigurada = arrayToString($parts_msg);
                        $msgConfigurada = addHashtag($msgConfigurada);                
                        echo "<p>Servidor Respondeu :".$msgConfigurada." ---ATUALIZOU NUMERO PRINCIPAL</p>";                                  
                        return $msgConfigurada;
                    }else{                        
                        $parts_msg[3]="Numero Master não registrado na Plataforma"; 
                        $msgConfigurada = arrayToString($parts_msg);
                        $msgConfigurada = addHashtag($msgConfigurada);                                     
                        echo "<p>Servidor Respondeu :".$msgConfigurada." --NÃO ATUALIZOU NUMERO PRINCIPAL</p>";            
                        return $msgConfigurada;
                    }
                                       
                }else{                    
                    $parts_msg[3]="Numero Master não registrado na Plataforma e nem o IMEI"; 
                    $msgConfigurada = arrayToString($parts_msg);
                    $msgConfigurada = addHashtag($msgConfigurada);                
                    echo "<p>Servidor Respondeu :".$msgConfigurada." ---NÃO CADASTROU IMEI E NEM NUMERO PRINCIPAL</p>";
                    return $msgConfigurada;
                }
            }else{               
                $result = updateNumeroPrincipal($part);
                if ($result==true) {
                    return "atualizouNumeroPrincipal";
                }else{
                    return "naoAtualizouNumeroPrincipal";
                }
            }       
        }elseif ($parts_msg[2]=="MQ#") {//MQ é o comando para o tracker saber se deve CARREGAR DADOS (MQ)                               
            $msg = convertArrayToString($parts_msg);
            echo "<p>Servidor Respondeu :".$msg." --CARREGAR dados (MQ) </p>";
            return $msg;        
        }else{
            return "sem tratamento";
        }        
//        if(explode("#", $parts_msg[2])){
//            echo "<p>_explode $parts_msg[2]</p>";
//             
//            $parts_msg[2] = removeHashtag($parts_msg);
////            print_r($msg[15]);
//            echo "<p>_explode Retirando $parts_msg[2]</p>";
//            return "removeu hashtag";       
//        }
    }
    
    