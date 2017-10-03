<?php


require_once("C:/xampp/htdocs/Master-yi/Util/Config.php");


//fecha conexao com MySqli

function dbClose($link){
    mysqli_close($link)or die($link);    
}
//abre e fecha conexao
function dbConnectClose(){
    $link = dbConnect();
    mysqli_close($link)or die($link);    
}

//abre conexão com Mysqli
function dbConnect(){

    $link = mysqli_connect('localhost','root','root','tracker')or die(mysqli_connect_error());
    //mysqli_set_charset($link, DB_CHARSET) or die(mysqli_error($link));
    //print_r($link);
    if ($link==null) {
        return null;
    }else{
        return $link;    
    }
}

function dbEscape($dados){
    $link = dbConnect();
    
    if(!is_array($dados)){
        $dados = mysqli_real_escape_string($link,$dados);
    }else{
        $arr = $dados;
    }
    
    foreach ($arr as $key => $value) {
        $key = mysqli_real_escape_string($link,$key);
        $value = mysqli_real_escape_string($link,$value);
        $dados [$key] = $value;
        
    }
    
    dbClose($link);
    return $dados;
}

//executa Query
function dbExecute($query){
    $link = dbConnect();
    $result = mysqli_query($link, $query)or die(mysqli_error($link));
      
    dbClose($link);
    return $result;
}

function dbInsert($query){
    $link = dbConnect();
    $result = mysqli_query($link, $query)or die(mysqli_error($link));
    if($result==1){
        $id = mysqli_insert_id($link);
         dbClose($link);
         //echo "<br>cadastrou o id ".$id;
        return $id;
    }else{
         dbClose($link);
         return false;
    }   
   
    
}
function dbSelectId($query){
    $link = dbConnect();
    $result = mysqli_query($link, $query)or die(mysqli_error($link));
    //print_r($result);
    if($result->num_rows>0){
        $row = $result->fetch_assoc();
        $id = $row['id'];
         dbClose($link);
        return $id;
    }else{
         dbClose($link);
         return false;
    }
}
function insertMsgTracker($query){
    $link = dbConnect();
    mysqli_query($link, $query)or die(mysqli_error($link));
    dbClose($link);
    
}
function selectComando($query){
    $link = dbConnect();
    $result = mysqli_query($link, $query)or die(mysqli_error($link));
    dbClose($link);
    return $result;
    
}

