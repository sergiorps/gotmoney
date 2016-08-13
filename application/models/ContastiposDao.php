<?php

class Application_Model_ContastiposDao
{

    protected $_dbTable;

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            $logger = Zend_Registry::get('logger');
            $logger->err('Invalid table data gateway provided');
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    } //end FUNCTION



    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_Contastipos');
        }
        return $this->_dbTable;
    } //end FUNCTION


    public function find($idtipo)
    {
        $result = 0;
        try {
            $result = $this->getDbTable()->find(array('idtipo = ?' => $idtipo));
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
        if (count($result) === 0) {
            return null;
        }
        return $result;
    } //end FUNCTION


    public function fetchAll()
    {
        try {
            $select = $this->getDbTable()->select()->order('descricao')->where('inativo = ?', 0);
            $resultSet = $this->getDbTable()->fetchAll($select);
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
        return $resultSet;
    } //end FUNCTION

} //end CLASS
