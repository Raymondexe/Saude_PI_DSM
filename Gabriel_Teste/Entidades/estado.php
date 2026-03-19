<?php

class Estado {
    private $id;
    private $nome;
    private $sigla;

    public function __construct($nome, $sigla){
        $this->nome = $nome;
        $this->sigla = $sigla;
    } 


    public getId(){
        return $this->id;
    }

    public getNome(){
        return $this->nome;
    }

    public setEstadoId($nome){
        $this->nome = $nome;
    }

    public getSigla(){
        return $this->sigla;
    }

    public setEstadoId($sigla){
        $this->sigla = $sigla;
    }
}

    

?>