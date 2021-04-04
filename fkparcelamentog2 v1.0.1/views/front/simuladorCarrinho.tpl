
<div id="fkparcg2_carrinho" style="width: {$fkparcg2_largura_carrinho}">
    <div class="fkparcg2-titulo">
        {l s='Parcelamento' mod='fkparcelamentog2'}
    </div>

    <ul id="tabs" class="fkparcg2nav fkparcg2nav-tabs" data-tabs="tabs">
        <li class="active"><a href="#tab_1" data-toggle="tab">{$fkparcg2_titulo_1}</a></li>

        {if $fkparcg2_ativo_2 == 'on'}
            <li><a href="#tab_2" data-toggle="tab">{$fkparcg2_titulo_2}</a></li>
        {/if}
    </ul>
    <div class="fkparcg2tab-content">
        <div class="fkparcg2tab-pane active" id="tab_1">
            <div class="fkparcg2tab-panel">

                {if $fkparcg2_total_parcelas_1 == 0}
                    <div>
                        Parcelamento não disponível para o pedido
                    </div>
                {else}
                    {if $fkparcg2_sem_juros_1 > 1}
                        <div id="fkparcg2_sem_juros_1" style="color: {$fkparcg2_cor_fonte_sem_juros}; background-color: {$fkparcg2_cor_fundo_sem_juros}">
                            {l s='Pague em até ' mod='fkparcelamentog2'}{$fkparcg2_sem_juros_1}{l s='x sem juros' mod='fkparcelamentog2'}
                        </div>
                    {/if}

                    <div class="fkparcg2-parcelas">
                        <div id="fkparcg2_parcelas_1">
                            <table>
                                {for $i=0 to $fkparcg2_total_parcelas_1 step 2}

                                    <tr class="fkparcg2-tr">
                                        <td class="fkparcg2-td fkparcg2-td-parcela">{$fkparcg2_parcelas_1[$i]['parcela']}</td>
                                        <td class="fkparcg2-td fkparcg2-td-x">x</td>
                                        <td class="fkparcg2-td fkparcg2-td-valor">{$fkparcg2_parcelas_1[$i]['valor']}</td>

                                        {if $i+1 <= $fkparcg2_total_parcelas_1}
                                            <td class="fkparcg2-td fkparcg2-td-sep"></td>
                                            <td class="fkparcg2-td fkparcg2-td-parcela">{$fkparcg2_parcelas_1[$i+1]['parcela']}</td>
                                            <td class="fkparcg2-td fkparcg2-td-x">x</td>
                                            <td class="fkparcg2-td fkparcg2-td-valor">{$fkparcg2_parcelas_1[$i+1]['valor']}</td>
                                        {/if}
                                    </tr>

                                {/for}
                            </table>
                        </div>
                    </div>

                    <div id="fkparcg2_texto_1">
                        {$fkparcg2_texto_1}
                    </div>

                    {if $fkparcg2_juros_mes_1 > 0 and $fkparcg2_juros_ano_1 > 0}
                        <div id="fkparcg2_juros_1">
                            Nas parcelas com juros é cobrada uma taxa de {$fkparcg2_juros_mes_1}% ao mês e {$fkparcg2_juros_ano_1}% ao ano
                        </div>
                    {/if}
                {/if}

            </div>
        </div>

        {if $fkparcg2_ativo_2 == 'on'}
            <div class="fkparcg2tab-pane" id="tab_2">
                <div class="fkparcg2tab-panel">

                    {if $fkparcg2_total_parcelas_2 == 0}
                        <div>
                            Parcelamento não disponível para o pedido
                        </div>
                    {else}
                        {if $fkparcg2_sem_juros_2 > 1}
                            <div id="fkparcg2_sem_juros_2" style="color: {$fkparcg2_cor_fonte_sem_juros}; background-color: {$fkparcg2_cor_fundo_sem_juros}">
                                {l s='Pague em até ' mod='fkparcelamentog2'}{$fkparcg2_sem_juros_2}{l s='x sem juros' mod='fkparcelamentog2'}
                            </div>
                        {/if}

                        <div class="fkparcg2-parcelas">
                            <div id="fkparcg2_parcelas_1">
                                <table>
                                    {for $i=0 to $fkparcg2_total_parcelas_2 step 2}

                                        <tr class="fkparcg2-tr">
                                            <td class="fkparcg2-td fkparcg2-td-parcela">{$fkparcg2_parcelas_2[$i]['parcela']}</td>
                                            <td class="fkparcg2-td fkparcg2-td-x">x</td>
                                            <td class="fkparcg2-td fkparcg2-td-valor">{$fkparcg2_parcelas_2[$i]['valor']}</td>

                                            {if $i+1 <= $fkparcg2_total_parcelas_2}
                                                <td class="fkparcg2-td fkparcg2-td-sep"></td>
                                                <td class="fkparcg2-td fkparcg2-td-parcela">{$fkparcg2_parcelas_2[$i+1]['parcela']}</td>
                                                <td class="fkparcg2-td fkparcg2-td-x">x</td>
                                                <td class="fkparcg2-td fkparcg2-td-valor">{$fkparcg2_parcelas_2[$i+1]['valor']}</td>
                                            {/if}
                                        </tr>

                                    {/for}
                                </table>
                            </div>
                        </div>

                        <div id="fkparcg2_texto_2">
                            {$fkparcg2_texto_2}
                        </div>

                        {if $fkparcg2_juros_mes_2 > 0 and $fkparcg2_juros_ano_2 > 0}
                            <div id="fkparcg2_juros_2">
                                Nas parcelas com juros é cobrada uma taxa de {$fkparcg2_juros_mes_2}% ao mês e {$fkparcg2_juros_ano_2}% ao ano
                            </div>
                        {/if}
                    {/if}

                </div>
            </div>
        {/if}
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#tabs').tab();
    });
</script>