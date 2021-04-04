
<form id="configuration_form" class="defaultForm  form-horizontal" action="{$tab_2['formAction']}&origem=configGeral" method="post">

    <div class="fkcorreiosg2-panel" style="border-top-left-radius: 0">

        <div class="fkcorreiosg2-panel-heading">
            {l s="Configuração geral" mod="fkcorreiosg2cp1"}
        </div>

        <div class="fkcorreiosg2-panel-header">
            <button type="button" value="1" name="btnAjuda" class="fkcorreiosg2-button fkcorreiosg2-float-right" onClick="window.open('http://www.modulosfk.com.br/modulosfk/ajuda/fkcorreiosg2cp1_v1_0_0.pdf','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=900,height=500,left=500,top=150'); return false;">
                <i class="process-icon-help"></i>
                {l s="Ajuda" mod="fkcorreiosg2cp1"}
            </button>
        </div>

        <div class="fkcorreiosg2-panel">

            <div class="fkcorreiosg2-panel-heading">
                {l s="Diversos" mod="fkcorreiosg2cp1"}
            </div>

            <div class="fkcorreiosg2-form">
                <label for="fkcorreiosg2cp1_excluir_config" class="fkcorreiosg2-label fkcorreiosg2-col-lg-15"></label>
                <div class="fkcorreiosg2-float-left">
                    <input type="checkbox" name="fkcorreiosg2cp1_excluir_config" id="fkcorreiosg2cp1_excluir_config" value="on" onclick="fkcorreiosg2cp1ExcluirConf('Atenção: Você marcou para excluir a configuração do módulo na desinstalado. Confirma?','fkcorreiosg2cp1_excluir_config')" {if isset($tab_2['fkcorreiosg2cp1_excluir_config']) and $tab_2['fkcorreiosg2cp1_excluir_config'] == 'on'}checked="checked"{/if}>
                </div>
                <label class="fkcorreiosg2-label-right fkcorreiosg2cp1-col-lg-auto">
                    {l s="Excluir Configuração do Módulo na desinstalação" mod="fkcorreiosg2cp1"}
                </label>
            </div>

        </div>

        <div class="fkcorreiosg2-panel-footer">
            <button type="submit" value="1" name="btnSubmit" class="fkcorreiosg2-button fkcorreiosg2-float-right">
                <i class="process-icon-save"></i>
                {l s="Salvar" mod="fkcorreiosg2cp1"}
            </button>
        </div>

    </div>

</form>