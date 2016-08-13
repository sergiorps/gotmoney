<?php
class Extra_LoginGoogle
{

    /**
     * Return an associative array of the stored data.
     *
     * @return array
     */
    public static function login($data) {
        if (!empty($data['iduser'])) {
            $consumidoresDAO = new Application_Model_ConsumidoresDao();
            $consumidor = $consumidoresDAO->findGoogle($data['iduser']);

            //$googleAPIClient = new Google_Client();

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
        if ( Extra_UserSession::createLoginSession( 'google', $data['email'], $data['iduser'] ) ) {
            return true;
        }
        else {
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
        $consumidor->setGoogle( $data['iduser'] );
        $consumidor->setNome( $data['nome'] );
        $consumidor->setEmail( $data['email'] );
        $consumidor->setAlert( 1 );
        $consumidor->setSexo( $data['sexo'] );
        $consumidor->setDatanascimento( $data['datanascimento'] );
        $newPassword = sha1($data['email'] . $data['iduser']) . md5(uniqid(mt_rand(), true));
        $consumidor->setPasswd( $newPassword );
        $consumidoresDAO = new Application_Model_ConsumidoresDao();
        $result = $consumidoresDAO->save( $consumidor );

        //Conta criada automaticamente
        $conta = new Extra_Account( );
        $conta->createDefault( $result );
        return self::doLogin($data);
    }
}
