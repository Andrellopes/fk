
{assign var="class_tab_1" value=""}
{assign var="class_tab_2" value=""}
{assign var="class_tab_3" value=""}

{if $cartao == "on"}
    {assign var="class_tab_1" value="selected"}
{elseif $boleto == "on"}
    {assign var="class_tab_2" value="selected"}
{else}
    {assign var="class_tab_3" value="selected"}
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

    <ul id="menuTab_fkpagseguroct">
        {if $cartao == 'on'}
            <li id="menuTab1" class="menuTabButton_fkpagseguroct {$class_tab_1}">{l s="Cartão de Crédito" mod="fkpagseguroct"}</li>
        {/if}

        {if $boleto == 'on'}
            <li id="menuTab2" class="menuTabButton_fkpagseguroct {$class_tab_2}">{l s="Boleto Bancário" mod="fkpagseguroct"}</li>
        {/if}

        {if $transf == 'on'}
            <li id="menuTab3" class="menuTabButton_fkpagseguroct {$class_tab_3}">{l s="Transferência Online" mod="fkpagseguroct"}</li>
        {/if}
    </ul>

    <div id="tabList_fkpagseguroct">
        <div id="menuTab1_fkpagseguroct" class="tabItem_fkpagseguroct">
            {if $cartao == 'on'}
                {include file="{$smarty.current_dir}/cartao.tpl"}
            {/if}
        </div>

        <div id="menuTab2_fkpagseguroct" class="tabItem_fkpagseguroct">
            {if $boleto == 'on'}
                {include file="{$smarty.current_dir}/boleto.tpl"}
            {/if}
        </div>

        <div id="menuTab3_fkpagseguroct" class="tabItem_fkpagseguroct">
            {if $transf == 'on'}
                {include file="{$smarty.current_dir}/transf.tpl"}
            {/if}
        </div>
    </div>

    <p class="cart_navigation clearfix" id="cart_navigation" style="margin-top: 30px;">
        <a class="button-exclusive btn btn-default" href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}">
            <i class="icon-chevron-left"></i>{l s='Outras formas de pagamento' mod='fkpagseguroct'}
        </a>
    </p>

{/if}

<script>
    $(document).ready(function(){
        $("#menuTab1_fkpagseguroct").addClass("selected");
    })

    $(".menuTabButton_fkpagseguroct").click(function () {
        $(".menuTabButton_fkpagseguroct.selected").removeClass("selected");
        $(this).addClass("selected");
        $(".tabItem_fkpagseguroct.selected").removeClass("selected");
        $("#" + this.id + "_fkpagseguroct").addClass("selected");
    });
</script>



