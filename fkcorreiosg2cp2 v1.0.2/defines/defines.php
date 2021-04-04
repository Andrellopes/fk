<?php

if (!defined('FK_NOME_MODULO')) {
    define('FK_NOME_MODULO', 'fkcorreiosg2cp2');
}

if (!defined('FK_URL_IMG')) {
    define('FK_URL_IMG', Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.FK_NOME_MODULO.'/img/');
}

if (!defined('FK_URI_IMG')) {
    define('FK_URI_IMG', _PS_MODULE_DIR_.FK_NOME_MODULO.'/img/');
}