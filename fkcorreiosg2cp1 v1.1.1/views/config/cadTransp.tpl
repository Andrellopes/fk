<div class="fkcorreiosg2-panel">

    <div class="fkcorreiosg2-panel-heading">
        {l s="Transportadoras" mod="fkcorreiosg2cp1"}
    </div>

    <div class="fkcorreiosg2-panel-header">
        <form id="configuration_form" class="defaultForm  form-horizontal" action="{$tab_3['formAction']}&origem=cadTransp" method="post">
            <button type="submit" value="1" name="btnAddTransp" class="fkcorreiosg2-button fkcorreiosg2-float-left">
                <i class="process-icon-new"></i>
                {l s="Incluir Transportadora" mod="fkcorreiosg2cp1"}
            </button>
        </form>

        <button type="button" value="1" name="btnAjuda" class="fkcorreiosg2-button fkcorreiosg2-float-right" onClick="window.open('http://www.modulosfk.com.br/modulosfk/ajuda/fkcorreiosg2cp1_v1_0_0.pdf','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=900,height=500,left=500,top=150'); return false;">
            <i class="process-icon-help"></i>
            {l s="Ajuda" mod="fkcorreiosg2cp1"}
        </button>
    </div>

    {if isset($tab_3['transportadoras'])}
        {foreach $tab_3['transportadoras'] as $regTransp}

            <div class="fkcorreiosg2-panel">
                <div class="fkcorreiosg2-panel-heading {if isset($regTransp['ativo']) and $regTransp['ativo'] == '1'}fkcorreiosg2-toggle{else}fkcorreiosg2-toggle-inativo{/if}" onclick="fkcorreiosg2cp1Toggle('fkcorreiosg2cp1_toggle_itens_' + {$regTransp['id']})">
                    <i class="icon-resize-full"></i>
                    {$regTransp['nome_transp']}
                </div>

                {if $fkcorreiosg2cp1['abrirTransp'] == $regTransp['id']}
                    {assign var="classToggleTransp" value="fkcorreiosg2-toggle-item-open"}
                {else}
                    {assign var="classToggleTransp" value="fkcorreiosg2-toggle-item-close"}
                {/if}

                <div class="{$classToggleTransp}" id="fkcorreiosg2cp1_toggle_itens_{$regTransp['id']}">

                    <div class="fkcorreiosg2-panel">

                        <div class="fkcorreiosg2-panel-heading fkcorreiosg2-toggle" onclick="fkcorreiosg2cp1Toggle('fkcorreiosg2cp1_toggle_item_geral_' + {$regTransp['id']})">
                            <i class="icon-resize-full"></i>
                            {l s="Dados Gerais" mod="fkcorreiosg2cp1"}
                        </div>

                        <form id="configuration_form" class="defaultForm form-horizontal" action="{$tab_3['formAction']}&origem=cadTransp&idTransp={$regTransp['id']}" method="post" enctype="multipart/form-data">

                            {*** Campo hidden para controle de POST - mostra o Dados Gerais aberto/fechado ***}
                            <input type="hidden" name="fkcorreiosg2cp1_transp_post_{$regTransp['id']}">

                            {assign var="temp" value="fkcorreiosg2cp1_transp_post_`$regTransp['id']`"}
                            {if isset($smarty.post.$temp)}
                                {assign var="classToggleGeral" value="fkcorreiosg2-toggle-item-open"}
                            {else}
                                {assign var="classToggleGeral" value="fkcorreiosg2-toggle-item-close"}
                            {/if}

                            <div class="{$classToggleGeral}" id="fkcorreiosg2cp1_toggle_item_geral_{$regTransp['id']}">

                                <div class="fkcorreiosg2-form">
                                    <label class="fkcorreiosg2-label fkcorreiosg2-col-lg-15"></label>
                                    <div class="fkcorreiosg2-float-left">
                                        {assign var="temp" value="fkcorreiosg2cp1_transp_ativo_`$regTransp['id']`"}
                                        <input type="checkbox" name="fkcorreiosg2cp1_transp_ativo_{$regTransp['id']}" value="on" {if isset($smarty.post.$temp) and $smarty.post.$temp == 'on'}checked="checked"{else}{if isset($regTransp['ativo']) and $regTransp['ativo'] == '1'}checked="checked"{/if}{/if}>
                                    </div>
                                    <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                        {l s="Ativo" mod="fkcorreiosg2cp1"}
                                    </label>
                                </div>

                                <div class="fkcorreiosg2-panel fkcorreiosg2-sub-panel fkcorreiosg2-col-lg-40">
                                    <div class="fkcorreiosg2-panel-heading">
                                        {l s="Nome Transportadora" mod="fkcorreiosg2cp1"}
                                    </div>

                                    <div class="fkcorreiosg2-form">
                                        <div class="fkcorreiosg2-col-lg-70 fkcorreiosg2-float-left">
                                            {assign var="temp" value="fkcorreiosg2cp1_transp_nome_`$regTransp['id']`"}
                                            <input type="text" name="fkcorreiosg2cp1_transp_nome_{$regTransp['id']}" id="fkcorreiosg2cp1_transp_nome_{$regTransp['id']}" maxlength="64" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regTransp['nome_transp']}{/if}">
                                        </div>
                                    </div>
                                </div>

                                <div class="fkcorreiosg2-panel fkcorreiosg2-sub-panel fkcorreiosg2-col-lg-40">
                                    <div class="fkcorreiosg2-panel-heading">
                                        {l s="Grade de Velocidade" mod="fkcorreiosg2cp1"}
                                    </div>

                                    <div class="fkcorreiosg2-form">
                                        <div class="fkcorreiosg2-col-lg-20 fkcorreiosg2-float-left">
                                            {assign var="temp" value="fkcorreiosg2cp1_transp_grade_`$regTransp['id']`"}
                                            <input type="text" name="fkcorreiosg2cp1_transp_grade_{$regTransp['id']}" id="fkcorreiosg2cp1_transp_grade_{$regTransp['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regTransp['grade']}{/if}">
                                        </div>
                                    </div>
                                </div>

                                <div class="fkcorreiosg2-panel fkcorreiosg2-sub-panel fkcorreiosg2-col-lg-40">

                                    <div class="fkcorreiosg2-panel-heading">
                                        {l s="Logo da Transportadora" mod="fkcorreiosg2cp1"}
                                    </div>

                                    {assign var="urlLogoTransp" value="`$tab_3['urlLogoPS']``$regTransp['id_carrier']`.jpg"}
                                    {assign var="uriLogoTransp" value="`$tab_3['uriLogoPS']``$regTransp['id_carrier']`.jpg"}
                                    {assign var="urlNoImage" value="`$tab_3['urlImg']`no_image.jpg"}

                                    <div class="fkcorreiosg2-form">
                                        {if file_exists({$uriLogoTransp})}
                                            <img id="fkcorreiosg2cp1_logo_transp_{$regTransp['id']}" alt="Logo transportadora" src="{$urlLogoTransp}">
                                        {else}
                                            <img id="fkcorreiosg2cp1_logo_transp_{$regTransp['id']}" alt="Logo transportadora" src="{$urlNoImage}">
                                        {/if}
                                    </div>

                                    <div class="fkcorreiosg2-form">
                                        <input class="btn btn-default" type="file" name="fkcorreiosg2cp1_transp_logo_{$regTransp['id']}">
                                    </div>
                                    <p class="help-block">
                                        Formato jpg
                                        <br>
                                        Tamanho máximo do arquivo 8 MB
                                    </p>

                                    {if file_exists({$uriLogoTransp})}
                                        <script type="text/javascript">
                                            d = new Date();
                                            idLogo = '#fkcorreiosg2cp1_logo_transp_' + {$regTransp['id']};
                                            $(idLogo).attr("src", "{$urlLogoTransp}?" + d.getTime());
                                        </script>
                                    {/if}

                                </div>

                                <div class="fkcorreiosg2-panel-footer">
                                    <button type="submit" value="1" name="btnSubmitTransp" class="fkcorreiosg2-button fkcorreiosg2-float-right">
                                        <i class="process-icon-save"></i>
                                        {l s="Salvar" mod="fkcorreiosg2cp1"}
                                    </button>
                                </div>

                            </div>

                        </form>
                    </div>

                    <div class="fkcorreiosg2-panel">

                        <div class="fkcorreiosg2-panel-heading fkcorreiosg2-toggle" onclick="fkcorreiosg2cp1Toggle('fkcorreiosg2cp1_toggle_item_regras_preco_' + {$regTransp['id']})">
                            <i class="icon-resize-full"></i>
                            {l s="Regras de Preços" mod="fkcorreiosg2cp1"}
                        </div>

                        {if $fkcorreiosg2cp1['abrirRegra'] == $regTransp['id']}
                            {assign var="classToggleRegra" value="fkcorreiosg2-toggle-item-open"}
                        {else}
                            {assign var="classToggleRegra" value="fkcorreiosg2-toggle-item-close"}
                        {/if}

                        <div class="{$classToggleRegra}" id="fkcorreiosg2cp1_toggle_item_regras_preco_{$regTransp['id']}">

                            <div class="fkcorreiosg2-panel-header fkcorreiosg2-panel-header-sub-panel">
                                <form id="configuration_form" class="defaultForm  form-horizontal" action="{$tab_3['formAction']}&origem=cadRegras&idTransp={$regTransp['id']}" method="post">
                                    <button type="submit" value="1" name="btnAddRegra" class="fkcorreiosg2-button fkcorreiosg2-float-left">
                                        <i class="process-icon-new"></i>
                                        {l s="Incluir Regra de Preços" mod="fkcorreiosg2cp1"}
                                    </button>
                                </form>
                            </div>

                            {if isset($tab_3['regrasPrecos'])}
                                {foreach $tab_3['regrasPrecos'] as $regRegra}
                                    {if $regRegra['id_transp'] == $regTransp['id']}
                                        <form id="configuration_form" class="defaultForm form-horizontal" action="{$tab_3['formAction']}&origem=cadRegras&idRegra={$regRegra['id']}" method="post">

                                            <div class="fkcorreiosg2-panel">

                                                <div class="fkcorreiosg2-panel-heading {if isset($regRegra['ativo']) and $regRegra['ativo'] == '1'}fkcorreiosg2-toggle{else}fkcorreiosg2-toggle-inativo{/if}" onclick="fkcorreiosg2cp1Toggle('fkcorreiosg2cp1_toggle_item_regra_' + {$regRegra['id']})">
                                                    <i class="icon-resize-full"></i>
                                                    {$regRegra['nome_regra']}
                                                </div>

                                                {*** Campo hidden para controle de POST - mostra as Regras de precos aberto/fechado ***}
                                                <input type="hidden" name="fkcorreiosg2cp1_regra_post_{$regRegra['id']}">

                                                {assign var="temp" value="fkcorreiosg2cp1_regra_post_`$regRegra['id']`"}
                                                {if isset($smarty.post.$temp)}
                                                    {assign var="classToggleItem" value="fkcorreiosg2-toggle-item-open"}
                                                {else}
                                                    {assign var="classToggleItem" value="fkcorreiosg2-toggle-item-close"}
                                                {/if}

                                                <div class="{$classToggleItem}" id="fkcorreiosg2cp1_toggle_item_regra_{$regRegra['id']}">

                                                    <div class="fkcorreiosg2-form">
                                                        <label class="fkcorreiosg2-label fkcorreiosg2-col-lg-15"></label>
                                                        <div class="fkcorreiosg2-float-left">
                                                            {assign var="temp" value="fkcorreiosg2cp1_regra_ativo_`$regRegra['id']`"}
                                                            <input type="checkbox" name="fkcorreiosg2cp1_regra_ativo_{$regRegra['id']}" value="on" {if isset($smarty.post.$temp) and $smarty.post.$temp == 'on'}checked="checked"{else}{if isset($regRegra['ativo']) and $regRegra['ativo'] == '1'}checked="checked"{/if}{/if}>
                                                        </div>
                                                        <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                                            {l s="Ativo" mod="fkcorreiosg2cp1"}
                                                        </label>
                                                    </div>

                                                    <div class="fkcorreiosg2-panel fkcorreiosg2-sub-panel fkcorreiosg2-col-lg-40">
                                                        <div class="fkcorreiosg2-panel-heading">
                                                            {l s="Nome da Regra" mod="fkcorreiosg2cp1"}
                                                        </div>

                                                        <div class="fkcorreiosg2-form">
                                                            <div class="fkcorreiosg2-col-lg-70 fkcorreiosg2-float-left">
                                                                {assign var="temp" value="fkcorreiosg2cp1_regra_nome_`$regRegra['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp1_regra_nome_{$regRegra['id']}" id="fkcorreiosg2cp1_regra_nome_{$regRegra['id']}" maxlength="100" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegra['nome_regra']}{/if}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="fkcorreiosg2-panel fkcorreiosg2-sub-panel fkcorreiosg2-col-lg-40">
                                                    
                                                        <div class="fkcorreiosg2-panel-heading">
                                                            {l s="Tipo de Regra" mod="fkcorreiosg2cp1"}
                                                        </div>

                                                        {assign var="temp" value="fkcorreiosg2cp1_regra_tipo_`$regRegra['id']`"}
                                                        
                                                        {*** Se deve ser mostrado ou nao as opcoes de formula baseado no Tipo de Regra ***}
	                                                    {if isset($smarty.post.$temp)}
	                                                        {if $smarty.post.$temp == '1'}
	                                                            {assign var="classMostrar" value="fkcorreiosg2cp1-display-none"}
	                                                        {else}
	                                                            {assign var="classMostrar" value="fkcorreiosg2cp1-display-block"}
	                                                        {/if}
	                                                    {else}
	                                                        {if $regRegra['tipo_regra'] == '1'}
	                                                            {assign var="classMostrar" value="fkcorreiosg2cp1-display-none"}
	                                                        {else}
	                                                            {assign var="classMostrar" value="fkcorreiosg2cp1-display-block"}
	                                                        {/if}
	                                                    {/if}

                                                        <div class="fkcorreiosg2-form">
                                                            <div class="fkcorreiosg2-float-left">
                                                                <input type="radio" name="fkcorreiosg2cp1_regra_tipo_{$regRegra['id']}" value="1" onclick="fkcorreiosg2cp1MostraFormula(this, {$regRegra['id']})" {if isset($smarty.post.$temp) and $smarty.post.$temp == '1'}checked="checked"{else}{if isset($regRegra['tipo_regra']) and $regRegra['tipo_regra'] == '1'}checked="checked"{/if}{/if}>
                                                            </div>
                                                            <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                                                {l s="Valor Fixo" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                        </div>
                                                        <div class="fkcorreiosg2-form">
                                                            <div class="fkcorreiosg2-float-left">
                                                                <input type="radio" name="fkcorreiosg2cp1_regra_tipo_{$regRegra['id']}" value="2" onclick="fkcorreiosg2cp1MostraFormula(this, {$regRegra['id']})" {if isset($smarty.post.$temp) and $smarty.post.$temp == '2'}checked="checked"{else}{if isset($regRegra['tipo_regra']) and $regRegra['tipo_regra'] == '2'}checked="checked"{/if}{/if}>
                                                            </div>
                                                            <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                                                {l s="Valor com Base no Valor do Pedido" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                        </div>
                                                        <div class="fkcorreiosg2-form">
                                                            <div class="fkcorreiosg2-float-left">
                                                                <input type="radio" name="fkcorreiosg2cp1_regra_tipo_{$regRegra['id']}" value="3" onclick="fkcorreiosg2cp1MostraFormula(this, {$regRegra['id']})" {if isset($smarty.post.$temp) and $smarty.post.$temp == '3'}checked="checked"{else}{if isset($regRegra['tipo_regra']) and $regRegra['tipo_regra'] == '3'}checked="checked"{/if}{/if}>
                                                            </div>
                                                            <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                                                {l s="Valor com Base no Valor do Frete" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                        </div>
                                                        <div class="fkcorreiosg2-form">
                                                            <div class="fkcorreiosg2-float-left">
                                                                <input type="radio" name="fkcorreiosg2cp1_regra_tipo_{$regRegra['id']}" value="4" onclick="fkcorreiosg2cp1MostraFormula(this, {$regRegra['id']})" {if isset($smarty.post.$temp) and $smarty.post.$temp == '4'}checked="checked"{else}{if isset($regRegra['tipo_regra']) and $regRegra['tipo_regra'] == '4'}checked="checked"{/if}{/if}>
                                                            </div>
                                                            <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                                                {l s="Valor com Base no Peso do Pedido" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                        </div>
                                                        <div class="fkcorreiosg2-form">
                                                            <div class="fkcorreiosg2-float-left">
                                                                <input type="radio" name="fkcorreiosg2cp1_regra_tipo_{$regRegra['id']}" value="5" onclick="fkcorreiosg2cp1MostraFormula(this, {$regRegra['id']})" {if isset($smarty.post.$temp) and $smarty.post.$temp == '5'}checked="checked"{else}{if isset($regRegra['tipo_regra']) and $regRegra['tipo_regra'] == '5'}checked="checked"{/if}{/if}>
                                                            </div>
                                                            <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                                                {l s="Percentual com Base no Valor do Pedido" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                        </div>
                                                        <div class="fkcorreiosg2-form">
                                                            <div class="fkcorreiosg2-float-left">
                                                                <input type="radio" name="fkcorreiosg2cp1_regra_tipo_{$regRegra['id']}" value="6" onclick="fkcorreiosg2cp1MostraFormula(this, {$regRegra['id']})" {if isset($smarty.post.$temp) and $smarty.post.$temp == '6'}checked="checked"{else}{if isset($regRegra['tipo_regra']) and $regRegra['tipo_regra'] == '6'}checked="checked"{/if}{/if}>
                                                            </div>
                                                            <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                                                {l s="Percentual com Base no Valor do Frete" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                        </div>
                                                        <div class="fkcorreiosg2-form">
                                                            <label class="fkcorreiosg2-label">
                                                                {l s="Valor/Percentual:" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                            <div class="fkcorreiosg2-col-lg-30 fkcorreiosg2-float-left">
                                                                {assign var="temp" value="fkcorreiosg2cp1_regra_tipo_valor_`$regRegra['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp1_regra_tipo_valor_{$regRegra['id']}" id="fkcorreiosg2cp1_regra_tipo_valor_{$regRegra['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegra['tipo_regra_valor']}{/if}">
                                                            </div>
                                                            <p class="help-block fkcorreiosg2-clear">
                                                                Informe o valor ou percentual a ser acrescentado ao frete
                                                            </p>
                                                        </div>

                                                    </div>

                                                    <div class="fkcorreiosg2-panel fkcorreiosg2-sub-panel fkcorreiosg2-col-lg-40 {$classMostrar}" id="fkcorreiosg2cp1_formula_{$regRegra['id']}">
                                                        <div class="fkcorreiosg2-panel-heading">
                                                            {l s="Fórmula" mod="fkcorreiosg2cp1"}
                                                        </div>

                                                        {assign var="temp" value="fkcorreiosg2cp1_regra_formula_`$regRegra['id']`"}

                                                        <div class="fkcorreiosg2-form">
                                                            <div class="fkcorreiosg2-float-left">
                                                                <input type="radio" name="fkcorreiosg2cp1_regra_formula_{$regRegra['id']}" value="1" {if isset($smarty.post.$temp) and $smarty.post.$temp == '1'}checked="checked"{else}{if isset($regRegra['formula_regra']) and $regRegra['formula_regra'] == '1'}checked="checked"{/if}{/if}>
                                                            </div>
                                                            <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                                                {l s="por Intervalo" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                        </div>
                                                        <div class="fkcorreiosg2-form">
                                                            <div class="fkcorreiosg2-float-left">
                                                                <input type="radio" name="fkcorreiosg2cp1_regra_formula_{$regRegra['id']}" value="2" {if isset($smarty.post.$temp) and $smarty.post.$temp == '2'}checked="checked"{else}{if isset($regRegra['formula_regra']) and $regRegra['formula_regra'] == '2'}checked="checked"{/if}{/if}>
                                                            </div>
                                                            <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                                                {l s="por Valor Acima" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                        </div>
                                                        <div class="fkcorreiosg2-form">
                                                            <label class="fkcorreiosg2-label">
                                                                {l s="Valor:" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                            <div class="fkcorreiosg2-col-lg-30 fkcorreiosg2-float-left">
                                                                {assign var="temp" value="fkcorreiosg2cp1_regra_formula_valor_`$regRegra['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp1_regra_formula_valor_{$regRegra['id']}" id="fkcorreiosg2cp1_regra_formula_valor_{$regRegra['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegra['formula_regra_valor']}{/if}">
                                                            </div>
                                                            <p class="help-block fkcorreiosg2-clear">
                                                                Informe o valor "por Intervalo" ou "por Valor Acima" que a fórmula deve ser aplicada.
                                                            </p>
                                                        </div>
                                                        <div class="fkcorreiosg2-form">
                                                            <label class="fkcorreiosg2-label">
                                                                {l s="Valor Mínimo a ser Assumido:" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                            <div class="fkcorreiosg2-col-lg-30 fkcorreiosg2-float-left">
                                                                {assign var="temp" value="fkcorreiosg2cp1_regra_formula_valor_minimo_`$regRegra['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp1_regra_formula_valor_minimo_{$regRegra['id']}" id="fkcorreiosg2cp1_regra_formula_valor_minimo_{$regRegra['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegra['formula_regra_valor_minimo']}{/if}">
                                                            </div>
                                                            <p class="help-block fkcorreiosg2-clear">
                                                                Informe o valor mínimo que será assumido caso o valor da fórmula não atinja o valor.
                                                                <br>
                                                                Informe 0 (zero) para não considerar valor mínimo para a fórmula.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="fkcorreiosg2-panel-footer">
                                                        <button type="submit" value="1" name="btnDelRegra" class="fkcorreiosg2-button fkcorreiosg2-float-left" onclick="return fkcorreiosg2cp1Excluir('{l s="Confirma a exclusão da Regra de Preço?" mod="fkcorreiosg2cp1"}');">
                                                            <i class="process-icon-delete"></i>
                                                            {l s="Excluir Regra de Preço" mod="fkcorreiosg2cp1"}
                                                        </button>

                                                        <button type="submit" value="1" name="btnSubmitRegra" class="fkcorreiosg2-button fkcorreiosg2-float-right">
                                                            <i class="process-icon-save"></i>
                                                            {l s="Salvar" mod="fkcorreiosg2cp1"}
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>

                                        </form>
                                    {/if}
                                {/foreach}
                            {/if}

                        </div>

                    </div>

                    <div class="fkcorreiosg2-panel">

                        <div class="fkcorreiosg2-panel-heading fkcorreiosg2-toggle" onclick="fkcorreiosg2cp1Toggle('fkcorreiosg2cp1_toggle_item_regioes_' + {$regTransp['id']})">
                            <i class="icon-resize-full"></i>
                            {l s="Regiões" mod="fkcorreiosg2cp1"}
                        </div>


                        {if $fkcorreiosg2cp1['abrirRegiao'] == $regTransp['id']}
                            {assign var="classToggleRegiao" value="fkcorreiosg2-toggle-item-open"}
                        {else}
                            {assign var="classToggleRegiao" value="fkcorreiosg2-toggle-item-close"}
                        {/if}

                        <div class="{$classToggleRegiao}" id="fkcorreiosg2cp1_toggle_item_regioes_{$regTransp['id']}">

                            <div class="fkcorreiosg2-panel-header fkcorreiosg2-panel-header-sub-panel">
                                <form id="configuration_form" class="defaultForm  form-horizontal" action="{$tab_3['formAction']}&origem=cadRegioes&idTransp={$regTransp['id']}" method="post">
                                    <button type="submit" value="1" name="btnAddRegiao" class="fkcorreiosg2-button fkcorreiosg2-float-left">
                                        <i class="process-icon-new"></i>
                                        {l s="Incluir Região" mod="fkcorreiosg2cp1"}
                                    </button>
                                </form>
                            </div>

                            {if isset($tab_3['regioes'])}
                                {foreach $tab_3['regioes'] as $regRegiao}
                                    {if $regRegiao['id_transp'] == $regTransp['id']}
                                        <form id="configuration_form" class="defaultForm form-horizontal" action="{$tab_3['formAction']}&origem=cadRegioes&idRegiao={$regRegiao['id']}" method="post">

                                            <div class="fkcorreiosg2-panel">

                                                <div class="fkcorreiosg2-panel-heading {if isset($regRegiao['ativo']) and $regRegiao['ativo'] == '1'}fkcorreiosg2-toggle{else}fkcorreiosg2-toggle-inativo{/if}" onclick="fkcorreiosg2cp1Toggle('fkcorreiosg2cp1_toggle_item_regiao_' + {$regRegiao['id']})">
                                                    <i class="icon-resize-full"></i>
                                                    {$regRegiao['nome_regiao']}
                                                </div>

                                                {*** Campo hidden para controle de POST - mostra as Regioes aberto/fechado ***}
                                                <input type="hidden" name="fkcorreiosg2cp1_regiao_post_{$regRegiao['id']}">

                                                {assign var="temp" value="fkcorreiosg2cp1_regiao_post_`$regRegiao['id']`"}
                                                {if isset($smarty.post.$temp)}
                                                    {assign var="classToggleItem" value="fkcorreiosg2-toggle-item-open"}
                                                {else}
                                                    {assign var="classToggleItem" value="fkcorreiosg2-toggle-item-close"}
                                                {/if}

                                                <div class="{$classToggleItem}" id="fkcorreiosg2cp1_toggle_item_regiao_{$regRegiao['id']}">

                                                    <div class="fkcorreiosg2-form">
                                                        <label class="fkcorreiosg2-label fkcorreiosg2-col-lg-15"></label>
                                                        <div class="fkcorreiosg2-float-left">
                                                            {assign var="temp" value="fkcorreiosg2cp1_regiao_ativo_`$regRegiao['id']`"}
                                                            <input type="checkbox" name="fkcorreiosg2cp1_regiao_ativo_{$regRegiao['id']}" value="on" {if isset($smarty.post.$temp) and $smarty.post.$temp == 'on'}checked="checked"{else}{if isset($regRegiao['ativo']) and $regRegiao['ativo'] == '1'}checked="checked"{/if}{/if}>
                                                        </div>
                                                        <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                                            {l s="Ativo" mod="fkcorreiosg2cp1"}
                                                        </label>
                                                    </div>

                                                    <div class="fkcorreiosg2-panel fkcorreiosg2-sub-panel fkcorreiosg2-regioes">

                                                        <div class="fkcorreiosg2-panel-heading">
                                                            {l s="Nome da Região" mod="fkcorreiosg2cp1"}
                                                        </div>

                                                        <div class="fkcorreiosg2-form">
                                                            <div class="fkcorreiosg2-col-lg-70 fkcorreiosg2-float-left">
                                                                {assign var="temp" value="fkcorreiosg2cp1_regiao_nome_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp1_regiao_nome_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_nome_{$regRegiao['id']}" maxlength="100" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['nome_regiao']}{/if}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="fkcorreiosg2-panel fkcorreiosg2-sub-panel fkcorreiosg2-regioes">

                                                        <div class="fkcorreiosg2-panel-heading">
                                                            {l s="Prazo de Entrega" mod="fkcorreiosg2cp1"}
                                                        </div>

                                                        <div class="fkcorreiosg2-form">
                                                            <div class="fkcorreiosg2-col-lg-70">
                                                                {assign var="temp" value="fkcorreiosg2cp1_regiao_prazo_entrega_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp1_regiao_prazo_entrega_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_prazo_entrega_{$regRegiao['id']}" maxlength="250" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['prazo_entrega']}{/if}">
                                                            </div>
                                                            <p class="help-block">
                                                                Se o valor deste campo for numérico será adicionado o Tempo de Preparação definido.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="fkcorreiosg2-panel fkcorreiosg2-sub-panel fkcorreiosg2-regioes">

                                                        <div class="fkcorreiosg2-panel-heading">
                                                            {l s="Peso Máximo por Produto (kg)" mod="fkcorreiosg2cp1"}
                                                        </div>

                                                        <div class="fkcorreiosg2-form">
                                                            <div class="fkcorreiosg2-col-lg-20">
                                                                {assign var="temp" value="fkcorreiosg2cp1_regiao_peso_maximo_produto_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp1_regiao_peso_maximo_produto_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_peso_maximo_produto_{$regRegiao['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['peso_maximo_produto']}{/if}">
                                                            </div>
                                                            <p class="help-block">
                                                                Informe 0 (zero) para não considerar o peso do produto.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="fkcorreiosg2-panel fkcorreiosg2-sub-panel fkcorreiosg2-regioes">

                                                        <div class="fkcorreiosg2-panel-heading">
                                                            {l s="Estados Atendidos" mod="fkcorreiosg2cp1"}
                                                        </div>

                                                        <div class="fkcorreiosg2-panel">

                                                            <div class="fkcorreiosg2-panel-heading">
                                                                {l s="Filtro" mod="fkcorreiosg2cp1"}
                                                            </div>

                                                            <div class="fkcorreiosg2-form">

                                                                {assign var="temp" value="fkcorreiosg2cp1_regiao_filtro_uf_`$regRegiao['id']`"}

                                                                <div class="fkcorreiosg2-float-left">
                                                                    <input type="radio" name="fkcorreiosg2cp1_regiao_filtro_uf_{$regRegiao['id']}" value="1" {if isset($smarty.post.$temp) and $smarty.post.$temp == 1}checked="checked"{else}{if isset($regRegiao['filtro_regiao_uf']) and $regRegiao['filtro_regiao_uf'] == '1'}checked="checked"{/if}{/if}>
                                                                </div>
                                                                <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                                                    {l s="Todo o Estado" mod="fkcorreiosg2cp1"}
                                                                </label>

                                                                <div class="fkcorreiosg2-float-left fkcorreiosg2-margin">
                                                                    <input type="radio" name="fkcorreiosg2cp1_regiao_filtro_uf_{$regRegiao['id']}" value="2" {if isset($smarty.post.$temp) and $smarty.post.$temp == 2}checked="checked"{else}{if isset($regRegiao['filtro_regiao_uf']) and $regRegiao['filtro_regiao_uf'] == '2'}checked="checked"{/if}{/if}>
                                                                </div>
                                                                <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                                                    {l s="Somente Capital" mod="fkcorreiosg2cp1"}
                                                                </label>

                                                                <div class="fkcorreiosg2-float-left fkcorreiosg2-margin">
                                                                    <input type="radio" name="fkcorreiosg2cp1_regiao_filtro_uf_{$regRegiao['id']}" value="3" {if isset($smarty.post.$temp) and $smarty.post.$temp == 3}checked="checked"{else}{if isset($regRegiao['filtro_regiao_uf']) and $regRegiao['filtro_regiao_uf'] == '3'}checked="checked"{/if}{/if}>
                                                                </div>
                                                                <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                                                    {l s="Somente Interior" mod="fkcorreiosg2cp1"}
                                                                </label>

                                                            </div>

                                                        </div>

                                                        {*** Variavel de controle de UFs por linha ***}
                                                        {assign var="totEstados" value=1}
                                                        {assign var="maxEstados" value=10}

                                                        <div class="fkcorreiosg2-form">
                                                            {foreach $tab_3['arrayUF'][$regRegiao['id']] as $uf}

                                                                {if $totEstados > $maxEstados}
                                                                    {assign var="totEstados" value=1}
                                                                {/if}

                                                                <div class="fkcorreiosg2-float-left">
                                                                    {assign var="temp" value="fkcorreiosg2cp1_regiao_uf_`$regRegiao['id']`"}
                                                                    <input class="fkcorreiosg2cp1_regiao_uf_{$regRegiao['id']}" type="checkbox" name="fkcorreiosg2cp1_regiao_uf_{$regRegiao['id']}[]" value="{$uf['uf']}" {if isset($smarty.post.$temp) and $smarty.post.$temp == $uf['uf']}checked="checked"{else}{if isset($uf['ativo']) and $uf['ativo'] == '1'}checked="checked"{/if}{/if}>
                                                                </div>
                                                                <label class="fkcorreiosg2-label-right fkcorreiosg2-col-lg-estados">
                                                                    {$uf['uf']}
                                                                </label>

                                                                {assign var="totEstados" value=$totEstados+1}

                                                                {if $totEstados > $maxEstados}
                                                                    <div class="fkcorreiosg2-clear">
                                                                        <br>
                                                                    </div>
                                                                {/if}

                                                            {/foreach}
                                                        </div>

                                                        <div class="fkcorreiosg2-panel-footer">
                                                            <button type="button" value="1" name="btnMarcar" class="fkcorreiosg2-button fkcorreiosg2-float-left" onclick="fkcorreiosg2cp1Marcar('fkcorreiosg2cp1_regiao_uf_' + {$regRegiao['id']})">
                                                                <i class="process-icon-ok"></i>
                                                                {l s="Marcar Todos" mod="fkcorreiosg2cp1"}
                                                            </button>

                                                            <button type="button" value="1" name="btnDesmarcar" class="fkcorreiosg2-button fkcorreiosg2-float-right" onclick="fkcorreiosg2cp1Desmarcar('fkcorreiosg2cp1_regiao_uf_' + {$regRegiao['id']})">
                                                                <i class="process-icon-cancel"></i>
                                                                {l s="Desmarcar Todos" mod="fkcorreiosg2cp1"}
                                                            </button>
                                                        </div>

                                                    </div>

                                                    <div class="fkcorreiosg2-panel fkcorreiosg2-sub-panel fkcorreiosg2-regioes">

                                                        <div class="fkcorreiosg2-panel-heading">
                                                            {l s="Intervalo de CEP Atendidos" mod="fkcorreiosg2cp1"}
                                                        </div>

                                                        <div class="fkcorreiosg2-form">

                                                            <div class="fkcorreiosg2-col-lg-20 fkcorreiosg2-float-left">
                                                                <input class="fkcorreiosg2cp1-mask-cep" type="text" name="fkcorreiosg2cp1_regiao_cep1_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_cep1_{$regRegiao['id']}" value="">
                                                            </div>

                                                            <div class="fkcorreiosg2-float-left">
                                                                <span id="fkcorreiosg2cp1_span_regiao">a</span>
                                                            </div>

                                                            <div class="fkcorreiosg2-col-lg-20 fkcorreiosg2-float-left">
                                                                <input class="fkcorreiosg2cp1-mask-cep" type="text" name="fkcorreiosg2cp1_regiao_cep2_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_cep2_{$regRegiao['id']}" value="">
                                                            </div>

                                                            <div class="fkcorreiosg2-float-left" id="fkcorreiosg2cp1_button_regiao">
                                                                <input class="fkcorreiosg2-button" name="button" type="button" value="{l s="Incluir" mod="fkcorreiosg2cp1"}" onclick="fkcorreiosg2cp1IncluirCepRegiao({$regRegiao['id']});">
                                                            </div>

                                                        </div>

                                                        <div class="fkcorreiosg2-form">

                                                            <div class="fkcorreiosg2-col-lg-90">
                                                                {assign var="temp" value="fkcorreiosg2cp1_regiao_cep_`$regRegiao['id']`"}
                                                                <textarea name="fkcorreiosg2cp1_regiao_cep_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_cep_{$regRegiao['id']}">{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['regiao_cep']}{/if}</textarea>
                                                            </div>
                                                            <p class="help-block">
                                                                Os intervalos de CEP aqui relacionados serão atendidos por esta Região independentemente dos Estados selecionados
                                                            </p>

                                                        </div>

                                                    </div>

                                                    <div class="fkcorreiosg2-panel fkcorreiosg2-sub-panel fkcorreiosg2-regioes">

                                                        <div class="fkcorreiosg2-panel-heading">
                                                            {l s="Intervalo de CEP Excluídos" mod="fkcorreiosg2cp1"}
                                                        </div>

                                                        <div class="fkcorreiosg2-form">

                                                            <div class="fkcorreiosg2-col-lg-20 fkcorreiosg2-float-left">
                                                                <input class="fkcorreiosg2cp1-mask-cep" type="text" name="fkcorreiosg2cp1_regiao_cep1_excluido_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_cep1_excluido_{$regRegiao['id']}" value="">
                                                            </div>

                                                            <div class="fkcorreiosg2-float-left">
                                                                <span id="fkcorreiosg2cp1_span_regiao">a</span>
                                                            </div>

                                                            <div class="fkcorreiosg2-col-lg-20 fkcorreiosg2-float-left">
                                                                <input class="fkcorreiosg2cp1-mask-cep" type="text" name="fkcorreiosg2cp1_regiao_cep2_excluido_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_cep2_excluido_{$regRegiao['id']}" value="">
                                                            </div>

                                                            <div class="fkcorreiosg2-float-left" id="fkcorreiosg2cp1_button_regiao">
                                                                <input class="fkcorreiosg2-button" name="button" type="button" value="{l s="Incluir" mod="fkcorreiosg2cp1"}" onclick="fkcorreiosg2cp1IncluirCepRegiaoExcluido({$regRegiao['id']});">
                                                            </div>

                                                        </div>

                                                        <div class="fkcorreiosg2-form">
                                                            <div class="fkcorreiosg2-col-lg-90">
                                                                {assign var="temp" value="fkcorreiosg2cp1_regiao_cep_excluido_`$regRegiao['id']`"}
                                                                <textarea name="fkcorreiosg2cp1_regiao_cep_excluido_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_cep_excluido_{$regRegiao['id']}">{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['regiao_cep_excluido']}{/if}</textarea>
                                                            </div>
                                                            <p class="help-block">
                                                                Os intervalos de CEP aqui relacionados não serão atendidos por esta Região
                                                            </p>
                                                        </div>

                                                    </div>

                                                    <div class="fkcorreiosg2-panel fkcorreiosg2-sub-panel fkcorreiosg2-regioes">

                                                        <div class="fkcorreiosg2-panel-heading">
                                                            {l s="Tabela de Preços" mod="fkcorreiosg2cp1"}
                                                        </div>

                                                        <div class="fkcorreiosg2-panel">

                                                            <div class="fkcorreiosg2-panel-heading">
                                                                {l s="Tipo de Tabela" mod="fkcorreiosg2cp1"}
                                                            </div>

                                                            {assign var="temp" value="fkcorreiosg2cp1_regiao_tipo_tabela_`$regRegiao['id']`"}

                                                            <div class="fkcorreiosg2-form">

                                                                <div class="fkcorreiosg2-float-left">
                                                                    <input type="radio" name="fkcorreiosg2cp1_regiao_tipo_tabela_{$regRegiao['id']}" value="1" {if isset($smarty.post.$temp) and $smarty.post.$temp == 1}checked="checked"{else}{if isset($regRegiao['tipo_tabela']) and $regRegiao['tipo_tabela'] == '1'}checked="checked"{/if}{/if}>
                                                                </div>
                                                                <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                                                    {l s="Preço Fixo por Intervalo de Peso" mod="fkcorreiosg2cp1"}
                                                                </label>

                                                                <div class="fkcorreiosg2-form">
                                                                    <div class="fkcorreiosg2-float-left">
                                                                        <input type="radio" name="fkcorreiosg2cp1_regiao_tipo_tabela_{$regRegiao['id']}" value="2" {if isset($smarty.post.$temp) and $smarty.post.$temp == 2}checked="checked"{else}{if isset($regRegiao['tipo_tabela']) and $regRegiao['tipo_tabela'] == '2'}checked="checked"{/if}{/if}>
                                                                    </div>
                                                                    <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                                                                        {l s="Preço por Kilo por Intervalo de Peso" mod="fkcorreiosg2cp1"}
                                                                    </label>
                                                                </div>

                                                            </div>

                                                        </div>

                                                        <div class="fkcorreiosg2-form">

                                                            <label class="fkcorreiosg2-label fkcorreiosg2cp1-col-lg-auto">
                                                                {l s="Até (kg):" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                            <div class="fkcorreiosg2-col-lg-20 fkcorreiosg2-float-left">
                                                                <input type="text" name="fkcorreiosg2cp1_regiao_tabela1_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_tabela1_{$regRegiao['id']}" value="">
                                                            </div>

                                                            <label class="fkcorreiosg2-label fkcorreiosg2cp1-col-lg-auto">
                                                                {l s="Cobrar o Valor:" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                            <div class="fkcorreiosg2-col-lg-20 fkcorreiosg2-float-left">
                                                                <input type="text" name="fkcorreiosg2cp1_regiao_tabela2_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_tabela2_{$regRegiao['id']}" value="">
                                                            </div>

                                                            <div class="fkcorreiosg2-float-left" id="fkcorreiosg2cp1_button_regiao">
                                                                <input class="fkcorreiosg2-button" name="button" type="button" value="{l s="Incluir" mod="fkcorreiosg2cp1"}" onclick="fkcorreiosg2cp1IncluirTabelaPreco({$regRegiao['id']});">
                                                            </div>

                                                        </div>

                                                        <div class="fkcorreiosg2-form">

                                                            <div class="fkcorreiosg2-col-lg-90">
                                                                {assign var="temp" value="fkcorreiosg2cp1_regiao_tabela_preco_`$regRegiao['id']`"}
                                                                <textarea name="fkcorreiosg2cp1_regiao_tabela_preco_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_tabela_preco_{$regRegiao['id']}">{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['tabela_preco']}{/if}</textarea>
                                                            </div>

                                                        </div>

                                                        <div class="fkcorreiosg2-form">
                                                            <label class="fkcorreiosg2-label">
                                                                {l s="Fator Cubagem (m³):" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                            <div class="fkcorreiosg2-float-left fkcorreiosg2-col-lg-20">
                                                                {assign var="temp" value="fkcorreiosg2cp1_regiao_fator_cubagem_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp1_regiao_fator_cubagem_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_fator_cubagem_{$regRegiao['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['fator_cubagem']}{/if}">
                                                            </div>
                                                            <p class="help-block fkcorreiosg2-clear">
                                                                Informe o fator utilizado pela transportadora para cálculo por volume.
                                                                <br>
                                                                Caso não seja utilizado informe 0 (zero).
                                                            </p>
                                                        </div>

                                                        <div class="fkcorreiosg2-form">
                                                            <label class="fkcorreiosg2-label">
                                                                {l s="Valor por Kilo Excedente:" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                            <div class="fkcorreiosg2-float-left fkcorreiosg2-col-lg-20">
                                                                {assign var="temp" value="fkcorreiosg2cp1_regiao_kilo_adicional_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp1_regiao_kilo_adicional_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_kilo_adicional_{$regRegiao['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['valor_adicional_kilo']}{/if}">
                                                            </div>
                                                            <p class="help-block fkcorreiosg2-clear">
                                                                Informe o valor a ser cobrado para cada kilo que exceder os previstos na tabela.
                                                            </p>
                                                        </div>

                                                        <div class="fkcorreiosg2-form">
                                                            <label class="fkcorreiosg2-label">
                                                                {l s="Valor Adicional Fixo:" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                            <div class="fkcorreiosg2-float-left fkcorreiosg2-col-lg-20">
                                                                {assign var="temp" value="fkcorreiosg2cp1_regiao_valor_adicional_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp1_regiao_valor_adicional_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_valor_adicional_{$regRegiao['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['valor_adicional_fixo']}{/if}">
                                                            </div>
                                                            <p class="help-block fkcorreiosg2-clear">
                                                                Informe o valor fixo a ser adicionado ao valor obtido no processamento da tabela.
                                                            </p>
                                                        </div>

                                                        <div class="fkcorreiosg2-form">
                                                            <label class="fkcorreiosg2-label">
                                                                {l s="Frete Mínimo:" mod="fkcorreiosg2cp1"}
                                                            </label>
                                                            <div class="fkcorreiosg2-float-left fkcorreiosg2-col-lg-20">
                                                                {assign var="temp" value="fkcorreiosg2cp1_regiao_frete_minimo_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp1_regiao_frete_minimo_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_frete_minimo_{$regRegiao['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['frete_minimo']}{/if}">
                                                            </div>
                                                            <p class="help-block fkcorreiosg2-clear">
                                                                Informe o valor mínimo que será assumido caso o frete não atinja o valor.
                                                                <br>
                                                                Informe 0 (zero) para não considerar valor mínimo para o frete.
                                                            </p>
                                                        </div>

                                                        <div class="fkcorreiosg2-panel">

                                                            <div class="fkcorreiosg2-panel-heading">
                                                                {l s="Desconto no Frete" mod="fkcorreiosg2cp1"}
                                                            </div>

                                                            <div class="fkcorreiosg2-form">
                                                                <label class="fkcorreiosg2-label fkcorreiosg2-col-lg-40">
                                                                    {l s="Percentual de Desconto:" mod="fkcorreiosg2cp1"}
                                                                </label>
                                                                <div class="fkcorreiosg2-col-lg-20 fkcorreiosg2-float-left">
                                                                    {assign var="temp" value="fkcorreiosg2cp1_regiao_percentual_desc_`$regRegiao['id']`"}
                                                                    <input type="text" name="fkcorreiosg2cp1_regiao_percentual_desc_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_percentual_desc_{$regRegiao['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['percentual_desconto']}{/if}">
                                                                </div>
                                                            </div>

                                                            <div class="fkcorreiosg2-form">
                                                                <label class="fkcorreiosg2-label fkcorreiosg2-col-lg-40">
                                                                    {l s="Valor do Pedido:" mod="fkcorreiosg2cp1"}
                                                                </label>
                                                                <div class="fkcorreiosg2-col-lg-20 fkcorreiosg2-float-left">
                                                                    {assign var="temp" value="fkcorreiosg2cp1_regiao_valor_pedido_desc_`$regRegiao['id']`"}
                                                                    <input type="text" name="fkcorreiosg2cp1_regiao_valor_pedido_desc_{$regRegiao['id']}" id="fkcorreiosg2cp1_regiao_valor_pedido_desc_{$regRegiao['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['valor_pedido_desconto']}{/if}">
                                                                </div>
                                                            </div>

                                                            <p class="help-block">
                                                                Informe o valor 0 (zero) nos campos "Percentual de Desconto" e "Valor do Pedido" para não aplicar desconto ao frete
                                                            </p>

                                                        </div>

                                                    </div>

                                                    <div class="fkcorreiosg2-panel-footer">
                                                        <button type="submit" value="1" name="btnDelRegiao" class="fkcorreiosg2-button fkcorreiosg2-float-left" onclick="return fkcorreiosg2cp1Excluir('{l s="Confirma a exclusão da Região?" mod="fkcorreiosg2cp1"}');">
                                                            <i class="process-icon-delete"></i>
                                                            {l s="Excluir Região" mod="fkcorreiosg2cp1"}
                                                        </button>

                                                        <button type="submit" value="1" name="btnSubmitRegiao" class="fkcorreiosg2-button fkcorreiosg2-float-right">
                                                            <i class="process-icon-save"></i>
                                                            {l s="Salvar" mod="fkcorreiosg2cp1"}
                                                        </button>
                                                    </div>

                                                </div>

                                            </div>

                                        </form>
                                    {/if}
                                {/foreach}
                            {/if}

                        </div>

                    </div>

                    <div class="fkcorreiosg2-panel-footer">
                        <form id="configuration_form" class="defaultForm form-horizontal" action="{$tab_3['formAction']}&origem=cadTransp&idTransp={$regTransp['id']}" method="post">
                            <button type="submit" value="1" name="btnDelTransp" class="fkcorreiosg2-button fkcorreiosg2-float-left" onclick="return fkcorreiosg2cp1Excluir('{l s="Confirma a exclusão da Transportadora?" mod="fkcorreiosg2cp1"}');">
                                <i class="process-icon-delete"></i>
                                {l s="Excluir Transportadora" mod="fkcorreiosg2cp1"}
                            </button>
                        </form>
                    </div>

                </div>

            </div>

        {/foreach}
    {/if}

</div>