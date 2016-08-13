<?php
class Extra_LoginFacebook
{

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public static function login($data) {
        if (!empty($data['iduser'])) {
            $consumidoresDAO = new Application_Model_ConsumidoresDao();
            //$consumidor = $consumidoresDAO->findEmail($data['email']);
            $consumidor = $consumidoresDAO->findFacebook($data['iduser']);

            /*if (isset($consumidor->iduser) && isset($consumidorFacebook->iduser) && $consumidor->iduser !== $consumidorFacebook->iduser) {
                exit("ERRO FACEBOOK");
                //TODO: log
                return false;
            } elseif () {

            }*/

            if (!empty($consumidor)) {
                return self::doLogin($data);
            } else {
                return self::newUser($data);
            }
        } else {
            return false;
        }
    }


    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    private static function doLogin($data)
    {
        if ( Extra_UserSession::createLoginSession( 'facebook', $data['email'], $data['iduser'] ) ) {
            return true;
        } else {
            return false;
        }
    } //end FUNCTION


    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    private static function newUser($data)
    {
        $consumidor = new Application_Model_Consumidores();
        $consumidor->setIduser( str_replace('.', '', microtime(true)) );
        $consumidor->setFacebook( $data['iduser'] );
        $consumidor->setNome( $data['nome'] );
        $consumidor->setEmail( $data['email'] );
        $consumidor->setAlert( 1 );
        $consumidor->setSexo( $data['sexo'] );
        $consumidor->setDatanascimento( $data['datanascimento'] );
        $newPassword = sha1($data['iduser'] . $data['email']) . md5(uniqid(mt_rand(), true));
        $consumidor->setPasswd( $newPassword );
        $consumidoresDAO = new Application_Model_ConsumidoresDao();

        try {
            $consumidoresDAO->insert($consumidor);
        } catch (Zend_Exception $e) {
            return false;
        }

        //Conta criada automaticamente
        $conta = new Extra_Account( );
        ////TODO
        //$conta->createDefault( $result );
        return self::doLogin($data);
    }
}
