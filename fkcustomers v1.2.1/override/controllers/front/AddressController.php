<?php

class AddressController extends AddressControllerCore {

    public function initContent() {
        // Inclui as variaveis smarty
        include_once(_PS_MODULE_DIR_.'fkcustomers/includes/variaveis_smarty.php');

        // Inclui os cookies que serao utilizados pelo js
        include_once(_PS_MODULE_DIR_.'fkcustomers/includes/variaveis_cookie.php');

        parent::initContent();
    }

    public function setMedia() {

        parent::setMedia();

        // CSS
        $this->addCSS(_PS_MODULE_DIR_.'fkcustomers/css/fkcustomers_front.css');

        // JS
        $this->addJS(_PS_JS_DIR_.'jquery/plugins/fancybox/jquery.fancybox.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/jquery.maskedinput.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_cookie.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_cpf.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_cnpj.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_cep.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_endereco.js');
        $this->addJS(_PS_MODULE_DIR_.'fkcustomers/js/fkcustomers_front.js');
    }

    protected function processSubmitAddress() {

        include_once(_PS_MODULE_DIR_.'fkcustomers/models/FKcustomersClass.php');

        // Instancia FKcustomersClass
        $fkcustomersClass = new FKcustomersClass();

        if ( Configuration::get('FKCUSTOMERS_MODO') == '1') {
            // Numero
            if (!Tools::getValue('numend')) {
                $this->errors[] = Tools::displayError('O campo Número é obrigatório.');
            }
        }

        // Telefone
        $telefone = Tools::getValue('phone');

        if ($telefone) {
            if (!$fkcustomersClass->validaDDD($telefone)) {
                $this->errors[] = Tools::displayError('DDD do Telefone é inválido.');
            }
        }

        // Celular
        $celular = Tools::getValue('phone_mobile');

        if ($celular) {
            if (!$fkcustomersClass->validaDDD($celular)) {
                $this->errors[] = Tools::displayError('DDD do Celular é inválido.');
            }
        }

        if (count($this->errors)) {
            return;
        }

        parent::processSubmitAddress();

    }

    // TODO: alterar função quando mudar versão do Prestashop
    public function setTemplate($default_template) {

        if ($this->useMobileTheme()) {
            $this->setMobileTemplate($default_template);
        } else {
            $template = $this->getOverrideTemplate();
            if ($template) {
                parent::setTemplate($template);
            }else {
                if (Configuration::get('FKCUSTOMERS_MODO') == '1') {
                    // Verifica se existe tpl customizado
                    $custom = _PS_MODULE_DIR_.'fkcustomers/views/custom/address.tpl';

                    if (file_exists($custom)) {
                        $tpl = $custom;
                    }else {
                        // Seleciona tpl do fkcustomers conforme versao
                        if (version_compare(_PS_VERSION_, '1.6.0.5', '==')) {
                            $tpl = _PS_MODULE_DIR_ . 'fkcustomers/views/front/v1_6_0_5/address.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.6', '==')) {
                            $tpl = _PS_MODULE_DIR_ . 'fkcustomers/views/front/v1_6_0_6/address.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.7', '==') or version_compare(_PS_VERSION_, '1.6.0.8', '==')) {
                            $tpl = _PS_MODULE_DIR_ . 'fkcustomers/views/front/v1_6_0_7/address.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.9', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_0_9/address.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.11', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_0_11/address.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.13', '==') or version_compare(_PS_VERSION_, '1.6.0.14', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_0_13/address.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.1.0', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_1_0/address.tpl';
                        }else {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_1_1/address.tpl';
                        }
                    }

                    parent::setTemplate($tpl);
                }else {
                    parent::setTemplate($default_template);
                }
            }
        }
    }

}

?>
