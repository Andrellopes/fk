
<div class="fkcustomers-panel">

    <div class="fkcustomers-panel-heading">
        {l s="Informações da Configuração" mod="fkcustomers"}
    </div>

    <div class="fkcustomers-panel-header">
        <button type="button" value="1" name="btnAjuda" class="fkcustomers-button fkcustomers-float-right" onClick="window.open('http://www.fkmodulos.com.br/modulosfk/ajuda/fkcustomers_v1_0_0.pdf','Janela','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=900,height=500,left=500,top=150'); return false;">
            <i class="process-icon-help"></i>
            {l s="Ajuda" mod="fkcustomers"}
        </button>
    </div>

    <div class="fkcustomers-panel fkcustomers-col-lg-60 fkcustomers-sub-panel" id="fkcustomers_inf_configuracao">

        <div class="fkcustomers-panel-heading">
            {l s="PHP" mod="fkcustomers"}
        </div>

        <div class="row fkcustomers-inf-configuracao">
            <label class="fkcustomers-label">
                {l s="SOAP:" mod="fkcustomers"}
            </label>

            {if $tab_3['soap']}
                <img src="{$tab_3['urlImg']}ok_24.png">
            {else}
                <img src="{$tab_3['urlImg']}erro_24.png">
                <span class="fkcustomers-color-vermelho">{$tab_3['msgSoap']}</span>
            {/if}
        </div>
        <div class="row fkcustomers-inf-configuracao">
            <label class="fkcustomers-label">
                {l s="fopen:" mod="fkcustomers"}
            </label>

            {if $tab_3['fopen']}
                <img src="{$tab_3['urlImg']}ok_24.png">
            {else}
                <img src="{$tab_3['urlImg']}erro_24.png">
                <span class="fkcustomers-color-vermelho">{$tab_3['msgFopen']}</span>
            {/if}
        </div>
        <div class="row fkcustomers-inf-configuracao">
            <label class="fkcustomers-label">
                {l s="OUTPUT_BUFFERING:" mod="fkcustomers"}
            </label>

            {if $tab_3['outBuffering']}
                <img src="{$tab_3['urlImg']}ok_24.png">
            {else}
                <img src="{$tab_3['urlImg']}erro_24.png">
                <span class="fkcustomers-color-vermelho">{$tab_3['msgOutBuffering']}</span>
            {/if}
        </div>
    </div>

    <div class="fkcustomers-panel fkcustomers-col-lg-60 fkcustomers-sub-panel">

        <div class="fkcustomers-panel-heading">
            {l s="Prestashop" mod="fkcustomers"}
        </div>

        <div class="row fkcustomers-inf-configuracao">
            <label class="fkcustomers-label">
                {l s="Módulos não Nativos:" mod="fkcustomers"}
            </label>

            {if $tab_3['modulosNativos']}
                <img src="{$tab_3['urlImg']}ok_24.png">
            {else}
                <img src="{$tab_3['urlImg']}erro_24.png">
                <span class="fkcustomers-color-vermelho">{$tab_3['msgModulosNativos']}</span>
            {/if}
        </div>
        <div class="row fkcustomers-inf-configuracao">
            <label class="fkcustomers-label">
                {l s="Overrides:" mod="fkcustomers"}
            </label>

            {if $tab_3['overrides']}
                <img src="{$tab_3['urlImg']}ok_24.png">
            {else}
                <img src="{$tab_3['urlImg']}erro_24.png">
                <span class="fkcustomers-color-vermelho">{$tab_3['msgOverrides']}</span>
            {/if}
        </div>

    </div>

</div>