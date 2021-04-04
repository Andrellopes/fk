
{capture name=path}
    {l s='Pagamento através do PagSeguro' mod='fkpagseguroct'}
{/capture}

<div class="fkpagseguroct">
    <h4>
        {l s='Erro na transação de pagamento' mod='fkpagseguroct'}
    </h4>
    <div class="fkpagseguroct-panel" id="fkpagseguroct_erro_pagseguro">

    	{$fkpagseguroct_msg_2}
		<br>
		{$fkpagseguroct_erro}
		<br><br>

        <div class="fkpagseguroct-panel-footer">
            <a class="button lnk_view btn btn-default fkpagseguroct-float-right" href="{$fkpagseguroct_link}" title="Retornar ao Carrinho">
                <span>{l s='Retornar ao Carrinho' mod='fkpagseguroct'}</span>
            </a>
        </div>

    </div>
</div>