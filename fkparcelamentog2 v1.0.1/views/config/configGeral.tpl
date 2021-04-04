<form id="configuration_form" class="defaultForm  form-horizontal" action="{$formAction}&origem=configGeral" method="post">

    <div class="panel" style="border-top-left-radius: 0">

        <div class="panel-heading">
            {l s="Configuração geral" mod="fkparcelamentog2"}
        </div>

        <div class="fkparcg2-panel-header">
            <button type="button" value="1" name="btnAjuda" class="fkparcg2-button fkparcg2-float-right" onClick="window.open('http://www.modulosfk.com.br/modulosfk/ajuda/fkparcelamentog2_v1_0_0.pdf','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=900,height=500,left=500,top=150'); return false;">
                <i class="process-icon-help"></i>
                {l s="Ajuda" mod="fkparcelamentog2"}
            </button>
        </div>

        <div class="panel fkparcg2-margin-top_40 fkparcg2-col-lg-60">

            <div class="panel-heading">
                {l s="Bloco Simulador Parcelamento" mod="fkparcelamentog2"}
            </div>

            <div class="panel fkparcg2-sub-panel fkparcg2-col-lg-60">

                <div class="panel-heading">
                    {l s="Mostrar" mod="fkparcelamentog2"}
                </div>

                <div class="form-group">
                    <div class="fkparcg2-float-left">
                        <input type="checkbox" name="fkparcg2_bloco_produto" value="on" {if isset($tab_1['fkparcg2_bloco_produto']) and $tab_1['fkparcg2_bloco_produto'] == 'on'}checked="checked"{/if}>
                    </div>
                    <label class="fkparcg2-text-normal fkparcg2-col-lg-auto">
                        {l s="Detalhes do produto" mod="fkparcelamentog2"}
                    </label>
                </div>
                <div class="form-group">
                    <div class="fkparcg2-float-left">
                        <input type="checkbox" name="fkparcg2_bloco_carrinho" value="on" {if isset($tab_1['fkparcg2_bloco_carrinho']) and $tab_1['fkparcg2_bloco_carrinho'] == 'on'}checked="checked"{/if}>
                    </div>
                    <label class="fkparcg2-text-normal fkparcg2-col-lg-auto">
                        {l s="Carrinho de compras" mod="fkparcelamentog2"}
                    </label>
                </div>

            </div>

            <div class="panel fkparcg2-sub-panel fkparcg2-col-lg-60">

                <div class="panel-heading">
                    {l s="Tema" mod="fkparcelamentog2"}
                </div>

                <div class="form-group">
                    <div class="fkparcg2-col-lg-20 fkparcg2-float-left">
                        <input type="text" name="fkparcg2_cor_fundo_sem_juros" id="fkparcg2_cor_fundo_sem_juros" value="{$tab_1['fkparcg2_cor_fundo_sem_juros']}">
                    </div>
                    <label class="fkparcg2-text-normal fkparcg2-col-lg-auto">
                        {l s="Cor de Fundo Parcelas Sem Juros" mod="fkparcelamentog2"}
                    </label>
                </div>
                <div class="form-group">
                    <div class="fkparcg2-col-lg-20 fkparcg2-float-left">
                        <input type="text" name="fkparcg2_cor_fonte_sem_juros" id="fkparcg2_cor_fonte_sem_juros" value="{$tab_1['fkparcg2_cor_fonte_sem_juros']}">
                    </div>
                    <label class="fkparcg2-text-normal fkparcg2-col-lg-auto">
                        {l s="Cor da Fonte Parcelas Sem Juros" mod="fkparcelamentog2"}
                    </label>
                </div>

                <div class="panel fkparcg2-sub-panel fkparcg2-col-lg-80">

                    <div class="panel-heading">
                        {l s="Carrinho" mod="fkparcelamentog2"}
                    </div>

                    <div class="form-group">
                        <div class="fkparcg2-col-lg-20 fkparcg2-float-left">
                            <input type="text" name="fkparcg2_largura_carrinho" id="fkparcg2_largura_carrinho" value="{$tab_1['fkparcg2_largura_carrinho']}">
                        </div>
                        <label class="fkparcg2-text-normal fkparcg2-col-lg-auto">
                            {l s="Largura" mod="fkparcelamentog2"}
                        </label>
                    </div>

                </div>

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
