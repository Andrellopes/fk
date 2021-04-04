<?php

    include_once(dirname(__FILE__).'/../../config/config.inc.php');
    include_once(dirname(__FILE__).'/models/FKcustomersClass.php');

    // Recupera a funcao a ser executada
    $func = $_REQUEST['func'];

    // Instancia FKcustomersClass
    $fkcustomersClass = new FKcustomersClass();

    switch ($func) {

        case '1':
            $cep = $_REQUEST['cep'];

            echo $fkcustomersClass->pesquisaCepRV($cep);
            break;

        case '2':
            $cep = $_REQUEST['cep'];

            echo $fkcustomersClass->pesquisaCepBY($cep);
            break;

        case '3':
            $cep = $_REQUEST['cep'];

            echo $fkcustomersClass->pesquisaCepAC($cep);
            break;

        case '4':
            $uf = $_REQUEST['uf'];

            echo $fkcustomersClass->pesquisaUF($uf);
            break;

        case '5':
            $endereco = $_REQUEST['endereco'];

            echo $fkcustomersClass->validaEndereco($endereco);
            break;

        case '6':
            $cep = $_REQUEST['cep'];

            echo $fkcustomersClass->pesquisaCepCO($cep);
            break;
    }
