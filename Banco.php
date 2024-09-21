<?php
    $bdHost = 'localhost';
    $bdUsername = 'root';
    $bdPassword = 'root';
    $bdName = 'bd_clinica';

    $conexao = new mysqli($bdHost,$bdUsername,$bdPassword,$bdName);
    
    //if($conexao->connect_errno){
    //    echo "Error connecting to";
    //}
    //else{
    //    echo "connected successfully";
    //}
?>