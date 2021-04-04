
jQuery(function() {
    $(".fkcorreiosg2cp3-mask-cnpj").mask('99.999.999/9999-99');
});

function fkcorreiosg2cp3Toggle(id) {
    $('#' + id).toggle('slow','linear');
}

function fkcorreiosg2cp3Excluir(msg) {

    if (confirm(msg)) {
        return true;
    }

    return false;
}

function fkcorreiosg2cp3Marcar(idClass) {
    $('.' + idClass).each(
        function(){
            $(this).attr('checked', true);
        }
    );
}

function fkcorreiosg2cp3Desmarcar(idClass) {
    $('.' + idClass).each(
        function(){
            $(this).attr('checked', false);
        }
    );
}

function fkcorreiosg2cp3IncluirCepRegiao(id) {

    var campo = '';

    campo = $('#fkcorreiosg2cp3_regiao_cep_' + id).val();
    campo += $('#fkcorreiosg2cp3_regiao_cep1_' + id).val().replace(/[^0-9]/g,'');
    campo += ':';
    campo += $('#fkcorreiosg2cp3_regiao_cep2_' + id).val().replace(/[^0-9]/g,'');
    campo += '/';

    $('#fkcorreiosg2cp3_regiao_cep_' + id).val(campo);
    $('#fkcorreiosg2cp3_regiao_cep1_' + id).val('');
    $('#fkcorreiosg2cp3_regiao_cep2_' + id).val('');
    $('#fkcorreiosg2cp3_regiao_cep1_' + id).focus();
}

function fkcorreiosg2cp3IncluirCepRegiaoExcluido(id) {

    var campo = '';

    campo = $('#fkcorreiosg2cp3_regiao_cep_excluido_' + id).val();
    campo += $('#fkcorreiosg2cp3_regiao_cep1_excluido_' + id).val().replace(/[^0-9]/g,'');
    campo += ':';
    campo += $('#fkcorreiosg2cp3_regiao_cep2_excluido_' + id).val().replace(/[^0-9]/g,'');
    campo += '/';

    $('#fkcorreiosg2cp3_regiao_cep_excluido_' + id).val(campo);
    $('#fkcorreiosg2cp3_regiao_cep1_excluido_' + id).val('');
    $('#fkcorreiosg2cp3_regiao_cep2_excluido_' + id).val('');
    $('#fkcorreiosg2cp3_regiao_cep1_excluido_' + id).focus();
}

function fkcorreiosg2cp3ExcluirConf(msg, id) {

    if ($("#" + id).is(":checked")) {

        if (confirm(msg)) {
            return true;
        }

        $("#" + id).attr("checked", false);
        return false;
    }

    return true;

};
