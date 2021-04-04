<?php


class AdminOrdersController extends AdminOrdersControllerCore {

    public function renderView() {

        parent::renderView();

        // TODO: alterar quando mudar versão do Prestashop
        if (version_compare(_PS_VERSION_, '1.6.0.5', '==')) {
            $this->base_tpl_view = 'view_1_6_0_5.tpl';
        }elseif (version_compare(_PS_VERSION_, '1.6.0.6', '==')) {
            $this->base_tpl_view = 'view_1_6_0_6.tpl';
        }elseif (version_compare(_PS_VERSION_, '1.6.0.7', '==') or version_compare(_PS_VERSION_, '1.6.0.8', '==')) {
            $this->base_tpl_view = 'view_1_6_0_7.tpl';
        }elseif (version_compare(_PS_VERSION_, '1.6.0.9', '==')) {
            $this->base_tpl_view = 'view_1_6_0_9.tpl';
        }elseif (version_compare(_PS_VERSION_, '1.6.0.10', '==') or version_compare(_PS_VERSION_, '1.6.0.11', '==')) {
            $this->base_tpl_view = 'view_1_6_0_10.tpl';
        }elseif (version_compare(_PS_VERSION_, '1.6.0.12', '==') or version_compare(_PS_VERSION_, '1.6.0.13', '==') or version_compare(_PS_VERSION_, '1.6.0.14', '==')) {
            $this->base_tpl_view = 'view_1_6_0_12.tpl';
        }elseif (version_compare(_PS_VERSION_, '1.6.1.0', '==')) {
            $this->base_tpl_view = 'view_1_6_1_0.tpl';
        }else {
            $this->base_tpl_view = 'view_1_6_1_1.tpl';
        }

        $helper = new HelperView($this);
        $helper->module = module::getInstanceByName('fkcustomers');
        $this->setHelperDisplay($helper);

        if (version_compare(_PS_VERSION_, '1.6.0.5', '>=') and version_compare(_PS_VERSION_, '1.6.0.9', '<=')){
            $helper->tpl_vars = $this->tpl_view_vars;
        }else {
            $helper->tpl_vars = $this->getTemplateViewVars();
        }

        if (!is_null($this->base_tpl_view)) {
            $helper->base_tpl = $this->base_tpl_view;
        }

        $view = $helper->generateView();

        return $view;
    }

}