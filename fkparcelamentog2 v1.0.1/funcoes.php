<?php

include_once(dirname(__FILE__).'/models/FKparcg2Class.php');

if (isset($_REQUEST['func'])) {

    switch ($_REQUEST['func']) {

        case '1':
            // Recupera dados do POST
            $valor = $_REQUEST['valor'];

            // Instancia FKparcg2Class
            $fkparcg2Class = new FKparcg2Class();
            $retorno = $fkparcg2Class->procParcelamentoProduto($valor);
            echo $retorno;
            break;

        default:
            break;

    }
}else {
    // Retorna erro caso tenha problemas no Post
    echo 'erro';
}

