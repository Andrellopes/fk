<?php

    include_once(dirname(__FILE__).'/../../config/config.inc.php');
    include_once(dirname(__FILE__).'/models/FKpagseguroctInterfaceClass.php');
    
    // Recupera o tipo e codigo da notificação enviado pelo Pagseguro
    $id_type = $_POST['notificationType'];
    $id_code = $_POST['notificationCode'];
    
    // Verifica o tipo de notificação recebida
    if (!$id_type === 'transaction') {
        die();
    }
    
    // Instancia FKpagseguroctInterfaceClass
    $notificacao = new FKpagseguroctInterfaceClass();
    
    if (!$notificacao->consultaNotificacao($id_code)) {
        die();
    }
    
    // Atualiza a tabela de controle
    $dados = array(
        'status'        => $notificacao->getCodStatus(),
        'desc_status'   => $notificacao->getDescStatus(),
        'pagto'         => $notificacao->getCodPagto(),
        'desc_pagto'    => $notificacao->getDescPagto(),
        'data_status'   => date("Y/m/d h:i:s")
    );
    
    Db::getInstance()->update('fkpagseguroct', $dados, 'id_cart = '.(int)$notificacao->getReferencia());
    
    // Altera o status do pedido
    $notificacao->atualizaStatusPedido($notificacao->getReferencia(), $notificacao->getCodStatus());
