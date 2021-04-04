<?php

class AuthController extends AuthControllerCore {

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

    protected function processSubmitAccount() {

        include_once(_PS_MODULE_DIR_.'fkcustomers/models/FKcustomersClass.php');

        // Instancia FKcustomersClass
        $fkcustomersClass = new FKcustomersClass();

        // Valida CPF/CNPJ
        $cpf_cnpj = Tools::getValue('cpf_cnpj');

        if (!$cpf_cnpj) {
            if (Tools::getValue('tipo') == 'pf') {
                $this->errors[] = Tools::displayError('O campo CPF é obrigatório.');
            }else {
                $this->errors[] = Tools::displayError('O campo CNPJ é obrigatório.');
            }
        }else {
            if (Configuration::get('FKCUSTOMERS_DUPL_CPF_CNPJ') == 'on') {

                if ($fkcustomersClass->duplicidadeCPF_CNPJ($cpf_cnpj, '0')) {
                    if (Tools::getValue('tipo') == 'pf') {
                        $this->errors[] = Tools::displayError('CPF já cadastrado.');
                    }else {
                        $this->errors[] = Tools::displayError('CNPJ já cadastrado.');
                    }
                }
            }
        }

        // Valida RG/IE
        if (!Tools::getValue('rg_ie')) {
            if (Tools::getValue('tipo') == 'pf') {
                if (Configuration::get('FKCUSTOMERS_RG_REQ') == 'on') {
                    $this->errors[] = Tools::displayError('O campo RG é obrigatório.');
                }
            }else {
                if (Configuration::get('FKCUSTOMERS_IE_REQ') == 'on') {
                    $this->errors[] = Tools::displayError('O campo IE é obrigatório.');
                }
            }
        }

        // Valida Numero/Telefone/Celular se for One Step e Modo Completo
        if (Configuration::get('PS_REGISTRATION_PROCESS_TYPE') or $this->ajax) {

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

        }

        parent::processSubmitAccount();
    }

    // TODO: alterar função quando mudar versão do Prestashop
    public function setTemplate($default_template) {

        if ($this->useMobileTheme()) {
            $this->setMobileTemplate($default_template);
        } else {
            $template = $this->getOverrideTemplate();
            if ($template) {
                parent::setTemplate($template);
            } else {
                if (Configuration::get('FKCUSTOMERS_MODO') != '1') {
                    parent::setTemplate($default_template);
                }else {
                    if (Configuration::get('FKCUSTOMERS_MODO') == '1') {
                        // Verifica se existe tpl customizado
                        $custom = _PS_MODULE_DIR_.'fkcustomers/views/custom/authentication.tpl';

                        if (file_exists($custom)) {
                            $tpl = $custom;
                        }else {
                            // Seleciona tpl do fkcustomers conforme versao
                            if (version_compare(_PS_VERSION_, '1.6.0.5', '==')) {
                                $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_0_5/authentication.tpl';
                            }elseif (version_compare(_PS_VERSION_, '1.6.0.6', '==')) {
                                $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_0_6/authentication.tpl';
                            }elseif (version_compare(_PS_VERSION_, '1.6.0.7', '==') or version_compare(_PS_VERSION_, '1.6.0.8', '==')) {
                                $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_0_7/authentication.tpl';
                            }elseif (version_compare(_PS_VERSION_, '1.6.0.9', '==')) {
                                $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_0_9/authentication.tpl';
                            }elseif (version_compare(_PS_VERSION_, '1.6.0.11', '==')) {
                                $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_0_11/authentication.tpl';
                            }elseif (version_compare(_PS_VERSION_, '1.6.0.13', '==') or version_compare(_PS_VERSION_, '1.6.0.14', '==')) {
                                $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_0_13/authentication.tpl';
                            }elseif (version_compare(_PS_VERSION_, '1.6.1.0', '==')) {
                                $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_1_0/authentication.tpl';
                            }else {
                                $tpl = _PS_MODULE_DIR_.'fkcustomers/views/front/v1_6_1_1/authentication.tpl';
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

}

