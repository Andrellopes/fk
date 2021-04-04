
processar = 'sim';

$(document).ready(function(){

    if (processar == 'sim') {
        processar = 'nao';

        var valor = 0;

        if ($("#our_price_display").length ){
            valor = $('#our_price_display').text();
        }

        procParcelamento(valor);
    }

    $('#our_price_display').change(function(){
        var valor = $(this).text();
        procParcelamento(valor);
    })

});

function procParcelamento(valor) {

    // Recupera url Funcoes
    var urlFuncoes = decodeURIComponent(readCookie('fkparcg2_url_funcoes'));

    $.post(urlFuncoes, {func: '1', valor: valor}, function(retorno) {

        if (retorno.length > 1) {

            retorno = retorno.trim();

            if (retorno.substring(0,1) == '{') {

                var arRet = JSON.parse(retorno);

                // Processa Parcelamento 1
                var html = criaHtml(arRet.parcelamento1, '1');

                if (html != '') {
                    $("#fkparcg2_table_1").remove();
                    $("#fkparcg2_parcelas_1").append(html);
                    $("#fkparcg2_parcelamento_1").css("display", "block");
                }else {
                    $("#fkparcg2_msg_1").html('Parcelamento não disponível para o produto.');
                    $("#fkparcg2_msg_1").css("display", "block");
                }

                // Processa Parcelamento 2
                var html = criaHtml(arRet.parcelamento2, '2');

                if (html != '') {
                    $("#fkparcg2_table_2").remove();
                    $("#fkparcg2_parcelas_2").append(html);
                    $("#fkparcg2_parcelamento_2").css("display", "block");
                }else {
                    $("#fkparcg2_msg_2").html('Parcelamento não disponível para o produto.');
                    $("#fkparcg2_msg_2").css("display", "block");
                }
            }else {
                $("#fkparcg2_msg_2").html('Ooops! Ocorreu algum erro. Tente recarregar a página ou entre em contato com o Atendimentos ao Cliente.');
                $("#fkparcg2_msg_2").css("display", "block");
            }
        }else {
            $("#fkparcg2_msg_2").html('Ooops! Ocorreu algum erro. Tente recarregar a página ou entre em contato com o Atendimentos ao Cliente.');
            $("#fkparcg2_msg_2").css("display", "block");
        }

    });

}

function criaHtml(parcelamento, id) {

    var parcelas = parcelamento.length;
    var html = '';

    if (parcelas > 1) {

        html += '<table id="fkparcg2_table_' + id + '">';

        for (i = 0; i < parcelas; i=i+2){
            html += '<tr class="fkparcg2-tr">';
            html += '<td class="fkparcg2-td fkparcg2-td-parcela">' + parcelamento[i].parcela + '</td>';
            html += '<td class="fkparcg2-td fkparcg2-td-x">x</td>';
            html += '<td class="fkparcg2-td fkparcg2-td-valor">' + parcelamento[i].valor + '</td>';

            if ((i+1) < parcelas) {
                html += '<td class="fkparcg2-td fkparcg2-td-sep"></td>';
                html += '<td class="fkparcg2-td fkparcg2-td-parcela">' + parcelamento[i+1].parcela + '</td>';
                html += '<td class="fkparcg2-td fkparcg2-td-x">x</td>';
                html += '<td class="fkparcg2-td fkparcg2-td-valor">' + parcelamento[i+1].valor + '</td>';
            }

            html += '</tr>'
        }

        html += '</table>'
    }

    return html;
}