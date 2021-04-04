
<div class="fkpagseguroct">

    <div class="panel">

        <div class="panel-heading">
            {l s="Informações da Configuração" mod="fkpagseguroct"}
        </div>

        <div class="fkpagseguroct-panel-header">
            <button type="button" value="1" name="btnAjuda" class="fkpagseguroct-button fkpagseguroct-float-right" onClick="window.open('http://www.modulosfk.com.br/modulosfk/ajuda/fkpagseguroct_v1_0_0.pdf','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=900,height=500,left=500,top=150'); return false;">
                <i class="process-icon-help"></i>
                {l s="Ajuda" mod="fkpagseguroct"}
            </button>
        </div>

        <div class="panel fkpagseguroct-margin-panel fkpagseguroct-col-lg-60" id="fkpagseguroct_inf_configuracao">

            <div class="panel-heading">
                {l s="PHP" mod="fkpagseguroct"}
            </div>

            <div class="row fkpagseguroct-inf-configuracao">
                <label class="control-label">
                    {l s="SOAP:" mod="fkpagseguroct"}
                </label>

                {if $tab_3['soap']}
                    <img src="{$tab_3['urlImg']}ok_24.png">
                {else}
                    <img src="{$tab_3['urlImg']}erro_24.png">
                    <span>{$tab_3['msgSoap']}</span>
                {/if}
            </div>
            <div class="row fkpagseguroct-inf-configuracao">
                <label class="control-label">
                    {l s="cURL:" mod="fkpagseguroct"}
                </label>

                {if $tab_3['curl']}
                    <img src="{$tab_3['urlImg']}ok_24.png">
                {else}
                    <img src="{$tab_3['urlImg']}erro_24.png">
                    <span>{$tab_3['msgCurl']}</span>
                {/if}
            </div>

        </div>

        <div class="panel fkpagseguroct-margin-panel fkpagseguroct-col-lg-60" id="fkpagseguroct_inf_configuracao">

            <div class="panel-heading">
                {l s="Prestashop" mod="fkpagseguroct"}
            </div>

            <div class="row fkpagseguroct-inf-configuracao">
                <label class="control-label">
                    {l s="Módulos não Nativos:" mod="fkpagseguroct"}
                </label>

                {if $tab_3['modulosNativos']}
                    <img src="{$tab_3['urlImg']}ok_24.png">
                {else}
                    <img src="{$tab_3['urlImg']}erro_24.png">
                    <span class="fkpagseguroct-color-vermelho">{$tab_3['msgModulosNativos']}</span>
                {/if}
            </div>
            <div class="row fkpagseguroct-inf-configuracao">
                <label class="control-label">
                    {l s="Overrides:" mod="fkpagseguroct"}
                </label>

                {if $tab_3['overrides']}
                    <img src="{$tab_3['urlImg']}ok_24.png">
                {else}
                    <img src="{$tab_3['urlImg']}erro_24.png">
                    <span class="fkpagseguroct-color-vermelho">{$tab_3['msgOverrides']}</span>
                {/if}
            </div>

        </div>

    </div>

</div>