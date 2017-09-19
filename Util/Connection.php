<?php


require_once("C:/xampp/htdocs/ET-01/Util/Config.php");


//fecha conexao com MySqli

function dbClose($link){
    mysqli_close($link)or die($link);
    
}


//abre conexão com Mysqli
function dbConnect(){
//    $link = mysqli_connect("localhost","root","","rastreador")or die(mysqli_connect_error());
    $link = mysqli_connect(DB_HOSTNAME,DB_USERNAME,DB_PASSWORD,DB_DATABASE)or die(mysqli_connect_error());
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
