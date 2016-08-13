<?php

/**
 * Model table contas
 *
 * @author Mauricio Lauffer 
 */
class Application_Model_Contas extends Application_Model_TableAbstract
{

    //Define campos da tabela
    protected $idconta;
    protected $iduser;
    protected $idtipo;
    protected $descricao;
    protected $limitecredito;
    protected $saldo;
    protected $dataabertura;
    protected $diafatura;
    protected $lastchange;

    //Conjunto de SETs
    public function setIdconta($idconta)
    {
        $this->idconta = $idconta;
    } //end FUNCTION

    public function setIduser($iduser)
    {
        $this->iduser = $iduser;
    } //end FUNCTION

    public function setIdtipo($idtipo)
    {
        $this->idtipo = $idtipo;
    } //end FUNCTION

    public function setDescricao($descricao)
    {
        $this->descricao = $this->encodeString($descricao);
    } //end FUNCTION

    public function setLimitecredito($limitecredito)
    {
        $this->limitecredito = $limitecredito;
    } //end FUNCTION

    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;
    } //end FUNCTION

    public function setDataabertura($dataabertura)
    {
        //MySQL Date Format
        $date = new Zend_Date($dataabertura, Zend_Date::ISO_8601);
        $this->dataabertura = (empty($date)) ? new Zend_Db_Expr('NULL') : $date->get("YYYY-MM-dd");
    } //end FUNCTION

    public function setDiafatura($diafatura)
    {
        $this->diafatura = $diafatura;
    } //end FUNCTION


    public function getIduser()
    {
        return $this->iduser;
    } //end FUNCTION

    public function getIdconta()
    {
        return $this->idconta;
    } //end FUNCTION

    public function getIdtipo()
    {
        return $this->idtipo;
    } //end FUNCTION

    public function getDescricao()
    {
        return $this->decodeString($this->descricao);
    } //end FUNCTION

    public function getLimitecredito()
    {
        return $this->limitecredito;
    } //end FUNCTION

    public function getSaldo()
    {
        return $this->saldo;
    } //end FUNCTION

    public function getDataabertura()
    {
        return $this->dataabertura;
    } //end FUNCTION

    public function getDiafatura()
    {
        return $this->diafatura;
    } //end FUNCTION

    public function setLastchange()
    {
        //$this->$lastchange;
    } //end FUNCTION
    public function getLastchange()
    {
        return $this->lastchange;
    } //end FUNCTION
} //end CLASS
