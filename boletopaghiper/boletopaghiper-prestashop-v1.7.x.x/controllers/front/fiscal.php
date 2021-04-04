<?php
/**
* 2007-2015 PrestaShop
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
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class BoletoPagHiperFiscalModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
	public $display_column_higt = false;
    public $ssl = true;
        
    public function SomenteNumero($a) {
		return preg_replace('/\D/', '', $a);
	}
    
    public function postProcess()
    {
        //fix problemas ssl
        $this->ssl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')?true:false;
        
        //dados cliente e endereco e junta os dados
        $cliente = Context::getContext()->customer;
        $endereco = new Address((int)(Context::getContext()->cart->id_address_invoice));
        $array_cobranca = array_merge((array)$cliente,(array)$endereco);
        
        //verifica na tabela temporaria
        $sql = "SELECT * FROM `"._DB_PREFIX_."paghiper` WHERE id_cliente = '".(int)$cliente->id."'";
        $row = Db::getInstance()->getRow($sql);
        //tipo
        $tipo = isset($_GET['tipo'])?$_GET['tipo']:'boleto';
        
        //valida o cpf/cnpj
        $cpf_cnpj = isset($row['fiscal'])?$row['fiscal']:'';
        if(strlen($cpf_cnpj)==11 || strlen($cpf_cnpj)==14){
           //redireciona ao pagamento
           Tools::redirect(Context::getContext()->link->getModuleLink('boletopaghiper', $tipo, array(), true));
        }else{
        $campo_fiscal = Configuration::get('BOLETOPAGHIPER_FISCAL');
        if(!empty($campo_fiscal)){
            $partes = explode('.',$campo_fiscal);
			if(isset($array_cobranca[$partes[1]])){
				$cpfCnpjComprador = $this->SomenteNumero($array_cobranca[$partes[1]]);
                if(strlen($cpfCnpjComprador)==11 || strlen($cpfCnpjComprador)==14){
                    //atualiza a tabela
                    $cpf_cnpj = $cpfCnpjComprador;
                    $valido = $this->module->validar_fiscal($cpf_cnpj);
                    if($valido){
                    //caso nao tenha registro
                    if(!isset($row['fiscal'])){
                        $query = "INSERT INTO `"._DB_PREFIX_."paghiper` (`id_cliente`, `fiscal`) VALUES ('".(int)$cliente->id."', '".$cpf_cnpj."');";
                        Db::getInstance()->execute($query);
                    }else{
                        $query = "UPDATE `"._DB_PREFIX_."paghiper` SET `fiscal` = '".$cpf_cnpj."' WHERE `id_cliente` = '".(int)$cliente->id."'";
                        Db::getInstance()->execute($query);
                    }
                    //redireciona ao pagamento
                    Tools::redirect(Context::getContext()->link->getModuleLink('boletopaghiper', $tipo, array(), true));
                    }
                }
			}
        }
        }

        $this->context->smarty->assign(array(
            'cart_id' => Context::getContext()->cart->id,
            'fiscal' => $cpf_cnpj,
            'url_loja' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__,
            'tipo' => $tipo,
            'secure_key' => Context::getContext()->customer->secure_key,
        ));

        return $this->setTemplate('module:boletopaghiper/views/templates/front/fiscal.tpl');
    }
}
