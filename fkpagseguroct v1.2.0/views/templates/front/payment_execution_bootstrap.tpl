
{assign var="class_tab_1" value=""}
{assign var="class_tab_2" value=""}
{assign var="class_tab_3" value=""}

{if $cartao == "on"} 
	{assign var="class_tab_1" value="active"}
{elseif $boleto == "on"}
	{assign var="class_tab_2" value="active"}
{else}
	{assign var="class_tab_3" value="active"}
{/if}

{capture name=path}
    {l s='Pagamento através do PagSeguro' mod='fkpagseguroct'}
{/capture}

<div class="fkpagseguroct">
    <h4>
        {l s='Forma de Pagamento' mod='fkpagseguroct'}
    </h4>
    <div class="box">
    	<p>Obrigado por comprar conosco, para concluir escolha abaixo a forma de pagamento.</p>
    	<p><span class="fkpagseguroct-font-bold">Total a pagar: </span>{convertPrice price=$total}</p>
		<input type="hidden" name="fkpagseguroct_valor_pedido" id="fkpagseguroct_valor_pedido" value="{$total}">
    </div>
</div>

<br>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<script type="text/javascript">
    var urlImg = "{$url_img}";
    var urlFuncoes = "{$url_funcoes}";
    var dddValidos = "{$ddd_validos}";
    var parcelasSemJuros = "{$parcelas_sem_juros}"
</script>

{if $nbProducts <= 0}
    <p class="alert alert-warning">
        {l s='Seu carrinho está vazio.' mod='fkpagseguroct'}
    </p>
{else}
    <div class="fkpagseguroct-msg-erro" id="fkpagseguroct_msg_erro"></div>
    <div class="fkpagseguroct">
        <ul class="nav nav-tabs" data-tabs="tabs">
            {if $cartao == 'on'}
            	<li class="{$class_tab_1}"><a href="#tab_1" data-toggle="tab">{l s="Cartão de Crédito" mod="fkpagseguroct"}</a></li>
            {/if}
	    
            {if $boleto == 'on'}
            	<li class="{$class_tab_2}"><a href="#tab_2" data-toggle="tab">{l s="Boleto Bancário" mod="fkpagseguroct"}</a></li>
            {/if}
	    
            {if $transf == 'on'}
            	<li class="{$class_tab_3}"><a href="#tab_3" data-toggle="tab">{l s="Transferência Online" mod="fkpagseguroct"}</a></li>
            {/if}
        </ul>

        <div class="tab-content">
    	    {if $cartao == 'on'}
                <div class="tab-pane {$class_tab_1}" id="tab_1">
                    {include file="{$smarty.current_dir}/cartao.tpl"}
                </div>
    	    {/if}
			
    	    {if $boleto == 'on'}
                <div class="tab-pane {$class_tab_2}" id="tab_2">
                    {include file="{$smarty.current_dir}/boleto.tpl"}
                </div>
            {/if}
            
            {if $transf == 'on'}
                <div class="tab-pane {$class_tab_3}" id="tab_3">
                    {include file="{$smarty.current_dir}/transf.tpl"}
                </div>
            {/if}

        </div>
        
    </div>

    <p class="cart_navigation clearfix" id="cart_navigation" style="margin-top: 30px;">
        <a class="button-exclusive btn btn-default" href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
            <i class="icon-chevron-left"></i>{l s='Outras formas de pagamento' mod='fkpagseguroct'}
        </a>
    </p>


{/if}
