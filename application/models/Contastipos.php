<?php

/**
 * Model table contastipos
 *
 * @author Mauricio Lauffer 
 */
class Application_Model_Contastipos extends Application_Model_TableAbstract
{

    //Define campos da tabela
    protected $idtipo;
    protected $descricao;
    protected $icon;
    protected $inativo;


    //Conjunto de SETs
    public function setIdtipo($idtipo)
    {
        $this->idtipo = $idtipo;
    } //end FUNCTION

    public function setDescricao($descricao)
    {
        $this->descricao = $this->encodeString($descricao);
    } //end FUNCTION

    public function setIcon($icon)
    {
        $this->icon = $icon;
    } //end FUNCTION

    public function setInativo($inativo)
    {
        $this->inativo = $inativo;
    } //end FUNCTION


    public function getIdtipo()
    {
        return $this->idtipo;
    } //end FUNCTION

    public function getDescricao()
    {
        return $this->decodeString($this->descricao);
    } //end FUNCTION

    public function getIcon()
    {
        return $this->icon;
    } //end FUNCTION

    public function getInativo()
    {
        return $this->inativo;
    } //end FUNCTION
} //end CLASS
