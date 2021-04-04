
jQuery(function() {
    $(".fkcorreiosg2cp4-mask-cnpj").mask('99.999.999/9999-99');
    $('.fkcorreiosg2cp4-mask-cep').mask('99999-999');
});

function fkcorreiosg2cp4Toggle(id) {
    $('#' + id).toggle('slow','linear');
}

function fkcorreiosg2cp4Excluir(msg) {

    if (confirm(msg)) {
        return true;
    }

    return false;
}

function fkcorreiosg2cp4Marcar(idClass) {
    $('.' + idClass).each(
        function(){
            $(this).attr('checked', true);
        }
    );
}

function fkcorreiosg2cp4Desmarcar(idClass) {
    $('.' + idClass).each(
        function(){
            $(this).attr('checked', false);
        }
    );
}

function fkcorreiosg2cp4IncluirCepRegiao(id) {

    var campo = '';

    campo = $('#fkcorreiosg2cp4_regiao_cep_' + id).val();
    campo += $('#fkcorreiosg2cp4_regiao_cep1_' + id).val().replace(/[^0-9]/g,'');
    campo += ':';
    campo += $('#fkcorreiosg2cp4_regiao_cep2_' + id).val().replace(/[^0-9]/g,'');
    campo += '/';

    $('#fkcorreiosg2cp4_regiao_cep_' + id).val(campo);
    $('#fkcorreiosg2cp4_regiao_cep1_' + id).val('');
    $('#fkcorreiosg2cp4_regiao_cep2_' + id).val('');
}

function fkcorreiosg2cp4IncluirCepRegiaoExcluido(id) {

    var campo = '';

    campo = $('#fkcorreiosg2cp4_regiao_cep_excluido_' + id).val();
    campo += $('#fkcorreiosg2cp4_regiao_cep1_excluido_' + id).val().replace(/[^0-9]/g,'');
    campo += ':';
    campo += $('#fkcorreiosg2cp4_regiao_cep2_excluido_' + id).val().replace(/[^0-9]/g,'');
    campo += '/';

    $('#fkcorreiosg2cp4_regiao_cep_excluido_' + id).val(campo);
    $('#fkcorreiosg2cp4_regiao_cep1_excluido_' + id).val('');
    $('#fkcorreiosg2cp4_regiao_cep2_excluido_' + id).val('');
}

function fkcorreiosg2cp4ExcluirConf(msg, id) {

    if ($("#" + id).is(":checked")) {

        if (confirm(msg)) {
            return true;
        }

        $("#" + id).attr("checked", false);
        return false;
    }

    return true;

};
