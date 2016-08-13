<?php

/**
 * Model table lancamentos
 *
 * @author Mauricio Lauffer 
 */
class Application_Model_Lancamentos extends Application_Model_TableAbstract
{

    //Define campos da tabela
    protected $idlancamento;
    protected $iduser;
    protected $idlancamentopai;
    protected $idconta;
    protected $idstatus;
    protected $descricao;
    protected $parcela;
    protected $valor;
    protected $tipo;
    protected $datalancamento;
    protected $datavencimento;
    protected $tag;
    protected $origem;
    protected $lastchange;


    //Conjunto de SETs
    public function setIdlancamento($idlancamento)
    {
        $this->idlancamento = $idlancamento;
    } //end FUNCTION

    public function setIduser($iduser)
    {
        $this->iduser = $iduser;
    } //end FUNCTION

    public function setIdlancamentopai($idlancamento)
    {
        $this->idlancamentopai = $idlancamento;
    } //end FUNCTION

    public function setIdconta($idconta)
    {
        $this->idconta = $idconta;
    } //end FUNCTION

    public function setIdstatus($idstatus)
    {
        $this->idstatus = $idstatus;
    } //end FUNCTION

    public function setDescricao($descricao)
    {
        $this->descricao = $this->encodeString($descricao);
    } //end FUNCTION

    public function setParcela($parcela)
    {
        $this->parcela = $parcela;
    } //end FUNCTION

    public function setValor($valor)
    {
        $this->valor = $valor;
    } //end FUNCTION

    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    } //end FUNCTION

    public function setDatavencimento($datavencimento)
    {
        //MySQL Date Format
        $date = new Zend_Date($datavencimento, Zend_Date::ISO_8601);
        $this->datavencimento = (empty($date)) ? new Zend_Db_Expr('NULL') : $date->get("YYYY-MM-dd");
    } //end FUNCTION

    public function setDatalancamento($datalancamento)
    {
        //MySQL Date Format
        $date = (empty($datalancamento)) ? '' : new Zend_Date($datalancamento, Zend_Date::ISO_8601);
        $this->datalancamento = (empty($date)) ? new Zend_Db_Expr('NULL') : $date->get("YYYY-MM-dd");
    } //end FUNCTION

    public function setTag($tag)
    {
        $this->tag = $tag;
    } //end FUNCTION

    public function setOrigem($origem)
    {
        $this->origem = $origem;
    } //end FUNCTION

    public function setLastchange($lastchange)
    {
        $this->lastchange = $lastchange;
    } //end FUNCTION


    //Conjunto de GETs
    public function getIdlancamento()
    {
        return $this->idlancamento;
    } //end FUNCTION

    public function getIduser()
    {
        return $this->iduser;
    } //end FUNCTION

    public function getIdlancamentopai()
    {
        return $this->idlancamentopai;
    } //end FUNCTION

    public function getIdconta()
    {
        return $this->idconta;
    } //end FUNCTION

    public function getIdstatus()
    {
        return $this->idstatus;
    } //end FUNCTION

    public function getDescricao()
    {
        return $this->decodeString($this->descricao);
    } //end FUNCTION

    public function getParcela()
    {
        return $this->parcela;
    } //end FUNCTION

    public function getValor()
    {
        return $this->valor;
    } //end FUNCTION

    public function getTipo()
    {
        return $this->tipo;
    } //end FUNCTION

    public function getDatavencimento()
    {
        return $this->datavencimento;
    } //end FUNCTION

    public function getDatalancamento()
    {
        //MySQL Date Format
        $date = (empty($this->datalancamento) || $this->datalancamento == 'NULL') ? '' : new Zend_Date($this->datalancamento);
        //echo '\\ getDatalancamento() --';print_r($date);exit;
        //return (empty($date)) ? '' : $date->get('dd/MM/YYYY');
        return $this->datalancamento;
    } //end FUNCTION

    public function getTag()
    {
        /*$tagsWithHash = str_replace(', ', '', $this->tag);
        $tags = explode('#', $tagsWithHash);
        return $tags;*/
        return $this->tag;
    } //end FUNCTION

    public function getOrigem()
    {
        return $this->origem;
    } //end FUNCTION

    public function getLastchange()
    {
        return $this->lastchange;
    } //end FUNCTION
} //end CLASS
