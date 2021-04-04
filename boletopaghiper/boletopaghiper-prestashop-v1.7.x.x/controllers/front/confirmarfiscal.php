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

class BoletoPagHiperConfirmarFiscalModuleFrontController extends ModuleFrontController
{

    public $display_column_left = false;
	public $display_column_higt = false;
    public $display_header = false;
    public $display_header_javascript = false;
    public $display_footer = false;
    
    public function postProcess()
    {
        //salva o fiscal do cliente
        $cliente = Context::getContext()->customer;

        //verifica se o cliente ja tem registro
        $fiscal = isset($_POST['fiscal'])?$_POST['fiscal']:'11111111111';
        $sql = "SELECT * FROM `"._DB_PREFIX_."paghiper` WHERE id_cliente = '".(int)$cliente->id."'";
        $row = Db::getInstance()->getRow($sql);
        if(!isset($row['fiscal'])){
            $query = "INSERT INTO `"._DB_PREFIX_."paghiper` (`id_cliente`, `fiscal`) VALUES ('".(int)$cliente->id."', '".$fiscal."');";
            Db::getInstance()->execute($query);
        }else{
            $query = "UPDATE `"._DB_PREFIX_."paghiper` SET `fiscal` = '".$fiscal."' WHERE `id_cliente` = '".(int)$cliente->id."'";
            Db::getInstance()->execute($query);
        }
        echo json_encode(array('fiscal'=>$fiscal));
    }
}
