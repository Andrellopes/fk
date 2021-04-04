
<form id="configuration_form" class="defaultForm  form-horizontal" action="{$formAction}&origem=configParc_1" method="post">

<div class="panel">

    <div class="panel-heading">
        {l s="Parcelamento 1" mod="fkparcelamentog2"}
    </div>

    <div class="fkparcg2-panel-header">
        <button type="button" value="1" name="btnAjuda" class="fkparcg2-button fkparcg2-float-right" onClick="window.open('http://www.modulosfk.com.br/modulosfk/ajuda/fkparcelamentog2_v1_0_0.pdf','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=900,height=500,left=500,top=150'); return false;">
            <i class="process-icon-help"></i>
            {l s="Ajuda" mod="fkparcelamentog2"}
        </button>
    </div>

    <div class="panel fkparcg2-margin-top_40 fkparcg2-col-lg-60">

        <div class="panel-heading">
            {l s="Dados do Parcelamento" mod="fkparcelamentog2"}
        </div>

        <div class="form-group">
            <label for="fkparcg2_titulo_1" class="control-label fkparcg2-text-left fkparcg2-col-lg-40 fkparcg2-float-left">
                {l s="Título da aba" mod="fkparcelamentog2"}
            </label>
            <div class="fkparcg2-col-lg-20 fkparcg2-float-left">
                <input type="text" name="fkparcg2_titulo_1" id="fkparcg2_titulo_1" value="{$tab_2['fkparcg2_titulo_1']}">
            </div>
        </div>
        <div class="form-group">
            <label for="fkparcg2_parcelas_1" class="control-label fkparcg2-text-left fkparcg2-col-lg-40 fkparcg2-float-left">
                {l s="Total de parcelas" mod="fkparcelamentog2"}
            </label>
            <div class="fkparcg2-col-lg-10 fkparcg2-float-left">
                <input type="text" name="fkparcg2_parcelas_1" id="fkparcg2_parcelas_1" value="{$tab_2['fkparcg2_parcelas_1']}">
            </div>
        </div>
        <div class="form-group">
            <label for="fkparcg2_sem_juros_1" class="control-label fkparcg2-text-left fkparcg2-col-lg-40 fkparcg2-float-left">
                {l s="Parcelas sem juros" mod="fkparcelamentog2"}
            </label>
            <div class="fkparcg2-col-lg-10 fkparcg2-float-left">
                <input type="text" name="fkparcg2_sem_juros_1" id="fkparcg2_sem_juros_1" value="{$tab_2['fkparcg2_sem_juros_1']}">
            </div>
        </div>
        <div class="form-group">
            <label for="fkparcg2_valor_min_1" class="control-label fkparcg2-text-left fkparcg2-col-lg-40 fkparcg2-float-left">
                {l s="Valor mínimo da parcela" mod="fkparcelamentog2"}
            </label>
            <div class="fkparcg2-col-lg-10 fkparcg2-float-left">
                <input type="text" name="fkparcg2_valor_min_1" id="fkparcg2_valor_min_1" value="{$tab_2['fkparcg2_valor_min_1']}">
            </div>
        </div>
        <div class="form-group">
            <label for="fkparcg2_texto_1" class="control-label fkparcg2-text-left fkparcg2-col-lg-40 fkparcg2-float-left">
                {l s="Texto" mod="fkparcelamentog2"}
            </label>
            <div class="fkparcg2-col-lg-50 fkparcg2-float-left">
                <input type="text" name="fkparcg2_texto_1" id="fkparcg2_texto_1" value="{$tab_2['fkparcg2_texto_1']}">
            </div>
        </div>

    </div>

    <div class="panel fkparcg2-col-lg-60">

        <div class="panel-heading">
            {l s="Fatores" mod="fkparcelamentog2"}
        </div>

        <div id="fkparcg2_fatores_1">
            <div id="fkparcg2_itens_fatores_1">

                {assign var="idFator" value="0"}

                {foreach $tab_2['fkparcg2_fatores_1'] as $reg}

                    {assign var='idFator' value=$idFator + 1}

                    {if $reg != ''}
                        <div class="form-group">
                            <label for="fkparcg2_fator_1_{$idFator}" class="control-label fkparcg2-text-left fkparcg2-col-lg-40 fkparcg2-float-left">
                                Parcela {$idFator}
                            </label>
                            <div class="fkparcg2-col-lg-25 fkparcg2-float-left">
                                <input class="fkparc-valor-fator-1" type="text" name="fkparcg2_fator_1_{$idFator}" id="fkparcg2_fator_1_{$idFator}" value="{$reg}">
                            </div>
                        </div>
                    {/if}

                {/foreach}

            </div>
        </div>

    </div>

    <div class="panel fkparcg2-col-lg-60">

        <div class="panel-heading">
            {l s="Juros" mod="fkparcelamentog2"}
        </div>

        <div class="form-group">
            <label for="fkparcg2_juros_mes_1" class="control-label fkparcg2-text-left fkparcg2-col-lg-40 fkparcg2-float-left">
                {l s="Juros mensais" mod="fkparcelamentog2"}
            </label>
            <div class="fkparcg2-col-lg-10 fkparcg2-float-left">
                <input type="text" name="fkparcg2_juros_mes_1" id="fkparcg2_juros_mes_1" value="{$tab_2['fkparcg2_juros_mes_1']}">
            </div>
        </div>
        <div class="form-group">
            <label for="fkparcg2_juros_ano_1" class="control-label fkparcg2-text-left fkparcg2-col-lg-40 fkparcg2-float-left">
                {l s="Juros anuais" mod="fkparcelamentog2"}
            </label>
            <div class="fkparcg2-col-lg-10 fkparcg2-float-left">
                <input type="text" name="fkparcg2_juros_ano_1" id="fkparcg2_juros_ano_1" value="{$tab_2['fkparcg2_juros_ano_1']}">
            </div>
        </div>
        <div class="form-group">
            <div class="fkparcg2-float-left">
                <input type="checkbox" name="fkparcg2_juros_calculo_1" value="on" {if isset($tab_2['fkparcg2_juros_calculo_1']) and $tab_2['fkparcg2_juros_calculo_1'] == 'on'}checked="checked"{/if}>
            </div>
            <label class="fkparcg2-text-normal fkparcg2-col-lg-auto">
                {l s="Calcular automaticamente" mod="fkparcelamentog2"}
            </label>
        </div>

    </div>

    <div class="panel-footer">
        <button type="submit" value="1" name="btnSubmit" class="fkparcg2-button fkparcg2-float-right">
            <i class="process-icon-save"></i>
            {l s="Salvar" mod="fkparcelamentog2"}
        </button>
    </div>
</div>

</form>