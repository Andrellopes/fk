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

class BoletoPagHiperBoletoModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
	public $display_column_higt = false;
    public $ssl = true;
        
    public function SomenteNumero($a)
    {
		return preg_replace('/\D/', '', $a);
	}
    
    public function cpf_cnpj()
    {
        $sql = "SELECT * FROM `"._DB_PREFIX_."paghiper` WHERE id_cliente = '".(int)Context::getContext()->customer->id."'";
        $row = Db::getInstance()->getRow($sql);
        if(isset($row['fiscal']) && !empty($row['fiscal'])){
            return $row['fiscal'];
        }else{
            return '';
        }
    }
	
	public function CleanString($str) {
		$replaces = array(
			'S'=>'S', 's'=>'s', 'Z'=>'Z', 'z'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
			'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
			'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
			'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
			'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y'
		);
		
		return preg_replace('/[^0-9A-Za-z;,.\- ]/', '', strtoupper(strtr(trim($str), $replaces)));
	}
    
    public function gerar_boleto()
    {
        //dados do pedido
        $carrinho = Context::getContext()->cart;
        $link = Context::getContext()->link;
        $total = $carrinho->getOrderTotal(true, 3);
        $frete = $carrinho->getOrderTotal(true, 5);
        $produtos_array = $carrinho->getProducts();
        $desconto = $carrinho->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
        $endereco_id = $carrinho->id_address_invoice;
        $endereco = new Address((int)($endereco_id));
        $estado = new State((int)($endereco->id_state));
        $cliente = new Customer($carrinho->id_customer);
		
		//custom numero
        $numero = '*';
        $campo_numero = Configuration::get('BOLETOPAGHIPER_NUMERO');
        if(!empty($campo_numero)){
            $partes = explode('.',$campo_numero);
            $enderecoa = json_decode(json_encode($endereco),true);
            if(isset($enderecoa[$partes[1]]) && !empty($enderecoa[$partes[1]])){
                $numero = $enderecoa[$partes[1]];
            }
        }
		
		//dados do boleto
		$tel = !empty($endereco->phone)?$endereco->phone:$endereco->phone_mobile;
        $fiscal = preg_replace('/\D/', '', $this->cpf_cnpj());
        $json = array();
		$json['apiKey'] = trim(Configuration::get('BOLETOPAGHIPER_KEY'));
		$json['order_id'] = $carrinho->id;
		$json['partners_id'] = 'QWKN3MEV';
		$json['payer_email'] = $cliente->email;
		$json['payer_name'] = (preg_replace('/\s+/', ' ',$endereco->firstname.' '.$endereco->lastname));
		$json['payer_cpf_cnpj'] = $fiscal;
		$json['payer_phone'] = preg_replace('/\D/', '', $tel);
        $json['payer_street'] = $endereco->address1;
        $json['payer_district'] = $endereco->address2;
        $json['payer_city'] = $endereco->city;
        $json['payer_state'] = $estado->iso_code;
        $json['payer_zip_code'] = preg_replace('/\D/', '', $endereco->postcode);
        $json['payer_number'] = (!empty($numero)?$numero:'*');
        $json['payer_complement'] = '';
		$json['notification_url'] = Context::getContext()->link->getModuleLink('boletopaghiper', 'ipn', array('ajax'=>'true'), true);
		$json['discount_cents'] = number_format($desconto, 2, '', '');
		$json['shipping_price_cents'] = number_format($frete, 2, '', '');
		$json['days_due_date'] = (int)Configuration::get('BOLETOPAGHIPER_VALIDADE');
        $json['type_bank_slip'] = 'boletoA4';
		
		//produtos
		$i=1;
		foreach($produtos_array AS $k=>$v){
            $json['items'][$i]['item_id'] = $v['id_product'];
            $json['items'][$i]['description'] =  $this->CleanString(utf8_encode((isset($v['product_name'])?$v['product_name']:$v['name'])));
            $json['items'][$i]['price_cents'] = number_format($v['price_wt'], 2, '', '');
            $json['items'][$i]['quantity'] = $v['quantity'];
            $i++;
		}
        //print_r($json);
		//exit;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.paghiper.com/transaction/create/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Accept: application/json',
			'Content-Type: application/json'
		));
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $retorno = @json_decode($response,true);
        if(!$retorno){
            $retorno = $response;
        }
        curl_close($ch);
        
        return array('status'=>$httpcode,'enviado'=>$json,'retorno'=>$retorno);
	}
    
    public function postProcess()
    {
		
        $carrinho = Context::getContext()->cart;
		//se tem desconto 
		$total = (float)Configuration::get("BOLETOPAGHIPER_TAXA_BOLETO");
		if($total > 0){
			$this->module->aplicarDesconto($carrinho);
			$carrinho = Context::getContext()->cart;
		}
        $pague = $this->gerar_boleto();
		
        if(($pague['status']==200 || $pague['status']==201) && isset($pague['retorno']['create_request']) && $pague['retorno']['create_request']['result']=='success'){
            
            $link_boleto = $pague['retorno']['create_request']['bank_slip']['url_slip_pdf'];
            $transacao = $pague['retorno']['create_request']['transaction_id'];
            
            //tenta criar
            try {
                
                //vars
                $extraVars = array(
                    '{segunda_via}' => $link_boleto,
                    '{link_boleto}'    => $link_boleto,
                );

                //cria o pedido
                $cliente = Context::getContext()->customer;
                $frete = $carrinho->getOrderTotal(true, 5);
                $total = $carrinho->getOrderTotal(true, Cart::BOTH);
                $this->module->validateOrder($carrinho->id, Configuration::get('BOLETOPAGHIPER_INICIADA'), $total, $this->module->displayName, null, $extraVars, null, false, $cliente->secure_key);

                //consulta o pedido criado
                $order = new Order($this->module->currentOrder);

                //cria um log para o pedido
                $message = "-----------------\nID PagHiper: ".$transacao."\nID Boleto: ".str_pad($carrinho->id, 11, "0", STR_PAD_LEFT)."\nStatus: Aguardando Pagamento\n-----------------";
                $msg = new Message();
                $message = strip_tags($message, '<br>');
                if (Validate::isCleanHtml($message)){
                    $msg->message = $message;
                    $msg->id_order = intval($order->id);
                    $msg->private = 1;
                    $msg->add();
                }
                
                //cria o registro de banco de dados 
                Db::getInstance()->execute("INSERT INTO `"._DB_PREFIX_."paghiper_boleto` (`id_pedido`, `transacao`, `status`, `link_boleto`) VALUES ('".$this->module->currentOrder."', '".$transacao."', 'aguardando', '".$link_boleto."');");

                //url de confirmacao
                $confirmar = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'index.php?controller=order-confirmation&id_cart='.(int)($carrinho->id).'&id_module='.(int)($this->module->id).'&id_order='.$this->module->currentOrder.'&meio=boleto&transacao='.$transacao.'&key='.$cliente->secure_key;
                Tools::redirect($confirmar);
                exit;

            } catch (Exception $e) {
                $erro = 'Erro ao tentar criar pedido PagHiper do carrinho '.$carrinho->id.'! - '.$e->getMessage();
                PrestaShopLogger::addLog($erro, 2);
                $this->context->smarty->assign(array(
                    'cart_id' => Context::getContext()->cart->id,
                    'cliente' => Context::getContext()->customer,
                    'erro' => $erro,
                    'secure_key' => Context::getContext()->customer->secure_key,
                ));
                return $this->setTemplate('module:boletopaghiper/views/templates/front/error.tpl');
            }
            
        }elseif(isset($pague['retorno']['create_request']['response_message'])){
            $erro_boleto = $pague['retorno']['create_request']['response_message'];
            PrestaShopLogger::addLog($erro_boleto, 2);
            PrestaShopLogger::addLog(print_r($pague,true), 2);
            $this->context->smarty->assign(array(
                'cart_id' => Context::getContext()->cart->id,
                'cliente' => Context::getContext()->customer,
                'erro' => $erro_boleto,
                'secure_key' => Context::getContext()->customer->secure_key,
            ));
            return $this->setTemplate('module:boletopaghiper/views/templates/front/error.tpl');
        }else{
            $erro_boleto = 'Erro ao gerar Boleto junto ao PagHiper (ver logs)!';
            PrestaShopLogger::addLog($erro_boleto, 2);
            PrestaShopLogger::addLog(print_r($pague,true), 2);
            $this->context->smarty->assign(array(
                'cart_id' => Context::getContext()->cart->id,
                'cliente' => Context::getContext()->customer,
                'erro' => $erro_boleto,
                'secure_key' => Context::getContext()->customer->secure_key,
            ));
            return $this->setTemplate('module:boletopaghiper/views/templates/front/error.tpl');
        }
    }
}
