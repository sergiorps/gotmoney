<?php
class Extra_User
{

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function createLoginSession( $credentialColumn, $identity, $credential )
    {
        //$credentialColumn = facebook || google || passwd
        
        $auth = Zend_Auth::getInstance();
        //Verifica se login foi efetuado
        if ($auth->hasIdentity())
        {
            $user = $auth->getIdentity();
            return true; //$this->_redirect('transactions/list/date/' . date('Y-m'));
        }

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $adapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $adapter->setTableName('users')->setIdentityColumn('email')
                ->setCredentialColumn( $credentialColumn )
                ->setIdentity( $identity )->setCredential( $credential );

        if ($credentialColumn == 'passwd') {
            $adapter->setCredentialTreatment('SHA1(?)');
        }

        if ($auth->authenticate($adapter)->isValid())
        {
            //Armazena os dados do usuário em sessão
            $info = $adapter->getResultRowObject(null, 'passwd');
            unset($info->datacriacao);
            unset($info->active);
            unset($info->datanascimento);
            unset($info->sexo);
            unset($info->uf);
            unset($info->alert);
            unset($info->lastchange);
            /* //Verifica se consumidor está ativo
            if (empty($info->active)) {
            $auth->clearIdentity();
            $this->view->email = $info->email;
            //Redireciona para o Controller protegido
            return $this->_redirect( 'access/active/' );
            }*/

            //Cria token de sessão
            $info->token = sha1($info->iduser . $info->email . $_SERVER['HTTP_USER_AGENT']);
            $storage = $auth->getStorage();
            $storage->write($info);

            //Atualiza saldo de todas as contas do user
            $contasDao = new Application_Model_ContasDao();
            $contasDao->setBalance($info->iduser);

            //Redireciona para o Controller protegido
            //return $this->_redirect('transactions/list/date/' . date('Y-m'));            
            return true;
        }
        else {
            return false;
        }
    }
    
    
    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public function checkUserIsLogged()
    {
        $auth = Zend_Auth::getInstance();
        //Verifica se login existe login ativo
        if (!$auth->hasIdentity())
        {
            return false;
        }
        //Seta dados do consumidor logado
        $user = $auth->getIdentity();

        //Verifica token
        $token = sha1($user->iduser . $user->email . $_SERVER['HTTP_USER_AGENT']);
        if ($user->token != $token)
        {
            $auth->clearIdentity();
            return false; //$this->_redirect('user/login');
        }

        Zend_Layout::getMvcInstance()->assign('user', $user);
        return $user;
    } //end FUNCTION

}