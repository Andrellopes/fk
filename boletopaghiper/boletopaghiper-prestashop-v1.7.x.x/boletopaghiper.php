<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

class BoletoPagHiper extends PaymentModule
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'boletopaghiper';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'PagHiper.com';
        $this->need_instance = 0;
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->l('Boleto PagHiper');
        $this->description = $this->l('Módulo de pagamentos online Boleto PagHiper.');
        $this->confirmUninstall = $this->l('Tem certeza em remover o módulo?');
        $this->limited_currencies = array('BRL');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (extension_loaded('curl') == false)
        {
            $this->_errors[] = $this->l('O curl é obrigatorio!');
            return false;
        }
        include(dirname(__FILE__).'/sql/install.php');
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
			$this->registerHook('paymentOptions') &&
            $this->registerHook('payment') &&
            $this->registerHook('paymentReturn') &&
            $this->registerHook('actionPaymentConfirmation') &&
            $this->registerHook('displayAdminOrder') &&
            $this->registerHook('displayOrderDetail') &&
            $this->registerHook('displayPayment') &&
            $this->registerHook('displayPaymentReturn');
    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    public function getContent()
    {
		$output = '';
        if (((bool)Tools::isSubmit('submitModule')) == true) {
			$output .= $this->displayConfirmation($this->l('Dados do módulo atualizados com sucesso!'));
            $this->postProcess();
        }
        $url_loja = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
        $this->context->smarty->assign('module_dir', $this->_path);
        $this->context->smarty->assign('url_loja', $url_loja);
        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        return $output.$this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    private function campos_extras()
    {
        //querys
        $campos[] = array('id'=>'','campo'=>'Cliente informa manual');
        $clientes = Db::getInstance()->executeS("SHOW COLUMNS FROM `" . _DB_PREFIX_ . "customer`");	
        foreach($clientes AS $k=>$v){
            $input = _DB_PREFIX_.'customer.'.$v['Field'].'';
            $campos[] = array('id'=>$input,'campo'=>$input);
        }
        $enderecos = Db::getInstance()->executeS("SHOW COLUMNS FROM `" . _DB_PREFIX_ . "address`");
        foreach($enderecos AS $k=>$v){
            $input = _DB_PREFIX_.'address.'.$v['Field'].'';
            $campos[] = array('id'=>$input,'campo'=>$input);
        }
        return $campos;
    }
    
    protected function getConfigForm()
    {
		$extras = $this->campos_extras();
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Configurações'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 6,
                        'type' => 'text',
						'required' => true,
                        'desc' => $this->l('Obtenha em sua conta PagHiper > Credenciais.'),
                        'name' => 'BOLETOPAGHIPER_KEY',
                        'label' => $this->l('Api Key'),
                    ),
                    array(
                        'col' => 6,
                        'type' => 'text',
						'required' => true,
                        'desc' => $this->l('Obtenha em sua conta PagHiper > Credenciais.'),
                        'name' => 'BOLETOPAGHIPER_TOKEN',
                        'label' => $this->l('Api Token'),
                    ),
                    array(
                        'type' => 'select',
						'required' => true,
                        'name' => 'BOLETOPAGHIPER_FISCAL',
                        'desc' => $this->l('Campo customizado qual o CPF/CNPJ é salvo na loja!'),
                        'label' => $this->l('Origem CPF/CNPJ'),
                        'options' => array(
                            'query' => $extras,
                            'id' => 'id',
                            'name' => 'campo'
                        )
                    ),
                    array(
                        'type' => 'select',
						'required' => true,
                        'name' => 'BOLETOPAGHIPER_NUMERO',
                        'desc' => $this->l('Campo customizado qual o Número do endereço é salvo na loja!'),
                        'label' => $this->l('Origem Número do endereço'),
                        'options' => array(
                            'query' => $extras,
                            'id' => 'id',
                            'name' => 'campo'
                        )
                    ),
                    array(
                        'col' => 3,
						'required' => true,
                        'type' => 'text',
                        'class' => 'dinheiro',
                        'default' => 0.00,
                        'desc' => $this->l('Desconto para pagamento por Boleto'),
                        'name' => 'BOLETOPAGHIPER_TAXA_BOLETO',
                        'label' => $this->l('Desconto Boleto %'),
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
						'required' => true,
                        'default' => 5,
                        'desc' => $this->l('Prazo de validade em dias para o Boleto.'),
                        'name' => 'BOLETOPAGHIPER_VALIDADE',
                        'label' => $this->l('Validade em Dias'),
                    ),
                    array(
                        'type' => 'select',
						'required' => true,
                        'name' => 'BOLETOPAGHIPER_INICIADA',
                        'desc' => $this->l('Status customizado ou já existente!'),
                        'label' => $this->l('Status Aguardando Pagamento'),
                        'options' => array(
                            'query' => $this->GetStatusNomes(),
                            'id' => 'id_order_state',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
						'required' => true,
                        'name' => 'BOLETOPAGHIPER_PAGO',
                        'desc' => $this->l('Status customizado ou já existente!'),
                        'label' => $this->l('Status Pago'),
                        'options' => array(
                            'query' => $this->GetStatusNomes(),
                            'id' => 'id_order_state',
                            'name' => 'name'
                        )
                    ),
                    array(
                        'type' => 'select',
						'required' => true,
                        'name' => 'BOLETOPAGHIPER_NAO_PAGA',
                        'desc' => $this->l('Status customizado ou já existente!'),
                        'label' => $this->l('Status Não Pago'),
                        'options' => array(
                            'query' => $this->GetStatusNomes(),
                            'id' => 'id_order_state',
                            'name' => 'name'
                        )
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Salvar'),
                ),
            ),
        );
    }

    protected function getConfigFormValues()
    {
        $inputs = array();
        $form   = $this->getConfigForm();
        foreach ($form['form']['input'] as $v) {
            $chave          = $v['name'];
            $inputs[$chave] = Configuration::get($chave, '');
        }
        return $inputs;
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }
	
	public function hookPaymentOptions($params)
    {
		if (!$this->active)
			return;
		
		//verifica se e uma moeda aceita
        $currency_id = $params['cart']->id_currency;
        $currency = new Currency((int)$currency_id);
        if (in_array($currency->iso_code, $this->limited_currencies) == false){
            return false;
        }
		
		$opcoes = array();
		
		$boleto = new PaymentOption();
		$des_bol = (int)Configuration::get('BOLETOPAGHIPER_TAXA_BOLETO');
		if($des_bol > 0){
			$boleto->setCallToActionText($this->trans('Boleto Bancário', array(), 'Modules.BoletoPagHiper.Boleto').' (desconto '.(int)$des_bol.'%)');
		}else{
			$boleto->setCallToActionText($this->trans('Boleto Bancário', array(), 'Modules.BoletoPagHiper.Boleto'));
		}
		$boleto->setAction($this->context->link->getModuleLink($this->name, 'fiscal', ['tipo'=>'boleto'], true));
		$opcoes[] = $boleto;
		
		return $opcoes;
	}
    
    public function GetStatusNomes() 
    {
		global $cookie;
		return Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'order_state` AS a,`'._DB_PREFIX_.'order_state_lang` AS b WHERE b.id_lang = "'.$cookie->id_lang.'" AND a.deleted = "0" AND a.id_order_state=b.id_order_state');
	}
    
	public function aplicarDesconto($cart)
	{
		if(CartRule::cartRuleExists('Desconto para o carrinho #'.$cart->id)){
            return false;
        }
        $rule = 'V0C'.(int)($cart->id_customer).'O'.(int)($cart->id);
        if(CartRule::cartRuleExists($rule)){
            return false;
        }
        $total = (float)Configuration::get("BOLETOPAGHIPER_TAXA_BOLETO");
        if($total > 0){
            $tipoDesconto = 1;
            $cart_rule = new CartRule();
            $cart_rule->description = 'Desconto para o carrinho #'.$cart->id.'';
            $language_ids = Language::getIDs(false);
            foreach ($language_ids as $id_lang) {
                // Define a temporary name
                $cart_rule->name[$id_lang] = sprintf('V0C%1$dO%2$d', $cart->id_customer, $cart->id);
            }
            // Define a temporary code
            $cart_rule->code = sprintf('V0C%1$dO%2$d', $cart->id_customer, $cart->id);
            $cart_rule->quantity = 1;
            $cart_rule->quantity_per_user = 1;
            // Specific to the customer
            $cart_rule->id_customer = $cart->id_customer;
            $now = time();
            $cart_rule->date_from = date('Y-m-d H:i:s', $now);
            $cart_rule->date_to = date('Y-m-d H:i:s', strtotime('+2 day'));
            $cart_rule->partial_use = 1;
            $cart_rule->active = 1;
            $cart_rule->reduction_amount = ($tipoDesconto == 2 ? $total : '');
            $cart_rule->reduction_percent = ($tipoDesconto == 1 ? $total : '');
            $cart_rule->reduction_tax = 0;
            $cart_rule->minimum_amount_currency = $cart->id_currency;
            $cart_rule->reduction_currency = $cart->id_currency;
            if($cart_rule->add()){
                $cart->addCartRule((int)$cart_rule->id);
            }
        }
	}
    
    public function validar_fiscal($fiscal)
    {
        require_once(dirname(__FILE__).'/include/class-valida-cpf-cnpj.php');
        $cpf_cnpj = new ValidaCPFCNPJ($fiscal);
        return $cpf_cnpj->valida();
    }
    
    public function hookdisplayAdminOrder($params){
        $sql = "SELECT * FROM `"._DB_PREFIX_."paghiper_boleto` WHERE id_pedido = '".(int)$params['id_order']."'";
        $boleto = Db::getInstance()->getRow($sql);
        $html = '';
        if(isset($boleto['link_boleto'])){
            $html .= '<div class="panel">';
            $html .= 'O c&oacute;digo da transa&ccedil;&atilde;o deste boleto junto a PagHiper &eacute; '.$boleto['transacao'].', para imprimir novamente <a href="'.$boleto['link_boleto'].'" target="_blank"><b>clique aqui</b></a>!';
            $html .= '</div>';
        }
        return $html;
	}
    
    public function hookdisplayOrderDetail($params){
        $sql = "SELECT * FROM `"._DB_PREFIX_."paghiper_boleto` WHERE id_pedido = '".(int)$params['order']->id."'";
        $boleto = Db::getInstance()->getRow($sql);
        $html = '';
        if(isset($boleto['link_boleto'])){
            $html .= '<div class="box">';
            $html .= 'O c&oacute;digo da transa&ccedil;&atilde;o de seu boleto &eacute; '.$boleto['transacao'].', para imprimir novamente o seu boleto <a href="'.$boleto['link_boleto'].'" target="_blank"><b>clique aqui</b></a>!';
            $html .= '</div>';
        }
        return $html;
    }

    public function hookPaymentReturn($params)
    {
        if ($this->active == false)
            return;
        
        $order = new Order((int)$_GET['id_order']);

        //dados boleto 
        $sql = "SELECT * FROM `"._DB_PREFIX_."paghiper_boleto` WHERE id_pedido = '".(int)$order->id."'";
        $boleto = Db::getInstance()->getRow($sql);

        $this->smarty->assign(array(
            'id_order' => $order->id,
            'boleto' => $boleto,
            'meio' => 'boleto',
            'reference' => $order->reference,
            'params' => $params,
            'total' => Tools::displayPrice(
                    $params['order']->getOrdersTotalPaid(),
                    new Currency($params['order']->id_currency),
                    false
                ),
        ));

        return $this->display(__FILE__, 'views/templates/hook/confirmation.tpl');
    }
}
