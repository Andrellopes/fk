
jQuery(function() {
    $('.fkcorreiosg2cp1-mask-cep').mask('99999-999');
});

function fkcorreiosg2cp1Toggle(id) {
    $('#' + id).toggle('slow','linear');
}

function fkcorreiosg2cp1Excluir(msg) {

    if (confirm(msg)) {
        return true;
    }

    return false;
}

function fkcorreiosg2cp1ExcluirConf(msg, id) {

    if ($("#" + id).is(":checked")) {

        if (confirm(msg)) {
            return true;
        }

        $("#" + id).attr("checked", false);
        return false;
    }

    return true;

};

function fkcorreiosg2cp1MostraFormula(idCampo, idRegra) {

    if (idCampo.value == "1") {
        $("#fkcorreiosg2cp1_formula_" + idRegra).css("display", "none");
    }else {
        $("#fkcorreiosg2cp1_formula_" + idRegra).css("display", "block");
    }

}

function fkcorreiosg2cp1Marcar(idClass) {
    $('.' + idClass).each(
        function(){
            $(this).attr('checked', true);
        }
    );
}

function fkcorreiosg2cp1Desmarcar(idClass) {
    $('.' + idClass).each(
        function(){
            $(this).attr('checked', false);
        }
    );
}

function fkcorreiosg2cp1IncluirCepRegiao(id) {

    var campo = '';

    campo = $('#fkcorreiosg2cp1_regiao_cep_' + id).val();
    campo += $('#fkcorreiosg2cp1_regiao_cep1_' + id).val().replace(/[^0-9]/g,'');
    campo += ':';
    campo += $('#fkcorreiosg2cp1_regiao_cep2_' + id).val().replace(/[^0-9]/g,'');
    campo += '/';

    $('#fkcorreiosg2cp1_regiao_cep_' + id).val(campo);
    $('#fkcorreiosg2cp1_regiao_cep1_' + id).val('');
    $('#fkcorreiosg2cp1_regiao_cep2_' + id).val('');
    $('#fkcorreiosg2cp1_regiao_cep1_' + id).focus();
}

function fkcorreiosg2cp1IncluirCepRegiaoExcluido(id) {

    var campo = '';

    campo = $('#fkcorreiosg2cp1_regiao_cep_excluido_' + id).val();
    campo += $('#fkcorreiosg2cp1_regiao_cep1_excluido_' + id).val().replace(/[^0-9]/g,'');
    campo += ':';
    campo += $('#fkcorreiosg2cp1_regiao_cep2_excluido_' + id).val().replace(/[^0-9]/g,'');
    campo += '/';

    $('#fkcorreiosg2cp1_regiao_cep_excluido_' + id).val(campo);
    $('#fkcorreiosg2cp1_regiao_cep1_excluido_' + id).val('');
    $('#fkcorreiosg2cp1_regiao_cep2_excluido_' + id).val('');
    $('#fkcorreiosg2cp1_regiao_cep1_excluido_' + id).focus();
}

function fkcorreiosg2cp1IncluirTabelaPreco(id) {

    var campo = '';
    var peso = $('#fkcorreiosg2cp1_regiao_tabela1_' + id).val();
    var valor = $('#fkcorreiosg2cp1_regiao_tabela2_' + id).val();

    campo = $('#fkcorreiosg2cp1_regiao_tabela_preco_' + id).val();
    campo += peso;
    campo += ':';
    campo += valor;
    campo += '/';

    $('#fkcorreiosg2cp1_regiao_tabela_preco_' + id).val(campo);
    $('#fkcorreiosg2cp1_regiao_tabela1_' + id).val('');
    $('#fkcorreiosg2cp1_regiao_tabela2_' + id).val('');
    $('#fkcorreiosg2cp1_regiao_tabela1_' + id).focus();
}