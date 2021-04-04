<?php

/*
    Nesta rotina sao gravados os cookies que serao recuperados pelo js
*/

// Modo Operacao
setcookie('fkcustomes_modo_oper', Configuration::get('FKCUSTOMERS_MODO'), 0);

// Provedor CEP
setcookie('fkcustomes_provedor_cep', Configuration::get('FKCUSTOMERS_WS'), 0);

// URL de funcoes.php
setcookie('fkcustomes_url_funcoes', Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/fkcustomers/funcoes.php', 0);

// URL das imagens fkcustomes
setcookie('fkcustomes_url_img', Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/fkcustomers/img/', 0);