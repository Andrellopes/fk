
<div class="fkpagseguroct">

    <div class="fkpagseguroct-panel">
    
        <form id="fkpagseguroct_form_boleto" action="{$link->getModuleLink('fkpagseguroct', 'validation', [], true)|escape:'html':'UTF-8'}" method="post">
        
            <div class="fkpagseguroct-logops">
        		<img alt="" src="{$url_img}pagseguro_selo.gif">
            </div>
	    
	    	<div class="form-group">
        		<label for="fkpagseguroct_boleto_tel" class="fkpagseguroct-col-lg-20">
        		    {l s="Telefone:" mod="fkpagseguroct"}
        		</label>
		
				<input class="form-control fkpagseguroct-col-lg-20" type="text" name="fkpagseguroct_boleto_tel" id="fkpagseguroct_boleto_tel" autocomplete="off" value="">
            </div>

            <div class="fkpagseguroct-sub-panel fkpagseguroct-col-lg-40">

                <div class="fkpagseguroct-panel-heading">
                    {l s="Selecione CPF ou CNPJ" mod="fkpagseguroct"}
                </div>

                <div class="fkpagseguroct-radio-cpfcnpj">
                    <div class="form-group fkpagseguroct-float-left fkpagseguroct-margin-right">
                        <input type="radio" name="fkpagseguroct_boleto_cpf_cnpj" id="fkpagseguroct_boleto_radio_cpf" value="cpf" {if !$cnpj}checked="checked"{/if}>
                        {l s="CPF" mod="fkpagseguroct"}
                    </div>

                    <div class="form-group">
                        <input type="radio" name="fkpagseguroct_boleto_cpf_cnpj" id="fkpagseguroct_boleto_radio_cnpj" value="cnpj" {if $cnpj}checked="checked"{/if}>
                        {l s="CNPJ" mod="fkpagseguroct"}
                    </div>
                </div>

                <div class="form-group">
                    <input class="form-control fkpagseguroct-col-lg-50" type="text" name="fkpagseguroct_boleto_cpf" id="fkpagseguroct_boleto_cpf" autocomplete="off" value="{if isset($cpf)}{$cpf}{/if}" {if !$cnpj}style="display: block;"{else}style="display: none;"{/if}>
                </div>

                <div class="form-group">
                    <input class="form-control fkpagseguroct-col-lg-50" type="text" name="fkpagseguroct_boleto_cnpj" id="fkpagseguroct_boleto_cnpj" autocomplete="off" value="{if isset($cnpj)}{$cnpj}{/if}" {if $cnpj}style="display: block;"{else}style="display: none;"{/if}>
                </div>

            </div>

            {*** Mostra ou nao o bloco de Endereco de Entrega ***}
            {assign var="style" value="display: none;"}

            {if !$endereco_entrega['endereco'] or !$endereco_entrega['numero'] or !$endereco_entrega['bairro']}
                {assign var="style" value="display: block;"}
            {/if}

	    	<div class="fkpagseguroct-panel" id="fkpagseguroct_ender_entrega" style="{$style}">
			
				<div class="fkpagseguroct-panel-heading">
                    {l s="ENDEREÇO DE ENTREGA: Por favor confirme os dados do endereço" mod="fkpagseguroct"}
            	</div>
            
				<div class="form-group">
		    		<label for="fkpagseguroct_boleto_endereco_entrega" class="fkpagseguroct-col-lg-20">
			             {l s="Endereço:" mod="fkpagseguroct"}
		    		</label>
		    
		    		<input class="form-control fkpagseguroct-col-lg-20" type="text" name="fkpagseguroct_boleto_endereco_entrega" id="fkpagseguroct_boleto_endereco_entrega" autocomplete="off" maxlength="80" value="{if isset($smarty.post.fkpagseguroct_boleto_endereco_entrega)}{$smarty.post.fkpagseguroct_boleto_endereco_entrega}{else}{if isset($endereco_entrega['endereco'])}{$endereco_entrega['endereco']}{/if}{/if}">
	        	</div>
				<div class="form-group">
        		    <label for="fkpagseguroct_boleto_numero_entrega" class="fkpagseguroct-col-lg-20">
        			     {l s="Número:" mod="fkpagseguroct"}
        		    </label>
			
		    		<input class="form-control fkpagseguroct-col-lg-15" type="text" name="fkpagseguroct_boleto_numero_entrega" id="fkpagseguroct_boleto_numero_entrega" autocomplete="off" maxlength="20" value="{if isset($smarty.post.fkpagseguroct_boleto_numero_entrega)}{$smarty.post.fkpagseguroct_boleto_numero_entrega}{else}{if isset($endereco_entrega['numero'])}{$endereco_entrega['numero']}{/if}{/if}">
				</div>
        		<div class="form-group">
        		    <label for="fkpagseguroct_boleto_complemento_entrega" class="fkpagseguroct-col-lg-20">
        			     {l s="Complemento:" mod="fkpagseguroct"}
        		    </label>
        			
        		    <input class="form-control fkpagseguroct-col-lg-30" type="text" name="fkpagseguroct_boleto_complemento_entrega" id="fkpagseguroct_boleto_complemento_entrega" autocomplete="off" maxlength="40" value="{if isset($smarty.post.fkpagseguroct_boleto_complemento_entrega)}{$smarty.post.fkpagseguroct_boleto_complemento_entrega}{else}{if isset($endereco_entrega['complemento'])}{$endereco_entrega['complemento']}{/if}{/if}">
        		</div>
                <div class="form-group">
                    <label for="fkpagseguroct_boleto_bairro_entrega" class="fkpagseguroct-col-lg-20">
                        {l s="Bairro:" mod="fkpagseguroct"}
                    </label>

                    <input class="form-control fkpagseguroct-col-lg-40" type="text" name="fkpagseguroct_boleto_bairro_entrega" id="fkpagseguroct_boleto_bairro_entrega" autocomplete="off" maxlength="60" value="{if isset($smarty.post.fkpagseguroct_boleto_bairro_entrega)}{$smarty.post.fkpagseguroct_boleto_bairro_entrega}{else}{if isset($endereco_entrega['bairro'])}{$endereco_entrega['bairro']}{/if}{/if}">
                </div>

			</div>

			{if $msg_boleto}
				<div id="fkpagseguroct_msg_boleto">
					{$msg_boleto}
				</div>
			{/if}

            <div class="fkpagseguroct-panel-footer">
                <button class="button btn btn-default button-medium fkpagseguroct-float-right" type="button" name="btnBoleto" id="btnBoleto">
                    <span>{l s='Gerar Boleto' mod='fkpagseguroct'}<i class="icon-chevron-right right"></i></span>
                </button>
         	</div>
        	    
            <!-- Campos hidden -->
            <input type="hidden" name="fkpagseguroct_tipo" id="fkpagseguroct_tipo" value="boleto"/>
            <input type="hidden" name="fkpagseguroct_boleto_hash" id="fkpagseguroct_boleto_hash"/>
        
		</form>
        
    </div>

</div>