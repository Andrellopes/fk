<div class="fkcorreiosg2cp4-panel" style="border-top-left-radius: 0">

    <div class="fkcorreiosg2cp4-panel-header">
        <button type="button" value="1" name="btnAjuda" class="fkcorreiosg2cp4-button fkcorreiosg2cp4-float-right" onClick="window.open('http://www.modulosfk.com.br/modulosfk/ajuda/fkcorreiosg2cp4_v1_0_0.pdf','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=900,height=500,left=500,top=150'); return false;">
            <i class="process-icon-help"></i>
            {l s="Ajuda" mod="fkcorreiosg2cp4"}
        </button>
    </div>

    {if isset($tab_2['transportadoras'])}
        {foreach $tab_2['transportadoras'] as $regTransp}

            <div class="fkcorreiosg2cp4-panel">
                <div class="fkcorreiosg2cp4-panel-heading {if isset($regTransp['ativo']) and $regTransp['ativo'] == '1'}fkcorreiosg2cp4-toggle-ativo{else}fkcorreiosg2cp4-toggle-inativo{/if}" onclick="fkcorreiosg2cp4Toggle('fkcorreiosg2cp4_toggle_itens_' + {$regTransp['id']})">
                    <i class="icon-resize-full"></i>
                    {$regTransp['nome_transp']}
                </div>

                {if $fkcorreiosg2cp4['abrirTransp'] == $regTransp['id']}
                    {assign var="classToggleTransp" value="fkcorreiosg2cp4-toggle-item-open"}
                {else}
                    {assign var="classToggleTransp" value="fkcorreiosg2cp4-toggle-item-close"}
                {/if}

                <div class="{$classToggleTransp}" id="fkcorreiosg2cp4_toggle_itens_{$regTransp['id']}">

                    <div class="fkcorreiosg2cp4-panel">

                        <div class="fkcorreiosg2cp4-panel-heading fkcorreiosg2cp4-toggle-ativo" onclick="fkcorreiosg2cp4Toggle('fkcorreiosg2cp4_toggle_item_geral_' + {$regTransp['id']})">
                            <i class="icon-resize-full"></i>
                            {l s="Dados Gerais" mod="fkcorreiosg2cp4"}
                        </div>

                        <form id="configuration_form" class="defaultForm form-horizontal" action="{$tab_2['formAction']}&origem=cadTransp&idTransp={$regTransp['id']}" method="post" enctype="multipart/form-data">

                            {*** Campo hidden para controle de POST - mostra o Dados Gerais aberto/fechado ***}
                            <input type="hidden" name="fkcorreiosg2cp4_transp_post_{$regTransp['id']}">

                            {assign var="temp" value="fkcorreiosg2cp4_transp_post_`$regTransp['id']`"}
                            {if isset($smarty.post.$temp)}
                                {assign var="classToggleGeral" value="fkcorreiosg2cp4-toggle-item-open"}
                            {else}
                                {assign var="classToggleGeral" value="fkcorreiosg2cp4-toggle-item-close"}
                            {/if}

                            <div class="{$classToggleGeral}" id="fkcorreiosg2cp4_toggle_item_geral_{$regTransp['id']}">

                                <div class="fkcorreiosg2cp4-form">
                                    <label class="fkcorreiosg2cp4-label fkcorreiosg2cp4-col-lg-10"></label>
                                    <div class="fkcorreiosg2cp4-float-left">
                                        {assign var="temp" value="fkcorreiosg2cp4_transp_ativo_`$regTransp['id']`"}
                                        <input type="checkbox" name="fkcorreiosg2cp4_transp_ativo_{$regTransp['id']}" value="on" {if isset($smarty.post.$temp) and $smarty.post.$temp == 'on'}checked="checked"{else}{if isset($regTransp['ativo']) and $regTransp['ativo'] == '1'}checked="checked"{/if}{/if}>
                                    </div>
                                    <label class="fkcorreiosg2cp4-label-right fkcorreiosg2cp4-col-lg-auto">
                                        {l s="Ativo" mod="fkcorreiosg2cp4"}
                                    </label>
                                </div>

                                <div class="fkcorreiosg2cp4-panel fkcorreiosg2cp4-sub-panel fkcorreiosg2cp4-col-lg-40">
                                    <div class="fkcorreiosg2cp4-panel-heading">
                                        {l s="Meu CNPJ" mod="fkcorreiosg2cp4"}
                                    </div>

                                    <div class="fkcorreiosg2cp4-form">
                                        <div class="fkcorreiosg2cp4-col-lg-40">
                                            {assign var="temp" value="fkcorreiosg2cp4_meu_cnpj_`$regTransp['id']`"}
                                            <input class="fkcorreiosg2cp4-mask-cnpj" type="text" name="fkcorreiosg2cp4_meu_cnpj_{$regTransp['id']}" id="fkcorreiosg2cp4_meu_cnpj_{$regTransp['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regTransp['cnpj']}{/if}">
                                        </div>
                                    </div>
                                    <p class="help-block">
                                        Informe o CNPJ cadastrado na JAMEF
                                    </p>
                                </div>

                                <div class="fkcorreiosg2cp4-panel fkcorreiosg2cp4-sub-panel fkcorreiosg2cp4-col-lg-40">
                                    <div class="fkcorreiosg2cp4-panel-heading">
                                        {l s="Nome Transportadora" mod="fkcorreiosg2cp4"}
                                    </div>

                                    <div class="fkcorreiosg2cp4-form">
                                        <div class="fkcorreiosg2cp4-col-lg-70 fkcorreiosg2cp4-float-left">
                                            {assign var="temp" value="fkcorreiosg2cp4_transp_nome_`$regTransp['id']`"}
                                            <input type="text" name="fkcorreiosg2cp4_transp_nome_{$regTransp['id']}" id="fkcorreiosg2cp4_transp_nome_{$regTransp['id']}" maxlength="64" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regTransp['nome_transp']}{/if}">
                                        </div>
                                    </div>
                                </div>

                                <div class="fkcorreiosg2cp4-panel fkcorreiosg2cp4-sub-panel fkcorreiosg2cp4-col-lg-40">
                                    <div class="fkcorreiosg2cp4-panel-heading">
                                        {l s="Grade de Velocidade" mod="fkcorreiosg2cp4"}
                                    </div>

                                    <div class="fkcorreiosg2cp4-form">
                                        <div class="fkcorreiosg2cp4-col-lg-20 fkcorreiosg2cp4-float-left">
                                            {assign var="temp" value="fkcorreiosg2cp4_transp_grade_`$regTransp['id']`"}
                                            <input type="text" name="fkcorreiosg2cp4_transp_grade_{$regTransp['id']}" id="fkcorreiosg2cp4_transp_grade_{$regTransp['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regTransp['grade']}{/if}">
                                        </div>
                                    </div>
                                </div>

                                <div class="fkcorreiosg2cp4-panel fkcorreiosg2cp4-sub-panel fkcorreiosg2cp4-col-lg-40">

                                    <div class="fkcorreiosg2cp4-panel-heading">
                                        {l s="Logo da Transportadora" mod="fkcorreiosg2cp4"}
                                    </div>

                                    {assign var="urlLogoTransp" value="`$tab_2['urlLogoPS']``$regTransp['id_carrier']`.jpg"}
                                    {assign var="uriLogoTransp" value="`$tab_2['uriLogoPS']``$regTransp['id_carrier']`.jpg"}
                                    {assign var="urlNoImage" value="`$tab_2['urlImg']`no_image.jpg"}

                                    <div class="fkcorreiosg2cp4-form">
                                        {if file_exists({$uriLogoTransp})}
                                            <img id="fkcorreiosg2cp4_logo_transp_{$regTransp['id']}" alt="Logo transportadora" src="{$urlLogoTransp}">
                                        {else}
                                            <img id="fkcorreiosg2cp4_logo_transp_{$regTransp['id']}" alt="Logo transportadora" src="{$urlNoImage}">
                                        {/if}
                                    </div>

                                    <div class="fkcorreiosg2cp4-form">
                                        <input class="btn btn-default" type="file" name="fkcorreiosg2cp4_transp_logo_{$regTransp['id']}">
                                    </div>
                                    <p class="help-block">
                                        Formato jpg
                                        <br>
                                        Tamanho máximo do arquivo 8 MB
                                    </p>

                                    {if file_exists({$uriLogoTransp})}
                                        <script type="text/javascript">
                                            d = new Date();
                                            idLogo = '#fkcorreiosg2cp4_logo_transp_' + {$regTransp['id']};
                                            $(idLogo).attr("src", "{$urlLogoTransp}?" + d.getTime());
                                        </script>
                                    {/if}

                                </div>

                                <div class="fkcorreiosg2cp4-panel fkcorreiosg2cp4-sub-panel fkcorreiosg2cp4-col-lg-40">

                                    <div class="fkcorreiosg2cp4-panel-heading">
                                        {l s="Diversos" mod="fkcorreiosg2cp4"}
                                    </div>

                                    <div class="fkcorreiosg2cp4-form">
                                        <div class="fkcorreiosg2cp4-float-left">
                                            <input type="checkbox" name="fkcorreiosg2cp4_excluir_config" id="fkcorreiosg2cp4_excluir_config" value="on" onclick="fkcorreiosg2cp4ExcluirConf('Atenção: Você marcou para excluir a configuração do módulo na desinstalação. Confirma?','fkcorreiosg2cp4_excluir_config')" {if isset($tab_2['fkcorreiosg2cp4_excluir_config']) and $tab_2['fkcorreiosg2cp4_excluir_config'] == 'on'}checked="checked"{/if}>
                                        </div>
                                        <label class="fkcorreiosg2cp4-label-right fkcorreiosg2cp4-col-lg-auto">
                                            {l s="Excluir Configuração do Módulo na desinstalação" mod="fkcorreiosg2cp4"}
                                        </label>
                                    </div>

                                </div>

                                <div class="fkcorreiosg2cp4-panel-footer">
                                    <button type="submit" value="1" name="btnSubmitTransp" class="fkcorreiosg2cp4-button fkcorreiosg2cp4-float-right">
                                        <i class="process-icon-save"></i>
                                        {l s="Salvar" mod="fkcorreiosg2cp4"}
                                    </button>
                                </div>

                            </div>

                        </form>
                    </div>

                    <div class="fkcorreiosg2cp4-panel">

                        <div class="fkcorreiosg2cp4-panel-heading fkcorreiosg2cp4-toggle-ativo" onclick="fkcorreiosg2cp4Toggle('fkcorreiosg2cp4_toggle_item_regioes_' + {$regTransp['id']})">
                            <i class="icon-resize-full"></i>
                            {l s="Regiões" mod="fkcorreiosg2cp4"}
                        </div>

                        {if $fkcorreiosg2cp4['abrirRegiao'] == $regTransp['id']}
                            {assign var="classToggleRegiao" value="fkcorreiosg2cp4-toggle-item-open"}
                        {else}
                            {assign var="classToggleRegiao" value="fkcorreiosg2cp4-toggle-item-close"}
                        {/if}

                        <div class="{$classToggleRegiao}" id="fkcorreiosg2cp4_toggle_item_regioes_{$regTransp['id']}">

                            <div class="fkcorreiosg2cp4-panel-header fkcorreiosg2cp4-panel-header-sub-panel">
                                <form id="configuration_form" class="defaultForm  form-horizontal" action="{$tab_2['formAction']}&origem=cadRegioes&idTransp={$regTransp['id']}" method="post">
                                    <button type="submit" value="1" name="btnAddRegiao" class="fkcorreiosg2cp4-button fkcorreiosg2cp4-float-left">
                                        <i class="process-icon-new"></i>
                                        {l s="Incluir Região" mod="fkcorreiosg2cp4"}
                                    </button>
                                </form>
                            </div>

                            {if isset($tab_2['regioes'])}
                                {foreach $tab_2['regioes'] as $regRegiao}
                                    {if $regRegiao['id_transp'] == $regTransp['id']}
                                        <form id="configuration_form" class="defaultForm form-horizontal" action="{$tab_2['formAction']}&origem=cadRegioes&idTransp={$regTransp['id']}&idRegiao={$regRegiao['id']}" method="post">

                                            <div class="fkcorreiosg2cp4-panel fkcorreiosg2cp4-sub-panel">

                                                <div class="fkcorreiosg2cp4-panel-heading {if isset($regRegiao['ativo']) and $regRegiao['ativo'] == '1'}fkcorreiosg2cp4-toggle-ativo{else}fkcorreiosg2cp4-toggle-inativo{/if}" onclick="fkcorreiosg2cp4Toggle('fkcorreiosg2cp4_toggle_item_regiao_' + {$regRegiao['id']})">
                                                    <i class="icon-resize-full"></i>
                                                    {$regRegiao['nome_regiao']}
                                                </div>

                                                {*** Campo hidden para controle de POST - mostra as Regioes aberto/fechado ***}
                                                <input type="hidden" name="fkcorreiosg2cp4_regiao_post_{$regRegiao['id']}">

                                                {assign var="temp" value="fkcorreiosg2cp4_regiao_post_`$regRegiao['id']`"}
                                                {if isset($smarty.post.$temp)}
                                                    {assign var="classToggleItem" value="fkcorreiosg2cp4-toggle-item-open"}
                                                {else}
                                                    {assign var="classToggleItem" value="fkcorreiosg2cp4-toggle-item-close"}
                                                {/if}

                                                <div class="{$classToggleItem}" id="fkcorreiosg2cp4_toggle_item_regiao_{$regRegiao['id']}">

                                                    <div class="fkcorreiosg2cp4-form">
                                                        <label class="fkcorreiosg2cp4-label fkcorreiosg2cp4-col-lg-10"></label>
                                                        <div class="fkcorreiosg2cp4-float-left">
                                                            {assign var="temp" value="fkcorreiosg2cp4_regiao_ativo_`$regRegiao['id']`"}
                                                            <input type="checkbox" name="fkcorreiosg2cp4_regiao_ativo_{$regRegiao['id']}" value="on" {if isset($smarty.post.$temp) and $smarty.post.$temp == 'on'}checked="checked"{else}{if isset($regRegiao['ativo']) and $regRegiao['ativo'] == '1'}checked="checked"{/if}{/if}>
                                                        </div>
                                                        <label class="fkcorreiosg2cp4-label-right fkcorreiosg2cp4-col-lg-auto">
                                                            {l s="Ativo" mod="fkcorreiosg2cp4"}
                                                        </label>
                                                    </div>

                                                    <div class="fkcorreiosg2cp4-panel fkcorreiosg2cp4-sub-panel fkcorreiosg2cp4-regioes">

                                                        <div class="fkcorreiosg2cp4-panel-heading">
                                                            {l s="Filial JAMEF" mod="fkcorreiosg2cp4"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp4-form">
                                                            <label class="fkcorreiosg2cp4-label fkcorreiosg2cp4-col-lg-40">
                                                                {l s="Código Região:" mod="fkcorreiosg2cp4"}
                                                            </label>
                                                            <div class="fkcorreiosg2cp4-col-lg-20 fkcorreiosg2cp4-float-left">
                                                                {assign var="temp" value="fkcorreiosg2cp4_codigo_filial_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp4_codigo_filial_{$regRegiao['id']}" id="fkcorreiosg2cp4_codigo_filial_{$regRegiao['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['codigo_filial']}{/if}">
                                                            </div>
                                                        </div>
                                                        <div class="fkcorreiosg2cp4-form">
                                                            <label class="fkcorreiosg2cp4-label fkcorreiosg2cp4-col-lg-40">
                                                                {l s="UF Região:" mod="fkcorreiosg2cp4"}
                                                            </label>
                                                            <div class="fkcorreiosg2cp4-col-lg-10 fkcorreiosg2cp4-float-left">
                                                                {assign var="temp" value="fkcorreiosg2cp4_uf_filial_`$regRegiao['id']`"}
                                                                <input class="fkcorreiosg2cp4-uppercase" type="text" name="fkcorreiosg2cp4_uf_filial_{$regRegiao['id']}" id="fkcorreiosg2cp4_uf_filial_{$regRegiao['id']}" maxlength="2" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['uf_filial']}{/if}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="fkcorreiosg2cp4-panel fkcorreiosg2cp4-sub-panel fkcorreiosg2cp4-regioes">

                                                        <div class="fkcorreiosg2cp4-panel-heading">
                                                            {l s="Nome da Região" mod="fkcorreiosg2cp4"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp4-form">
                                                            <div class="fkcorreiosg2cp4-col-lg-70 fkcorreiosg2cp4-float-left">
                                                                {assign var="temp" value="fkcorreiosg2cp4_regiao_nome_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp4_regiao_nome_{$regRegiao['id']}" id="fkcorreiosg2cp4_regiao_nome_{$regRegiao['id']}" maxlength="100" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['nome_regiao']}{/if}">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="fkcorreiosg2cp4-panel fkcorreiosg2cp4-sub-panel fkcorreiosg2cp4-regioes">

                                                        <div class="fkcorreiosg2cp4-panel-heading">
                                                            {l s="Prazo de Entrega" mod="fkcorreiosg2cp4"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp4-form">
                                                            <div class="fkcorreiosg2cp4-col-lg-70">
                                                                {assign var="temp" value="fkcorreiosg2cp4_regiao_prazo_entrega_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp4_regiao_prazo_entrega_{$regRegiao['id']}" id="fkcorreiosg2cp4_regiao_prazo_entrega_{$regRegiao['id']}" maxlength="50" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['prazo_entrega']}{/if}">
                                                            </div>
                                                            <p class="help-block">
                                                                Se o valor deste campo for numérico será adicionado o Tempo de Preparação definido.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="fkcorreiosg2cp4-panel fkcorreiosg2cp4-sub-panel fkcorreiosg2cp4-regioes">

                                                        <div class="fkcorreiosg2cp4-panel-heading">
                                                            {l s="Peso Máximo por Produto (kg)" mod="fkcorreiosg2cp4"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp4-form">
                                                            <div class="fkcorreiosg2cp4-col-lg-20">
                                                                {assign var="temp" value="fkcorreiosg2cp4_regiao_peso_maximo_produto_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp4_regiao_peso_maximo_produto_{$regRegiao['id']}" id="fkcorreiosg2cp4_regiao_peso_maximo_produto_{$regRegiao['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['peso_maximo_produto']}{/if}">
                                                            </div>
                                                            <p class="help-block">
                                                                Informe 0 (zero) para não considerar o peso do produto.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="fkcorreiosg2cp4-panel fkcorreiosg2cp4-sub-panel fkcorreiosg2cp4-regioes">

                                                        <div class="fkcorreiosg2cp4-panel-heading">
                                                            {l s="Estados Atendidos" mod="fkcorreiosg2cp4"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp4-panel">

                                                            <div class="fkcorreiosg2cp4-panel-heading">
                                                                {l s="Filtro" mod="fkcorreiosg2cp4"}
                                                            </div>

                                                            <div class="fkcorreiosg2cp4-form">

                                                                {assign var="temp" value="fkcorreiosg2cp4_regiao_filtro_uf_`$regRegiao['id']`"}

                                                                <div class="fkcorreiosg2cp4-float-left">
                                                                    <input type="radio" name="fkcorreiosg2cp4_regiao_filtro_uf_{$regRegiao['id']}" value="1" {if isset($smarty.post.$temp) and $smarty.post.$temp == 1}checked="checked"{else}{if isset($regRegiao['filtro_regiao_uf']) and $regRegiao['filtro_regiao_uf'] == '1'}checked="checked"{/if}{/if}>
                                                                </div>
                                                                <label class="fkcorreiosg2cp4-label-right fkcorreiosg2cp4-col-lg-auto">
                                                                    {l s="Todo o Estado" mod="fkcorreiosg2cp4"}
                                                                </label>

                                                                <div class="fkcorreiosg2cp4-float-left fkcorreiosg2cp4-margin">
                                                                    <input type="radio" name="fkcorreiosg2cp4_regiao_filtro_uf_{$regRegiao['id']}" value="2" {if isset($smarty.post.$temp) and $smarty.post.$temp == 2}checked="checked"{else}{if isset($regRegiao['filtro_regiao_uf']) and $regRegiao['filtro_regiao_uf'] == '2'}checked="checked"{/if}{/if}>
                                                                </div>
                                                                <label class="fkcorreiosg2cp4-label-right fkcorreiosg2cp4-col-lg-auto">
                                                                    {l s="Somente Capital" mod="fkcorreiosg2cp4"}
                                                                </label>

                                                                <div class="fkcorreiosg2cp4-float-left fkcorreiosg2cp4-margin">
                                                                    <input type="radio" name="fkcorreiosg2cp4_regiao_filtro_uf_{$regRegiao['id']}" value="3" {if isset($smarty.post.$temp) and $smarty.post.$temp == 3}checked="checked"{else}{if isset($regRegiao['filtro_regiao_uf']) and $regRegiao['filtro_regiao_uf'] == '3'}checked="checked"{/if}{/if}>
                                                                </div>
                                                                <label class="fkcorreiosg2cp4-label-right fkcorreiosg2cp4-col-lg-auto">
                                                                    {l s="Somente Interior" mod="fkcorreiosg2cp4"}
                                                                </label>

                                                            </div>

                                                        </div>

                                                        {*** Variavel de controle de UFs por linha ***}
                                                        {assign var="totEstados" value=1}
                                                        {assign var="maxEstados" value=10}

                                                        <div class="fkcorreiosg2cp4-form">
                                                            {foreach $tab_2['arrayUF'][$regRegiao['id']] as $uf}

                                                                {if $totEstados > $maxEstados}
                                                                    {assign var="totEstados" value=1}
                                                                {/if}

                                                                <div class="fkcorreiosg2cp4-float-left">
                                                                    {assign var="temp" value="fkcorreiosg2cp4_regiao_uf_`$regRegiao['id']`"}
                                                                    <input class="fkcorreiosg2cp4_regiao_uf_{$regRegiao['id']}" type="checkbox" name="fkcorreiosg2cp4_regiao_uf_{$regRegiao['id']}[]" value="{$uf['uf']}" {if isset($smarty.post.$temp) and $smarty.post.$temp == $uf['uf']}checked="checked"{else}{if isset($uf['ativo']) and $uf['ativo'] == '1'}checked="checked"{/if}{/if}>
                                                                </div>
                                                                <label class="fkcorreiosg2cp4-label-right fkcorreiosg2cp4-col-lg-estados">
                                                                    {$uf['uf']}
                                                                </label>

                                                                {assign var="totEstados" value=$totEstados+1}

                                                                {if $totEstados > $maxEstados}
                                                                    <div class="fkcorreiosg2cp4-clear">
                                                                        <br>
                                                                    </div>
                                                                {/if}

                                                            {/foreach}
                                                        </div>

                                                        <div class="fkcorreiosg2cp4-panel-footer">
                                                            <button type="button" value="1" name="btnMarcar" class="fkcorreiosg2cp4-button fkcorreiosg2cp4-float-left" onclick="fkcorreiosg2cp4Marcar('fkcorreiosg2cp4_regiao_uf_' + {$regRegiao['id']})">
                                                                <i class="process-icon-ok"></i>
                                                                {l s="Marcar Todos" mod="fkcorreiosg2cp4"}
                                                            </button>

                                                            <button type="button" value="1" name="btnDesmarcar" class="fkcorreiosg2cp4-button fkcorreiosg2cp4-float-right" onclick="fkcorreiosg2cp4Desmarcar('fkcorreiosg2cp4_regiao_uf_' + {$regRegiao['id']})">
                                                                <i class="process-icon-cancel"></i>
                                                                {l s="Desmarcar Todos" mod="fkcorreiosg2cp4"}
                                                            </button>
                                                        </div>

                                                    </div>

                                                    <div class="fkcorreiosg2cp4-panel fkcorreiosg2cp4-sub-panel fkcorreiosg2cp4-regioes">

                                                        <div class="fkcorreiosg2cp4-panel-heading">
                                                            {l s="Intervalo de CEP Atendidos" mod="fkcorreiosg2cp4"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp4-form">

                                                            <div class="fkcorreiosg2cp4-col-lg-20 fkcorreiosg2cp4-float-left">
                                                                <input class="fkcorreiosg2cp4-mask-cep" type="text" name="fkcorreiosg2cp4_regiao_cep1_{$regRegiao['id']}" id="fkcorreiosg2cp4_regiao_cep1_{$regRegiao['id']}" value="">
                                                            </div>

                                                            <div class="fkcorreiosg2cp4-float-left">
                                                                <span id="fkcorreiosg2cp4_span_regiao">a</span>
                                                            </div>

                                                            <div class="fkcorreiosg2cp4-col-lg-20 fkcorreiosg2cp4-float-left">
                                                                <input class="fkcorreiosg2cp4-mask-cep" type="text" name="fkcorreiosg2cp4_regiao_cep2_{$regRegiao['id']}" id="fkcorreiosg2cp4_regiao_cep2_{$regRegiao['id']}" value="">
                                                            </div>

                                                            <div class="fkcorreiosg2cp4-float-left" id="fkcorreiosg2cp4_button_regiao">
                                                                <input class="fkcorreiosg2cp4-button" name="button" type="button" value="{l s="Incluir" mod="fkcorreiosg2cp4"}" onclick="fkcorreiosg2cp4IncluirCepRegiao({$regRegiao['id']});">
                                                            </div>

                                                        </div>

                                                        <div class="fkcorreiosg2cp4-form">

                                                            <div class="fkcorreiosg2cp4-col-lg-95">
                                                                {assign var="temp" value="fkcorreiosg2cp4_regiao_cep_`$regRegiao['id']`"}
                                                                <textarea name="fkcorreiosg2cp4_regiao_cep_{$regRegiao['id']}" id="fkcorreiosg2cp4_regiao_cep_{$regRegiao['id']}">{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['regiao_cep']}{/if}</textarea>
                                                            </div>
                                                            <p class="help-block">
                                                                Os intervalos de CEP aqui relacionados serão atendidos por esta Região independentemente dos Estados selecionados
                                                            </p>

                                                        </div>

                                                    </div>

                                                    <div class="fkcorreiosg2cp4-panel fkcorreiosg2cp4-sub-panel fkcorreiosg2cp4-regioes">

                                                        <div class="fkcorreiosg2cp4-panel-heading">
                                                            {l s="Intervalo de CEP Excluídos" mod="fkcorreiosg2cp4"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp4-form">

                                                            <div class="fkcorreiosg2cp4-col-lg-20 fkcorreiosg2cp4-float-left">
                                                                <input class="fkcorreiosg2cp4-mask-cep" type="text" name="fkcorreiosg2cp4_regiao_cep1_excluido_{$regRegiao['id']}" id="fkcorreiosg2cp4_regiao_cep1_excluido_{$regRegiao['id']}" value="">
                                                            </div>

                                                            <div class="fkcorreiosg2cp4-float-left">
                                                                <span id="fkcorreiosg2cp4_span_regiao">a</span>
                                                            </div>

                                                            <div class="fkcorreiosg2cp4-col-lg-20 fkcorreiosg2cp4-float-left">
                                                                <input class="fkcorreiosg2cp4-mask-cep" type="text" name="fkcorreiosg2cp4_regiao_cep2_excluido_{$regRegiao['id']}" id="fkcorreiosg2cp4_regiao_cep2_excluido_{$regRegiao['id']}" value="">
                                                            </div>

                                                            <div class="fkcorreiosg2cp4-float-left" id="fkcorreiosg2cp4_button_regiao">
                                                                <input class="fkcorreiosg2cp4-button" name="button" type="button" value="{l s="Incluir" mod="fkcorreiosg2cp4"}" onclick="fkcorreiosg2cp4IncluirCepRegiaoExcluido({$regRegiao['id']});">
                                                            </div>

                                                        </div>

                                                        <div class="fkcorreiosg2cp4-form">
                                                            <div class="fkcorreiosg2cp4-col-lg-95">
                                                                {assign var="temp" value="fkcorreiosg2cp4_regiao_cep_excluido_`$regRegiao['id']`"}
                                                                <textarea name="fkcorreiosg2cp4_regiao_cep_excluido_{$regRegiao['id']}" id="fkcorreiosg2cp4_regiao_cep_excluido_{$regRegiao['id']}">{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['regiao_cep_excluido']}{/if}</textarea>
                                                            </div>
                                                            <p class="help-block">
                                                                Os intervalos de CEP aqui relacionados não serão atendidos por esta Região
                                                            </p>
                                                        </div>

                                                    </div>

                                                    <div class="fkcorreiosg2cp4-panel fkcorreiosg2cp4-sub-panel fkcorreiosg2cp4-regioes">

                                                        <div class="fkcorreiosg2cp4-panel-heading">
                                                            {l s="Desconto no Frete" mod="fkcorreiosg2cp4"}
                                                        </div>

                                                        <div class="fkcorreiosg2cp4-form">
                                                            <label class="fkcorreiosg2cp4-label fkcorreiosg2cp4-col-lg-40">
                                                                {l s="Percentual de Desconto:" mod="fkcorreiosg2cp4"}
                                                            </label>
                                                            <div class="fkcorreiosg2cp4-col-lg-20 fkcorreiosg2cp4-float-left">
                                                                {assign var="temp" value="fkcorreiosg2cp4_regiao_percentual_desc_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp4_regiao_percentual_desc_{$regRegiao['id']}" id="fkcorreiosg2cp4_regiao_percentual_desc_{$regRegiao['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['percentual_desconto']}{/if}">
                                                            </div>
                                                        </div>

                                                        <div class="fkcorreiosg2cp4-form">
                                                            <label class="fkcorreiosg2cp4-label fkcorreiosg2cp4-col-lg-40">
                                                                {l s="Valor do Pedido:" mod="fkcorreiosg2cp4"}
                                                            </label>
                                                            <div class="fkcorreiosg2cp4-col-lg-20 fkcorreiosg2cp4-float-left">
                                                                {assign var="temp" value="fkcorreiosg2cp4_regiao_valor_pedido_desc_`$regRegiao['id']`"}
                                                                <input type="text" name="fkcorreiosg2cp4_regiao_valor_pedido_desc_{$regRegiao['id']}" id="fkcorreiosg2cp4_regiao_valor_pedido_desc_{$regRegiao['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{$regRegiao['valor_pedido_desconto']}{/if}">
                                                            </div>
                                                        </div>

                                                        <p class="help-block">
                                                            Informe o valor 0 (zero) nos campos "Percentual de Desconto" e "Valor do Pedido" para não aplicar desconto ao frete
                                                        </p>

                                                    </div>

                                                    <div class="fkcorreiosg2cp4-panel-footer">
                                                        <button type="submit" value="1" name="btnDelRegiao" class="fkcorreiosg2cp4-button fkcorreiosg2cp4-float-left" onclick="return fkcorreiosg2cp4Excluir('{l s="Confirma a exclusão da Região?" mod="fkcorreiosg2cp4"}');">
                                                            <i class="process-icon-delete"></i>
                                                            {l s="Excluir Região" mod="fkcorreiosg2cp4"}
                                                        </button>

                                                        <button type="submit" value="1" name="btnSubmitRegiao" class="fkcorreiosg2cp4-button fkcorreiosg2cp4-float-right">
                                                            <i class="process-icon-save"></i>
                                                            {l s="Salvar" mod="fkcorreiosg2cp4"}
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