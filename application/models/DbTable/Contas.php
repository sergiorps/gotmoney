<?php

/**
 * Definition for table: contas
 *
 * @author Mauricio Lauffer 
 */
class Application_Model_DbTable_Contas extends Zend_Db_Table_Abstract
{
    //Define dados da tabela
    //protected $_schema = '';
    protected $_name = 'contas'; //Nome da tabela
    protected $_primary = 'idconta'; //Chave primária da tabela
    //protected $_primary = array('iduser', 'idconta'); //Chave primária da tabela
} //end CLASS
