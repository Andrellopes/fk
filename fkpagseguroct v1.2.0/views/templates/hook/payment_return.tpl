
<div class="fkpagseguroct">
    
    <div class="fkpagseguroct-panel" id="fkpagseguroct_finalizacao_pagseguro">
		<h4>
			{if $fkpagseguroct_link_boleto}
				{l s='Aguardando pagamento do boleto' mod='fkpagseguroct'}
			{elseif $fkpagseguroct_link_transf}
				{l s='Aguardando transferência online' mod='fkpagseguroct'}
			{else}
                {l s='Pagamento efetuado com sucesso' mod='fkpagseguroct'}
			{/if}
    	</h4>

    	{$fkpagseguroct_msg_1}
		<br>

		{if $fkpagseguroct_link_boleto}
			<input class="fkpagseguroct-button-link" type="button" name="btnBoleto" id="btnBoleto" value="{l s='Clique para imprimir o boleto' mod='fkpagseguroct'}" onClick="window.open('{$fkpagseguroct_link_boleto}','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=800,height=800,left=400,top=100'); return false;">
        	<br><br><br>
		{elseif $fkpagseguroct_link_transf}
			<input class="fkpagseguroct-button-link" type="button" name="btnTransf" id="btnTransf" value="{l s='Clique para efetuar a transferência bancária' mod='fkpagseguroct'}" onClick="window.open('{$fkpagseguroct_link_transf}','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=800,height=800,left=400,top=100'); return false;">
        	<br><br><br>
        {/if}

		<p>{l s='Abaixo os dados referente ao seu pagamento:' mod='fkpagseguroct'}</p>
    	<ul>
    		<li>Código da transação: {$fkpagseguroct_cod_transacao}</li>
    		<li>Número do pedido: {$fkpagseguroct_pedido}</li>
    		<li>Referência do pedido: {$fkpagseguroct_referencia}</li>
    		<li>Valor do pedido: {$fkpagseguroct_valor}</li>
    	</ul>

    </div>
</div>