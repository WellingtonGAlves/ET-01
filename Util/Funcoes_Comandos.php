<?php
    require_once("C:/xampp/htdocs/Master-yi/dao/Connection.php");
    
    
    
    function selectIMEI($imei) {       
        $query = dbExecute("SELECT `imei`,`id` FROM `configuracaos` WHERE `imei` = '$imei'");
        if ($query->num_rows >0){        
            
            return $query;
        }else{ 
//            echo 'false';
            return false;
        }    
    }
       
    function cadastrarIMEI($imei) {
//        echo '<br>entrou para cadastrar';
        //cadastrando com informações default
        $id = dbInsert("INSERT INTO `configuracaos`(`imei`,`tolerancia`, `rastreamento_ao_vivo`, `intervalo`, `contagem`, `server_ip`, `udp_porta`, `run`, `cmd_alarme_potencia`, `cerca`, `lmt_velocidade`, `sair_suspender`, `entrar_suspender`, `sensibilidade`, `intervalo_trans_dirigindo`, `intervalo_trans_parado`, `modo_economico`, `numero_principal`, `fuso_horario`, `linguagem`, `sms`, `restart`, `status_tracker`, `ativo`) "
                . "VALUES ($imei,null,'GZ','0005','0001','179.184.59.144','D431','1','ativaCombustivel',FALSE,NULL,'00F0','00F0','00','05','0A','01',NULL,'3.0','01','01',FALSE,'Aguardando resposta',true)");
        
//        print_r("_".$query_insert."_");
        if($id > 0){            
            return $id;
        }else{
//            echo "falha ao cadastrar";            
            return false;
        }
    }
    function salvarLocalAtual($msg,$idConfiguracao){
        
        $posicaoUltimoCaracter = strlen ($msg[15]);        
        if(explode("#", $msg[15])){
            
            $msg[15] = substr($msg[15], 0, $posicaoUltimoCaracter-1);
//            print_r($msg[15]);
        }
        if(dbExecute("INSERT INTO `registros`( `idConfiguracao`,`imei`, `cmd`, `validade_dados`, `yymmdd`, `hhmmss`, `lat`, `lng`, `velocidade`, `curso`, `status`, `sinal`, `power`, `oleo`, `altitude`, `quilometragem`) "
                . "VALUES ('$idConfiguracao','$msg[1]','$msg[2]','$msg[3]','$msg[4]','$msg[5]','$msg[6]','$msg[7]','$msg[8]','$msg[9]','$msg[10]','$msg[11]','$msg[12]','$msg[13]','$msg[14]','$msg[15]')")==1){
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
    
    //function removeHashtag($param) {
       // $posicaoUltimoCaracter = strlen ($param); 
       // $param = substr($param, 0, $posicaoUltimoCaracter-1);
       // return $param;
    //}
    function removeHashtag($param) {
        $param = str_replace('#', '', $param);
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
        

        $checkResultado = dbExecute("SELECT  `imei`,`cmd` FROM  `cmd_trackers` WHERE  `imei` =  '$imei' and `sucesso` = 'false'");
        //print_r($checkResultado);
        if($checkResultado->num_rows >0){ //VERIFICA SE VOLTOU ALGUM REGISTRO
            $checkResultado->data_seek(0);    
            while($row = $checkResultado->fetch_assoc()){    
                $comandoPlataforma = $row['cmd']; // COMANDO É A COLUNA QUE ESTÁ NA TABELA
                $imeiPlataforma = $row['imei'];
                $comandoPlataforma = trim($comandoPlataforma); //limpando espaços em branco
                
                $checkResultado = convertStringToArray($comandoPlataforma);
                
                //print_r($checkResultado);
                if($checkResultado[0] == "cadastrarNumeroMaster"){// ----CADASTRAR O NUMERO MASTER DE MONITORAMENTO - ZH
                $comando = "*ET,$imeiPlataforma,ZH,$checkResultado[2]#";            
                echo "<p>Servidor Respondeu :".$comando." --CADASTRAR O NUMERO MASTER DE MONITORAMENTO - ZH</p>";
                return $comando;
                }elseif($checkResultado[0] == "cadastrarNumeroSecundario"){// ----CADASTRAR NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH
                    //$checkResultado[3] = str_replace(",", "", $checkResultado[3]);
                    $comando = "*ET,$imeiPlataforma,FH,$checkResultado[3],$checkResultado[2]#";                
                    echo "<p>Servidor Respondeu :".$comando." --CADASTRAR NUMERO DE MONITORAMENTO SECUNDÁRIO - FH</p>";
                    return $comando;
                }elseif($checkResultado[0] == "descadastrarNumeroSecundario"){// ----DESCADASTRAR NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH
                    $comando = "*ET,$imeiPlataforma,FH,$checkResultado[3],$checkResultado[2]#";                
                    echo "<p>Servidor Respondeu :".$comando." --DESCADASTRAR NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH</p>";
                    return $comando;
                }elseif($checkResultado[0] == "descadastrarTodosNumeroSecundario"){// ----DESCADASTRAR TODOS OS NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH
                    $comando = "*ET,$imeiPlataforma,FH,$checkResultado[2]#";                
                    echo "<p>Servidor Respondeu :".$comando." --DESCADASTRAR TODOS OS NÚMERO DE MONITORAMENTO SECUNDÁRIO - FH</p>";
                    return $comando;
                }elseif($checkResultado[0] == "ativarEconomiaEnergia"){// ----ATIVAR MODO DE ECONOMIA DE ENERGIA - PM
                    $comando = "*ET,$imeiPlataforma,PM,$checkResultado[2]#";                                
                    echo "<p>Servidor Respondeu :".$comando." --ATIVAR MODO DE ECONOMIA DE ENERGIA - PM</p>";
                    return $comando;
                }elseif($checkResultado[0] == "desativarEconomiaEnergia"){// ----DESATIVAR MODO DE ECONOMIA DE ENERGIA - PM
                    $comando = "*ET,$imeiPlataforma,PM,$checkResultado[2]#";                                
                    echo "<p>Servidor Respondeu :".$comando." --DESATIVAR MODO DE ECONOMIA DE ENERGIA - PM</p>";
                    return $comando;
                }elseif($checkResultado[0]=="ativarAntiRoubo") { //--ANTI-ROUBO ATIVADO - FD
                    $comando = "*ET,".$imeiPlataforma.",FD,F1#";                
                    echo "<p>Servidor Respondeu :".$comando." --ANTI-ROUBO ATIVADO</p>";
                    return $comando;
                }elseif($checkResultado[0]=="desativarAntiRoubo") { //--ANTI-ROUBO DESATIVADO - FD
                    $comando = "*ET,".$imeiPlataforma.",FD,F2#";                
                    echo "<p>Servidor Respondeu :".$comando." --ANTI-ROUBO DESATIVADO</p>";
                    return $comando;
                }elseif($checkResultado[0]=="desativaCombustivel") { //--DESATIVA COMBUSTIVEL - FD
                    $comando = "*ET,".$imeiPlataforma.",FD,Y1#";               
                    echo "<p>Servidor Respondeu :".$comando." --DESATIVA COMBUSTIVEL</p>";
                    return $comando;
                }elseif($checkResultado[0]=="ativaCombustivel") { //--ATIVA COMBUSTIVEL - FD
                    $comando = "*ET,".$imeiPlataforma.",FD,Y2#";                
                    echo "<p>Servidor Respondeu :".$comando." --ATIVA COMBUSTIVEL</p>";
                    return $comando;
                }elseif($checkResultado[0]=="ativaAlarmeCombustivel") { //--ATIVA ALARME E COMBUSTIVEL - FD
                    $comando = "*ET,".$imeiPlataforma.",FD,F1Y2#";                
                    echo "<p>Servidor Respondeu :".$comando." --ATIVA ALARME E COMBUSTIVEL</p>";
                    return $comando;
                }elseif($checkResultado[0]=="desativaAlarmeCombustivel") { //--DESATIVA ALARME E COMBUSTIVEL - FD
                    $comando = "*ET,".$imeiPlataforma.",FD,F2Y1#";                
                    echo "<p>Servidor Respondeu :".$comando." --DESATIVA ALARME E COMBUSTIVEL</p>";
                    return $comando;
                }elseif($checkResultado[0]=="desativaAlarmeAtivaCombustivel") { //--DESATIVA ALARME E ATIVA COMBUSTIVEL - FD
                    $comando = "*ET,".$imeiPlataforma.",FD,F2Y2#";                
                    echo "<p>Servidor Respondeu :".$comando." --DESATIVA ALARME E ATIVA COMBUSTIVEL</p>";
                    return $comando;
                }elseif($checkResultado[0]=="ativaAlarmeDesativaCombustivel") { //--ATIVA ALARME E DESATIVA COMBUSTIVEL - FD
                    $comando = "*ET,".$imeiPlataforma.",FD,F1Y1#";                
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
                //echo 'entrou no if para cadastrar';
                $id = cadastrarIMEI($parts_msg[1]);//cadastra IMEI
                if ($id != false) {   //verifica se cadastrou IMEI
//                    echo 'vai salvar localização';
                    if (salvarLocalAtual($parts_msg,$id)) {//salva localização atual do rastreador
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
                $id = dbSelectId("SELECT `id` from configuracaos where `imei` = '$parts_msg[1]' ");
                //echo "<br>id :".$id;
                if($id != false){
                    //echo "<br>Entrou para salvar local atual";
                    if (salvarLocalAtual($parts_msg,$id)) {//atualiza o local atual do rastreador
//                  echo '<br>atualizou local atual...fechamos a conexão com o socket';                                        
                    return "atualizouLocalizacao";
                    }else{
//                  echo '<br>nao atualizou a locallização';
                        return "naoAtualizouLocalizacao";
                    }
                }else{
                    return "<br>não atualizou local atual imei:".$parts_msg[1];
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
    }

    function checkResposta($parts_msg){
        print_r($parts_msg);
        if ($parts_msg[2]=="FD") {//controle de alarme e potencia            
            $resposta = $parts_msg[3];            
            $respostaComustivel = $parts_msg[4] ?? null;
            $selectCmd = dbExecute("SELECT * FROM `cmd_trackers` WHERE `imei`='$parts_msg[1]' AND `tipo` = 'FD' AND `sucesso` = false");
            if ($selectCmd->num_rows > 0 ) {
                $resposta = removeHashtag($resposta);
                $respostaComustivel = removeHashtag($respostaComustivel);                
                $row = $selectCmd->fetch_assoc();
                $cmd = $row;

                echo "----cmd: ".$cmd."<br>--resposta: ".$resposta."<br>---respostaComustivel: ".$respostaComustivel;
                $tratamento = updateCmdTrackers($cmd,$resposta,$respostaComustivel);              
                return $tratamento;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    function updateCmdTrackers($cmd,$resposta,$respostaComustivel){
        $resposta = trim($resposta);
        $respostaComustivel = trim($respostaComustivel);
        $imei = $cmd['imei'];
        $cmdTracker = $cmd['cmd'];
        $cmdTracker = trim($cmdTracker);

        switch ($cmdTracker) {
            case "ativarAntiRoubo":
                if ($resposta=="F10") {
                    $result = dbExecute("UPDATE `cmd_trackers` SET `sucesso`='0', `resposta`='Não foi possível ativar o alarme' WHERE `imei` = '$imei' AND `tipo` = 'FD'");
                    return "NOTativarAntiRoubo";
                }elseif($resposta=="F11"){
                    $result = dbExecute("UPDATE `tracker`.`cmd_trackers` SET `sucesso`='1', `resposta`='Alarme ativado com sucesso' WHERE `imei`='$imei' AND `tipo` = 'FD'");
                    return "ativarAntiRoubo";
                }else{
                    return false;
                }                
            case "desativarAntiRoubo":
                if ($resposta=="F20") {
                    $result = dbExecute("UPDATE `tracker`.`cmd_trackers` SET `sucesso`='0', `resposta`='Não foi possível desativar ativar o alarme' WHERE `imei`='$imei' AND `tipo` = 'FD'");
                    return "NOTdesativarAntiRoubo";
                }elseif($resposta=="F21"){
                    $result = dbExecute("UPDATE `tracker`.`cmd_trackers` SET `sucesso`='1', `resposta`='Alarme desativado com sucesso' WHERE `imei`='$imei' AND `tipo` = 'FD'");
                    return "desativarAntiRoubo";
                }else{
                    return false;
                } 
                
            case "desativaCombustivel":
                if ($resposta=="Y10") {
                    $result = dbExecute("UPDATE `tracker`.`cmd_trackers` SET `sucesso`='0', `resposta`='Não foi possível desativar o combustivel' WHERE `imei`='$imei' AND `tipo` = 'FD'");                    
                    return "NOTdesativaCombustivel";
                }elseif($resposta=="Y11"){
                    $result = dbExecute("UPDATE `tracker`.`cmd_trackers` SET `sucesso`='1', `resposta`='Combustivel desativado com sucesso' WHERE `imei`='$imei' AND `tipo` = 'FD'");
                    $result = dbExecute("UPDATE `tracker`.`configuracaos` SET `cmd_alarme_potencia`='$cmdTracker' WHERE `imei`='$imei'");
                    return "desativaCombustivel";
                }else{
                    return false;
                } 
            case "ativaCombustivel":                
                
                if ($resposta=="Y20") {
                    $result = dbExecute("UPDATE `tracker`.`cmd_trackers` SET `sucesso`='0', `resposta`='Não foi possível ativar o combustivel' WHERE `imei`='$imei' AND `tipo` = 'FD'");
                    return "NOTativaCombustivel";
                }elseif($resposta == "Y21"){
                    $result = dbExecute("UPDATE `tracker`.`cmd_trackers` SET `sucesso`='1', `resposta`='Combustivel ativado com sucesso' WHERE `imei`='$imei' AND `tipo` = 'FD'");
                    $result = dbExecute("UPDATE `tracker`.`configuracaos` SET `cmd_alarme_potencia`='$cmdTracker' WHERE `imei`='$imei'");
                    
                    return "ativaCombustivel";
                }else{
                    echo "<br>retornou false";
                    return false;
                } 
            case "ativaAlarmeCombustivel":
                echo "<br>resposta:_".$resposta."_<br>_respostaCombustive:_".$respostaComustivel."_<br>";
                if (($resposta=="F10") &&($respostaComustivel=="Y20")) {
                    $result = dbExecute("UPDATE `tracker`.`cmd_trackers` SET `sucesso`='0', `resposta`='Não foi possível ativar o alarme e o combustivel' WHERE `imei`='$imei' AND `tipo` = 'FD'");
                    return "NOTativaAlarmeCombustivel";
                }elseif(($resposta=="F11") &&($respostaComustivel=="Y21")){
                    $result = dbExecute("UPDATE `tracker`.`cmd_trackers` SET `sucesso`='1', `resposta`='Alarme e Combustivel ativados com sucesso' WHERE `imei`='$imei' AND `tipo` = 'FD'");
                    return "ativaAlarmeCombustivel";
                }else{
                    return false;
                } 
            case "desativaAlarmeCombustivel":
                if (($resposta=="F20") &&($respostaComustivel=="Y10")) {
                    $result = dbExecute("UPDATE `tracker`.`cmd_trackers` SET `sucesso`='0', `resposta`='Não foi possível desativar o alarme e o combustivel' WHERE `imei`='$imei' AND `tipo` = 'FD'");
                    return "NOTdesativaAlarmeCombustivel";
                }elseif(($resposta=="F21") &&($respostaComustivel=="Y11")){
                   $result = dbExecute("UPDATE `tracker`.`cmd_trackers` SET `sucesso`='1', `resposta`='Alarme e Combustivel desativados com sucesso' WHERE `imei`='$imei' AND `tipo` = 'FD'");
                   return "desativaAlarmeCombustivel";
                }else{
                    return false;
                } 
            case "desativaAlarmeAtivaCombustivel":
                if (($resposta=="F20") &&($respostaComustivel=="Y20")) {
                    $result = dbExecute("UPDATE `tracker`.`cmd_trackers` SET `sucesso`='0', `resposta`='Não foi possível desativa o alarme e ativa o Combustivel' WHERE `imei`='$imei' AND `tipo` = 'FD'");
                    return "NOTdesativaAlarmeAtivaCombustivel";
                }elseif(($resposta=="F21") &&($respostaComustivel=="Y21")){
                   $result = dbExecute("UPDATE `tracker`.`cmd_trackers` SET `sucesso`='1', `resposta`='Desativado alarme e ativado combustivel com sucesso' WHERE `imei`='$imei' AND `tipo` = 'FD'");
                   return "desativaAlarmeAtivaCombustivel";
                }else{
                    return false;
                } 
            case "ativaAlarmeDesativaCombustivel":
                if (($resposta=="F10") &&($respostaComustivel=="Y10")) {
                    $result = dbExecute("UPDATE `tracker`.`cmd_trackers` SET `sucesso`='0', `resposta`='Não foi possível ativar o alarme e desativar o combustivel' WHERE `imei`='$imei' AND `tipo` = 'FD'");
                    return "NOTativaAlarmeDesativaCombustivel";
                }elseif(($resposta=="F11") &&($respostaComustivel=="Y11")){
                    $result = dbExecute("UPDATE `tracker`.`cmd_trackers` SET `sucesso`='1', `resposta`='Ativado alarme e desativado o Combustivel com sucesso' WHERE `imei`='$imei' AND `tipo` = 'FD'");
                    return "ativaAlarmeDesativaCombustivel";
                }else{
                    return false;
                } 
            default:
                return "Comandos não interpretados";
        }
    }

    ?>
    