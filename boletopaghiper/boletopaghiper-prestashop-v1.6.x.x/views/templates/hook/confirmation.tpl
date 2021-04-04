{*
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
*}

<h4>{l s='Resultado de sua transação!' mod='boletopaghiper'}</h4>
<p>

	- {l s='Valor' mod='boletopaghiper'} : <span class="price"><strong>{$total}</strong></span>
	<br />- {l s='Referência' mod='boletopaghiper'} : <span class="reference"><strong>{$reference|escape:'html':'UTF-8'}</strong></span>
    <br />- {l s='Status' mod='boletopaghiper'} : <span class="status"><strong>Aguardando Pagamento</strong></span>
    <br />- {l s='Forma de Pagamento' mod='boletopaghiper'} : <span class="venda"><strong>Boleto Banc&aacute;rio</strong></span>
    <br />- {l s='Transação' mod='boletopaghiper'} : <span class="venda"><strong>{$boleto['transacao']}</strong></span>

    <br><br><a class="btn btn-success" href="{$boleto['link_boleto']}" target="_blank"><i class="icon-print"></i> Imprimir Boleto de Pagamento</a>
    
	<br /><br />{l s='Enviamos um e-mail com detalhes de seu pedido.' mod='boletopaghiper'}
	<br /><br />{l s='Para qualquer duvida ou informação ' mod='boletopaghiper'} <a href="{$link->getPageLink('contact', true)|escape:'html':'UTF-8'}">{l s='clique aqui e entre em contato com nosso atendimento.' mod='boletopaghiper'}</a>
</p>

<hr />