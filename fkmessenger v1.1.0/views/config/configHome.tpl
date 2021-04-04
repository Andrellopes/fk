
<form id="configuration_form" class="defaultForm  form-horizontal" action="{$tab_2['formAction']}&origem=configHome" method="post" enctype="multipart/form-data">

    <div class="fkmessenger-panel" style="border-top-left-radius: 0">
    
        <div class="fkmessenger-panel-heading">
            {l s="Home" mod="fkmessenger"}
        </div>
        
        <div class="fkmessenger-panel-header">
            <button type="button" value="1" name="btnAjuda" class="fkmessenger-button fkmessenger-float-right" onClick="window.open('http://www.modulosfk.com.br/modulosfk/ajuda/fkmessenger_v1_0_0.pdf','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=900,height=500,left=500,top=150'); return false;">
                <i class="process-icon-help"></i>
                {l s="Ajuda" mod="fkmessenger"}
            </button>
        </div>
        
        <div class="fkmessenger-panel">
        
            <div class="fkmessenger-panel-heading fkmessenger-heading-color">
                {l s="Mensagem Home" mod="fkmessenger"}
            </div>
        
            <div class="fkmessenger-form">
                <label for="fkmessenger_home_ativo" class="fkmessenger-label fkmessenger-col-lg-10"></label>
                <div class="fkmessenger-float-left">
                    <input type="checkbox" name="fkmessenger_home_ativo" id="fkmessenger_home_ativo" value="1" {if isset($smarty.post.fkmessenger_home_ativo) and $smarty.post.fkmessenger_home_ativo == 1}checked="checked"{else}{if isset($tab_2['mensagens']['ativo']) and $tab_2['mensagens']['ativo'] == 1}checked="checked"{/if}{/if}>
                </div>
                <label for="fkmessenger_home_ativo" class="fkmessenger-label-right fkmessenger-col-lg-auto">
                    {l s="Ativo" mod="fkmessenger"}
                </label>
            </div>

            <div class="fkmessenger-panel-margin fkmessenger-col-lg-70">

                <div class="fkmessenger-panel-heading">
                    {l s="Frequência" mod="fkmessenger"}
                </div>

                <div class="fkmessenger-form">
                    <div class="fkmessenger-float-left">
                        <input type="radio" name="fkmessenger_home_freq" id="fkmessenger_home_freq" value="1" {if isset($smarty.post.fkmessenger_home_freq) and $smarty.post.fkmessenger_home_freq == 1}checked="checked"{else}{if !isset($tab_2['mensagens']['frequencia']) or (isset($tab_2['mensagens']['frequencia']) and $tab_2['mensagens']['frequencia'] == 1)}checked="checked"{/if}{/if}>
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Mostrar sempre" mod="fkmessenger"}
                    </label>
                </div>

                <div class="fkmessenger-form">
                    <div class="fkmessenger-float-left">
                        <input type="radio" name="fkmessenger_home_freq" id="fkmessenger_home_freq" value="2" {if isset($smarty.post.fkmessenger_home_freq) and $smarty.post.fkmessenger_home_freq == 2}checked="checked"{else}{if isset($tab_2['mensagens']['frequencia']) and $tab_2['mensagens']['frequencia'] == 2}checked="checked"{/if}{/if}>
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Mostrar sempre na abertura da sessão" mod="fkmessenger"}
                    </label>
                </div>

            </div>

            <div class="fkmessenger-panel-margin fkmessenger-col-lg-70">
            
                <div class="fkmessenger-panel-heading">
                    {l s="Especificações LightBox" mod="fkmessenger"}
                </div>

                <div class="fkmessenger-form">
                    <div class="fkmessenger-float-left">
                        <input type="checkbox" name="fkmessenger_home_modal" id="fkmessenger_home_modal" value="1" {if isset($smarty.post.fkmessenger_home_modal) and $smarty.post.fkmessenger_home_modal == 1}checked="checked"{else}{if isset($tab_2['mensagens']['modal']) and $tab_2['mensagens']['modal'] == 1}checked="checked"{/if}{/if}>
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Não mostrar o botão Fechar" mod="fkmessenger"}
                    </label>
                </div>
            
                <div class="fkmessenger-form">
                    <div class="fkmessenger-col-lg-10 fkmessenger-float-left">
                        <input type="text" name="fkmessenger_home_largura" id="fkmessenger_home_largura" placeholder="{l s="Largura" mod="fkmessenger"}" value="{if isset($smarty.post.fkmessenger_home_largura)}{$smarty.post.fkmessenger_home_largura}{else}{if isset($tab_2['mensagens']['largura'])}{$tab_2['mensagens']['largura']}{/if}{/if}">
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Largura" mod="fkmessenger"}
                    </label>
                </div>
                <div class="fkmessenger-form">
                    <div class="fkmessenger-col-lg-10 fkmessenger-float-left">
                        <input type="text" name="fkmessenger_home_altura" id="fkmessenger_home_altura" placeholder="{l s="Altura" mod="fkmessenger"}" value="{if isset($smarty.post.fkmessenger_home_altura)}{$smarty.post.fkmessenger_home_altura}{else}{if isset($tab_2['mensagens']['altura'])}{$tab_2['mensagens']['altura']}{/if}{/if}">
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
                        <input type="text" name="fkmessenger_home_botao1" id="fkmessenger_home_botao1" placeholder="{l s="Nome do botão 1" mod="fkmessenger"}" value="{if isset($smarty.post.fkmessenger_home_botao1)}{$smarty.post.fkmessenger_home_botao1}{else}{if isset($tab_2['mensagens']['nome_botao_1'])}{$tab_2['mensagens']['nome_botao_1']}{/if}{/if}">
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Nome do Botão 1" mod="fkmessenger"}
                    </label>
                </div>
                <div class="fkmessenger-form">
                    <div class="fkmessenger-col-lg-50 fkmessenger-float-left">
                        <input type="text" name="fkmessenger_home_link1" id="fkmessenger_home_link1" placeholder="{l s="Link do botão 1" mod="fkmessenger"}" value="{if isset($smarty.post.fkmessenger_home_link1)}{$smarty.post.fkmessenger_home_link1}{else}{if isset($tab_2['mensagens']['link_botao_1'])}{$tab_2['mensagens']['link_botao_1']}{/if}{/if}">
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
                        <input type="checkbox" name="fkmessenger_home_link1_np" id="fkmessenger_home_link1_np" value="1" {if isset($smarty.post.fkmessenger_home_link1_np) and $smarty.post.fkmessenger_home_link1_np == 1}checked="checked"{else}{if isset($tab_2['mensagens']['nova_pagina_1']) and $tab_2['mensagens']['nova_pagina_1'] == 1}checked="checked"{/if}{/if}>
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Nova página" mod="fkmessenger"}
                    </label>
                </div>
            
                <div class="fkmessenger-form">
                    <div class="fkmessenger-float-left">
                        <input type="radio" name="fkmessenger_home_cor1" id="fkmessenger_home_cor1" value="1" {if isset($smarty.post.fkmessenger_home_cor1) and $smarty.post.fkmessenger_home_cor1 == 1}checked="checked"{else}{if isset($tab_2['mensagens']['cor_1']) and $tab_2['mensagens']['cor_1'] == 1}checked="checked"{/if}{/if}>
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Branco" mod="fkmessenger"}
                    </label>
                    
                    <div class="fkmessenger-float-left">
                        <input type="radio" name="fkmessenger_home_cor1" id="fkmessenger_home_cor1" value="2" {if isset($smarty.post.fkmessenger_home_cor1) and $smarty.post.fkmessenger_home_cor1 == 2}checked="checked"{else}{if isset($tab_2['mensagens']['cor_1']) and $tab_2['mensagens']['cor_1'] == 2}checked="checked"{/if}{/if}>
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Preto" mod="fkmessenger"}
                    </label>
                    
                    <div class="fkmessenger-float-left">
                        <input type="radio" name="fkmessenger_home_cor1" id="fkmessenger_home_cor1" value="3" {if isset($smarty.post.fkmessenger_home_cor1) and $smarty.post.fkmessenger_home_cor1 == 3}checked="checked"{else}{if isset($tab_2['mensagens']['cor_1']) and $tab_2['mensagens']['cor_1'] == 3}checked="checked"{/if}{/if}>
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Azul" mod="fkmessenger"}
                    </label>
                    
                    <div class="fkmessenger-float-left">
                        <input type="radio" name="fkmessenger_home_cor1" id="fkmessenger_home_cor1" value="4" {if isset($smarty.post.fkmessenger_home_cor1) and $smarty.post.fkmessenger_home_cor1 == 4}checked="checked"{else}{if isset($tab_2['mensagens']['cor_1']) and $tab_2['mensagens']['cor_1'] == 4}checked="checked"{/if}{/if}>
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Verde" mod="fkmessenger"}
                    </label>
                    
                    <div class="fkmessenger-float-left">
                        <input type="radio" name="fkmessenger_home_cor1" id="fkmessenger_home_cor1" value="5" {if isset($smarty.post.fkmessenger_home_cor1) and $smarty.post.fkmessenger_home_cor1 == 5}checked="checked"{else}{if isset($tab_2['mensagens']['cor_1']) and $tab_2['mensagens']['cor_1'] == 5}checked="checked"{/if}{/if}>
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Vermelho" mod="fkmessenger"}
                    </label>
                </div>
                
                <div class="fkmessenger-form"></div>
            
                <div class="fkmessenger-form">
                    <div class="fkmessenger-col-lg-25 fkmessenger-float-left">
                        <input type="text" name="fkmessenger_home_botao2" id="fkmessenger_home_botao2" placeholder="{l s="Nome do botão 2" mod="fkmessenger"}" value="{if isset($smarty.post.fkmessenger_home_botao2)}{$smarty.post.fkmessenger_home_botao2}{else}{if isset($tab_2['mensagens']['nome_botao_2'])}{$tab_2['mensagens']['nome_botao_2']}{/if}{/if}">
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Nome do Botão 2" mod="fkmessenger"}
                    </label>
                </div>
                <div class="fkmessenger-form">
                    <div class="fkmessenger-col-lg-50 fkmessenger-float-left">
                        <input type="text" name="fkmessenger_home_link2" id="fkmessenger_home_link2" placeholder="{l s="Link do botão 2" mod="fkmessenger"}" value="{if isset($smarty.post.fkmessenger_home_link2)}{$smarty.post.fkmessenger_home_link2}{else}{if isset($tab_2['mensagens']['link_botao_2'])}{$tab_2['mensagens']['link_botao_2']}{/if}{/if}">
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
                        <input type="checkbox" name="fkmessenger_home_link2_np" id="fkmessenger_home_link2_np" value="1" {if isset($smarty.post.fkmessenger_home_link2_np) and $smarty.post.fkmessenger_home_link2_np == 1}checked="checked"{else}{if isset($tab_2['mensagens']['nova_pagina_2']) and $tab_2['mensagens']['nova_pagina_2'] == 1}checked="checked"{/if}{/if}>
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Nova página" mod="fkmessenger"}
                    </label>
                </div>
                
                <div class="fkmessenger-form">
                    <div class="fkmessenger-float-left">
                        <input type="radio" name="fkmessenger_home_cor2" id="fkmessenger_home_cor2" value="1" {if isset($smarty.post.fkmessenger_home_cor2) and $smarty.post.fkmessenger_home_cor2 == 1}checked="checked"{else}{if isset($tab_2['mensagens']['cor_2']) and $tab_2['mensagens']['cor_2'] == 1}checked="checked"{/if}{/if}>
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Branco" mod="fkmessenger"}
                    </label>
                    
                    <div class="fkmessenger-float-left">
                        <input type="radio" name="fkmessenger_home_cor2" id="fkmessenger_home_cor2" value="2" {if isset($smarty.post.fkmessenger_home_cor2) and $smarty.post.fkmessenger_home_cor2 == 2}checked="checked"{else}{if isset($tab_2['mensagens']['cor_2']) and $tab_2['mensagens']['cor_2'] == 2}checked="checked"{/if}{/if}>
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Preto" mod="fkmessenger"}
                    </label>
                    
                    <div class="fkmessenger-float-left">
                        <input type="radio" name="fkmessenger_home_cor2" id="fkmessenger_home_cor2" value="3" {if isset($smarty.post.fkmessenger_home_cor2) and $smarty.post.fkmessenger_home_cor2 == 3}checked="checked"{else}{if isset($tab_2['mensagens']['cor_2']) and $tab_2['mensagens']['cor_2'] == 3}checked="checked"{/if}{/if}>
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Azul" mod="fkmessenger"}
                    </label>
                    
                    <div class="fkmessenger-float-left">
                        <input type="radio" name="fkmessenger_home_cor2" id="fkmessenger_home_cor2" value="4" {if isset($smarty.post.fkmessenger_home_cor2) and $smarty.post.fkmessenger_home_cor2 == 4}checked="checked"{else}{if isset($tab_2['mensagens']['cor_2']) and $tab_2['mensagens']['cor_2'] == 4}checked="checked"{/if}{/if}>
                    </div>
                    <label class="fkmessenger-label-right fkmessenger-col-lg-auto">
                        {l s="Verde" mod="fkmessenger"}
                    </label>
                    
                    <div class="fkmessenger-float-left">
                        <input type="radio" name="fkmessenger_home_cor2" id="fkmessenger_home_cor2" value="5" {if isset($smarty.post.fkmessenger_home_cor2) and $smarty.post.fkmessenger_home_cor2 == 5}checked="checked"{else}{if isset($tab_2['mensagens']['cor_2']) and $tab_2['mensagens']['cor_2'] == 5}checked="checked"{/if}{/if}>
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
                    <textarea class="fkmessenger-tinymce-textarea" name="fkmessenger_home_mensagem" id="fkmessenger_home_mensagem">
                        {if isset($smarty.post.fkmessenger_home_mensagem)}{$smarty.post.fkmessenger_home_mensagem}{else}{if isset($tab_2['mensagens']['mensagem'])}{$tab_2['mensagens']['mensagem']}{/if}{/if}
                    </textarea>
                </div>
                
            </div>
        
            <div class="fkmessenger-panel-footer">
                <button type="submit" value="1" name="btnSubmit" class="fkmessenger-button fkmessenger-float-right">
                    <i class="process-icon-save"></i>
                    {l s="Salvar" mod="fkmessenger"}
                </button>
            </div>
        </div>
    </div>

</form>
