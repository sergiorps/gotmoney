<?php
class Extra_UserSession
{

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public static function createLoginSession( $credentialColumn, $identity, $credential )
    {
        switch ($credentialColumn) {
            case 'facebook';
                $identityColumn = 'facebook';
                $identity = $credential;
                break;
            case 'google';
                $identityColumn = 'google';
                $identity = $credential;
                break;
            case 'passwd';
                $identityColumn = 'email';
                $credential = self::verifyPassword($identity, $credential);
                break;
            default:
                $logger = Zend_Registry::get('logger');
                $logger->err('Login type do not exist: ' . $credentialColumn);
                return false;
                break;
        }

        $auth = Zend_Auth::getInstance();
        //Verifica se login foi efetuado
        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
        }

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $adapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        //$adapter->setTableName('users')->setIdentityColumn('email')
        $adapter->setTableName('users')->setIdentityColumn($identityColumn)
            ->setCredentialColumn( $credentialColumn )
            ->setIdentity( $identity )->setCredential( $credential );

        /*if ($credentialColumn === 'passwd') {
            $adapter->setCredentialTreatment('SHA1(?)');
        } else {
            //$adapter->setCredentialTreatment('SHA1(?)');
        }*/

        Zend_Session::regenerateId(true);

        //print_r($auth->authenticate($adapter));
        if ($auth->authenticate($adapter)->isValid()) {
            self::persistUser($adapter->getResultRowObject(null, 'passwd'));
            return true;
        } else {
            $logger = Zend_Registry::get('logger');
            $logger->err('Authentication is invalid!');
            return false;
        }
    }



    /**
     * Return an associative array of the stored data.
     *
     * @return boolean
     */
    public static function isUserLogged()
    {
        $auth = Zend_Auth::getInstance();
        //Check if has active login
        if (!$auth->hasIdentity()) { return false; }

        if (!self::isValidUserSession()) {
            $auth->clearIdentity();
            return false;
        }

        return true;
    } //end FUNCTION



    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public static function getUserLogged() {
        if (!self::isUserLogged()) {
            return null;
        }

        return Zend_Auth::getInstance()->getIdentity();
    } //end FUNCTION


    private static function persistUser($user) {
        //Armazena os dados do usuário em sessão
        unset($user->passwd);
        unset($user->datacriacao);
        unset($user->active);
        unset($user->datanascimento);
        unset($user->sexo);
        unset($user->uf);
        unset($user->alert);
        unset($user->lastchange);

        /* //Verifica se consumidor está ativo
        if (empty($info->active)) {
        $auth->clearIdentity();
        $this->view->email = $info->email;
        //Redireciona para o Controller protegido
        return $this->_redirect( 'access/active/' );
        }*/

        //Cria token de sessão
        $user->token = self::getToken($user->iduser);
        //$auth->getStorage()->write($info);
        Zend_Auth::getInstance()->getStorage()->write($user);

        //Atualiza saldo de todas as contas do user
        $contasDao = new Application_Model_ContasDao();
        //TODO : balance
        //$contasDao->setBalance($info->iduser);
    }


    private static function getToken($iduser) {
        return sha1($iduser . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
    }

    private static function isValidUserSession() {
        $user = Zend_Auth::getInstance()->getIdentity();

        //Check session token
        if ($user->token != self::getToken($user->iduser)) {
            return false;
        }
        return true;
    }

    private static function verifyPassword($identity, $credential) {
        $consumidoresDao = new Application_Model_ConsumidoresDao();
        $consumidor = $consumidoresDao->findEmail($identity);
        if ($consumidor) {
            if (password_verify($credential, $consumidor->passwd)) {
                $credential = $consumidor->passwd;
            }
        }
        return $credential;
    }
}
