<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body><pre>
        <?php
            require "Util/Config.php";
            require_once("C:/xampp/htdocs/ET/Util/Connection.php");
            require_once("C:/xampp/htdocs/ET/Util/Database.php");
                
            $link = dbConnect();
            //dbClose($link);
//            $nome = "Well";
//            $dados = array('nome'=>"lucas 'Pires'",'idade'=>"18");
//            $query = "INSERT INTO CLIENTE (nome,idade)VALUES('Lucas Pires',18)";
//            //var_dump(dbExecute($query));
             
            print_r($link);
            //phpinfo();
        ?>
        </pre></body>
</html>
