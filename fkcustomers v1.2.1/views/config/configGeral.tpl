
<form id="configuration_form" class="defaultForm  form-horizontal" action="{$tab_2['formAction']}&origem=configGeral" method="post" enctype="multipart/form-data">

    <div class="fkcustomers-panel" style="border-top-left-radius: 0">

        <div class="fkcustomers-panel-heading">
            {l s="Configuração" mod="fkcustomers"}
        </div>

        <div class="fkcustomers-panel-header">
            <button type="button" value="1" name="btnAjuda" class="fkcustomers-button fkcustomers-float-right" onClick="window.open('http://www.fkmodulos.com.br/modulosfk/ajuda/fkcustomers_v1_0_0.pdf','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=900,height=500,left=500,top=150'); return false;">
                <i class="process-icon-help"></i>
                {l s="Ajuda" mod="fkcustomers"}
            </button>
        </div>

        <div class="fkcustomers-panel fkcustomers-col-lg-70">

            <div class="fkcustomers-panel-heading">
                {l s="Modo de Operação" mod="fkcustomers"}
            </div>

            <div class="fkcustomers-form">
                <label class="fkcustomers-label fkcustomers-col-lg-10"></label>
                <div class="fkcustomers-float-left">
                    <input type="radio" name="fkcustomers_modo" id="fkcustomers_modo" value="1" {if isset($tab_2['fkcustomers_modo']) and $tab_2['fkcustomers_modo'] == '1'}checked="checked"{/if}>
                </div>
                <label class="fkcustomers-label-right fkcustomers-col-lg-auto">
                    {l s="Completo" mod="fkcustomers"}
                </label>
            </div>

            <div class="fkcustomers-form">
                <label class="fkcustomers-label fkcustomers-col-lg-10"></label>
                <div class="fkcustomers-float-left">
                    <input type="radio" name="fkcustomers_modo" id="fkcustomers_modo" value="2" {if isset($tab_2['fkcustomers_modo']) and $tab_2['fkcustomers_modo'] == '2'}checked="checked"{/if}>
                </div>
                <label class="fkcustomers-label-right fkcustomers-col-lg-auto">
                    {l s="Compatibilidade" mod="fkcustomers"}
                </label>
            </div>

        </div>

        <div class="fkcustomers-panel fkcustomers-col-lg-70">

            <div class="fkcustomers-panel-heading">
                {l s="Provedores de CEP" mod="fkcustomers"}
            </div>

            <div class="fkcustomers-form">
                <label class="fkcustomers-label fkcustomers-col-lg-10"></label>
                <div class="fkcustomers-float-left">
                    <input type="radio" name="fkcustomers_ws" value="CO" {if isset($tab_2['fkcustomers_ws']) and $tab_2['fkcustomers_ws'] == 'CO'}checked="checked"{/if}>
                </div>
                <label class="fkcustomers-label-right fkcustomers-col-lg-auto">
                    {l s="Correios" mod="fkcustomers"}
                </label>
            </div>

            <div class="fkcustomers-form">
                <label class="fkcustomers-label fkcustomers-col-lg-10"></label>
                <div class="fkcustomers-float-left">
                    <input type="radio" name="fkcustomers_ws" value="RV" {if isset($tab_2['fkcustomers_ws']) and $tab_2['fkcustomers_ws'] == 'RV'}checked="checked"{/if}>
                </div>
                <label class="fkcustomers-label-right fkcustomers-col-lg-auto">
                    {l s="República Virtual" mod="fkcustomers"}
                </label>
            </div>

            <div class="fkcustomers-form">
                <label class="fkcustomers-label fkcustomers-col-lg-10"></label>
                <div class="fkcustomers-float-left">
                    <input type="radio" name="fkcustomers_ws" value="BY" {if isset($tab_2['fkcustomers_ws']) and $tab_2['fkcustomers_ws'] == 'BY'}checked="checked"{/if}>
                </div>
                <label class="fkcustomers-label-right fkcustomers-col-lg-auto">
                    {l s="BYJG" mod="fkcustomers"}
                </label>
            </div>
            <div class="fkcustomers-form">
                <label for="fkcustomers_usuarioby" class="fkcustomers-label fkcustomers-col-lg-20">
                    {l s="Usuário" mod="fkcustomers"}
                </label>
                <div class="fkcustomers-col-lg-15 fkcustomers-float-left">
                    <input type="text" name="fkcustomers_usuarioby" id="fkcustomers_usuarioby" value="{if isset($smarty.post.fkcustomers_usuarioby)}{$smarty.post.fkcustomers_usuarioby}{else}{if isset($tab_2['fkcustomers_ws']) and $tab_2['fkcustomers_ws'] == 'BY'}{$tab_2['usuario']}{/if}{/if}">
                </div>
            </div>
            <div class="fkcustomers-form">
                <label for="fkcustomers_senhaby" class="fkcustomers-label fkcustomers-col-lg-20">
                    {l s="Senha" mod="fkcustomers"}
                </label>
                <div class="fkcustomers-col-lg-15 fkcustomers-float-left">
                    <input type="password" name="fkcustomers_senhaby" id="fkcustomers_senhaby" value="{if isset($smarty.post.fkcustomers_senhaby)}{$smarty.post.fkcustomers_senhaby}{else}{if isset($tab_2['fkcustomers_ws']) and $tab_2['fkcustomers_ws'] == 'BY'}{$tab_2['senha']}{/if}{/if}">
                </div>
            </div>

            <div class="fkcustomers-form">
                <label class="fkcustomers-label fkcustomers-col-lg-10"></label>
                <div class="fkcustomers-float-left">
                    <input type="radio" name="fkcustomers_ws" value="AC" {if isset($tab_2['fkcustomers_ws']) and $tab_2['fkcustomers_ws'] == 'AC'}checked="checked"{/if}>
                </div>
                <label class="fkcustomers-label-right fkcustomers-col-lg-auto">
                    {l s="AutoCep" mod="fkcustomers"}
                </label>
            </div>
            <div class="fkcustomers-form">
                <label for="fkcustomers_codigoac" class="fkcustomers-label fkcustomers-col-lg-20">
                    {l s="Código" mod="fkcustomers"}
                </label>
                <div class="fkcustomers-col-lg-15 fkcustomers-float-left">
                    <input type="text" name="fkcustomers_codigoac" id="fkcustomers_codigoac" value="{if isset($smarty.post.fkcustomers_codigoac)}{$smarty.post.fkcustomers_codigoac}{else}{if isset($tab_2['fkcustomers_ws']) and $tab_2['fkcustomers_ws'] == 'AC'}{$tab_2['usuario']}{/if}{/if}">
                </div>
            </div>
            <div class="fkcustomers-form">
                <label for="fkcustomers_chaveac" class="fkcustomers-label fkcustomers-col-lg-20">
                    {l s="Chave" mod="fkcustomers"}
                </label>
                <div class="fkcustomers-col-lg-15 fkcustomers-float-left">
                    <input type="password" name="fkcustomers_chaveac" id="fkcustomers_chaveac" value="{if isset($smarty.post.fkcustomers_chaveac)}{$smarty.post.fkcustomers_chaveac}{else}{if isset($tab_2['fkcustomers_ws']) and $tab_2['fkcustomers_ws'] == 'AC'}{$tab_2['senha']}{/if}{/if}">
                </div>
            </div>

        </div>

        <div class="fkcustomers-panel fkcustomers-col-lg-70">

            <div class="fkcustomers-panel-heading">
                {l s="Grupo de clientes para Pessoa Jurídica" mod="fkcustomers"}
            </div>

            {foreach $tab_2['grupo_clientes'] as $grupo}
                <div class="fkcustomers-form">
                    <label class="fkcustomers-label fkcustomers-col-lg-10"></label>
                    <div class="fkcustomers-float-left">
                        <input type="radio" name="fkcustomers_grupo" id="fkcustomers_grupo" value="{$grupo['id_group']}" {if isset($tab_2['fkcustomers_grupo']) and $tab_2['fkcustomers_grupo'] == {$grupo['id_group']}}checked="checked"{/if}>
                    </div>
                    <label class="fkcustomers-label-right fkcustomers-col-lg-auto">
                        {$grupo['name']}
                    </label>
                </div>
            {/foreach}

        </div>

        <div class="fkcustomers-panel fkcustomers-col-lg-70">

            <div class="fkcustomers-panel-heading">
                {l s="DDD Válidos" mod="fkcustomers"}
            </div>

            <div class="fkcustomers-form">
                <label class="fkcustomers-label fkcustomers-col-lg-10"></label>
                <div class="fkcustomers-float-left fkcustomers-col-lg-80">
                    <textarea name="fkcustomers_ddd" id="fkcustomers_ddd">
                        {if isset($smarty.post.fkcustomers_ddd)}{$smarty.post.fkcustomers_ddd}{else}{if isset($tab_2['fkcustomers_ddd'])}{$tab_2['fkcustomers_ddd']}{/if}{/if}
                    </textarea>
                </div>
                <div class="fkcustomers-form">
                    <label class="fkcustomers-label fkcustomers-col-lg-10"></label>
                    <span>{l s="Informe o DDD entre pipe (|)" mod="fkcustomers"}</span>
                </div>
            </div>

        </div>

        <div class="fkcustomers-panel fkcustomers-col-lg-70">

            <div class="fkcustomers-panel-heading">
                {l s="Configurações Diversas" mod="fkcustomers"}
            </div>

            <div class="fkcustomers-form">
                <label class="fkcustomers-label fkcustomers-col-lg-10"></label>
                <div class="fkcustomers-float-left">
                    <input type="checkbox" name="fkcustomers_rg_req" id="fkcustomers_rg_req" value="on" {if isset($smarty.post.fkcustomers_rg_req) and $smarty.post.fkcustomers_rg_req == 'on'}checked="checked"{else}{if isset($tab_2['fkcustomers_rg_req']) and $tab_2['fkcustomers_rg_req'] == 'on'}checked="checked"{/if}{/if}>
                </div>
                <label class="fkcustomers-label-right fkcustomers-col-lg-auto">
                    {l s="Campo RG obrigatório" mod="fkcustomers"}
                </label>
            </div>

            <div class="fkcustomers-form">
                <label class="fkcustomers-label fkcustomers-col-lg-10"></label>
                <div class="fkcustomers-float-left">
                    <input type="checkbox" name="fkcustomers_ie_req" id="fkcustomers_ie_req" value="on" {if isset($smarty.post.fkcustomers_ie_req) and $smarty.post.fkcustomers_ie_req == 'on'}checked="checked"{else}{if isset($tab_2['fkcustomers_ie_req']) and $tab_2['fkcustomers_ie_req'] == 'on'}checked="checked"{/if}{/if}>
                </div>
                <label class="fkcustomers-label-right fkcustomers-col-lg-auto">
                    {l s="Campo IE obrigatório" mod="fkcustomers"}
                </label>
            </div>

            <div class="fkcustomers-form">
                <label class="fkcustomers-label fkcustomers-col-lg-10"></label>
                <div class="fkcustomers-float-left">
                    <input type="checkbox" name="fkcustomers_dupl_cpf_cnpj" id="fkcustomers_dupl_cpf_cnpj" value="on" {if isset($smarty.post.fkcustomers_dupl_cpf_cnpj) and $smarty.post.fkcustomers_dupl_cpf_cnpj == 'on'}checked="checked"{else}{if isset($tab_2['fkcustomers_dupl_cpf_cnpj']) and $tab_2['fkcustomers_dupl_cpf_cnpj'] == 'on'}checked="checked"{/if}{/if}>
                </div>
                <label class="fkcustomers-label-right fkcustomers-col-lg-auto">
                    {l s="Verificar duplicidade de CPF e CNPJ" mod="fkcustomers"}
                </label>
            </div>

            <div class="fkcustomers-form">
                <label class="fkcustomers-label fkcustomers-col-lg-10"></label>
                <div class="fkcustomers-float-left">
                    <input type="checkbox" name="fkcustomers_delcampos" id="fkcustomers_delcampos" value="on" onclick="confirmaDelCampos('Atenção: Você marcou para excluir da tabela os novos campos criados quando o módulo for desinstalado. Confirma?','fkcustomers_delcampos')" {if isset($smarty.post.fkcustomers_delcampos) and $smarty.post.fkcustomers_delcampos == 'on'}checked="checked"{else}{if isset($tab_2['fkcustomers_delcampos']) and $tab_2['fkcustomers_delcampos'] == 'on'}checked="checked"{/if}{/if}>
                </div>
                <label class="fkcustomers-label-right fkcustomers-col-lg-auto">
                    {l s="Excluir da tabela os novos campos criados quando o módulo for desinstalado" mod="fkcustomers"}
                </label>
            </div>

        </div>

        <div class="fkcustomers-panel-footer">
            <button type="submit" value="1" name="btnSubmit" class="fkcustomers-button fkcustomers-float-right">
                <i class="process-icon-save"></i>
                {l s="Salvar" mod="fkcustomers"}
            </button>
        </div>

    </div>

</form>