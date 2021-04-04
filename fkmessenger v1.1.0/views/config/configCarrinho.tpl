
<div class="fkmessenger-panel">
    
    <div class="fkmessenger-panel-heading">
        {l s="Carrinho de Compras" mod="fkmessenger"}
    </div>

    <form id="configuration_form" class="defaultForm  form-horizontal" action="{$tab_4['formAction']}&origem=configCarrinho" method="post" enctype="multipart/form-data">
        <div class="fkmessenger-panel-header">
            <button type="submit" value="1" name="btnAdd" class="fkmessenger-button">
                <i class="process-icon-new"></i>
                {l s="Incluir Mensagem" mod="fkmessenger"}
            </button>
            <button type="button" value="1" name="btnAjuda" class="fkmessenger-button fkmessenger-float-right" onClick="window.open('http://www.modulosfk.com.br/modulosfk/ajuda/fkmessenger_v1_0_0.pdf','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=900,height=500,left=500,top=150'); return false;">
                <i class="process-icon-help"></i>
                {l s="Ajuda" mod="fkmessenger"}
            </button>
        </div>
    </form>
    
    {foreach $tab_4['mensagens'] as $mensagem}

        <form id="configuration_form" class="defaultForm  form-horizontal" action="{$tab_4['formAction']}&origem=configCarrinho&id={$mensagem['id']}" method="post" enctype="multipart/form-data">
            
            <div class="fkmessenger-panel">
            
                <div class="fkmessenger-panel-heading fkmessenger-heading-color fkmessenger-toggle" onclick="fkmessengerToggle('fkmessenger_toggle_itens_{$mensagem['id']}')">
                    <i class="icon-resize-full"></i>
                    {$mensagem['nome_mensagem']}
                </div>
            
                <div class="fkmessenger-toggle-itens" id="fkmessenger_toggle_itens_{$mensagem['id']}">
                    <div class="fkmessenger-form">
                        <label class="fkmessenger-label fkmessenger-col-lg-10"></label>
                        <div class="fkmessenger-float-left">
                            {assign var="temp" value="fkmessenger_carrinho_ativo_`$mensagem['id']`"}
                            <input type="checkbox" name="fkmessenger_carrinho_ativo_{$mensagem['id']}" id="fkmessenger_carrinho_ativo_{$mensagem['id']}" value="1" {if isset($smarty.post.$temp) and $smarty.post.$temp == 1}checked="checked"{else}{if isset($mensagem['ativo']) and $mensagem['ativo'] == 1}checked="checked"{/if}{/if}>
                        </div>
                        <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                            {l s="Ativo" mod="fkmessenger"}
                        </label>
                    </div>
                    
                    <div class="fkmessenger-panel-margin fkmessenger-col-lg-70">
                        
                        <div class="fkmessenger-panel-heading">
                            {l s="Identificação da mensagem" mod="fkmessenger"}
                        </div>
                        
                        <div class="fkmessenger-form">
                            <div class="fkmessenger-col-lg-50 fkmessenger-float-left">
                                {assign var="temp" value="fkmessenger_carrinho_nome_msg_`$mensagem['id']`"}
                                <input type="text" name="fkmessenger_carrinho_nome_msg_{$mensagem['id']}" id="fkmessenger_carrinho_nome_msg_{$mensagem['id']}" placeholder="{l s="Identificação da mensagem" mod="fkmessenger"}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{if isset($mensagem['nome_mensagem'])}{$mensagem['nome_mensagem']}{/if}{/if}">
                            </div>
                            <div class="fkmessenger-row">
                                <span>Informe um nome para identificar a mensagem</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fkmessenger-panel-margin fkmessenger-col-lg-70">
                    
                        <div class="fkmessenger-panel-heading">
                            {l s="Frequência" mod="fkmessenger"}
                        </div>
                        
                        {assign var="temp" value="fkmessenger_carrinho_freq_`$mensagem['id']`"}
                        
                        <div class="fkmessenger-form">
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_freq_{$mensagem['id']}" id="fkmessenger_carrinho_freq_{$mensagem['id']}" value="1" onclick="fkmessengerHideId('fkmessenger_filtro_{$mensagem['id']}')" {if isset($smarty.post.$temp) and $smarty.post.$temp == 1}checked="checked"{else}{if isset($mensagem['frequencia']) and $mensagem['frequencia'] == 1}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Mostrar sempre" mod="fkmessenger"}
                            </label>
                        </div>
                        
                        <div class="fkmessenger-form">
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_freq_{$mensagem['id']}" id="fkmessenger_carrinho_freq_{$mensagem['id']}" value="2" onclick="fkmessengerHideId('fkmessenger_filtro_{$mensagem['id']}')" {if isset($smarty.post.$temp) and $smarty.post.$temp == 2}checked="checked"{else}{if isset($mensagem['frequencia']) and $mensagem['frequencia'] == 2}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Mostrar sempre na abertura da sessão" mod="fkmessenger"}
                            </label>
                        </div>
                        
                        <div class="fkmessenger-form">
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_freq_{$mensagem['id']}" id="fkmessenger_carrinho_freq_{$mensagem['id']}" value="3" onclick="fkmessengerShowId('fkmessenger_filtro_{$mensagem['id']}')" {if isset($smarty.post.$temp) and $smarty.post.$temp == 3}checked="checked"{else}{if isset($mensagem['frequencia']) and $mensagem['frequencia'] == 3}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Mostrar sempre que o valor do pedido for menor que o definido no filtro" mod="fkmessenger"}
                            </label>
                        </div>
                        
                        <div class="fkmessenger-form">
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_freq_{$mensagem['id']}" id="fkmessenger_carrinho_freq_{$mensagem['id']}" value="4" onclick="fkmessengerShowId('fkmessenger_filtro_{$mensagem['id']}')" {if isset($smarty.post.$temp) and $smarty.post.$temp == 4}checked="checked"{else}{if isset($mensagem['frequencia']) and $mensagem['frequencia'] == 4}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Mostrar uma única vez quando o valor do pedido for menor que o definido no filtro" mod="fkmessenger"}
                            </label>
                        </div>

                        <div class="fkmessenger-form">
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_freq_{$mensagem['id']}" id="fkmessenger_carrinho_freq_{$mensagem['id']}" value="5" onclick="fkmessengerShowId('fkmessenger_filtro_{$mensagem['id']}')" {if isset($smarty.post.$temp) and $smarty.post.$temp == 5}checked="checked"{else}{if isset($mensagem['frequencia']) and $mensagem['frequencia'] == 5}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Mostrar sempre que o valor do pedido for maior que o definido no filtro" mod="fkmessenger"}
                            </label>
                        </div>

                        <div class="fkmessenger-form">
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_freq_{$mensagem['id']}" id="fkmessenger_carrinho_freq_{$mensagem['id']}" value="6" onclick="fkmessengerShowId('fkmessenger_filtro_{$mensagem['id']}')" {if isset($smarty.post.$temp) and $smarty.post.$temp == 6}checked="checked"{else}{if isset($mensagem['frequencia']) and $mensagem['frequencia'] == 6}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Mostrar uma única vez quando o valor do pedido for maior que o definido no filtro" mod="fkmessenger"}
                            </label>
                        </div>
                        
                        {assign var="temp" value="fkmessenger_carrinho_freq_`$mensagem['id']`"}
                        
                        <div class="fkmessenger-panel" id="fkmessenger_filtro_{$mensagem['id']}" style="{if isset($smarty.post.$temp) and ($smarty.post.$temp >= 3 and $smarty.post.$temp <= 6)}display: block;{else}{if isset($mensagem['frequencia']) and ($mensagem['frequencia'] >= 3 and $mensagem['frequencia'] <= 6)}display: block;{else}display: none;{/if}{/if}">
                            <div class="fkmessenger-panel-heading">
                                {l s="Filtro" mod="fkmessenger"}
                            </div>

                            <div class="fkmessenger-form">
                                <div class="fkmessenger-col-lg-10 fkmessenger-float-left">
                                    {assign var="temp" value="fkmessenger_carrinho_filtro_`$mensagem['id']`"}
                                    <input type="text" name="fkmessenger_carrinho_filtro_{$mensagem['id']}" id="fkmessenger_carrinho_filtro_{$mensagem['id']}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{if isset($mensagem['valor_pedido'])}{$mensagem['valor_pedido']}{/if}{/if}">
                                </div>
                                <div class="fkmessenger-row">
                                    <span>Preencha o valor do pedido.</span>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="fkmessenger-panel-margin fkmessenger-col-lg-70">
                    
                        <div class="fkmessenger-panel-heading">
                            {l s="Especificações LightBox" mod="fkmessenger"}
                        </div>

                        <div class="fkmessenger-form">
                            <div class="fkmessenger-float-left">
                                {assign var="temp" value="fkmessenger_carrinho_modal_`$mensagem['id']`"}
                                <input type="checkbox" name="fkmessenger_carrinho_modal_{$mensagem['id']}" id="fkmessenger_carrinho_modal_{$mensagem['id']}" value="1" {if isset($smarty.post.$temp) and $smarty.post.$temp == 1}checked="checked"{else}{if isset($mensagem['modal']) and $mensagem['modal'] == 1}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Não mostrar o botão Fechar" mod="fkmessenger"}
                            </label>
                        </div>
                        <div class="fkmessenger-form">
                            <div class="fkmessenger-col-lg-10 fkmessenger-float-left">
                                {assign var="temp" value="fkmessenger_carrinho_largura_`$mensagem['id']`"}
                                <input type="text" name="fkmessenger_carrinho_largura_{$mensagem['id']}" id="fkmessenger_carrinho_largura_{$mensagem['id']}" placeholder="{l s="Largura" mod="fkmessenger"}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{if isset($mensagem['largura'])}{$mensagem['largura']}{/if}{/if}">
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Largura" mod="fkmessenger"}
                            </label>
                        </div>
                        <div class="fkmessenger-form">
                            <div class="fkmessenger-col-lg-10 fkmessenger-float-left">
                                {assign var="temp" value="fkmessenger_carrinho_altura_`$mensagem['id']`"}
                                <input type="text" name="fkmessenger_carrinho_altura_{$mensagem['id']}" id="fkmessenger_carrinho_altura_{$mensagem['id']}" placeholder="{l s="Altura" mod="fkmessenger"}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{if isset($mensagem['altura'])}{$mensagem['altura']}{/if}{/if}">
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Altura" mod="fkmessenger"}
                            </label>
                            <div class="fkmessenger-row">
                                <span>Para tamanho automático preencha com o valor 0.</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fkmessenger-panel-margin fkmessenger-col-lg-70">
                    
                        <div class="fkmessenger-panel-heading">
                            {l s="Botões" mod="fkmessenger"}
                        </div>
                        
                        <div class="fkmessenger-form">
                            <div class="fkmessenger-col-lg-25 fkmessenger-float-left">
                                {assign var="temp" value="fkmessenger_carrinho_botao1_`$mensagem['id']`"}
                                <input type="text" name="fkmessenger_carrinho_botao1_{$mensagem['id']}" id="fkmessenger_carrinho_botao1_{$mensagem['id']}" placeholder="{l s="Nome do botão 1" mod="fkmessenger"}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{if isset($mensagem['nome_botao_1'])}{$mensagem['nome_botao_1']}{/if}{/if}">
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Nome do Botão 1" mod="fkmessenger"}
                            </label>
                        </div>
                        <div class="fkmessenger-form">
                            <div class="fkmessenger-col-lg-50 fkmessenger-float-left">
                                {assign var="temp" value="fkmessenger_carrinho_link1_`$mensagem['id']`"}
                                <input type="text" name="fkmessenger_carrinho_link1_{$mensagem['id']}" id="fkmessenger_carrinho_link1_{$mensagem['id']}" placeholder="{l s="Link do botão 1" mod="fkmessenger"}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{if isset($mensagem['link_botao_1'])}{$mensagem['link_botao_1']}{/if}{/if}">
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Link do Botão 1" mod="fkmessenger"}
                            </label>
                            <div class="fkmessenger-row">
                                <span>Para desabilitar o botão deixe o campo em branco.</span>
                            </div>
                        </div>
                        <div class="fkmessenger-form">
                            <div class="fkmessenger-float-left">
                                {assign var="temp" value="fkmessenger_carrinho_link1_np_`$mensagem['id']`"}
                                <input type="checkbox" name="fkmessenger_carrinho_link1_np_{$mensagem['id']}" id="fkmessenger_carrinho_link1_np_{$mensagem['id']}" value="1" {if isset($smarty.post.$temp) and $smarty.post.$temp == 1}checked="checked"{else}{if isset($mensagem['nova_pagina_1']) and $mensagem['nova_pagina_1'] == 1}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Nova página" mod="fkmessenger"}
                            </label>
                        </div>
                    
                        {assign var="temp" value="fkmessenger_carrinho_cor1_`$mensagem['id']`"}
                    
                        <div class="fkmessenger-form">
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_cor1_{$mensagem['id']}" id="fkmessenger_carrinho_cor1_{$mensagem['id']}" value="1" {if isset($smarty.post.$temp) and $smarty.post.$temp == 1}checked="checked"{else}{if isset($mensagem['cor_1']) and $mensagem['cor_1'] == 1}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Branco" mod="fkmessenger"}
                            </label>
                            
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_cor1_{$mensagem['id']}" id="fkmessenger_carrinho_cor1_{$mensagem['id']}" value="2" {if isset($smarty.post.$temp) and $smarty.post.$temp == 2}checked="checked"{else}{if isset($mensagem['cor_1']) and $mensagem['cor_1'] == 2}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Preto" mod="fkmessenger"}
                            </label>
                            
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_cor1_{$mensagem['id']}" id="fkmessenger_carrinho_cor1_{$mensagem['id']}" value="3" {if isset($smarty.post.$temp) and $smarty.post.$temp == 3}checked="checked"{else}{if isset($mensagem['cor_1']) and $mensagem['cor_1'] == 3}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Azul" mod="fkmessenger"}
                            </label>
                            
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_cor1_{$mensagem['id']}" id="fkmessenger_carrinho_cor1_{$mensagem['id']}" value="4" {if isset($smarty.post.$temp) and $smarty.post.$temp == 4}checked="checked"{else}{if isset($mensagem['cor_1']) and $mensagem['cor_1'] == 4}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Verde" mod="fkmessenger"}
                            </label>
                            
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_cor1_{$mensagem['id']}" id="fkmessenger_carrinho_cor1_{$mensagem['id']}" value="5" {if isset($smarty.post.$temp) and $smarty.post.$temp == 5}checked="checked"{else}{if isset($mensagem['cor_1']) and $mensagem['cor_1'] == 5}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Vermelho" mod="fkmessenger"}
                            </label>
                        </div>
                        
                        <div class="fkmessenger-form"></div>
                    
                        <div class="fkmessenger-form">
                            <div class="fkmessenger-col-lg-25 fkmessenger-float-left">
                                {assign var="temp" value="fkmessenger_carrinho_botao2_`$mensagem['id']`"}
                                <input type="text" name="fkmessenger_carrinho_botao2_{$mensagem['id']}" id="fkmessenger_carrinho_botao2_{$mensagem['id']}" placeholder="{l s="Nome do botão 2" mod="fkmessenger"}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{if isset($mensagem['nome_botao_2'])}{$mensagem['nome_botao_2']}{/if}{/if}">
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Nome do Botão 2" mod="fkmessenger"}
                            </label>
                        </div>
                        <div class="fkmessenger-form">
                            <div class="fkmessenger-col-lg-50 fkmessenger-float-left">
                                {assign var="temp" value="fkmessenger_carrinho_link2_`$mensagem['id']`"}
                                <input type="text" name="fkmessenger_carrinho_link2_{$mensagem['id']}" id="fkmessenger_carrinho_link2_{$mensagem['id']}" placeholder="{l s="Link do botão 2" mod="fkmessenger"}" value="{if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{if isset($mensagem['link_botao_2'])}{$mensagem['link_botao_2']}{/if}{/if}">
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Link do Botão 2" mod="fkmessenger"}
                            </label>
                            <div class="fkmessenger-row">
                                <span>Para desabilitar o botão deixe o campo em branco.</span>
                            </div>
                        </div>
                        
                        <div class="fkmessenger-form">
                            <div class="fkmessenger-float-left">
                                {assign var="temp" value="fkmessenger_carrinho_link2_np_`$mensagem['id']`"}
                                <input type="checkbox" name="fkmessenger_carrinho_link2_np_{$mensagem['id']}" id="fkmessenger_carrinho_link2_np_{$mensagem['id']}" value="1" {if isset($smarty.post.$temp) and $smarty.post.$temp == 1}checked="checked"{else}{if isset($mensagem['nova_pagina_2']) and $mensagem['nova_pagina_2'] == 1}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Nova página" mod="fkmessenger"}
                            </label>
                        </div>
                        
                        {assign var="temp" value="fkmessenger_carrinho_cor2_`$mensagem['id']`"}
                        
                        <div class="fkmessenger-form">
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_cor2_{$mensagem['id']}" id="fkmessenger_carrinho_cor2_{$mensagem['id']}" value="1" {if isset($smarty.post.$temp) and $smarty.post.$temp == 1}checked="checked"{else}{if isset($mensagem['cor_2']) and $mensagem['cor_2'] == 1}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Branco" mod="fkmessenger"}
                            </label>
                            
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_cor2_{$mensagem['id']}" id="fkmessenger_carrinho_cor2_{$mensagem['id']}" value="2" {if isset($smarty.post.$temp) and $smarty.post.$temp == 2}checked="checked"{else}{if isset($mensagem['cor_2']) and $mensagem['cor_2'] == 2}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Preto" mod="fkmessenger"}
                            </label>
                            
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_cor2_{$mensagem['id']}" id="fkmessenger_carrinho_cor2_{$mensagem['id']}" value="3" {if isset($smarty.post.$temp) and $smarty.post.$temp == 3}checked="checked"{else}{if isset($mensagem['cor_2']) and $mensagem['cor_2'] == 3}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Azul" mod="fkmessenger"}
                            </label>
                            
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_cor2_{$mensagem['id']}" id="fkmessenger_carrinho_cor2_{$mensagem['id']}" value="4" {if isset($smarty.post.$temp) and $smarty.post.$temp == 4}checked="checked"{else}{if isset($mensagem['cor_2']) and $mensagem['cor_2'] == 4}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Verde" mod="fkmessenger"}
                            </label>
                            
                            <div class="fkmessenger-float-left">
                                <input type="radio" name="fkmessenger_carrinho_cor2_{$mensagem['id']}" id="fkmessenger_carrinho_cor2_{$mensagem['id']}" value="5" {if isset($smarty.post.$temp) and $smarty.post.$temp == 5}checked="checked"{else}{if isset($mensagem['cor_2']) and $mensagem['cor_2'] == 5}checked="checked"{/if}{/if}>
                            </div>
                            <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                                {l s="Vermelho" mod="fkmessenger"}
                            </label>
                        </div>
                        
                    </div>
                    
                    <div class="fkmessenger-panel-margin fkmessenger-col-lg-70">
                    
                        <div class="fkmessenger-panel-heading">
                            {l s="Mensagem" mod="fkmessenger"}
                        </div>
                        
                        <div class="fkmessenger-tinymce">
                            {assign var="temp" value="fkmessenger_carrinho_mensagem_`$mensagem['id']`"}
                            <textarea class="fkmessenger-tinymce-textarea" name="fkmessenger_carrinho_mensagem_{$mensagem['id']}" id="fkmessenger_carrinho_mensagem_{$mensagem['id']}">
                                {if isset($smarty.post.$temp)}{$smarty.post.$temp}{else}{if isset($mensagem['mensagem'])}{$mensagem['mensagem']}{/if}{/if}
                            </textarea>
                        </div>
                        
                    </div>
                
                    <div class="fkmessenger-panel-footer">
                        <button type="submit" value="1" name="btnDelete" class="fkmessenger-button" onclick="return fkmessengerConfirma('Confirma a exclusão da mensagem?')">
                            <i class="process-icon-delete"></i>
                            {l s="Excluir" mod="fkmessenger"}
                        </button>
                        <button type="submit" value="1" name="btnSubmit" class="fkmessenger-button fkmessenger-float-right">
                            <i class="process-icon-save"></i>
                            {l s="Salvar" mod="fkmessenger"}
                        </button>
                    </div>
                </div>
            </div>

        </form>

    {/foreach}

</div>