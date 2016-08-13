<?php

/**
 * Definition for table: categorias
 *
 * @author Mauricio Lauffer 
 */
class Application_Model_DbTable_Categorias extends Zend_Db_Table_Abstract
{
    //Define dados da tabela
    //protected $_schema = '';
    protected $_name = 'categorias'; //Nome da tabela
    protected $_primary = 'idcategoria'; //Chave primária da tabela
    //protected $_primary = array('iduser', 'idcategoria'); //Chave primária da tabela
} //end CLASS
