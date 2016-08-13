<?php

/**
 * Model table consumidores
 *
 * @author Mauricio Lauffer 
 */
class Application_Model_Consumidores extends Application_Model_TableAbstract
{

    //Define campos da tabela
    protected $iduser;
    protected $nome;
    protected $sexo;
    protected $datanascimento;
    protected $email;
    protected $passwd;
    protected $datacriacao;
    protected $alert;
    //protected $active;
    protected $facebook;
    protected $google;
    protected $twitter;


    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function setEmail($email)
    {
        $this->email = $this->encodeString($email);
    } //end FUNCTION

    //Conjunto de SETs
    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function setIduser($iduser)
    {
        $this->iduser = $iduser;
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function setNome($nome)
    {
        $this->nome = $this->encodeString($nome);
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function setDatanascimento($datanascimento)
    {
        //MySQL Date Format
        $date = new Zend_Date($datanascimento, Zend_Date::ISO_8601);
        $this->datanascimento = (empty($date)) ? new Zend_Db_Expr('NULL') : $date->get("YYYY-MM-dd");
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function setPasswd($passwd)
    {
        if (empty($passwd)) {
            $passwd = sha1(date() . uniqid(mt_rand(), true));
        }

        $options = array('cost' => 10);
        $this->passwd = password_hash($passwd, PASSWORD_BCRYPT, $options);
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function setDatacriacao($datacriacao)
    {
        //MySQL Date Format
        $date = new Zend_Date($datacriacao, Zend_Date::ISO_8601);
        $this->datacriacao = $date->get("YYYY-MM-dd");
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function setAlert($alert)
    {
        $this->alert = ($alert === true) ? 1 : 0;
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function setGoogle($google)
    {
        $this->google = $google;
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    /*public function getActive( $active )
    {
    $this->active = $active;
    }//end FUNCTION*/


    //Conjunto de GETs
    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function getIduser()
    {
        return $this->iduser;
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function getNome()
    {
        return $this->decodeString($this->nome);
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function getSexo()
    {
        return $this->sexo;
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function getDatanascimento()
    {
        //MySQL Date Format
        $date = new Zend_Date($this->datanascimento);
        return $this->datanascimento;
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function getEmail()
    {
        return $this->decodeString($this->email);
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function getPasswd()
    {
        return $this->passwd;
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function getDatacriacao()
    {
        //MySQL Date Format
        $date = new Zend_Date($this->datacriacao);
        return $this->datacriacao;
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function getAlert()
    {
        return $this->alert;
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function getActve()
    {
        return $this->active;
    } //end FUNCTION

    /** 
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function getFacebook()
    {
        return $this->facebook;
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function getGoogle()
    {
        return $this->google;
    } //end FUNCTION

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function getTwitter()
    {
        return $this->twitter;
    } //end FUNCTION
} //end CLASS
