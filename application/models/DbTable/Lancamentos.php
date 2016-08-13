<?php

/**
 * Definition for table: lancamentos
 *
 * @author Mauricio Lauffer 
 */
class Application_Model_DbTable_Lancamentos extends Zend_Db_Table_Abstract
{
    //Define dados da tabela
    //protected $_schema = '';
    protected $_name = 'lancamentos'; //Nome da tabela
    protected $_primary = array('iduser', 'idlancamento'); //Chave primária da tabela
} //end CLASS
