
<form id="configuration_form" class="defaultForm  form-horizontal" action="{$tab_2['formAction']}&origem=configGeral" method="post" enctype="multipart/form-data">

    <div class="fkpagseguroct">

        <div class="panel" style="border-top-left-radius: 0">

            <div class="panel-heading">
                {l s="Configuração" mod="fkpagseguroct"}
            </div>

            <div class="fkpagseguroct-panel-header">
                <button type="button" value="1" name="btnAjuda" class="fkpagseguroct-button fkpagseguroct-float-right" onClick="window.open('http://www.fkmodulos.com.br/modulosfk/ajuda/fkpagseguroct_v1_0_0.pdf','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=900,height=500,left=500,top=150'); return false;">
                    <i class="process-icon-help"></i>
                    {l s="Ajuda" mod="fkpagseguroct"}
                </button>
            </div>

            <div class="panel fkpagseguroct-margin-panel fkpagseguroct-col-lg-70">

                <div class="panel-heading">
                    {l s="Modo de Operação" mod="fkpagseguroct"}
                </div>

                <div class="form-group">
                    <label class="control-label fkpagseguroct-col-lg-25 fkpagseguroct-float-left"></label>
                    <div class="fkpagseguroct-float-left">
                        <input type="radio" name="fkpagseguroct_modo" id="fkpagseguroct_modo" value="1" {if isset($tab_2['fkpagseguroct_modo']) and $tab_2['fkpagseguroct_modo'] == '1'}checked="checked"{/if}>
                    </div>
                    <label class="fkpagseguroct-label-right fkpagseguroct-col-lg-auto">
                        {l s="Produção" mod="fkpagseguroct"}
                    </label>
                </div>

                <div class="form-group">
                    <label class="control-label fkpagseguroct-col-lg-25 fkpagseguroct-float-left"></label>
                    <div class="fkpagseguroct-float-left">
                        <input type="radio" name="fkpagseguroct_modo" id="fkpagseguroct_modo" value="2" {if isset($tab_2['fkpagseguroct_modo']) and $tab_2['fkpagseguroct_modo'] == '2'}checked="checked"{/if}>
                    </div>
                    <label class="fkpagseguroct-label-right fkpagseguroct-col-lg-auto">
                        {l s="SandBox" mod="fkpagseguroct"}
                    </label>
                </div>

            </div>
            
            <div class="panel fkpagseguroct-col-lg-70">

                <div class="panel-heading">
                    {l s="Pagseguro" mod="fkpagseguroct"}
                </div>

                <div class="form-group">
                    <label for="fkpagseguroct_email" class="control-label fkpagseguroct-col-lg-25 fkpagseguroct-float-left">
                        {l s="E-mail" mod="fkpagseguroct"}
                    </label>
                    <div class="fkpagseguroct-col-lg-40 fkpagseguroct-float-left">
                        <input type="text" name="fkpagseguroct_email" id="fkpagseguroct_email" value="{if isset($smarty.post.fkpagseguroct_email)}{$smarty.post.fkpagseguroct_email}{else}{if isset($tab_2['fkpagseguroct_email'])}{$tab_2['fkpagseguroct_email']}{/if}{/if}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="fkpagseguroct_token" class="control-label fkpagseguroct-col-lg-25 fkpagseguroct-float-left">
                        {l s="Token" mod="fkpagseguroct"}
                    </label>
                    <div class="fkpagseguroct-col-lg-40 fkpagseguroct-float-left">
                        <input type="text" name="fkpagseguroct_token" id="fkpagseguroct_token" value="{if isset($smarty.post.fkpagseguroct_token)}{$smarty.post.fkpagseguroct_token}{else}{if isset($tab_2['fkpagseguroct_token'])}{$tab_2['fkpagseguroct_token']}{/if}{/if}">
                    </div>
                </div>
		        <div class="form-group">
                    <label for="fkpagseguroct_charset" class="control-label fkpagseguroct-col-lg-25 fkpagseguroct-float-left">
                        {l s="Charset" mod="fkpagseguroct"}
                    </label>
                    <div class="fkpagseguroct-col-lg-20 fkpagseguroct-float-left">
                        <select class="select" name="fkpagseguroct_charset" id="fkpagseguroct_charset">
                            {foreach from=$tab_2['charsetOptions'] key=key item=charset}
                                <option value="{$key}" {if ($charset == $tab_2['fkpagseguroct_charset'])} selected="selected"{/if}>{$charset}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="fkpagseguroct_http_version" class="control-label fkpagseguroct-col-lg-25 fkpagseguroct-float-left">
                        {l s="Versão HTTP" mod="fkpagseguroct"}
                    </label>
                    <div class="fkpagseguroct-col-lg-20 fkpagseguroct-float-left">
                        <select class="select" name="fkpagseguroct_http_version" id="fkpagseguroct_http_version">
                            {foreach from=$tab_2['httpVersions'] key=key item=httpVersion}
                                <option value="{$key}" {if ($httpVersion == $tab_2['fkpagseguroct_http_version'])} selected="selected"{/if}>{$httpVersion}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                
                <div class="fkpagseguroct-sub-panel">
	                <div class="panel fkpagseguroct-col-lg-60">
	                
		                <div class="panel-heading">
		                    {l s="Formas de Pagamento" mod="fkpagseguroct"}
		                </div>
		                
		                <div class="form-group">
		                    <div class="fkpagseguroct-float-left">
		                        <input type="checkbox" name="fkpagseguroct_cartao" id="fkpagseguroct_cartao" value="on" {if isset($tab_2['fkpagseguroct_cartao']) and $tab_2['fkpagseguroct_cartao'] == 'on'}checked="checked"{/if}>
		                    </div>
		                    <label class="fkpagseguroct-label-right fkpagseguroct-col-lg-auto">
		                        {l s="Cartão de Crédito" mod="fkpagseguroct"}
		                    </label>
	                	</div>
	                	<div class="form-group">
		                    <div class="fkpagseguroct-float-left">
		                        <input type="checkbox" name="fkpagseguroct_boleto" id="fkpagseguroct_boleto" value="on" {if isset($tab_2['fkpagseguroct_boleto']) and $tab_2['fkpagseguroct_boleto'] == 'on'}checked="checked"{/if}>
		                    </div>
		                    <label class="fkpagseguroct-label-right fkpagseguroct-col-lg-auto">
		                        {l s="Boleto Bancário" mod="fkpagseguroct"}
		                    </label>
	                	</div>
	                	<div class="form-group">
		                    <div class="fkpagseguroct-float-left">
		                        <input type="checkbox" name="fkpagseguroct_transf" id="fkpagseguroct_transf" value="on" {if isset($tab_2['fkpagseguroct_transf']) and $tab_2['fkpagseguroct_transf'] == 'on'}checked="checked"{/if}>
		                    </div>
		                    <label class="fkpagseguroct-label-right fkpagseguroct-col-lg-auto">
		                        {l s="Transferência Online" mod="fkpagseguroct"}
		                    </label>
	                	</div>
	                
	                </div>
                </div>




                <div class="fkpagseguroct-sub-panel">
                    <div class="panel fkpagseguroct-col-lg-60">

                        <div class="panel-heading">
                            {l s="Transferência Online" mod="fkpagseguroct"}
                        </div>

                        <div class="form-group">
                            <div class="fkpagseguroct-float-left">
                                <input type="checkbox" name="fkpagseguroct_bb" id="fkpagseguroct_bb" value="on" {if isset($tab_2['fkpagseguroct_bb']) and $tab_2['fkpagseguroct_bb'] == 'on'}checked="checked"{/if}>
                            </div>
                            <label class="fkpagseguroct-label-right fkpagseguroct-col-lg-auto">
                                {l s="Banco do Brasil" mod="fkpagseguroct"}
                            </label>
                        </div>
                        <div class="form-group">
                            <div class="fkpagseguroct-float-left">
                                <input type="checkbox" name="fkpagseguroct_banrisul" id="fkpagseguroct_banrisul" value="on" {if isset($tab_2['fkpagseguroct_banrisul']) and $tab_2['fkpagseguroct_banrisul'] == 'on'}checked="checked"{/if}>
                            </div>
                            <label class="fkpagseguroct-label-right fkpagseguroct-col-lg-auto">
                                {l s="Banrisul" mod="fkpagseguroct"}
                            </label>
                        </div>
                        <div class="form-group">
                            <div class="fkpagseguroct-float-left">
                                <input type="checkbox" name="fkpagseguroct_bradesco" id="fkpagseguroct_bradesco" value="on" {if isset($tab_2['fkpagseguroct_bradesco']) and $tab_2['fkpagseguroct_bradesco'] == 'on'}checked="checked"{/if}>
                            </div>
                            <label class="fkpagseguroct-label-right fkpagseguroct-col-lg-auto">
                                {l s="Bradesco" mod="fkpagseguroct"}
                            </label>
                        </div>
                        <div class="form-group">
                            <div class="fkpagseguroct-float-left">
                                <input type="checkbox" name="fkpagseguroct_hsbc" id="fkpagseguroct_hsbc" value="on" {if isset($tab_2['fkpagseguroct_hsbc']) and $tab_2['fkpagseguroct_hsbc'] == 'on'}checked="checked"{/if}>
                            </div>
                            <label class="fkpagseguroct-label-right fkpagseguroct-col-lg-auto">
                                {l s="HSBC" mod="fkpagseguroct"}
                            </label>
                        </div>
                        <div class="form-group">
                            <div class="fkpagseguroct-float-left">
                                <input type="checkbox" name="fkpagseguroct_itau" id="fkpagseguroct_itau" value="on" {if isset($tab_2['fkpagseguroct_itau']) and $tab_2['fkpagseguroct_itau'] == 'on'}checked="checked"{/if}>
                            </div>
                            <label class="fkpagseguroct-label-right fkpagseguroct-col-lg-auto">
                                {l s="Itaú" mod="fkpagseguroct"}
                            </label>
                        </div>

                    </div>
                </div>



















                <div class="fkpagseguroct-sub-panel">
                    <div class="panel fkpagseguroct-col-lg-60">

                        <div class="panel-heading">
                            {l s="Parcelas sem Juros" mod="fkpagseguroct"}
                        </div>

                        <div class="form-group">
                            <div class="fkpagseguroct-col-lg-20 fkpagseguroct-float-left">
                                <input type="text" name="fkpagseguroct_parcelas_sem_juros" id="fkpagseguroct_parcelas_sem_juros" value="{if isset($smarty.post.fkpagseguroct_parcelas_sem_juros)}{$smarty.post.fkpagseguroct_parcelas_sem_juros}{else}{if isset($tab_2['fkpagseguroct_parcelas_sem_juros'])}{$tab_2['fkpagseguroct_parcelas_sem_juros']}{/if}{/if}">
                            </div>
                        </div>

                        <div class="form-group">
                            <span class="help-block">
                                {l s="Informe a quantidade de parcelas sem juros." mod="fkpagseguroct"}
                                <br>
                                {l s="O valor deste campo deve ser idêntico ao definido em sua conta no Pagseguro." mod="fkpagseguroct"}
                            </span>
                        </div>

                    </div>

                </div>

            </div>
            
            <div class="panel fkpagseguroct-col-lg-70">

                <div class="panel-heading">
                    {l s="Mensagens" mod="fkpagseguroct"}
                </div>

				<div class="form-group">
                    <label for="fkpagseguroct_msg_3" class="control-label fkpagseguroct-col-lg-25 fkpagseguroct-float-left">
                        {l s="Boleto" mod="fkpagseguroct"}
                    </label>
                    <div class="fkpagseguroct-col-lg-70 fkpagseguroct-float-left">
                        <textarea rows=3 name="fkpagseguroct_msg_3" id="fkpagseguroct_msg_3">{if isset($smarty.post.fkpagseguroct_msg_3)}{$smarty.post.fkpagseguroct_msg_3}{else}{if isset($tab_2['fkpagseguroct_msg_3'])}{$tab_2['fkpagseguroct_msg_3']}{/if}{/if}</textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="fkpagseguroct_msg_1" class="control-label fkpagseguroct-col-lg-25 fkpagseguroct-float-left">
                        {l s="Pagamento concluído" mod="fkpagseguroct"}
                    </label>
                    <div class="fkpagseguroct-col-lg-70 fkpagseguroct-float-left">
                        <textarea rows=3 name="fkpagseguroct_msg_1" id="fkpagseguroct_msg_1">{if isset($smarty.post.fkpagseguroct_msg_1)}{$smarty.post.fkpagseguroct_msg_1}{else}{if isset($tab_2['fkpagseguroct_msg_1'])}{$tab_2['fkpagseguroct_msg_1']}{/if}{/if}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="fkpagseguroct_msg_2" class="control-label fkpagseguroct-col-lg-25 fkpagseguroct-float-left">
                        {l s="Pagamento não concluído" mod="fkpagseguroct"}
                    </label>
                    <div class="fkpagseguroct-col-lg-70 fkpagseguroct-float-left">
                        <textarea rows=3 name="fkpagseguroct_msg_2" id="fkpagseguroct_msg_2">{if isset($smarty.post.fkpagseguroct_msg_2)}{$smarty.post.fkpagseguroct_msg_2}{else}{if isset($tab_2['fkpagseguroct_msg_2'])}{$tab_2['fkpagseguroct_msg_2']}{/if}{/if}</textarea>
                    </div>
                </div>
            
            </div>

			<div class="panel fkpagseguroct-col-lg-70">

                <div class="panel-heading">
                    {l s="DDD Válidos" mod="fkpagseguroct"}
                </div>
    
                <div class="form-group">
					<label for="fkpagseguroct_ddd" class="control-label fkpagseguroct-col-lg-25 fkpagseguroct-float-left">
                        {l s="DDD" mod="fkpagseguroct"}
                    </label>
                    <div class="fkpagseguroct-col-lg-70 fkpagseguroct-float-left">
                        <textarea rows=3 name="fkpagseguroct_ddd" id="fkpagseguroct_ddd">{if isset($smarty.post.fkpagseguroct_ddd)}{$smarty.post.fkpagseguroct_ddd}{else}{if isset($tab_2['fkpagseguroct_ddd'])}{$tab_2['fkpagseguroct_ddd']}{/if}{/if}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="control-label fkpagseguroct-col-lg-25"></label>

                        <span class="help-block">
                            {l s="Informe o DDD entre pipe (|)" mod="fkpagseguroct"}
                        </span>
                    </div>
                </div>

        	</div>
            
            <div class="panel fkpagseguroct-col-lg-70">

                <div class="panel-heading">
                    {l s="Opções Diversas" mod="fkpagseguroct"}
                </div>
                
                <div class="form-group">
                    <label class="control-label fkpagseguroct-col-lg-25 fkpagseguroct-float-left"></label>
                    <div class="fkpagseguroct-float-left">
                        <input type="checkbox" name="fkpagseguroct_status_pago" id="fkpagseguroct_status_pago" value="on" {if isset($tab_2['fkpagseguroct_status_pago']) and $tab_2['fkpagseguroct_status_pago'] == 'on'}checked="checked"{/if}>
                    </div>
                    <label class="fkpagseguroct-label-right fkpagseguroct-col-lg-auto">
                        {l s="Alterar status do pedido quando receber notificação de Pagamento Aceito" mod="fkpagseguroct"}
                    </label>
                </div>
                <div class="form-group">
                    <label class="control-label fkpagseguroct-col-lg-25 fkpagseguroct-float-left"></label>
                    <div class="fkpagseguroct-float-left">
                        <input type="checkbox" name="fkpagseguroct_status_canc" id="fkpagseguroct_status_canc" value="on" {if isset($tab_2['fkpagseguroct_status_canc']) and $tab_2['fkpagseguroct_status_canc'] == 'on'}checked="checked"{/if}>
                    </div>
                    <label class="fkpagseguroct-label-right fkpagseguroct-col-lg-auto">
                        {l s="Alterar status do pedido quando receber notificação de Cancelamento" mod="fkpagseguroct"}
                    </label>
                </div>
                <div class="form-group">
                    <label class="control-label fkpagseguroct-col-lg-25 fkpagseguroct-float-left"></label>
                    <div class="fkpagseguroct-float-left">
                        <input type="checkbox" name="fkpagseguroct_bootstrap" id="fkpagseguroct_bootstrap" value="on" {if isset($tab_2['fkpagseguroct_bootstrap']) and $tab_2['fkpagseguroct_bootstrap'] == 'on'}checked="checked"{/if}>
                    </div>
                    <label class="fkpagseguroct-label-right fkpagseguroct-col-lg-auto">
                        {l s="Usar tema padrão Bootstrap" mod="fkpagseguroct"}
                    </label>
                </div>

             </div>

            <div class="fkpagseguroct-panel-footer">
                <button type="submit" value="1" name="btnSubmit" class="fkpagseguroct-button fkpagseguroct-float-right">
                    <i class="process-icon-save"></i>
                    {l s="Salvar" mod="fkpagseguroct"}
                </button>
            </div>

        </div>
    
    </div>

</form>