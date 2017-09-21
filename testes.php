<pre>
<?php
require_once("C:/xampp/htdocs/ET-01/Util/Connection.php");
require_once("C:/xampp/htdocs/ET-01/server.php");
/*$ET;
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
    $Power;
    $oil;
    $LC;


$string = "*ET,358155100088765,HB,A,11090f,103307,80e9391f,81c2dce1,002f,0000,00400000,14,52,0000,97,879#";
$chunks = spliti (",", $string);
print_r($chunks);
$dados = array('nome'=>"lucas 'Pires'",'idade'=>"18");
print_r($dados);


$string = "*ET,SN,DW,A/V,YYMMDD,HHMMSS,Latitude,Longitude,Speed,Course,Status,Signal,Power,oil,LC#";
$chunks = spliti (",", $string);
print_r($chunks);
$socket = fsockopen('udp://pool.ntp.br', 123, $err_no, $err_str, 1);
if ($socket)
{
    if (fwrite($socket, chr(bindec('00'.sprintf('%03d', decbin(3)).'011')).str_repeat(chr(0x0), 39).pack('N', time()).pack("N", 0)))
    {
        stream_set_timeout($socket, 1);
        $unpack0 = unpack("N12", fread($socket, 48));
        echo date('Y-m-d H:i:s', $unpack0[7]);
    }

    fclose($socket);
}
echo"-----<br>";
$msg = "*ET,358155100088765,HB,A,11090f,103307,80e9391f,81c2dce1,002f,0000,00400000,14,52,0000,97,879#";
//*ET,SN,DW,A/V,YYMMDD,HHMMSS,Latitude,Longitude,Speed,Course,Status,Signal,Power,oil,LC#    
    $array = spliti (",", $msg);
    $resultado = array();
    //$tamanhoArray = count($array);
    foreach ($array as  $value) {
        $ultimaPosicaoValue = strlen($value);        
        $ultimaPosicaoArray = count($array);
        //echo "<p>$ultimaPosicaoValue---</p>";
        if($value[0]=="*"){
            $resultado[0]= substr($value, 1);
        }elseif($value[$ultimaPosicaoValue-1]=="#"){
            
            $resultado[$ultimaPosicaoArray] = substr($value, 0, $ultimaPosicaoValue-1);
        }else{
            $resultado[]= $value;
        }            
    }
    print_r($resultado);
    
    $juncao = array();
    #texto;
    for($i=0;$i< count($chunks);$i++){
        
    }
    $mystring = 'abc';
$findme   = 'a';
$pos = strpos($mystring, $findme);

// Note o uso de ===.  Simples == não funcionaria como esperado
// por causa da posição de 'a' é 0 (primeiro) caractere.
if ($pos === false) {
    echo "A string '$findme' não foi encontrada na string '$mystring'";
} else {
    echo "A string '$findme' foi encontrada na string '$mystring'";
    echo " e existe na posição $pos";
}
 GLOBAL $akiiiiiiiiii;
 echo "<br><br><br><br>$akiiiiiiiiii"."hdfsdhfsdkfjhdskjf";
    */
echo "começando";
    $result = dbExecute("SELECT  `COMANDO` FROM  `COMANDOS_RASTREADOR` WHERE  `IMEI` =  '$imei' and `EXECUTADO` = 'false'");
    if($result->num_rows >0){
        $result->data_seek(0);
        echo "<table><tr><th>Comando</tr></th>";
        while($row = $result->fetch_assoc()){
            echo "<tr><td>".$row['comando']."</td></td>";
        }
        echo "</table>";
    }else{
        echo"nenhuma linha selecionada";
    }
//$msg = "*ET,135790246811221,RG,13691779574#";
//$parts = explode(",", $msg);
//print_r($parts);
//$parts[3] = "trocou";
//print_r($parts);
//$implode = implode(",", $parts);
//$implode .="#";
//print_r($implode);




?>
</pre>