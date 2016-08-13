<?php

/**
 * Model table categorias
 *
 * @author Mauricio Lauffer 
 */
class Application_Model_Categorias extends Application_Model_TableAbstract
{

    //Define campos da tabela
    protected $idcategoria;
    protected $iduser;
    protected $descricao;

    //Conjunto de SETs
    public function setIdcategoria($idcategoria)
    {
        $this->idcategoria = $idcategoria;
    } //end FUNCTION

    public function setIduser($iduser)
    {
        $this->iduser = $iduser;
    } //end FUNCTION

    public function setDescricao($descricao)
    {
        $this->descricao = $this->encodeString($descricao);
    } //end FUNCTION


    //Conjunto de GETs
    public function getIdcategoria()
    {
        return $this->idcategoria;
    } //end FUNCTION

    public function getIduser()
    {
        return $this->iduser;
    } //end FUNCTION

    public function getDescricao()
    {
        return $this->decodeString($this->descricao);
    } //end FUNCTION
}
