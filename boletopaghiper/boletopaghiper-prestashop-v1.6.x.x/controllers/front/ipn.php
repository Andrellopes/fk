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

class BoletoPagHiperIpnModuleFrontController extends ModuleFrontController
{

    public $display_column_left = false;
	public $display_column_higt = false;
    public $display_header = false;
    public $display_header_javascript = false;
    public $display_footer = false;
    public $ssl = true;
    
    public function postProcess()
    {
		$transacao = '';
		$id_notificacao = '';
		if(isset($_POST['transaction_id'])){
			$transacao = $_POST['transaction_id'];
		}
		if(isset($_POST['notification_id'])){
			$id_notificacao = $_POST['notification_id'];
		}
		if(!empty($transacao) && !empty($id_notificacao) && $_POST['apiKey']==trim(Configuration::get('BOLETOPAGHIPER_KEY'))){
			$json = array();
			$json['token'] = trim(Configuration::get('BOLETOPAGHIPER_TOKEN'));
			$json['apiKey'] = trim(Configuration::get('BOLETOPAGHIPER_KEY'));
			$json['transaction_id'] = trim($transacao);
			$json['notification_id'] = trim($id_notificacao);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.paghiper.com/transaction/notification/');
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
			$retorno = @json_decode($response,true);
			curl_close($ch);
			if(isset($retorno['status_request']['result']) && $retorno['status_request']['result']=='success'){
				
				$pedido = Db::getInstance()->getRow("SELECT * FROM `"._DB_PREFIX_."orders` WHERE module = 'boletopaghiper' AND id_cart = '".(int)$retorno['status_request']['order_id']."' AND current_state = '".Configuration::get('BOLETOPAGHIPER_INICIADA')."'"); 
				$order = new Order($pedido['id_order']);
				
				if($order){
					if($retorno['status_request']['status']=='paid'){
						$history = new OrderHistory();
						$history->id_order = (int)$order->id;
						$history->changeIdOrderState(Configuration::get('BOLETOPAGHIPER_PAGO'), $order);
						$history->addWithemail(true, null);
					}elseif($retorno['status_request']['status']=='canceled'){
						$history = new OrderHistory();
						$history->id_order = (int)$order->id;
						$history->changeIdOrderState(Configuration::get('BOLETOPAGHIPER_NAO_PAGA'), $order);
						$history->addWithemail(true, null);
					}
				}
			}
		}
		echo 'IPN PagHiper';
    }
}
