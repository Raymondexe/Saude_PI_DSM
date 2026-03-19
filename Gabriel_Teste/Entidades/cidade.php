<?php

class Cidade{

    private $id;
    private $estadoId;
    private $nome;

    public function __construct($estadoId, $nome){
        $this->estadoId = $estadoId;
        $this->nome = $nome;
    } 


    public getId(){
        return $this->id;
    }

    public getEstadoId(){
        return $this->estadoId;
    }

    public setEstadoId($estadoId){
        $this->estadoId = $estadoId;
    }

    public getNome(){
        return $this->nome;
    }

    public setEstadoId($nome){
        $this->nome = $nome;
    }
}



?>