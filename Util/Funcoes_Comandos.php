<?php
    require_once("C:/xampp/htdocs/ET-01/dao/Connection.php");
    
    function checkAntiRouboPower($imei) {
        $result = dbExecute("SELECT  `COMANDOS_ALARME_POTENCIA` FROM  `config_et` WHERE  `IMEI` =  '$imei' and `COMANDOS_ALARME_POTENCIA` = 'F1Y1'");
        print_r($result);
        if ($result->num_rows == 1) {
            return "ativarAntiRoubo";
        }else{            
            return false;
        }
    }
    
    function selectIMEI($imei) {
        $query = "SELECT `IMEI` FROM `config_et` WHERE `IMEI` = '$imei'";
        if ($query!=0){
            return true;
        }else{            
            return false;
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

    function updateNumeroMaster($parts_msg) {
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
    
    
    function lerMensagem($msg){
    //*ET,SN,DW,A/V,YYMMDD,HHMMSS,Latitude,Longitude,Speed,Course,Status,Signal,Power,oil,LC#   
    
    $parts_msg = explode(',',$msg);
    $tot_parts = count($parts_msg);
    echo "<p>_Total de partes $tot_parts <-- </p>";
    
    $comando = checkAntiRouboPower($parts_msg[1]);
    echo "<br>daas=$comando";
    if($comando=="ativarAntiRoubo") {
        return "ativarAntiRoubo"; // ativar anti-roubo
    }
    if($tot_parts==16){
        //está mandando sua localização
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
                return "dbConnect() falhou";
            }
        }elseif($parts_msg[2]=="RG") {
            $resultado_query = selectIMEI($parts_msg[1]);           
            if ($resultado_query === false) {//verifica se o IMEI está cadastrado
//                echo 'entrou no if para cadastrar';
                $cadastrou = cadastrarIMEI($parts_msg[1]);//cadastra IMEI
                if ($cadastrou === true) {   //verifica se cadastrou IMEI
                    echo "cadastrou";                    
                    $result = updateNumeroMaster($part);
                    if ($result==true) {
                        return "atualizouNumeroPrincipal";
                    }else{
                        return "naoAtualizouNumeroPrincipal";
                    }
                                       
                }else{
                    return "naoCadastrou/naoRegistrouNumeroPrincipal";
                }
            }else{
                dbConnectClose();
                $result = updateNumeroMaster($part);
                if ($result==true) {
                    return "atualizouNumeroPrincipal";
                }else{
                    return "naoAtualizouNumeroPrincipal";
                }
            }       
        }elseif ($parts_msg[2]=="MQ#") {
            echo "<br>elseif ($parts_msg[2]==MQ<br>";
            return "MQ";
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
    
    