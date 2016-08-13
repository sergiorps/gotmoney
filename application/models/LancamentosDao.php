<?php

class Application_Model_LancamentosDao
{

    protected $_dbTable;

    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    } //end FUNCTION


    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Application_Model_DbTable_Lancamentos');
        }
        return $this->_dbTable;
    } //end FUNCTION



    public function insert(Application_Model_Lancamentos $lancamentosModel)
    {
        $lancamento = $lancamentosModel->toArray();
        $lancamento['origem'] = 'W';
        if (empty($lancamento['idlancamentopai'])) unset($lancamento['idlancamentopai']);
        $result = 0;
        try {
            $result = $this->getDbTable()->insert($lancamento);
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



    public function update(Application_Model_Lancamentos $lancamentosModel)
    {
        //echo 333;
        //print_r($lancamentosModel);
        //1print_r($lancamentosModel->toArray());
        $lancamento = $lancamentosModel->toArray();
        unset($lancamento['idlancamentopai']);
        unset($lancamento['parcela']);
        $result = 0;
        try {
            $result = $this->getDbTable()->update($lancamento, array('iduser = ?' => $lancamento['iduser'], 'idlancamento = ?' => $lancamento['idlancamento']));
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
    } //end FUNCTION



    public function delete($iduser, $idlancamento)
    {
        $result = 0;
        try {
            $result = $this->getDbTable()->delete(array('iduser = ?' => $iduser, 'idlancamento = ?' => $idlancamento));
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











    public function editRecurrency(Application_Model_Lancamentos $lancamento)
    {
        $data = $lancamento->toArray();
        $hasIdlancamentopai = (empty($data['idlancamentopai'])) ? false : true;

        //Para lançamentos recorrentes, somente alguns campos serão atualizados
        unset(
            $data['idlancamento'], $data['iduser'], $data['idlancamentopai'], $data['idstatus'],
            $data['parcela'], $data['datalancamento'], $data['datavencimento'], $data['origem'], $data['lastchange']
        );

        if ($hasIdlancamentopai)
        {
            $this->getDbTable()->update($data, array('iduser = ?' => $lancamento->getIduser(), 'idlancamento = ?' => $lancamento->getIdlancamentopai()));
            $this->getDbTable()->update($data, array('iduser = ?' => $lancamento->getIduser(), 'idlancamentopai = ?' => $lancamento->getIdlancamentopai()));
        } else {
            $this->getDbTable()->update($data, array('iduser = ?' => $lancamento->getIduser(), 'idlancamento = ?' => $lancamento->getIdlancamento()));
            $this->getDbTable()->update($data, array('iduser = ?' => $lancamento->getIduser(), 'idlancamentopai = ?' => $lancamento->getIdlancamento()));
        }
    } //end FUNCTION



    public function find($iduser, $idlancamento)
    {
        $result = 0;
        try {
            $select = $this->getDbTable()->select()->where('iduser = ?', $iduser)->where('idlancamento = ?', $idlancamento);
            $result = $this->getDbTable()->fetchAll($select)->current();
            //$result = $this->getDbTable()->find(array('iduser = ?' => $iduser, 'idlancamento = ?' => $idlancamento));
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
        if (count($result) === 0) {
            return null;
        }

        $tagsWithHash = str_replace(', ', '', $result->tag);
        $result->tag = explode('#', $tagsWithHash);
        return $result;
    } //end FUNCTION


    public function fetchAll($iduser)
    {
        try {
            $select = $this->getDbTable()->select()->where('iduser = ? ', $iduser)->order('datavencimento');
            $resultSet = $this->getDbTable()->fetchAll($select);
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }

        for ($i = 0; $i < count($resultSet); $i++) {
            $row = $resultSet->getRow($i);
            $tagsWithHash = str_replace(', ', '', $row->tag);
            $row->tag = explode('#', $tagsWithHash);
        }

        return $resultSet;
    } //end FUNCTION


    public function fetchByDate($iduser, $year, $month)
    {
        try {
            $select = $this->getDbTable()->select();
            $select->where('iduser = ? ', $iduser)->order('datavencimento');
            if (!empty($month)) {
                $select->where('MONTH(datavencimento) = ? ', $month);
            } else {
                $select->where('YEAR(datavencimento) = ? ', $year);
            }
            $resultSet = $this->getDbTable()->fetchAll($select);

        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
        return $resultSet;
    } //end FUNCTION


    public function fetchByDay($iduser, $year, $month)
    {
        $select = $this->getDbTable()->select();
        $select->from('lancamentos', array(new Zend_Db_Expr('SUM(valor) AS valor')));
        $select->columns('tipo')->columns('DAY(datavencimento) AS day');
        $select->where('iduser = ?', $iduser)->where('YEAR(datavencimento) = ?', $year)->where('MONTH(datavencimento) = ?', $month);
        $select->group('datavencimento')->group('tipo');
        $resultSet = $this->getDbTable()->fetchAll($select);
        return $resultSet->toArray();
    } //end FUNCTION


    public function fetchByMonth($iduser, $year)
    {
        $select = $this->getDbTable()->select();
        $select->from('lancamentos', array(new Zend_Db_Expr('SUM(valor) AS valor')));
        $select->columns('tipo')->columns('MONTH(datavencimento) AS month');
        $select->where('iduser = ?', $iduser)->where('YEAR(datavencimento) = ?', $year);
        $select->group('MONTH(datavencimento)')->group('tipo');
        $resultSet = $this->getDbTable()->fetchAll($select);
        return $resultSet->toArray();
    } //end FUNCTION


    public function fetchByYear($iduser, $year)
    {
        $select = $this->getDbTable()->select();
        $select->from('lancamentos', array(new Zend_Db_Expr('SUM(valor) AS valor')));
        $select->columns('tipo')->columns('YEAR(datavencimento) AS year');
        $select->where('iduser = ?', $iduser)->where('YEAR(datavencimento) = ?', $year);
        $select->group('tipo');
        $resultSet = $this->getDbTable()->fetchAll($select);
        return $resultSet->toArray();
    } //end FUNCTION

    public function fetchByDayAndAccount($iduser, $year, $month)
    {
        $select = $this->getDbTable()->select();
        $select->setIntegrityCheck(false);
        //$select->from('lancamentos', array(new Zend_Db_Expr('SUM(valor) AS valor')));
        $select->from(array('l' => 'lancamentos'),
                      array('idconta', 'tipo', new Zend_Db_Expr('SUM(valor) AS valor'), 'DAY(datavencimento) AS day'));
        $select->join(array('c' => 'contas'),
                      'l.idconta = c.idconta',
                      array('c.descricao'));
        $select->where('l.iduser = ?', $iduser)->where('YEAR(l.datavencimento) = ?', $year)->where('MONTH(l.datavencimento) = ?', $month);
        $select->group('l.idconta')->group('l.tipo');
        $resultSet = $this->getDbTable()->fetchAll($select);
        return $resultSet->toArray();
    } //end FUNCTION

    public function fetchByMonthAndAccount($iduser, $year)
    {
        $select = $this->getDbTable()->select();
        $select->setIntegrityCheck(false);
        //$select->from('lancamentos', array(new Zend_Db_Expr('SUM(valor) AS valor')));
        $select->from(array('l' => 'lancamentos'),
                      array('idconta', 'tipo', new Zend_Db_Expr('SUM(valor) AS valor'), 'MONTH(datavencimento) AS month'));
        $select->join(array('c' => 'contas'),
                      'l.idconta = c.idconta',
                      array('c.descricao'));
        $select->where('l.iduser = ?', $iduser)->where('YEAR(l.datavencimento) = ?', $year);
        $select->group('MONTH(l.datavencimento)')->group('l.tipo');
        $resultSet = $this->getDbTable()->fetchAll($select);
        return $resultSet->toArray();
    } //end FUNCTION


    public function fetchByYearAndAccount($iduser, $year)
    {
        /*$select = $this->getDbTable()->select();
        $select->from('lancamentos', array(new Zend_Db_Expr('SUM(valor) AS valor')));
        $select->columns('idconta')->columns('tipo')->columns('YEAR(datavencimento) AS year');
        $select->where('iduser = ?', $iduser)->where('YEAR(datavencimento) = ?', $year);
        $select->group('idconta')->group('tipo');
        $resultSet = $this->getDbTable()->fetchAll($select);
        print_r($resultSet->toArray());exit;
        return $resultSet->toArray();*/

        $select = $this->getDbTable()->select();
        $select->setIntegrityCheck(false);
        //$select->from('lancamentos', array(new Zend_Db_Expr('SUM(valor) AS valor')));
        $select->from(array('l' => 'lancamentos'),
                      array('idconta', 'tipo', new Zend_Db_Expr('SUM(valor) AS valor'), 'YEAR(l.datavencimento) AS year'));
        $select->join(array('c' => 'contas'),
                      'l.idconta = c.idconta',
                      array('c.descricao'));
        $select->where('l.iduser = ?', $iduser)->where('YEAR(l.datavencimento) = ?', $year);//->where('tipo = ?', 'C');
        $select->group('l.idconta')->group('l.tipo');
        $resultSet = $this->getDbTable()->fetchAll($select);
        $credito = $resultSet->toArray();
        return $resultSet->toArray();

        $select = '';

        $select = $this->getDbTable()->select();
        $select->setIntegrityCheck(false);
        //$select->from('lancamentos', array(new Zend_Db_Expr('SUM(valor) AS valor')));
        $select->from(array('l' => 'lancamentos'),
                      array('idconta', 'tipo', new Zend_Db_Expr('SUM(valor) AS valor'), 'YEAR(l.datavencimento) AS year'));
        $select->join(array('c' => 'contas'),
                      'l.idconta = c.idconta',
                      array('c.descricao'));
        $select->where('l.iduser = ?', $iduser)->where('YEAR(l.datavencimento) = ?', $year)->where('tipo = ?', 'D');
        $select->group('l.idconta')->group('l.tipo');
        $resultSet = $this->getDbTable()->fetchAll($select);
        $debito = $resultSet->toArray();

        print_r($credito);print_r($debito);
        $resultSet = array();
        foreach($debito as $deb) {
            $checked = false;
            $valor = 0;
            foreach($credito as $cred) {
                if ($cred['idconta'] == $deb['idconta']) {
                    $checked = true;
                    $valor = $cred['valor'] - $deb['valor'];
                }
            }
            if (!$checked) {
                echo $valor;
            }
            else {
                //$resultSet
            }
        }
        foreach($credito as $cred) {
            $checked = false;
            $valor = 0;
            foreach($debito as $deb) {
                if ($cred['idconta'] == $deb['idconta']) {
                    $checked = true;
                    $valor = $cred['valor'] - $deb['valor'];
                    //echo $valor;
                }
            }
        }
        //echo count($credito);
        exit;

        return $resultSet->toArray();
    } //end FUNCTION


    public function fetchAllLate($iduser)
    {
        try {
            $select = $this->getDbTable()->select()->where('iduser = ? ', $iduser)->where('datavencimento < NOW()')->order('datavencimento');
            $resultSet = $this->getDbTable()->fetchAll($select);
        } catch (Zend_Exception $e) {
            $logger = Zend_Registry::get('logger');
            $logger->err($e);
            throw new Zend_Exception($e->getMessage());
        }
        return $resultSet;
    } //end FUNCTION
} //end CLASS
