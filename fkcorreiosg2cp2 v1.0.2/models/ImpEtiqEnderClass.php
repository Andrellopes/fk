<?php

include_once _PS_MODULE_DIR_.'fkcorreiosg2cp2/defines/defines.php';

class ImpEtiqEnderClass extends ObjectModel {

    public static $definition = array(
        'table' => 'fkcorreiosg2cp2_etiquetas_ender',
        'primary' => 'id',
        'multilang' => false,
        'fields' => array(
            'id_shop'       =>	array('type' => self::TYPE_INT),
            'arquivo_pf'    => 	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 255),
            'data_criacao'  =>	array('type' => self::TYPE_DATE),
        ),
    );

    public function recuperaNomeArquivo($id) {

        $sql = "SELECT arquivo_pdf
                FROM "._DB_PREFIX_."fkcorreiosg2cp2_etiquetas_ender
                WHERE id = ".(int)$id;

        return Db::getInstance()->getRow($sql);
    }

    public function excluiRegistro($parm) {

        if (!is_array($parm)) {
            $parm = array('0' => $parm);
        }

        foreach ($parm as $id) {

            // Recupera nome do arquivo PDF e exclui fisicamente
            $arquivo = $this->recuperaNomeArquivo($id);

            if (file_exists(_PS_MODULE_DIR_.FK_NOME_MODULO.'/pdf/'.$arquivo['arquivo_pdf'])) {
                unlink(_PS_MODULE_DIR_.FK_NOME_MODULO.'/pdf/'.$arquivo['arquivo_pdf']);
            }

            // Exclui registro da tabela de controle
            Db::getInstance()->delete('fkcorreiosg2cp2_etiquetas_ender', 'id = '.(int)$id);
        }

        return true;
    }
}