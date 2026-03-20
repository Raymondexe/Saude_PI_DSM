<?php

class usuario {
    private $id;
    private $nome;
    private $dataNascimento;
    private $endereco;
    private $email;
    private $dataCadastro;

    public function __construct($nome, $dataNascimento, $endereco, $email, $dataCadastro){
        $this->nome = $nome;
        $this->dataNascimento = $dataNascimento;
        $this->endero = $endero;
        $this->email = $email;
        $this->dataCadastro = $dataCadastro;
        
    }

    public function getId(){
        return $this->id;
    }

    public function getNome(){
        return $this->nome;
    }

    public function setNome($nome){
        $this->nome = $nome;
    }


    public function getDataNascimento(){
        return $this->dataNascimento;
    }

    public function setDataNascimento($dataNascimento){
        $this->dataNascimento = $dataNascimento;
    }
    

    public function getEndereco(){
        return $this->Endereco;
    }

    public function setEndereco($endereco){
        $this->endereco = $endereco;
    }

    public function getEmail(){
        return $this->email;
    }

    public function setEmail($email){
        $this->email = $email;
    }

    public function getDataCadastro(){
        return $this->dataCadastro;
    }

    public function setDataCadastro($dataCadastro){
        $this->dataCadastro = $dataCadastro;
    }

}



?>