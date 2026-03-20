<?php 
    //PDO 
    
    //em teoria se conectaria com qualquer banco local de máquina


    $pdo = null;

    try {
        
        global $pdo = new PDO("mysql:host=localhost;
                    dbname = bem_estar;", 'username', 'password');
        global $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
    
    }catch(PDOException &e){
        echo 'ERRO: '. $e ->getMessage();
    }

    
    #$pdo = null

?>