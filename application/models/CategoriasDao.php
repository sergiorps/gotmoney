<?php

class Application_Model_CategoriasDao
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
            $this->setDbTable('Application_Model_DbTable_Categorias');
        }
        return $this->_dbTable;
    } //end FUNCTION



    public function insert(Application_Model_Categorias $categoriasModel)
    {
        $categoria = $categoriasModel->toArray();
        $result = 0;
        try {
            $result = $this->getDbTable()->insert($categoria);
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
        if ($result === 0) {
            $logger = Zend_Registry::get('logger');
            $logger->err('Error on INSERT into DB!');
        	throw new Zend_Exception('Error on INSERT into DB!');
        }
    } //end FUNCTION



    public function update(Application_Model_Categorias $categoriasModel)
    {
        $categoria = $categoriasModel->toArray();
        $result = 0;
        try {
            $result = $this->getDbTable()->update($categoria, array('iduser = ?' => $categoria['iduser'], 'idcategoria = ?' => $categoria['idcategoria']));
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
        return $result;
    } //end FUNCTION



    public function delete($iduser, $idcategoria)
    {
        $result = 0;
        try {
            $result = $this->getDbTable()->delete(array('iduser = ?' => $iduser, 'idcategoria = ?' => $idcategoria));
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
        if ($result === 0) {
            $logger = Zend_Registry::get('logger');
            $logger->err('Error on DELETE from DB!');
        	throw new Zend_Exception('Error on DELETE from DB!');
        }
    } //end FUNCTION



    public function find($iduser, $idcategoria)
    {
        $result = 0;
        try {
            $result = $this->getDbTable()->find(array('iduser = ?' => $iduser, 'idcategoria = ?' => $idcategoria));
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



    public function fetchAll($iduser)
    {
        try {
            $select = $this->getDbTable()->select()->order('descricao')->where('iduser = ?', $iduser);
            $resultSet = $this->getDbTable()->fetchAll($select);
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
        return $resultSet;
    } //end FUNCTION



    public function getSoma($iduser)
    {
        $select = $this->getDbTable()->select();
        $select->from('lancamentos', array(new Zend_Db_Expr('SUM(valor)')));
        $select->where('iduser = ?', $iduser)->where('tipo = ?', 'C')->where('idstatus = ?', 1);
        $result = $this->getDbTable()->fetchAll($select);
        print_r($result);
        exit;
    }
} //end CLASS
