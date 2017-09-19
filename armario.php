<?php
//$ET;
//    $SN;
//    $DW;    
//    $ValidadeDeDados;
//    $YYMMDD;
//    $HHMMSS;
//    $Latitude;
//    $Longitude;
//    $Speed;
//    $Course;
//    $Status;
//    $Signal;
//    $power;
//    $oil;
//    $lc;
//    $altitude;

//foreach ($parts_msg as  $value) {
//        $ultimaPosicaoValue = count($value);
//        $ultimaPosicaoArray = count($parts_msg);
//        if($value[0]=="*"){
//            $array[0]= substr($value, 1);
//        }elseif($ultimaPosicaoValue=="#"){
//            
//            $array[$ultimaPosicaoArray] = substr($value, 0, $ultimaPosicaoValue-1);
//        }else{
//            $array[]= $value;
//        }            
//    }

//------------------------------cadastra/atualiza/le  a mensagem
//$msg = "*ET,358155100088765,HB,A,11090f,0c0933,80e9393a,81c2dd2e,006c,0000,40c00000,18,100,0000,96,903#";
//lerMensagem($msg);
//function lerMensagem($msg){
//    //*ET,SN,DW,A/V,YYMMDD,HHMMSS,Latitude,Longitude,Speed,Course,Status,Signal,Power,oil,LC#    
//    $parts_msg = explode(',',$msg);
//    $tot_parts = count($parts_msg);
//    if($tot_parts==16){
//        //está mandando sua localização
//        $link = dbConnect();
//        if($link != null){
//            dbClose($link);
//            $query = "SELECT `IMEI` FROM `config_et` WHERE `IMEI` = $parts_msg[1]";            
//            $resultado_query = dbExecute($query);
//            
//            //print_r("<br>".$parts_msg[6]."<br>");
//           
//            
//            if (mysqli_num_rows($resultado_query)==0) {
//                echo 'entrou no if para cadastrar';
//                $cadastrou = cadastrarIMEI($parts_msg[1]);
//                if ($cadastrou === true) {   
//                    echo 'vai salvar localização';
//                    if (salvarLocalAtual($parts_msg)) {
//                        echo 'salvou local atual...fechamos a conexão com o socket';
//                    }else{
//                        echo 'nao salvou a locallização';
//                    }
//                }else{
//                    
//                }
//            }else{                
//                echo '<br>vai atualizar localização do dispositivo existente<br>';
//                    if (atualizarLocalAtual($parts_msg)) {
//                        echo '<br>atualizou local atual...fechamos a conexão com o socket';
//                    }else{
//                        echo '<br>nao atualizou a locallização';
//                    }
//                
//            }
//            
//        }else{
//            return null;
//        }
//    }
//    
//}
//
//    function cadastrarIMEI($imei) {
//        echo '<br>entrou para cadastrar';
//        //cadastrando com informações default
//        $query_insert = dbExecute("INSERT INTO `config_et`(`IMEI`,`TOLERANCIA`, `RASTREAMENTO_AO_VIVO`, `INTERVALO`, `CONTAGEM`, `SERVER_IP`, `UDP_PORTA`, `CORRE`, `CONTROLE_ALARME_POTENCIA`, `COMANDOS_ALARME_POTENCIA`, `CERCA`, `LIMITE_VELOCIDADE`, `SAIR_SUSPENDER`, `ENTRAR_SUSPENDER`, `SENSIBILIDADE`, `INTERVALO_TRANS_DIRIGINDO`, `INTERVALO_TRANS_PARADO`, `MODO_ECONOMICO`, `NUMERO_PRINCIPAL`, `FUSO_HORARIO`, `LINGUAGEM`, `SMS`, `RESTART`, `STATUS_DISPOSITIVO`, `ATIVO`) "
//                . "VALUES ($imei,null,'GZ','0005','0001','179.184.59.144','D431','1',TRUE,'F1Y2',FALSE,NULL,'00F0','00F0','00','05','0A','01',NULL,'3.0','01','01',FALSE,'Aguardando resposta',true)");
//        
//        print_r("_".$query_insert."_");
//        if(($query_insert)==1){
//            echo "<br>Cadastrou";
//            return true;
//        }else{
//            echo "falha ao cadastrar";            
//            false;
//        }
//    }
//    function salvarLocalAtual($msg){
//        
//        $posicaoUltimoCaracter = strlen ($msg[15]);        
//        if(explode("#", $msg[15])){
//            
//            $msg[15] = substr($msg[15], 0, $posicaoUltimoCaracter-1);
//            print_r($msg[15]);
//        }
//        if(dbExecute("INSERT INTO `local_atual_et`( `IMEI`, `COMANDO`, `VALIDADE_DADOS`, `YYMMDD`, `HHMMSS`, `LATITUDE`, `LONGITUDE`, `VELOCIDADE`, `CURSO`, `STATUS`, `SINAL`, `POWER`, `OLEO`, `ALTITUDE`, `QUILOMETRAGEM`) "
//                . "VALUES ('$msg[1]','$msg[2]','$msg[3]','$msg[4]','$msg[5]','$msg[6]','$msg[7]','$msg[8]','$msg[9]','$msg[10]','$msg[11]','$msg[12]','$msg[13]','$msg[14]','$msg[15]')")==1){
//            return true;//cadastrou
//        }else{
//            return false;//nao cadastrou
//        }
//    }
//    function atualizarLocalAtual($msg){
//        $posicaoUltimoCaracter = strlen ($msg[15]);        
//        if(explode("#", $msg[15])){
//            
//            $msg[15] = substr($msg[15], 0, $posicaoUltimoCaracter-1);
//            print_r($msg[15]);
//        }
//        if (dbExecute("UPDATE `local_atual_et` SET `COMANDO`='$msg[2]',`VALIDADE_DADOS`='$msg[3]',`YYMMDD`='$msg[4]',`HHMMSS`='$msg[5]',`LATITUDE`='$msg[6]',`LONGITUDE`='$msg[7]',`VELOCIDADE`='$msg[8]',`CURSO`='$msg[9]',`STATUS`='$msg[10]',`SINAL`='$msg[11]',`POWER`='$msg[12]',`OLEO`='$msg[13]',`ALTITUDE`='$msg[14]',`QUILOMETRAGEM`='$msg[15]' WHERE IMEI = '$msg[1]'")) {
//            echo '<br>atualizou';        
//            return true;//atualizou
//        }else{
//            echo '<br>não atualizou';   
//            return false;//nao atualizou
//        }
//    }
//------------------------------------------fim cadastra/atualiza/le  a mensagem-------------------------------