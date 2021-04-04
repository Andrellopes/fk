
<div class="fkpagseguroct">

    <div class="fkpagseguroct-panel">
    
        <form id="fkpagseguroct_form_cartao" action="{$link->getModuleLink('fkpagseguroct', 'validation', [], true)|escape:'html':'UTF-8'}" method="post">
        
    	    <div class="fkpagseguroct-logops">
    			<img alt="" src="{$url_img}pagseguro_selo.gif">
    	    </div>

            <div class="form-group">
        		<label for="fkpagseguroct_cartao_titular" class="fkpagseguroct-col-lg-40">
        		    {l s="Titular do Cartão (conforme impresso):" mod="fkpagseguroct"}
        		</label>
        		
        		<input class="form-control fkpagseguroct-col-lg-30" type="text" name="fkpagseguroct_cartao_titular" id="fkpagseguroct_cartao_titular" autocomplete="off" value="">
            </div>
            
            <div class="form-group">
        		<label for="fkpagseguroct_cartao_nasc" class="fkpagseguroct-col-lg-40">
        		    {l s="Data de Nascimento:" mod="fkpagseguroct"}
        		</label>
        		
        		<input class="form-control fkpagseguroct-col-lg-20" type="text" name="fkpagseguroct_cartao_nasc" id="fkpagseguroct_cartao_nasc" autocomplete="off" value="">
            </div>
            
            <div class="form-group">
        		<label for="fkpagseguroct_cartao_tel" class="fkpagseguroct-col-lg-40">
        		    {l s="Telefone:" mod="fkpagseguroct"}
        		</label>
        		
        		<input class="form-control fkpagseguroct-col-lg-20" type="text" name="fkpagseguroct_cartao_tel" id="fkpagseguroct_cartao_tel" autocomplete="off" value="">
            </div>
            
            <div class="form-group">
        		<label for="fkpagseguroct_cartao_cpf" class="fkpagseguroct-col-lg-40">
        		    {l s="CPF:" mod="fkpagseguroct"}
        		</label>
        		
        		<input class="form-control fkpagseguroct-col-lg-20" type="text" name="fkpagseguroct_cartao_cpf" id="fkpagseguroct_cartao_cpf" autocomplete="off" value="{if isset($smarty.post.fkpagseguroct_cartao_cpf)}{$smarty.post.fkpagseguroct_cartao_cpf}{else}{if isset($cpf)}{$cpf}{/if}{/if}">
            </div>

            <div class="form-group">
                <label for="fkpagseguroct_cartao_cnpj" class="fkpagseguroct-col-lg-40">
                    {l s="CNPJ (opcional):" mod="fkpagseguroct"}
                </label>

                <input class="form-control fkpagseguroct-col-lg-20" type="text" name="fkpagseguroct_cartao_cnpj" id="fkpagseguroct_cartao_cnpj" autocomplete="off" value="{if isset($smarty.post.fkpagseguroct_cartao_cnpj)}{$smarty.post.fkpagseguroct_cartao_cnpj}{else}{if isset($cnpj)}{$cnpj}{/if}{/if}">
            </div>
            
            <div class="form-group">
        		<label for="fkpagseguroct_cartao_numero" class="fkpagseguroct-col-lg-40">
        		    {l s="Número do Cartão:" mod="fkpagseguroct"}
        		</label>
        		
        		<input class="form-control fkpagseguroct-col-lg-20" type="text" name="fkpagseguroct_cartao_numero" id="fkpagseguroct_cartao_numero" autocomplete="off" value="">
        		<img id="fkpagseguroct_img_bandeira" alt="Bandeira Cartão" src="{$url_img}bandeira.png">
            </div>
            
            <div class="form-group">
        		<label class="fkpagseguroct-col-lg-40">
        		    {l s="Vencimento (MM/AAAA):" mod="fkpagseguroct"}
        		</label>
		
        		<input class="form-control" type="text" name="fkpagseguroct_cartao_venc_mes" id="fkpagseguroct_cartao_venc_mes" maxlength="2" autocomplete="off" value="">
        		<span id="fkpagseguroct_cartao_venc_sep">/</span>
        		<input class="form-control" type="text" name="fkpagseguroct_cartao_venc_ano" id="fkpagseguroct_cartao_venc_ano" maxlength="4" autocomplete="off" value="">
            </div>
            
            <div class="form-group">
        		<label for="fkpagseguroct_cartao_codigo" class="fkpagseguroct-col-lg-40">
        		    {l s="Código de Segurança (no verso do cartão):" mod="fkpagseguroct"}
        		</label>
        		
        		<input class="form-control fkpagseguroct-col-lg-10" type="text" name="fkpagseguroct_cartao_codigo" id="fkpagseguroct_cartao_codigo" maxlength="5" autocomplete="off" value="">
            </div>

            <div class="form-group">
        		<label for="fkpagseguroct_cartao_parcelas" class="fkpagseguroct-col-lg-40">
        		    {l s="Parcelamento (após informado o Cartão):" mod="fkpagseguroct"}
        		</label>
        		
        		<select class="form-control" name="fkpagseguroct_cartao_parcelas" id="fkpagseguroct_cartao_parcelas"></select>
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
        		    <label for="fkpagseguroct_cartao_endereco_entrega" class="fkpagseguroct-col-lg-40">
        			{l s="Endereço:" mod="fkpagseguroct"}
        		    </label>
        		    
        		    <input class="form-control fkpagseguroct-col-lg-40" type="text" name="fkpagseguroct_cartao_endereco_entrega" id="fkpagseguroct_cartao_endereco_entrega" autocomplete="off" maxlength="80" value="{if isset($smarty.post.fkpagseguroct_cartao_endereco_entrega)}{$smarty.post.fkpagseguroct_cartao_endereco_entrega}{else}{if isset($endereco_entrega['endereco'])}{$endereco_entrega['endereco']}{/if}{/if}">
	        	</div>
				<div class="form-group">
        		    <label for="fkpagseguroct_cartao_numero_entrega" class="fkpagseguroct-col-lg-40">
        			{l s="Número:" mod="fkpagseguroct"}
        		    </label>
        			
        		    <input class="form-control fkpagseguroct-col-lg-15" type="text" name="fkpagseguroct_cartao_numero_entrega" id="fkpagseguroct_cartao_numero_entrega" autocomplete="off" maxlength="20" value="{if isset($smarty.post.fkpagseguroct_cartao_numero_entrega)}{$smarty.post.fkpagseguroct_cartao_numero_entrega}{else}{if isset($endereco_entrega['numero'])}{$endereco_entrega['numero']}{/if}{/if}">
				</div>
				<div class="form-group">
        		    <label for="fkpagseguroct_cartao_complemento_entrega" class="fkpagseguroct-col-lg-40">
        			{l s="Complemento:" mod="fkpagseguroct"}
        		    </label>
			
		    		<input class="form-control fkpagseguroct-col-lg-30" type="text" name="fkpagseguroct_cartao_complemento_entrega" id="fkpagseguroct_cartao_complemento_entrega" autocomplete="off" maxlength="40" value="{if isset($smarty.post.fkpagseguroct_cartao_complemento_entrega)}{$smarty.post.fkpagseguroct_cartao_complemento_entrega}{else}{if isset($endereco_entrega['complemento'])}{$endereco_entrega['complemento']}{/if}{/if}">
				</div>
                <div class="form-group">
                    <label for="fkpagseguroct_cartao_bairro_entrega" class="fkpagseguroct-col-lg-40">
                        {l s="Bairro:" mod="fkpagseguroct"}
                    </label>

                    <input class="form-control fkpagseguroct-col-lg-40" type="text" name="fkpagseguroct_cartao_bairro_entrega" id="fkpagseguroct_cartao_bairro_entrega" autocomplete="off" maxlength="60" value="{if isset($smarty.post.fkpagseguroct_cartao_bairro_entrega)}{$smarty.post.fkpagseguroct_cartao_bairro_entrega}{else}{if isset($endereco_entrega['bairro'])}{$endereco_entrega['bairro']}{/if}{/if}">
                </div>
	    	</div>

            {*** Mostra ou nao o bloco de Endereco de Cobranca ***}
            {assign var="style" value="display: none;"}

            {if !$endereco_cobranca['endereco'] or !$endereco_cobranca['numero'] or !$endereco_cobranca['bairro']}
                {assign var="style" value="display: block;"}
            {/if}
			
	    	<div class="fkpagseguroct-panel" id="fkpagseguroct_ender_cobranca" style="{$style}">
			
				<div class="fkpagseguroct-panel-heading">
                    {l s="ENDEREÇO DE COBRANÇA: Por favor confirme os dados do endereço" mod="fkpagseguroct"}
            	</div>
            
				<div class="form-group">
	            	<label for="fkpagseguroct_cartao_endereco_cobranca" class="fkpagseguroct-col-lg-40">
		              {l s="Endereço:" mod="fkpagseguroct"}
		    		</label>
		            
		    		<input class="form-control fkpagseguroct-col-lg-40" type="text" name="fkpagseguroct_cartao_endereco_cobranca" id="fkpagseguroct_cartao_endereco_cobranca" autocomplete="off" maxlength="80" value="{if isset($smarty.post.fkpagseguroct_cartao_endereco_cobranca)}{$smarty.post.fkpagseguroct_cartao_endereco_cobranca}{else}{if isset($endereco_cobranca['endereco'])}{$endereco_cobranca['endereco']}{/if}{/if}">
	        	</div>
        		<div class="form-group">
        		    <label for="fkpagseguroct_cartao_numero_cobranca" class="fkpagseguroct-col-lg-40">
        			{l s="Número:" mod="fkpagseguroct"}
        		    </label>
			
		    		<input class="form-control fkpagseguroct-col-lg-15" type="text" name="fkpagseguroct_cartao_numero_cobranca" id="fkpagseguroct_cartao_numero_cobranca" autocomplete="off" maxlength="20" value="{if isset($smarty.post.fkpagseguroct_cartao_numero_cobranca)}{$smarty.post.fkpagseguroct_cartao_numero_cobranca}{else}{if isset($endereco_cobranca['numero'])}{$endereco_cobranca['numero']}{/if}{/if}">
				</div>
        		<div class="form-group">
        		    <label for="fkpagseguroct_cartao_complemento_cobranca" class="fkpagseguroct-col-lg-40">
        			{l s="Complemento:" mod="fkpagseguroct"}
        		    </label>
			
		    		<input class="form-control fkpagseguroct-col-lg-30" type="text" name="fkpagseguroct_cartao_complemento_cobranca" id="fkpagseguroct_cartao_complemento_cobranca" autocomplete="off" maxlength="40" value="{if isset($smarty.post.fkpagseguroct_cartao_complemento_cobranca)}{$smarty.post.fkpagseguroct_cartao_complemento_cobranca}{else}{if isset($endereco_cobranca['complemento'])}{$endereco_cobranca['complemento']}{/if}{/if}">
				</div>
                <div class="form-group">
                    <label for="fkpagseguroct_cartao_bairro_cobranca" class="fkpagseguroct-col-lg-40">
                        {l s="Bairro:" mod="fkpagseguroct"}
                    </label>

                    <input class="form-control fkpagseguroct-col-lg-40" type="text" name="fkpagseguroct_cartao_bairro_cobranca" id="fkpagseguroct_cartao_bairro_cobranca" autocomplete="off" maxlength="60" value="{if isset($smarty.post.fkpagseguroct_cartao_bairro_cobranca)}{$smarty.post.fkpagseguroct_cartao_bairro_cobranca}{else}{if isset($endereco_cobranca['bairro'])}{$endereco_cobranca['bairro']}{/if}{/if}">
                </div>
	    	</div>
            
            <div class="fkpagseguroct-panel-footer">
        		<button class="button btn btn-default button-medium fkpagseguroct-float-right" type="button" name="btnCartao" id="btnCartao">
        		    <span>{l s='Pagar' mod='fkpagseguroct'}<i class="icon-chevron-right right"></i></span>
        		</button>
            </div>

    	    <!-- Campos hidden -->
    	    <input type="hidden" name="fkpagseguroct_tipo" id="fkpagseguroct_tipo" value="cartao"/>
    	    <input type="hidden" name="fkpagseguroct_cartao_token" id="fkpagseguroct_cartao_token"/>
    	    <input type="hidden" name="fkpagseguroct_cartao_hash" id="fkpagseguroct_cartao_hash"/>
    	    <input type="hidden" name="fkpagseguroct_cartao_valor_parcela" id="fkpagseguroct_cartao_valor_parcela"/>
            
        </form>
        
    </div>

</div>