
<div class="row">

    {if $ps_version == '1.6.0.5' || $ps_version == '1.6.0.6' || $ps_version == '1.6.0.7' || $ps_version == '1.6.0.8' || $ps_version == '1.6.0.9'}
        <div class="col-xs-12 col-md-6 fkpagseguroct-payment">
    {else}
        <div class="col-xs-12 fkpagseguroct-payment">
    {/if}
            <p class="payment_module">
                <a class="fkpagseg" href="{$link->getModuleLink('fkpagseguroct', 'payment', [], true)|escape:'html':'UTF-8'}" title="{l s='Pagamento através do PagSeguro' mod='fkpagseguroct'}">
                    {l s='Pagamento através do PagSeguro' mod='fkpagseguroct'} <span>{l s='(várias formas de pagamentos disponíveis)' mod='fkpagseguroct'}</span>
                </a>
            </p>
        </div>

</div>


