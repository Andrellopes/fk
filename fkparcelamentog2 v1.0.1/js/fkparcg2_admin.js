
// Monitoramento automatico de acoes
$(document).ready(function(){

    // Alteração no campo Total de Parcelas
    $(document).on('blur', 'input[name=fkparcg2_parcelas_1]', function(e) {

        // Recupera Total de Parcelas e Parcelas sem Juros
        var totalParcelas = this.value;
        var parcSemJuros = $('#fkparcg2_sem_juros_1').val();

        if (parcSemJuros > totalParcelas) {
            alert('Parcelas sem Juros não pode ser maior que o Total de Parcelas');
            return false;
        }

        // Verifica se os campos sao numericos
        if (!isNaN(totalParcelas) && !isNaN(parcSemJuros)) {
            criaCamposFatores(parseInt(totalParcelas), parseInt(parcSemJuros), '1')
        }

    });

    $(document).on('blur', 'input[name=fkparcg2_parcelas_2]', function(e) {

        // Recupera Total de Parcelas e Parcelas sem Juros
        var totalParcelas = this.value;
        var parcSemJuros = $('#fkparcg2_sem_juros_2').val();

        // Verifica se os campos sao numericos
        if (!isNaN(totalParcelas) && !isNaN(parcSemJuros)) {
            criaCamposFatores(parseInt(totalParcelas), parseInt(parcSemJuros), '2')
        }

    });

    // Alteração no campo Parcelas sem Juros
    $(document).on('blur', 'input[name=fkparcg2_sem_juros_1]', function(e) {

        // Recupera Total de Parcelas e Parcelas sem Juros
        var totalParcelas = $('#fkparcg2_parcelas_1').val();
        var parcSemJuros = this.value;

        // Verifica se os campos sao numericos
        if (!isNaN(totalParcelas) && !isNaN(parcSemJuros)) {
            criaCamposFatores(parseInt(totalParcelas), parseInt(parcSemJuros), '1')
        }

    });

    $(document).on('blur', 'input[name=fkparcg2_sem_juros_2]', function(e) {

        // Recupera Total de Parcelas e Parcelas sem Juros
        var totalParcelas = $('#fkparcg2_parcelas_2').val();
        var parcSemJuros = this.value;

        // Verifica se os campos sao numericos
        if (!isNaN(totalParcelas) && !isNaN(parcSemJuros)) {
            criaCamposFatores(parseInt(totalParcelas), parseInt(parcSemJuros), '2')
        }

    });

});

function recuperaFatores(id) {

    var fatores = [];

    $('.fkparc-valor-fator-' + id).each(
        function(){
            fatores.push($(this).val());
        }
    );

    return fatores;
};

function criaCamposFatores(totalParcelas, parcSemJuros, id) {

    var fatoresGravados = recuperaFatores(id);
    var fator = '0.00';

    // Monta html
    var html = '<div id="fkparcg2_itens_fatores_' + id + '">';

    for (i = 1; i <= totalParcelas; i++){

        if (i <= parcSemJuros) {
            fator = 1 / i;
            fator = fator.toFixed(16);
        }else {
            if (fatoresGravados.length >= i) {
                fator = fatoresGravados[i-1];
            }else {
                fator = '0.00';
            }
        }

        html += '<div class="form-group">';
        html += '   <label for="fkparcg2_fator_' + id + '_' + i +'" class="control-label fkparcg2-text-left fkparcg2-col-lg-40 fkparcg2-float-left">';
        html += '       Parcela ' + i;
        html += '   </label>';
        html += '   <div class="fkparcg2-col-lg-25 fkparcg2-float-left">';
        html += '       <input class="fkparc-valor-fator-' + id + '" type="text" name="fkparcg2_fator_' + id + '_' + i + '" id="fkparcg2_fator_' + id + '_' + i + '" value="' + fator + '">';
        html += '   </div>'
        html += '</div>'

    }

    html += '</div>';

    $("#fkparcg2_itens_fatores_" + id).remove();
    $("#fkparcg2_fatores_" + id).append(html);

}