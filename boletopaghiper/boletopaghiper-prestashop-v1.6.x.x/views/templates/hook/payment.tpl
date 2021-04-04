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

{if $total_boleto > 0}
<div class="row">
	<div class="col-xs-12 col-md-6">
		<p class="payment_module" id="boletopaghiper_payment_button">
			<a onclick="$(this).attr('disabled','disabled');" class="bankwire" href="{$link->getModuleLink('boletopaghiper', 'fiscal', ['tipo'=>'boleto'], true)|escape:'htmlall':'UTF-8'}" title="{l s='Pagar com Boleto' mod='boletopaghiper'}">
				Boleto Banc&aacute;rio <span id="total-boleto">{$frase}</span>
			</a>
		</p>
	</div>
</div>
{/if}