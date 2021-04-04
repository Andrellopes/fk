<?php

include_once _PS_MODULE_DIR_.'fkmessenger/defines/defines.php';
  
class MessengerClass {

    public function recuperaMensagem_tudo($origem, $id_shop) {

        $sql = "SELECT *
                FROM "._DB_PREFIX_."fkmessenger
                WHERE origem = ".(int)$origem." and id_shop = ".(int)$id_shop;

        if ($origem == _origemHome_) {
            return Db::getInstance()->getRow($sql);
        }else {
            return Db::getInstance()->executeS($sql);
        }

    }
    
    public function recuperaMensagem_ativo($origem, $id_shop) {

        $sql = "SELECT *
                FROM "._DB_PREFIX_."fkmessenger
                WHERE ativo = 1 And origem = ".(int)$origem." and id_shop = ".(int)$id_shop;

        if ($origem == _origemHome_) {
            return Db::getInstance()->getRow($sql);
        }else {
            return Db::getInstance()->executeS($sql);
        }

    }
        
}  

?>
