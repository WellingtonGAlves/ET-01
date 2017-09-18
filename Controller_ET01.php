<?php

    require_once("Connection.php");
    //*ET,SN,DW,A/V,YYMMDD,HHMMSS,Latitude,Longitude,Speed,Course,Status,Signal,Power,oil,LC#
    $ET;
    $SN;
    $DW;    
    $ValidadeDeDados;
    $YYMMDD;
    $HHMMSS;
    $Latitude;
    $Longitude;
    $Speed;
    $Course;
    $Status;
    $Signal;
    $power;
    $oil;
    $lc;
    $altitude;


    error_reporting(0);
    set_time_limit(0);
    $host = "192.168.25.216";
    $port = 54321;
    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create
    socket<br>");
    $result_bind = socket_bind($socket,$host,54321) or die("Could not bind to  socket<br>");
    $result_listen = socket_listen($socket) or die("Could not set up socket
    listener<br>");
    echo "Waiting for connections... <br>";
    $cont = 0;
    $qnt = 0;

    while(1)
    {
    $cont = $cont +1;
    $spawn[++$i] = socket_accept($socket) or die("Could not accept incoming
    connection <br>");
    echo "| _".$cont."_ | <br>";
    $socket_read = socket_read($spawn[$i],1024);
    $msg_rastreador = $socket_read;

    echo "<p>Rastreador Enviou : ".$msg_rastreador."</p>";
    echo "<p>Rastreador Enviou : ".$spawn."</p>";
    
    lerMensagem($msg_rastreador);

    socket_close($spawn[$i]);
    if($cont == 10){
            exit;
    }

    }
    socket_close($socket);
    ?>		

<?php 

//
//*ET,358155100088765,HB,A,11090f,0c0933,80e9393a,81c2dd2e,006c,0000,40c00000,18,100,0000,96,903# 
function lerMensagem($msg){
    //*ET,SN,DW,A/V,YYMMDD,HHMMSS,Latitude,Longitude,Speed,Course,Status,Signal,Power,oil,LC#    
    $parts_msg = explode(',',$msg);
    $tot_parts = count($parts_msg);
    if($tot_parts==16){
        //está mandando sua localização
        $link = dbConnect();
        if($link != null){
            $query = "SELECT `IMEI` FROM `config_et` WHERE `IMEI` = 'DFSFSD'";
            if ($query == 0) {
                
            }
        }else{
            return null;
        }
    }
    
}


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
?>


