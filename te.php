
    <html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <pre>
        <?php
        require_once("C:/xampp/htdocs/ET-01/dao/Connection.php");
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$result = dbExecute("SELECT  `COMANDO` FROM  `COMANDOS_RASTREADOR` WHERE  `IMEI` =  '358100000000000' and `EXECUTADO` = 'false'");
print_r($result);

if($result->num_rows >0){
    $result->data_seek(0);    
    while($row = $result->fetch_assoc()){
        
        $COMANDO = $row['COMANDO'];
        echo "$COMANDO";
    }   
}else{
    echo"<br>nao tem<br>";
}
       
    ?>
        
        
        </pre>   
    </body>
</html>