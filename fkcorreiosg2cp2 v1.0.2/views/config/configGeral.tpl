
<form id="configuration_form" class="defaultForm  form-horizontal" action="{$tab_2['formAction']}&origem=configGeral" method="post" enctype="multipart/form-data">

    <div class="fkcorreiosg2-panel" style="border-top-left-radius: 0">

        <div class="fkcorreiosg2-panel-header">
            <button type="button" value="1" name="btnAjuda" class="fkcorreiosg2-button fkcorreiosg2-float-right" onClick="window.open('http://www.modulosfk.com.br/modulosfk/ajuda/fkcorreiosg2cp2_v1_0_0.pdf','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=900,height=500,left=500,top=150'); return false;">
                <i class="process-icon-help"></i>
                {l s="Ajuda" mod="fkcorreiosg2cp2"}
            </button>
        </div>

        <div class="fkcorreiosg2-panel fkcorreiosg2-col-lg-50 fkcorreiosg2-sub-panel" style="margin-top: 30px !important;">

            <div class="fkcorreiosg2-panel-heading">
                {l s="Dados do Remetente" mod="fkcorreiosg2cp2"}
            </div>

            <div class="fkcorreiosg2-form">
                <label class="fkcorreiosg2-label fkcorreiosg2-col-lg-20">
                    {l s="Remetente:" mod="fkcorreiosg2cp2"}
                </label>
                <div class="fkcorreiosg2-col-lg-70 fkcorreiosg2-float-left">
                    <input type="text" name="fkcorreiosg2cp2_remetente" id="fkcorreiosg2cp2_remetente" value="{$tab_2['fkcorreiosg2cp2_remetente']}">
                </div>
            </div>
            <div class="fkcorreiosg2-form">
                <label class="fkcorreiosg2-label fkcorreiosg2-col-lg-20">
                    {l s="Endereço:" mod="fkcorreiosg2cp2"}
                </label>
                <div class="fkcorreiosg2-col-lg-70 fkcorreiosg2-float-left">
                    <input type="text" name="fkcorreiosg2cp2_endereco" id="fkcorreiosg2cp2_endereco" value="{$tab_2['fkcorreiosg2cp2_endereco']}">
                </div>
            </div>
            <div class="fkcorreiosg2-form">
                <label class="fkcorreiosg2-label fkcorreiosg2-col-lg-20">
                    {l s="Número:" mod="fkcorreiosg2cp2"}
                </label>
                <div class="fkcorreiosg2-col-lg-20 fkcorreiosg2-float-left">
                    <input type="text" name="fkcorreiosg2cp2_numero" id="fkcorreiosg2cp2_numero" maxlength="5" value="{$tab_2['fkcorreiosg2cp2_numero']}">
                </div>
            </div>
            <div class="fkcorreiosg2-form">
                <label class="fkcorreiosg2-label fkcorreiosg2-col-lg-20">
                    {l s="Bairro:" mod="fkcorreiosg2cp2"}
                </label>
                <div class="fkcorreiosg2-col-lg-70 fkcorreiosg2-float-left">
                    <input type="text" name="fkcorreiosg2cp2_bairro" id="fkcorreiosg2cp2_bairro" value="{$tab_2['fkcorreiosg2cp2_bairro']}">
                </div>
            </div>
            <div class="fkcorreiosg2-form">
                <label class="fkcorreiosg2-label fkcorreiosg2-col-lg-20">
                    {l s="Cidade:" mod="fkcorreiosg2cp2"}
                </label>
                <div class="fkcorreiosg2-col-lg-70 fkcorreiosg2-float-left">
                    <input type="text" name="fkcorreiosg2cp2_cidade" id="fkcorreiosg2cp2_cidade" value="{$tab_2['fkcorreiosg2cp2_cidade']}">
                </div>
            </div>
            <div class="fkcorreiosg2-form">
                <label class="fkcorreiosg2-label fkcorreiosg2-col-lg-20">
                    {l s="Estado:" mod="fkcorreiosg2cp2"}
                </label>
                <div class="fkcorreiosg2-col-lg-10 fkcorreiosg2-float-left">
                    <input type="text" name="fkcorreiosg2cp2_estado" id="fkcorreiosg2cp2_estado" maxlength="2" value="{$tab_2['fkcorreiosg2cp2_estado']}">
                </div>
            </div>
            <div class="fkcorreiosg2-form">
                <label class="fkcorreiosg2-label fkcorreiosg2-col-lg-20">
                    {l s="CEP:" mod="fkcorreiosg2cp2"}
                </label>
                <div class="fkcorreiosg2-col-lg-20 fkcorreiosg2-float-left">
                    <input class="fkcorreiosg2cp2-mask-cep" type="text" name="fkcorreiosg2cp2_cep" id="fkcorreiosg2cp2_cep" value="{$tab_2['fkcorreiosg2cp2_cep']}">
                </div>
            </div>

        </div>

        <div class="fkcorreiosg2-panel fkcorreiosg2-col-lg-50 fkcorreiosg2-sub-panel"">

            <div class="fkcorreiosg2-panel-heading">
                {l s="Logotipo" mod="fkcorreiosg2cp2"}
            </div>

            {assign var="urlLogo" value="`$tab_2['urlLogo']`"}
            {assign var="uriLogo" value="`$tab_2['uriLogo']`"}
            {assign var="urlNoImage" value="`$tab_2['urlNoImage']`"}

            <div class="fkcorreiosg2-form">
                {if file_exists({$uriLogo})}
                    <img id="fkcorreiosg2cp2_logo" alt="Logo remetente" src="{$urlLogo}">
                {else}
                    <img id="fkcorreiosg2cp2_logo" alt="Logo remetente" src="{$urlNoImage}">
                {/if}
            </div>

            <div class="fkcorreiosg2-form">
                <input class="btn btn-default" type="file" name="fkcorreiosg2cp2_logo">
            </div>
            <p class="help-block">
                Formato jpg
            </p>

            {if file_exists({$uriLogo})}
                <script type="text/javascript">
                    d = new Date();
                    idLogo = '#fkcorreiosg2cp2_logo';
                    $(idLogo).attr("src", "{$urlLogo}?" + d.getTime());
                </script>
            {/if}

        </div>

        <div class="fkcorreiosg2-panel fkcorreiosg2-col-lg-50 fkcorreiosg2-sub-panel">

            <div class="fkcorreiosg2-panel-heading">
                {l s="Diversos" mod="fkcorreiosg2cp2"}
            </div>

            <div class="fkcorreiosg2-panel fkcorreiosg2-col-lg-70">

                <div class="fkcorreiosg2-panel-heading">
                    {l s="Etiquetas por página" mod="fkcorreiosg2cp2"}
                </div>

                <div class="fkcorreiosg2-form">
                    <div class="fkcorreiosg2-float-left">
                        <input type="radio" name="fkcorreiosg2cp2_etiq_pagina" value="2" {if isset($tab_2['fkcorreiosg2cp2_etiq_pagina']) and $tab_2['fkcorreiosg2cp2_etiq_pagina'] == '2'}checked="checked"{/if}>
                    </div>
                    <label class="fkcorreiosg2-label-right fkcorreiosg2-col-lg-auto">
                        {l s="2 etiquetas" mod="fkcorreiosg2cp2"}
                    </label>
                </div>

                <div class="fkcorreiosg2-form">
                    <div class="fkcorreiosg2-float-left">
                        <input type="radio" name="fkcorreiosg2cp2_etiq_pagina" value="4" {if isset($tab_2['fkcorreiosg2cp2_etiq_pagina']) and $tab_2['fkcorreiosg2cp2_etiq_pagina'] == '4'}checked="checked"{/if}>
                    </div>
                    <label class="fkcorreiosg2-label-right fkcorreiosg2-col-lg-auto">
                        {l s="4 etiquetas" mod="fkcorreiosg2cp2"}
                    </label>
                </div>

            </div>

            <div class="fkcorreiosg2-panel fkcorreiosg2-col-lg-70">

                <div class="fkcorreiosg2-panel-heading">
                    {l s="Considerar Impresso até o Pedido" mod="fkcorreiosg2cp2"}
                </div>

                <div class="fkcorreiosg2-form">
                    <div class="fkcorreiosg2-col-lg-20 fkcorreiosg2-float-left">
                        <input type="text" name="fkcorreiosg2cp2_impresso" id="fkcorreiosg2cp2_impresso" value="{$tab_2['fkcorreiosg2cp2_impresso']}">
                    </div>
                </div>
                <p class="help-block">
                    Informe até qual Pedido deve ser considerado como impresso.
                </p>

            </div>

            <div class="fkcorreiosg2-form">
                <label for="fkcorreiosg2cp2_nao_pagos" class="fkcorreiosg2-label fkcorreiosg2-col-lg-15"></label>
                <div class="fkcorreiosg2-float-left">
                    <input type="checkbox" name="fkcorreiosg2cp2_nao_pagos" id="fkcorreiosg2cp2_nao_pagos" value="on" {if isset($tab_2['fkcorreiosg2cp2_nao_pagos']) and $tab_2['fkcorreiosg2cp2_nao_pagos'] == 'on'}checked="checked"{/if}>
                </div>
                <label class="fkcorreiosg2-label-right fkcorreiosg2-col-lg-auto">
                    {l s="Mostrar Pedidos Não Pagos" mod="fkcorreiosg2cp2"}
                </label>
            </div>
            <div class="fkcorreiosg2-form">
                <label for="fkcorreiosg2cp2_excluir_config" class="fkcorreiosg2-label fkcorreiosg2-col-lg-15"></label>
                <div class="fkcorreiosg2-float-left">
                    <input type="checkbox" name="fkcorreiosg2cp2_excluir_config" id="fkcorreiosg2cp2_excluir_config" value="on" onclick="fkcorreiosg2cp2ExcluirConf('Atenção: Você marcou para excluir a configuração do módulo na desinstalado. Confirma?','fkcorreiosg2cp2_excluir_config')" {if isset($tab_2['fkcorreiosg2cp2_excluir_config']) and $tab_2['fkcorreiosg2cp2_excluir_config'] == 'on'}checked="checked"{/if}>
                </div>
                <label class="fkcorreiosg2-label-right fkcorreiosg2-col-lg-auto">
                    {l s="Excluir Configuração do Módulo na desinstalação" mod="fkcorreiosg2cp2"}
                </label>
            </div>

        </div>

        <div class="fkcorreiosg2-panel-footer">
            <button type="submit" value="1" name="btnSubmit" class="fkcorreiosg2-button fkcorreiosg2-float-right">
                <i class="process-icon-save"></i>
                {l s="Salvar" mod="fkcorreiosg2cp2"}
            </button>
        </div>

    </div>

</form>