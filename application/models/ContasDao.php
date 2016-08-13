<?php

class Application_Model_ContasDao
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
            $this->setDbTable('Application_Model_DbTable_Contas');
        }
        return $this->_dbTable;
    } //end FUNCTION



    public function insert(Application_Model_Contas $contasModel)
    {
        $conta = $contasModel->toArray();
        $conta['saldo'] = 0;
        $result = 0;
        try {
            $result = $this->getDbTable()->insert($conta);
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



    public function update(Application_Model_Contas $contasModel)
    {
        $conta = $contasModel->toArray();
        unset($conta['saldo']);
        try {
            $result = $this->getDbTable()->update($conta, array('iduser = ?' => $conta['iduser'], 'idconta = ?' => $conta['idconta']));
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
    } //end FUNCTION



    public function delete($iduser, $idconta)
    {
        $result = 0;
        try {
            $result = $this->getDbTable()->delete(array('iduser = ?' => $iduser, 'idconta = ?' => $idconta));
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


    public function find($iduser, $idconta)
    {
        $result = 0;
        try {
            $result = $this->getDbTable()->find(array('iduser = ?' => $iduser, 'idconta = ?' => $idconta));
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
            $select = $this->getDbTable()->select()->order('descricao')->where('iduser = ? ', $iduser);
            $resultSet = $this->getDbTable()->fetchAll($select);
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
        return $resultSet;
    } //end FUNCTION


    public function getBalance($iduser)
    {
        $select = $this->getDbTable()->select();
        $select->where('iduser = ? ', $iduser);
        $resultSet = $this->getDbTable()->fetchAll($select);
        $balance = 0;
        foreach ($resultSet as $row):
        $balance += $row->saldo;
        endforeach;
        return $balance;
    } //end FUNCTION


    public function setBalance($iduser)
    {
        $table = new Application_Model_DbTable_Lancamentos();
        $credits = $debits = $results = array();
        //Get Credits
        $select = $table->select();
        $select->from('lancamentos', array(new Zend_Db_Expr('SUM(valor)')));
        $select->columns('idconta');
        $select->where('iduser = ?', $iduser)->where("tipo = 'C'")->where('idstatus = 1')->
            group('idconta');
        $resultSet = $table->fetchAll($select);
        $credits = $resultSet->toArray();

        //Get Debits
        $select->reset('where');
        $select->where('iduser = ?', $iduser)->where("tipo = 'D'")->where('idstatus = 1');
        $resultSet = $table->fetchAll($select);
        $debits = $resultSet->toArray();

        //Calcula a partir dos d�bitos
        foreach ($debits as $debit)
        {
            $saldo = 0;
            foreach ($credits as $credit)
            {
                if ($debit['idconta'] == $credit['idconta'])
                {
                    $saldo = $credit['SUM(valor)'] - $debit['SUM(valor)'];
                    $results[] = array('idconta' => $debit['idconta'], 'saldo' => $saldo);
                    break;
                }
            }
            if ($saldo == 0)
            {
                $saldo = $debit['SUM(valor)'] * -1;
                $results[] = array('idconta' => $debit['idconta'], 'saldo' => $saldo);
            }
        }

        //Calcula a partir dos cr�ditos (contas n�o encontradas com d�bito)
        foreach ($credits as $credit)
        {
            $update = 0;
            foreach ($results as $result)
            {
                if ($credit['idconta'] == $result['idconta'])
                {
                    $update = 1;
                    break;
                }
            }
            if ($update == 0)
            {
                $saldo = $credit['SUM(valor)'];
                $results[] = array('idconta' => $credit['idconta'], 'saldo' => $saldo);
            }
        }

        //Atualiza contas
        $contas = $this->fetchAll($iduser);
        foreach ($contas as $conta)
        {
            $update = 0;
            foreach ($results as $result)
            {
                if ($result['idconta'] == $conta->getIdconta())
                {
                    $conta->setSaldo($result['saldo']);
                    $update = 1;
                    break;
                }
            }
            if ($update == 0)
            {
                $conta->setSaldo(0);
            }
            //Update
            $this->save($conta);
        }
    } //end FUNCTION
} //end CLASS
