<?php

class Application_Model_ConsumidoresDao
{

    protected $_dbTable;

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
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


    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_Consumidores');
        }
        return $this->_dbTable;
    } //end FUNCTION


    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function insert(Application_Model_Consumidores $consumidoresModel)
    {
        $consumidor = $consumidoresModel->toArray();
        $consumidor['active'] = 1;
        $consumidor['datacriacao'] = new Zend_Db_Expr('CURDATE()');
        $result = 0;
        try {
            $result = $this->getDbTable()->insert($consumidor);
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



    public function update(Application_Model_Consumidores $consumidoresModel)
    {
        $consumidor = $consumidoresModel->toArray();
        if (empty($consumidor['passwd'])) {
            unset($consumidor['passwd']);
        }
        unset($consumidor['datacriacao']);
        $result = 0;
        try {
            $result = $this->getDbTable()->update($consumidor, array('iduser = ?' => $consumidor['iduser']));
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
        return $result;
    } //end FUNCTION


    public function delete($idconsumidor)
    {
        $result = 0;
        try {
            $result = $this->getDbTable()->delete(array('iduser = ?' => $idconsumidor));
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



    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function find($idconsumidor)
    {
        $result = 0;
        try {
            $result = $this->getDbTable()->find(array('iduser = ?' => $idconsumidor));
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


    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function findEmail($email)
    {
        try {
            $select = $this->getDbTable()->select()->where('email = ? ', $email);
            $resultSet = $this->getDbTable()->fetchAll($select);
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
        return $resultSet->current();
    } //end FUNCTION



    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function fetchAll()
    {
        try {
            $resultSet = $this->getDbTable()->fetchAll();
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
        return $resultSet;
    } //end FUNCTION


    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function findFacebook($id)
    {
        try {
            $select = $this->getDbTable()->select()->where('facebook = ? ', $id);
            $resultSet = $this->getDbTable()->fetchAll($select);
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
        return $resultSet->current();
    } //end FUNCTION


    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function findGoogle($id)
    {
        try {
            $select = $this->getDbTable()->select()->where('google = ? ', $id);
            $resultSet = $this->getDbTable()->fetchAll($select);
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
        return $resultSet->current();
    } //end FUNCTION
} //end CLASS
