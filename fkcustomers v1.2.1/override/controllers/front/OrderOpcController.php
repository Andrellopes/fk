<?php

class OrderOpcController extends OrderOpcControllerCore {

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
                    // Verifica se é o template a ser alterado visto que neste Controller existem retorno de vários templates
                    if (strpos($default_template, 'order-opc.tpl') === false) {
                        parent::setTemplate($default_template);
                    }

                    // Verifica se existe tpl customizado
                    $custom = _PS_MODULE_DIR_.'fkcustomers/views/custom/order-opc.tpl';

                    if (file_exists($custom)) {
                        $tpl = $custom;
                    }else {
                        // Seleciona tpl do fkcustomers conforme versao
                        if (version_compare(_PS_VERSION_, '1.6.0.5', '==')) {
                            $tpl = _PS_MODULE_DIR_ . 'fkcustomers/views/front/v1_6_0_5/order-opc.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.6', '==')) {
                            $tpl = _PS_MODULE_DIR_ . 'fkcustomers/views/front/v1_6_0_6/order-opc.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.7', '==') or version_compare(_PS_VERSION_, '1.6.0.8', '==')) {
                            $tpl = _PS_MODULE_DIR_ . 'fkcustomers/views/front/v1_6_0_7/order-opc.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.9', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_0_9/order-opc.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.11', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_0_11/order-opc.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.0.13', '==') or version_compare(_PS_VERSION_, '1.6.0.14', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_0_13/order-opc.tpl';
                        }elseif (version_compare(_PS_VERSION_, '1.6.1.0', '==')) {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_1_0/order-opc.tpl';
                        }else {
                            $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_1_1/order-opc.tpl';
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

