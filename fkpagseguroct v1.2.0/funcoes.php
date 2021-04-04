<?php

    include_once(dirname(__FILE__).'/../../config/config.inc.php');
    include_once(dirname(__FILE__).'/models/FKpagseguroctInterfaceClass.php');

    // Recupera a funcao a ser executada
    $func = $_REQUEST['func'];

    // Instancia FKpagseguroctInterfaceClass
    $checkout = new FKpagseguroctInterfaceClass();

    switch ($func) {

        case '1':
            echo $checkout->obterSessionId();
            break;

        
    }
