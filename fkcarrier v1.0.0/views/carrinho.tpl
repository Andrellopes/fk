
{if $fkcarrier_foco == true}
    <a name="fkcarrier-foco"></a>
    <a href="#fkcarrier-foco" id="fkcarrier-foco"></a>
    
    <script type="text/javascript">
        $(document).ready(function(){
            document.getElementById("fkcarrier-foco").click();
        });
    </script>
{/if}

<div class="fkcarrier-box">

    <div class="fkcarrier-legenda">
    
        <p>{l s='Informe o CEP para cálculo do frete do produto' mod='fkcarrier'}</p>
    
        <div class="fkcarrier-form">
            <form action="#" method="post">
                <input type="text" class="fkcarrier_cep_mask" size="10" name="fkcarrier_cep_carrinho" value="{$fkcarrier_cep}"/>
                <input type="submit" class="fkcarrier-button" value="{l s='Calcular frete' mod='fkcarrier'}" name="submitCarrinho"/>
            </form>
        </div>

    </div>
    
    <div class="fkcarrier-transportadoras">
    
        <p>{$fkcarrier_msg}</p>
    
        {if isset($fkcarrier_frete)}
            <table>
                {foreach $fkcarrier_frete as $frete}
                    <tr>
                        <td id="fkcarrier-img">
                            <img src="{$frete['url_imagem']}" alt="{$frete['nome_carrier']}"/>
                        </td>
                        <td id="fkcarrier-nome">
                            <b>{$frete['nome_carrier']}</b>
                            <br>
                            {l s='Entrega:' mod='fkcarrier'} {$frete['prazo_entrega']}
                        </td>
                        <td id="fkcarrier-valor">
                            {convertPrice price=$frete['valor_frete']}
                        </td>
                    </tr>
                {/foreach}
            </table>
        {/if}
    
    </div>
    
</div>
