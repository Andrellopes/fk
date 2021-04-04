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

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div class="box">
<style>
@media screen and (max-width: 600px) {
  .input_100P {
	width:100% !important;
  }
}
</style>

	<h3>{l s='Confirmar dados obrigatorios' mod='boletopaghiper'}:</h3>
	<ul class="alert alert-info">
			<li>{l s='Precisamos que confirme seu CPF/CNPJ para que possamos gerá seu pedido na loja.' mod='boletopaghiper'}.</li>
	</ul>
   
<form class="form-horizontal">
  <div class="form-group">
    <label for="fiscal" class="col-sm-2 control-label">CPF/CNPJ</label>
    <div class="col-sm-10">
      <input type="text" onkeypress="return isNumberKey(event)" value="{$fiscal}" class="form-control input_100P" name="fiscal" id="fiscal" placeholder="CPF/CNPJ">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="button" id="botao-fiscal" onclick="salvar_fiscal()" class="btn btn-info">CONTINUAR</button>
    </div>
  </div>
</form>   
    
</div>

<script>
//urls do sistema
var url_modulo = "{$url_loja}index.php?fc=module&module=boletopaghiper&controller=confirmarfiscal";
var url_redirecionar = "{$url_loja}index.php?fc=module&module=boletopaghiper&controller=boleto";
{literal}
//funcoes
function salvar_fiscal()
{
    //valida
	var fiscal = $('#fiscal').val();
	var fiscal_valido = validarCpfCnpj(fiscal);
	if(!fiscal_valido){
        $('#fiscal').focus();
        alert('Digite um CPF/CNPJ valido!');
        return false;
	}
    //se ok salva
    $('#botao-fiscal').text('Salvando...');
    $.ajax({
        type: 'post',
        data: {fiscal: fiscal},
        url: url_modulo,
        cache: false,
        success: function() {
            location = url_redirecionar;
            return true;
        }
	});
	return true;
}
function isNumberKey(evt)
{
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57)){
		return false;
	}
	return true;
}
function validaCPF(s) 
{
	var c = s.substr(0,9);
	var dv = s.substr(9,2);
	var d1 = 0;
	for (var i=0; i<9; i++) {
		d1 += c.charAt(i)*(10-i);
 	}
	if (d1 == 0) return false;
	d1 = 11 - (d1 % 11);
	if (d1 > 9) d1 = 0;
	if (dv.charAt(0) != d1){
		return false;
	}
	d1 *= 2;
	for (var i = 0; i < 9; i++)	{
 		d1 += c.charAt(i)*(11-i);
	}
	d1 = 11 - (d1 % 11);
	if (d1 > 9) d1 = 0;
	if (dv.charAt(1) != d1){
		return false;
	}
    return true;
}
function validaCNPJ(CNPJ) 
{
	var a = new Array();
	var b = new Number;
	var c = [6,5,4,3,2,9,8,7,6,5,4,3,2];
	for (i=0; i<12; i++){
		a[i] = CNPJ.charAt(i);
		b += a[i] * c[i+1];
	}
	if ((x = b % 11) < 2) { a[12] = 0 } else { a[12] = 11-x }
	b = 0;
	for (y=0; y<13; y++) {
		b += (a[y] * c[y]);
	}
	if ((x = b % 11) < 2) { a[13] = 0; } else { a[13] = 11-x; }
	if ((CNPJ.charAt(12) != a[12]) || (CNPJ.charAt(13) != a[13])){
		return false;
	}
	return true;
}

function validarCpfCnpj(valor) 
{
	var s = (valor).replace(/\D/g,'');
	var tam=(s).length;
	if (!(tam==11 || tam==14)){
		return false;
	}
	if (tam==11 ){
		if (!validaCPF(s)){
			return false;
		}
		return true;
	}		
	if (tam==14){
		if(!validaCNPJ(s)){
			return false;			
		}
		return true;
	}
}
{/literal}
</script>
