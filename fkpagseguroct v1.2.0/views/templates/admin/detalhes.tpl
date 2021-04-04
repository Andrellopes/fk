
<div class="fkpagseguroct">

	<div class="panel">
	
	    <h3>
	        {l s="Dados Cadastrais" mod='fkpagseguroct'}
	    </h3>
	
	    <div class="form-group">
	
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Id:" mod='fkpagseguroct'}</label>{$cadastro['id_customer']}
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Nome:" mod='fkpagseguroct'}</label>{$cadastro['firstname']} {$cadastro['lastname']}
	
	        <br>
	
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="CPF/CNPJ:" mod='fkpagseguroct'}</label>{$cadastro['cpf_cnpj']}
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="RG/IE:" mod='fkpagseguroct'}</label>{$cadastro['rg_ie']}
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="E-mail:" mod='fkpagseguroct'}</label>{$cadastro['email']}
	
	        <br>
	
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Data de nascimento:" mod='fkpagseguroct'}</label>
	        {if $cadastro['birthday'] != "0000-00-00"}
	            {$cadastro['birthday']|date_format:"%d/%m/%Y"}
	        {/if}
	
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Data cadastro:" mod='fkpagseguroct'}</label>{$cadastro['date_add']|date_format:"%d/%m/%Y %H:%M"}
	
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Newsletter:" mod='fkpagseguroct'}</label>
	        {if $cadastro['newsletter'] == 0}
	            Não
	        {else}
	            Sim (desde: {$cadastro['newsletter_date_add']|date_format:"%d/%m/%Y"})
	        {/if}
	
	        <br>
	
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Grupo padrão:" mod='fkpagseguroct'}</label>{$cadastro['name']}
	
	    </div>
	
	</div>
	
	<div class="panel">
	
	    <h3>
	        {l s="Endereço de entrega:" mod='fkpagseguroct'} {$endereco['alias']}
	    </h3>
	
	    <div class="form-group">
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Nome:" mod='fkpagseguroct'}</label>{$endereco['firstname']} {$endereco['lastname']}
	
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Empresa:" mod='fkpagseguroct'}</label>
	        {$endereco['company']}
	
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Telefone:" mod='fkpagseguroct'}</label>
	        {$endereco['phone']}
	
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Celular:" mod='fkpagseguroct'}</label>
	        {$endereco['phone_mobile']}
	
	        <br>
	
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Documento (DNI):" mod='fkpagseguroct'}</label>
	        {$endereco['dni']}
	
	        <br>
	
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="CEP:" mod='fkpagseguroct'}</label>{$endereco['postcode']}
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Endereço:" mod='fkpagseguroct'}</label>{$endereco['address1']}
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Número:" mod='fkpagseguroct'}</label>{$endereco['numend']}
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Complemento:" mod='fkpagseguroct'}</label>{$endereco['compl']}
	
	        <br>
	
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Bairro:" mod='fkpagseguroct'}</label>{$endereco['address2']}
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Cidade:" mod='fkpagseguroct'}</label>{$endereco['city']}
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Estado:" mod='fkpagseguroct'}</label>{$endereco['estado']}
	        <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Pais:" mod='fkpagseguroct'}</label>{$endereco['pais']}
	
	    </div>
	
	</div>
	
	<div class="panel">
	
	    <h3>
	        {l s="Pedido" mod='fkpagseguroct'}
	    </h3>
	    
	    <div class="panel">
	    
	        <h3>
	            {l s="Pedido:" mod='fkpagseguroct'} {$pedido['id_order']} ({$pedido['reference']}) - {$pedido['state_name']}
	        </h3>
	        
	        <div class="form-group">
	        
	            <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Data do pedido:" mod='fkpagseguroct'}</label>{$pedido['date_add']|date_format:"%d/%m/%Y %H:%M"}
	
	            <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Data do pagamento:" mod='fkpagseguroct'}</label>
	            {if $pedido['invoice_date'] == "0000-00-00 00:00:00"}
	                Pendente
	            {else}
	                {$pedido['invoice_date']|date_format:"%d/%m/%Y %H:%M"}
	            {/if}
	
	            <br>
	
	            <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Forma de pagamento:" mod='fkpagseguroct'}</label>{$pedido['payment']}
	            <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Transportadora:" mod='fkpagseguroct'}</label>{$pedido['carrier_name']}
	
	            <br>
	
	            <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Total produtos:" mod='fkpagseguroct'}</label>{$pedido['total_products_wt']|number_format:2:",":"."}
	            <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Total frete:" mod='fkpagseguroct'}</label>{$pedido['total_shipping']|number_format:2:",":"."}
	            <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Total embalagem:" mod='fkpagseguroct'}</label>{$pedido['total_wrapping']|number_format:2:",":"."}
	            <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Total descontos:" mod='fkpagseguroct'}</label>{$pedido['total_discounts']|number_format:2:",":"."}
	            <label class="fkpagseguroct-font-bold fkpagseguroct-col-lg-auto">{l s="Total pedido:" mod='fkpagseguroct'}</label>{$pedido['total_paid']|number_format:2:",":"."}
	
	            <br><br>
	            
	            <div class="panel" style="margin-left: 32px; width: 900px;">
	                <h3>
	                    {l s="Produtos" mod='fkpagseguroct'}
	                </h3>
	
	                <table>
	                    <th>{l s="Id" mod='fkpagseguroct'}</th>
	                    <th>{l s="Descrição" mod='fkpagseguroct'}</th>
	                    <th>{l s="Quantidade" mod='fkpagseguroct'}</th>
	                    <th>{l s="Valor unitário" mod='fkpagseguroct'}</th>
	                    <th>{l s="Valor total" mod='fkpagseguroct'}</th>
	
	                    {foreach $pedido['produtos'] AS $produto}
	
	                        <tr>
	                            <td>{$produto['product_id']}</td>
	                            <td>{$produto['product_name']}</td>
	                            <td style="text-align: center;">{$produto['product_quantity']}</td>
	                            <td style="text-align: right;">{$produto['unit_price_tax_incl']|number_format:2:",":"."}</td>
	                            <td style="text-align: right;">{$produto['total_price_tax_incl']|number_format:2:",":"."}</td>
	                        </tr>
	
	                    {/foreach}
	
	                </table>
	
	            </div>
	        
	        </div>
	        
	    </div>
	    
	</div>
</div>


