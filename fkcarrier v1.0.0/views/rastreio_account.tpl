
<div class="fkcarrier-rastreio-account">
    <div class="fkcarrier-rastreio-titulo">
        {l s='Rastreio Encomenda' mod='fkcarrier'}
    </div>

    <div class="fkcarrier-rastreio-conteudo">

        <input type="text" name="fkcarrier_rastreio_account" id="fkcarrier_rastreio_account" placeholder="{l s="Informe o código de rastreio" mod="fkcarrier"}" value="">
        <button class="fkcarrier-button-rastreio" type="button" onclick="mostraRastreio($('#fkcarrier_rastreio_account').val())">
            <span>Ok</span>
        </button>

    </div>
</div>