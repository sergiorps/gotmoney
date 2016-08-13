<?php
class Extra_Account
{
    public function createDefault( $iduser )
    {
        //Conta criada automaticamente
        $conta = new Application_Model_Contas();
        $conta->setIduser( $iduser );
        $conta->setIdtipo('001');
        $conta->setDescricao('Cash');
        $conta->setDataabertura(date('Y-m-d'));
        $contasDAO = new Application_Model_ContasDao();
        return $contasDAO->insert($conta);
    }
}