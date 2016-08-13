<?php
class Extra_Category
{
    public function createDefault( $iduser )
    {
        //Categoria criada automaticamente
        //New category
        $categoriasDAO = new Application_Model_CategoriasDao();
        $categoria = new Application_Model_Categorias();
        $categoria->setIduser( $iduser );
        $categoria->setDescricao('Lazer');
        $categoriasDAO->save($categoria);
        //New category
        $categoriasDAO = new Application_Model_CategoriasDao();
        $categoria = new Application_Model_Categorias();
        $categoria->setIduser( $iduser );
        $categoria->setDescricao('Alimentos');
        $categoriasDAO->save($categoria);
        //New category
        $categoriasDAO = new Application_Model_CategoriasDao();
        $categoria = new Application_Model_Categorias();
        $categoria->setIduser( $iduser );
        $categoria->setDescricao('Casa');
        $categoriasDAO->save($categoria);
        return true;
    }
}