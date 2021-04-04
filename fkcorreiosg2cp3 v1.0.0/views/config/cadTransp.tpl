<div class="fkcorreiosg2cp3-panel" style="border-top-left-radius: 0">

    <div class="fkcorreiosg2cp3-panel-header">
        <button type="button" value="1" name="btnAjuda" class="fkcorreiosg2cp3-button fkcorreiosg2cp3-float-right" onClick="window.open('http://www.fkmodulos.com/modulosfk/ajuda/fkcorreiosg2cp3.pdf','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=900,height=500,left=500,top=150'); return false;">
            <i class="process-icon-help"></i>
            {l s="Ajuda" mod="fkcorreiosg2cp3"}
        </button>
    </div>

    {if isset($tab_2['transportadoras'])}
        {foreach $tab_2['transportadoras'] as $regTransp}

            <div class="fkcorreiosg2cp3-panel">
                <div class="fkcorreiosg2cp3-panel-heading {if isset($regTransp['ativo']) and $regTransp['ativo'] == '1'}fkcorreiosg2cp3-toggle-ativo{else}fkcorreiosg2cp3-toggle-inativo{/if}" onclick="fkcorreiosg2cp3Toggle('fkcorreiosg2cp3_toggle_itens_' + {$regTransp['id']})">
                    <i class="icon-resize-full"></i>
                    {$regTransp['nome_transp']}
                </div>

                {if $fkcorreiosg2cp3['abrirTransp'] == $regTransp['id']}
                    {assign var="classToggleTransp" value="fkcorreiosg2cp3-toggle-item-open"}
                {else}
                    {assign var="classToggleTransp" value="fkcorreiosg2cp3-toggle-item-close"}
                {/if}

                <div class="{$classToggleTransp}" id="fkcorreiosg2cp3_toggle_itens_{$regTransp['id']}">

                    <div class="fkcorreiosg2cp3-panel">

                        <div class="fkcorreiosg2cp3-panel-heading fkcorreiosg2cp3-toggle-ativo" onclick="fkcorreiosg2cp3Toggle('fkcorreiosg2cp3_toggle_item_geral_' + {$regTransp['id']})">
                            <i class="icon-resize-full"></i>
                            {l s="Dados Gerais" mod="fkcorreiosg2cp3"}
                        </div>

                        <form id="configuration_form" class="defaultForm form-horizontal" action="{$tab_2['formAction']}&origem=cadTransp&idTransp={$regTransp['id']}" method="post" enctype="multipart/form-data">

                            {*** Campo hidden para controle de POST - mostra o Dados Gerais aberto/fechado ***}
                            <input type="hidden" name="fkcorreiosg2cp3_transp_post_{$regTransp['id']}">

                            {assign var="temp" value="fkcorreiosg2cp3_transp_post_`$regTransp['id']`"}
                            {if isset($smarty.post.$temp)}
                                {assign var="classToggleGeral" value="fkcorreiosg2cp3-toggle-item-open"}
                            {else}
                                {assign var="classToggleGeral" value="fkcorreiosg2cp3-toggle-item-close"}
                            {/if}

                            <div class="{$classToggleGeral}" id="fkcorreiosg2cp3_toggle_item_geral_{$regTransp['id']}">

                                <div class="fkcorreiosg2cp3-form">
                                    <label class="fkcorreiosg2cp3-label fkcorreiosg2cp3-col-lg-10"></label>
                                    <div class="fkcorreiosg2cp3-float-left">
                                        {assign var="temp" value="fkcorreiosg2cp3_transp_ativo_`$regTransp['id']`"}
                                        <input type="checkbox" name="fkcorreiosg2cp3_transp_ativo_{$regTransp['id']}" value="on" {if isset($smarty.post.$temp) and $smarty.post.$temp == 'on'}checked="checked"{else}{if isset($regTransp['ativo']) and $regTransp['ativo'] == '1'}checked="checked"{/if}{/if}>
                                    </div>
                                    <label class="fkcorreiosg2cp3-label-right fkcorreiosg2cp3-col-lg-auto">
                                        {l s="Ativo" mod="fkcorreiosg2cp3"}
                                    </label>
                                </div>

                                <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-sub-panel fkcorreiosg2cp3-col-lg-40">
                                    <div class="fkcorreiosg2cp3-panel-heading">
                                        {l s="Serviço JADLOG" mod="fkcorreiosg2cp3"}
                                    </div>

                                    <div class="fkcorreiosg2cp3-form">
                                        <label class="fkcorreiosg2cp3-label fkcorreiosg2cp3-col-lg-40">
                                            {l s="CNPJ do contratante:" mod="fkcorreiosg2cp3"}
                                        </label>
                                        <div class="fkcorreiosg2cp3-col-lg-30 fkcorreiosg2cp3-float-left">
                                            {assign var="temp" value="fkcorreiosg2cp3_transp_cnpj_`$regTransp['id']`"}
                                            <input class="fkcorreiosg2cp3-mask-cnpj" type="text" name="fkcorreiosg2cp3_transp_cnpj_{$regTransp['id']}" id="fkcorreiosg2cp3_transp_cnpj_{$regTransp['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regTransp['cnpj']}{/if}">
                                        </div>
                                    </div>

                                    <div class="fkcorreiosg2cp3-form">
                                        <label class="fkcorreiosg2cp3-label fkcorreiosg2cp3-col-lg-40">
                                            {l s="Senha:" mod="fkcorreiosg2cp3"}
                                        </label>
                                        <div class="fkcorreiosg2cp3-col-lg-30 fkcorreiosg2cp3-float-left">
                                            {assign var="temp" value="fkcorreiosg2cp3_transp_senha_`$regTransp['id']`"}
                                            <input type="password" name="fkcorreiosg2cp3_transp_senha_{$regTransp['id']}" id="fkcorreiosg2cp3_transp_senha_{$regTransp['id']}" maxlength="8" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regTransp['senha']}{/if}">
                                        </div>
                                    </div>

                                    <div class="fkcorreiosg2cp3-form">
                                        <label class="fkcorreiosg2cp3-label fkcorreiosg2cp3-col-lg-40">
                                            {l s="Valor da coleta:" mod="fkcorreiosg2cp3"}
                                        </label>
                                        <div class="fkcorreiosg2cp3-col-lg-20 fkcorreiosg2cp3-float-left">
                                            {assign var="temp" value="fkcorreiosg2cp3_transp_valor_coleta_`$regTransp['id']`"}
                                            <input type="text" name="fkcorreiosg2cp3_transp_valor_coleta_{$regTransp['id']}" id="fkcorreiosg2cp3_transp_valor_coleta_{$regTransp['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regTransp['valor_coleta']}{/if}">
                                        </div>
                                    </div>

                                    <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-col-lg-70">

                                        <div class="fkcorreiosg2cp3-panel-heading">
                                            {l s="Seguro" mod="fkcorreiosg2cp3"}
                                        </div>

                                        {assign var="temp" value="fkcorreiosg2cp3_transp_tipo_seguro_`$regTransp['id']`"}

                                        {foreach from=$tab_2['tipoSeguro'] item=tipo key=codigo }
                                            <div class="fkcorreiosg2cp3-form">
                                                <div class="fkcorreiosg2cp3-float-left">
                                                    <input type="radio" name="fkcorreiosg2cp3_transp_tipo_seguro_{$regTransp['id']}" value="{$codigo}" {if isset($smarty.post.$temp) and $smarty.post.$temp == $codigo}checked="checked"{else}{if isset($regTransp['tipo_seguro']) and $regTransp['tipo_seguro'] == $codigo}checked="checked"{/if}{/if}>
                                                </div>
                                                <label class="fkcorreiosg2cp3-label-right fkcorreiosg2cp3-col-lg-auto">
                                                    {$tipo}
                                                </label>
                                            </div>
                                        {/foreach}

                                    </div>

                                    <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-col-lg-70">

                                        <div class="fkcorreiosg2cp3-panel-heading">
                                            {l s="Pagamento" mod="fkcorreiosg2cp3"}
                                        </div>

                                        {assign var="temp" value="fkcorreiosg2cp3_transp_pagamento_`$regTransp['id']`"}

                                        {foreach from=$tab_2['pagamento'] item=tipo key=codigo }
                                            <div class="fkcorreiosg2cp3-form">
                                                <div class="fkcorreiosg2cp3-float-left">
                                                    <input type="radio" name="fkcorreiosg2cp3_transp_pagamento_{$regTransp['id']}" value="{$codigo}" {if isset($smarty.post.$temp) and $smarty.post.$temp == $codigo}checked="checked"{else}{if isset($regTransp['pagamento']) and $regTransp['pagamento'] == $codigo}checked="checked"{/if}{/if}>
                                                </div>
                                                <label class="fkcorreiosg2cp3-label-right fkcorreiosg2cp3-col-lg-auto">
                                                    {$tipo}
                                                </label>
                                            </div>
                                        {/foreach}

                                    </div>

                                    <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-col-lg-70">

                                        <div class="fkcorreiosg2cp3-panel-heading">
                                            {l s="Entrega" mod="fkcorreiosg2cp3"}
                                        </div>

                                        {assign var="temp" value="fkcorreiosg2cp3_transp_entrega_`$regTransp['id']`"}

                                        {foreach from=$tab_2['entrega'] item=tipo key=codigo }
                                            <div class="fkcorreiosg2cp3-form">
                                                <div class="fkcorreiosg2cp3-float-left">
                                                    <input type="radio" name="fkcorreiosg2cp3_transp_entrega_{$regTransp['id']}" value="{$codigo}" {if isset($smarty.post.$temp) and $smarty.post.$temp == $codigo}checked="checked"{else}{if isset($regTransp['tipo_entrega']) and $regTransp['tipo_entrega'] == $codigo}checked="checked"{/if}{/if}>
                                                </div>
                                                <label class="fkcorreiosg2cp3-label-right fkcorreiosg2cp3-col-lg-auto">
                                                    {$tipo}
                                                </label>
                                            </div>
                                        {/foreach}

                                    </div>

                                </div>

                                <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-sub-panel fkcorreiosg2cp3-col-lg-40">
                                    <div class="fkcorreiosg2cp3-panel-heading">
                                        {l s="Nome Transportadora" mod="fkcorreiosg2cp3"}
                                    </div>

                                    <div class="fkcorreiosg2cp3-form">
                                        <div class="fkcorreiosg2cp3-col-lg-70 fkcorreiosg2cp3-float-left">
                                            {assign var="temp" value="fkcorreiosg2cp3_transp_nome_`$regTransp['id']`"}
                                            <input type="text" name="fkcorreiosg2cp3_transp_nome_{$regTransp['id']}" id="fkcorreiosg2cp3_transp_nome_{$regTransp['id']}" maxlength="64" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regTransp['nome_transp']}{/if}">
                                        </div>
                                    </div>
                                </div>

                                <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-sub-panel fkcorreiosg2cp3-col-lg-40">
                                    <div class="fkcorreiosg2cp3-panel-heading">
                                        {l s="Grade de Velocidade" mod="fkcorreiosg2cp3"}
                                    </div>

                                    <div class="fkcorreiosg2cp3-form">
                                        <div class="fkcorreiosg2cp3-col-lg-20 fkcorreiosg2cp3-float-left">
                                            {assign var="temp" value="fkcorreiosg2cp3_transp_grade_`$regTransp['id']`"}
                                            <input type="text" name="fkcorreiosg2cp3_transp_grade_{$regTransp['id']}" id="fkcorreiosg2cp3_transp_grade_{$regTransp['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regTransp['grade']}{/if}">
                                        </div>
                                    </div>
                                </div>

                                <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-sub-panel fkcorreiosg2cp3-col-lg-40">

                                    <div class="fkcorreiosg2cp3-panel-heading">
                                        {l s="Logo da Transportadora" mod="fkcorreiosg2cp3"}
                                    </div>

                                    {assign var="urlLogoTransp" value="`$tab_2['urlLogoPS']``$regTransp['id_carrier']`.jpg"}
                                    {assign var="uriLogoTransp" value="`$tab_2['uriLogoPS']``$regTransp['id_carrier']`.jpg"}
                                    {assign var="urlNoImage" value="`$tab_2['urlImg']`no_image.jpg"}

                                    <div class="fkcorreiosg2cp3-form">
                                        {if file_exists({$uriLogoTransp})}
                                            <img id="fkcorreiosg2cp3_logo_transp_{$regTransp['id']}" alt="Logo transportadora" src="{$urlLogoTransp}">
                                        {else}
                                            <img id="fkcorreiosg2cp3_logo_transp_{$regTransp['id']}" alt="Logo transportadora" src="{$urlNoImage}">
                                        {/if}
                                    </div>

                                    <div class="fkcorreiosg2cp3-form">
                                        <input class="btn btn-default" type="file" name="fkcorreiosg2cp3_transp_logo_{$regTransp['id']}">
                                    </div>
                                    <p class="help-block">
                                        Formato jpg
                                        <br>
                                        Tamanho máximo do arquivo 8 MB
                                    </p>

                                    {if file_exists({$uriLogoTransp})}
                                        <script type="text/javascript">
                                            d = new Date();
                                            idLogo = '#fkcorreiosg2cp3_logo_transp_' + {$regTransp['id']};
                                            $(idLogo).attr("src", "{$urlLogoTransp}?" + d.getTime());
                                        </script>
                                    {/if}

                                </div>

                                <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-sub-panel fkcorreiosg2cp3-col-lg-40">

                                    <div class="fkcorreiosg2cp3-panel-heading">
                                        {l s="Diversos" mod="fkcorreiosg2cp3"}
                                    </div>

                                    <div class="fkcorreiosg2cp3-form">
                                        <div class="fkcorreiosg2cp3-float-left">
                                            <input type="checkbox" name="fkcorreiosg2cp3_excluir_config" id="fkcorreiosg2cp3_excluir_config" value="on" onclick="fkcorreiosg2cp3ExcluirConf('Atenção: Você marcou para excluir a configuração do módulo na desinstalação. Confirma?','fkcorreiosg2cp3_excluir_config')" {if isset($tab_2['fkcorreiosg2cp3_excluir_config']) and $tab_2['fkcorreiosg2cp3_excluir_config'] == 'on'}checked="checked"{/if}>
                                        </div>
                                        <label class="fkcorreiosg2cp3-label-right fkcorreiosg2cp3-col-lg-auto">
                                            {l s="Excluir Configuração do Módulo na desinstalação" mod="fkcorreiosg2cp3"}
                                        </label>
                                    </div>

                                </div>

                                <div class="fkcorreiosg2cp3-panel-footer">
                                    <button type="submit" value="1" name="btnSubmitTransp" class="fkcorreiosg2cp3-button fkcorreiosg2cp3-float-right">
                                        <i class="process-icon-save"></i>
                                        {l s="Salvar" mod="fkcorreiosg2cp3"}
                                    </button>
                                </div>

                            </div>

                        </form>
                    </div>

                    <div class="fkcorreiosg2cp3-panel">

                        <div class="fkcorreiosg2cp3-panel-heading fkcorreiosg2cp3-toggle-ativo" onclick="fkcorreiosg2cp3Toggle('fkcorreiosg2cp3_toggle_item_regioes_' + {$regTransp['id']})">
                            <i class="icon-resize-full"></i>
                            {l s="Regiões" mod="fkcorreiosg2cp3"}
                        </div>


                        {if $fkcorreiosg2cp3['abrirRegiao'] == $regTransp['id']}
                            {assign var="classToggleRegiao" value="fkcorreiosg2cp3-toggle-item-open"}
                        {else}
                            {assign var="classToggleRegiao" value="fkcorreiosg2cp3-toggle-item-close"}
                        {/if}

                        <div class="{$classToggleRegiao}" id="fkcorreiosg2cp3_toggle_item_regioes_{$regTransp['id']}">

                            <div class="fkcorreiosg2cp3-panel-header fkcorreiosg2cp3-panel-header-sub-panel">
                                <form id="configuration_form" class="defaultForm  form-horizontal" action="{$tab_2['formAction']}&origem=cadRegioes&idTransp={$regTransp['id']}" method="post">
                                    <button type="submit" value="1" name="btnAddRegiao" class="fkcorreiosg2cp3-button fkcorreiosg2cp3-float-left">
                                        <i class="process-icon-new"></i>
                                        {l s="Incluir Região" mod="fkcorreiosg2cp3"}
                                    </button>
                                </form>
                            </div>

                            {if isset($tab_2['regioes'])}
                                {foreach $tab_2['regioes'] as $regRegiao}
                                    {if $regRegiao['id_transp'] == $regTransp['id']}
                                        <form id="configuration_form" class="defaultForm form-horizontal" action="{$tab_2['formAction']}&origem=cadRegioes&idRegiao={$regRegiao['id']}" method="post">

                                            <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-sub-panel">

                                                <div class="fkcorreiosg2cp3-panel-heading {if isset($regRegiao['ativo']) and $regRegiao['ativo'] == '1'}fkcorreiosg2cp3-toggle-ativo{else}fkcorreiosg2cp3-toggle-inativo{/if}" onclick="fkcorreiosg2cp3Toggle('fkcorreiosg2cp3_toggle_item_regiao_' + {$regRegiao['id']})">
                                                    <i class="icon-resize-full"></i>
                                                    {$regRegiao['nome_regiao']}
                                                </div>

                                                {*** Campo hidden para controle de POST - mostra as Regioes aberto/fechado ***}
                                                <input type="hidden" name="fkcorreiosg2cp3_regiao_post_{$regRegiao['id']}">

                                                {assign var="temp" value="fkcorreiosg2cp3_regiao_post_`$regRegiao['id']`"}
                                                {if isset($smarty.post.$temp)}
                                                    {assign var="classToggleItem" value="fkcorreiosg2cp3-toggle-item-open"}
                                                {else}
                                                    {assign var="classToggleItem" value="fkcorreiosg2cp3-toggle-item-close"}
                                                {/if}

                                                <div class="{$classToggleItem}" id="fkcorreiosg2cp3_toggle_item_regiao_{$regRegiao['id']}">

                                                    <div class="fkcorreiosg2cp3-form">
                                                        <label class="fkcorreiosg2cp3-label fkcorreiosg2cp3-col-lg-10"></label>
                                                        <div class="fkcorreiosg2cp3-float-left">
                                                            {assign var="temp" value="fkcorreiosg2cp3_regiao_ativo_`$regRegiao['id']`"}
                                                            <input type="checkbox" name="fkcorreiosg2cp3_regiao_ativo_{$regRegiao['id']}" value="on" {if isset($smarty.post.$temp) and $smarty.post.$temp == 'on'}checked="checked"{else}{if isset($regRegiao['ativo']) and $regRegiao['ativo'] == '1'}checked="checked"{/if}{/if}>
                                                        </div>
                                                        <label class="fkcorreiosg2cp3-label-right fkcorreiosg2cp3-col-lg-auto">
                                                            {l s="Ativo" mod="fkcorreiosg2cp3"}
                                                        </label>
                                                    </div>

                                                    <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-sub-panel fkcorreiosg2cp3-regioes">

                                                        <div class="fkcorreiosg2cp3-panel-heading">
                                                            {l s="Serviço JADLOG" mod="fkcorreiosg2cp3"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-col-lg-70">

                                                            <div class="fkcorreiosg2cp3-panel-heading">
                                                                {l s="Modalidade do Frete" mod="fkcorreiosg2cp3"}
                                                            </div>

                                                            {assign var="temp" value="fkcorreiosg2cp3_regiao_modalidade_frete_`$regRegiao['id']`"}

                                                            {foreach from=$tab_2['modalidadeFrete'] item=tipo key=codigo }
                                                                <div class="fkcorreiosg2cp3-form">
                                                                    <div class="fkcorreiosg2cp3-float-left">
                                                                        <input type="radio" name="fkcorreiosg2cp3_regiao_modalidade_frete_{$regRegiao['id']}" value="{$codigo}" {if isset($smarty.post.$temp) and $smarty.post.$temp == $codigo}checked="checked"{else}{if isset($regRegiao['modalidade_frete']) and $regRegiao['modalidade_frete'] == $codigo}checked="checked"{/if}{/if}>
                                                                    </div>
                                                                    <label class="fkcorreiosg2cp3-label-right fkcorreiosg2cp3-col-lg-auto">
                                                                        {$tipo['descricao']}
                                                                    </label>
                                                                </div>
                                                            {/foreach}

                                                        </div>
                                                    </div>

                                                    <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-sub-panel fkcorreiosg2cp3-regioes">

                                                        <div class="fkcorreiosg2cp3-panel-heading">
                                                            {l s="Nome da Região" mod="fkcorreiosg2cp3"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp3-form">
                                                            <div class="fkcorreiosg2cp3-col-lg-70 fkcorreiosg2cp3-float-left">
                                                                {assign var="temp" value="fkcorreiosg2cp3_regiao_nome_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp3_regiao_nome_{$regRegiao['id']}" id="fkcorreiosg2cp3_regiao_nome_{$regRegiao['id']}" maxlength="100" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['nome_regiao']}{/if}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-sub-panel fkcorreiosg2cp3-regioes">

                                                        <div class="fkcorreiosg2cp3-panel-heading">
                                                            {l s="Prazo de Entrega" mod="fkcorreiosg2cp3"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp3-form">
                                                            <div class="fkcorreiosg2cp3-col-lg-70">
                                                                {assign var="temp" value="fkcorreiosg2cp3_regiao_prazo_entrega_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp3_regiao_prazo_entrega_{$regRegiao['id']}" id="fkcorreiosg2cp3_regiao_prazo_entrega_{$regRegiao['id']}" maxlength="50" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['prazo_entrega']}{/if}">
                                                            </div>
                                                            <p class="help-block">
                                                                Se o valor deste campo for numérico será adicionado o Tempo de Preparação definido.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-sub-panel fkcorreiosg2cp3-regioes">

                                                        <div class="fkcorreiosg2cp3-panel-heading">
                                                            {l s="Peso Máximo por Produto (kg)" mod="fkcorreiosg2cp3"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp3-form">
                                                            <div class="fkcorreiosg2cp3-col-lg-20">
                                                                {assign var="temp" value="fkcorreiosg2cp3_regiao_peso_maximo_produto_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp3_regiao_peso_maximo_produto_{$regRegiao['id']}" id="fkcorreiosg2cp3_regiao_peso_maximo_produto_{$regRegiao['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['peso_maximo_produto']}{/if}">
                                                            </div>
                                                            <p class="help-block">
                                                                Informe 0 (zero) para não considerar o peso do produto.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-sub-panel fkcorreiosg2cp3-regioes">

                                                        <div class="fkcorreiosg2cp3-panel-heading">
                                                            {l s="Estados Atendidos" mod="fkcorreiosg2cp3"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp3-panel">

                                                            <div class="fkcorreiosg2cp3-panel-heading">
                                                                {l s="Filtro" mod="fkcorreiosg2cp3"}
                                                            </div>

                                                            <div class="fkcorreiosg2cp3-form">

                                                                {assign var="temp" value="fkcorreiosg2cp3_regiao_filtro_uf_`$regRegiao['id']`"}

                                                                <div class="fkcorreiosg2cp3-float-left">
                                                                    <input type="radio" name="fkcorreiosg2cp3_regiao_filtro_uf_{$regRegiao['id']}" value="1" {if isset($smarty.post.$temp) and $smarty.post.$temp == 1}checked="checked"{else}{if isset($regRegiao['filtro_regiao_uf']) and $regRegiao['filtro_regiao_uf'] == '1'}checked="checked"{/if}{/if}>
                                                                </div>
                                                                <label class="fkcorreiosg2cp3-label-right fkcorreiosg2cp3-col-lg-auto">
                                                                    {l s="Todo o Estado" mod="fkcorreiosg2cp3"}
                                                                </label>

                                                                <div class="fkcorreiosg2cp3-float-left fkcorreiosg2cp3-margin">
                                                                    <input type="radio" name="fkcorreiosg2cp3_regiao_filtro_uf_{$regRegiao['id']}" value="2" {if isset($smarty.post.$temp) and $smarty.post.$temp == 2}checked="checked"{else}{if isset($regRegiao['filtro_regiao_uf']) and $regRegiao['filtro_regiao_uf'] == '2'}checked="checked"{/if}{/if}>
                                                                </div>
                                                                <label class="fkcorreiosg2cp3-label-right fkcorreiosg2cp3-col-lg-auto">
                                                                    {l s="Somente Capital" mod="fkcorreiosg2cp3"}
                                                                </label>

                                                                <div class="fkcorreiosg2cp3-float-left fkcorreiosg2cp3-margin">
                                                                    <input type="radio" name="fkcorreiosg2cp3_regiao_filtro_uf_{$regRegiao['id']}" value="3" {if isset($smarty.post.$temp) and $smarty.post.$temp == 3}checked="checked"{else}{if isset($regRegiao['filtro_regiao_uf']) and $regRegiao['filtro_regiao_uf'] == '3'}checked="checked"{/if}{/if}>
                                                                </div>
                                                                <label class="fkcorreiosg2cp3-label-right fkcorreiosg2cp3-col-lg-auto">
                                                                    {l s="Somente Interior" mod="fkcorreiosg2cp3"}
                                                                </label>

                                                            </div>

                                                        </div>

                                                        {*** Variavel de controle de UFs por linha ***}
                                                        {assign var="totEstados" value=1}
                                                        {assign var="maxEstados" value=10}

                                                        <div class="fkcorreiosg2cp3-form">
                                                            {foreach $tab_2['arrayUF'][$regRegiao['id']] as $uf}

                                                                {if $totEstados > $maxEstados}
                                                                    {assign var="totEstados" value=1}
                                                                {/if}

                                                                <div class="fkcorreiosg2cp3-float-left">
                                                                    {assign var="temp" value="fkcorreiosg2cp3_regiao_uf_`$regRegiao['id']`"}
                                                                    <input class="fkcorreiosg2cp3_regiao_uf_{$regRegiao['id']}" type="checkbox" name="fkcorreiosg2cp3_regiao_uf_{$regRegiao['id']}[]" value="{$uf['uf']}" {if isset($smarty.post.$temp) and $smarty.post.$temp == $uf['uf']}checked="checked"{else}{if isset($uf['ativo']) and $uf['ativo'] == '1'}checked="checked"{/if}{/if}>
                                                                </div>
                                                                <label class="fkcorreiosg2cp3-label-right fkcorreiosg2cp3-col-lg-estados">
                                                                    {$uf['uf']}
                                                                </label>

                                                                {assign var="totEstados" value=$totEstados+1}

                                                                {if $totEstados > $maxEstados}
                                                                    <div class="fkcorreiosg2cp3-clear">
                                                                        <br>
                                                                    </div>
                                                                {/if}

                                                            {/foreach}
                                                        </div>

                                                        <div class="fkcorreiosg2cp3-panel-footer">
                                                            <button type="button" value="1" name="btnMarcar" class="fkcorreiosg2cp3-button fkcorreiosg2cp3-float-left" onclick="fkcorreiosg2cp3Marcar('fkcorreiosg2cp3_regiao_uf_' + {$regRegiao['id']})">
                                                                <i class="process-icon-ok"></i>
                                                                {l s="Marcar Todos" mod="fkcorreiosg2cp3"}
                                                            </button>

                                                            <button type="button" value="1" name="btnDesmarcar" class="fkcorreiosg2cp3-button fkcorreiosg2cp3-float-right" onclick="fkcorreiosg2cp3Desmarcar('fkcorreiosg2cp3_regiao_uf_' + {$regRegiao['id']})">
                                                                <i class="process-icon-cancel"></i>
                                                                {l s="Desmarcar Todos" mod="fkcorreiosg2cp3"}
                                                            </button>
                                                        </div>

                                                    </div>

                                                    <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-sub-panel fkcorreiosg2cp3-regioes">

                                                        <div class="fkcorreiosg2cp3-panel-heading">
                                                            {l s="Intervalo de CEP Atendidos" mod="fkcorreiosg2cp3"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp3-form">

                                                            <div class="fkcorreiosg2cp3-col-lg-20 fkcorreiosg2cp3-float-left">
                                                                <input class="fkcorreiosg2cp3-mask-cep" type="text" name="fkcorreiosg2cp3_regiao_cep1_{$regRegiao['id']}" id="fkcorreiosg2cp3_regiao_cep1_{$regRegiao['id']}" value="">
                                                            </div>

                                                            <div class="fkcorreiosg2cp3-float-left">
                                                                <span id="fkcorreiosg2cp3_span_regiao">a</span>
                                                            </div>

                                                            <div class="fkcorreiosg2cp3-col-lg-20 fkcorreiosg2cp3-float-left">
                                                                <input class="fkcorreiosg2cp3-mask-cep" type="text" name="fkcorreiosg2cp3_regiao_cep2_{$regRegiao['id']}" id="fkcorreiosg2cp3_regiao_cep2_{$regRegiao['id']}" value="">
                                                            </div>

                                                            <div class="fkcorreiosg2cp3-float-left" id="fkcorreiosg2cp3_button_regiao">
                                                                <input class="fkcorreiosg2cp3-button" name="button" type="button" value="{l s="Incluir" mod="fkcorreiosg2cp3"}" onclick="fkcorreiosg2cp3IncluirCepRegiao({$regRegiao['id']});">
                                                            </div>

                                                        </div>

                                                        <div class="fkcorreiosg2cp3-form">

                                                            <div class="fkcorreiosg2cp3-col-lg-95">
                                                                {assign var="temp" value="fkcorreiosg2cp3_regiao_cep_`$regRegiao['id']`"}
                                                                <textarea name="fkcorreiosg2cp3_regiao_cep_{$regRegiao['id']}" id="fkcorreiosg2cp3_regiao_cep_{$regRegiao['id']}">{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['regiao_cep']}{/if}</textarea>
                                                            </div>
                                                            <p class="help-block">
                                                                Os intervalos de CEP aqui relacionados serão atendidos por esta Região independentemente dos Estados selecionados
                                                            </p>

                                                        </div>

                                                    </div>

                                                    <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-sub-panel fkcorreiosg2cp3-regioes">

                                                        <div class="fkcorreiosg2cp3-panel-heading">
                                                            {l s="Intervalo de CEP Excluídos" mod="fkcorreiosg2cp3"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp3-form">

                                                            <div class="fkcorreiosg2cp3-col-lg-20 fkcorreiosg2cp3-float-left">
                                                                <input class="fkcorreiosg2cp3-mask-cep" type="text" name="fkcorreiosg2cp3_regiao_cep1_excluido_{$regRegiao['id']}" id="fkcorreiosg2cp3_regiao_cep1_excluido_{$regRegiao['id']}" value="">
                                                            </div>

                                                            <div class="fkcorreiosg2cp3-float-left">
                                                                <span id="fkcorreiosg2cp3_span_regiao">a</span>
                                                            </div>

                                                            <div class="fkcorreiosg2cp3-col-lg-20 fkcorreiosg2cp3-float-left">
                                                                <input class="fkcorreiosg2cp3-mask-cep" type="text" name="fkcorreiosg2cp3_regiao_cep2_excluido_{$regRegiao['id']}" id="fkcorreiosg2cp3_regiao_cep2_excluido_{$regRegiao['id']}" value="">
                                                            </div>

                                                            <div class="fkcorreiosg2cp3-float-left" id="fkcorreiosg2cp3_button_regiao">
                                                                <input class="fkcorreiosg2cp3-button" name="button" type="button" value="{l s="Incluir" mod="fkcorreiosg2cp3"}" onclick="fkcorreiosg2cp3IncluirCepRegiaoExcluido({$regRegiao['id']});">
                                                            </div>

                                                        </div>

                                                        <div class="fkcorreiosg2cp3-form">
                                                            <div class="fkcorreiosg2cp3-col-lg-95">
                                                                {assign var="temp" value="fkcorreiosg2cp3_regiao_cep_excluido_`$regRegiao['id']`"}
                                                                <textarea name="fkcorreiosg2cp3_regiao_cep_excluido_{$regRegiao['id']}" id="fkcorreiosg2cp3_regiao_cep_excluido_{$regRegiao['id']}">{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['regiao_cep_excluido']}{/if}</textarea>
                                                            </div>
                                                            <p class="help-block">
                                                                Os intervalos de CEP aqui relacionados não serão atendidos por esta Região
                                                            </p>
                                                        </div>

                                                    </div>

                                                    <div class="fkcorreiosg2cp3-panel fkcorreiosg2cp3-sub-panel fkcorreiosg2cp3-regioes">

                                                        <div class="fkcorreiosg2cp3-panel-heading">
                                                            {l s="Desconto no Frete" mod="fkcorreiosg2cp3"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp3-form">
                                                            <label class="fkcorreiosg2cp3-label fkcorreiosg2cp3-col-lg-40">
                                                                {l s="Percentual de Desconto:" mod="fkcorreiosg2cp3"}
                                                            </label>
                                                            <div class="fkcorreiosg2cp3-col-lg-20 fkcorreiosg2cp3-float-left">
                                                                {assign var="temp" value="fkcorreiosg2cp3_regiao_percentual_desc_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp3_regiao_percentual_desc_{$regRegiao['id']}" id="fkcorreiosg2cp3_regiao_percentual_desc_{$regRegiao['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['percentual_desconto']}{/if}">
                                                            </div>
                                                        </div>

                                                        <div class="fkcorreiosg2cp3-form">
                                                            <label class="fkcorreiosg2cp3-label fkcorreiosg2cp3-col-lg-40">
                                                                {l s="Valor do Pedido:" mod="fkcorreiosg2cp3"}
                                                            </label>
                                                            <div class="fkcorreiosg2cp3-col-lg-20 fkcorreiosg2cp3-float-left">
                                                                {assign var="temp" value="fkcorreiosg2cp3_regiao_valor_pedido_desc_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp3_regiao_valor_pedido_desc_{$regRegiao['id']}" id="fkcorreiosg2cp3_regiao_valor_pedido_desc_{$regRegiao['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['valor_pedido_desconto']}{/if}">
                                                            </div>
                                                        </div>

                                                        <p class="help-block">
                                                            Informe o valor 0 (zero) nos campos "Percentual de Desconto" e "Valor do Pedido" para não aplicar desconto ao frete
                                                        </p>

                                                    </div>

                                                    <div class="fkcorreiosg2cp3-panel-footer">
                                                        <button type="submit" value="1" name="btnDelRegiao" class="fkcorreiosg2cp3-button fkcorreiosg2cp3-float-left" onclick="return fkcorreiosg2cp3Excluir('{l s="Confirma a exclusão da Região?" mod="fkcorreiosg2cp3"}');">
                                                            <i class="process-icon-delete"></i>
                                                            {l s="Excluir Região" mod="fkcorreiosg2cp3"}
                                                        </button>

                                                        <button type="submit" value="1" name="btnSubmitRegiao" class="fkcorreiosg2cp3-button fkcorreiosg2cp3-float-right">
                                                            <i class="process-icon-save"></i>
                                                            {l s="Salvar" mod="fkcorreiosg2cp3"}
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

                </div>

            </div>

        {/foreach}
    {/if}

</div>