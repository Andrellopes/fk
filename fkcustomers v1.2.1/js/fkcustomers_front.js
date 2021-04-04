
// Define variaveis
var validaNumero = true;
var modoOper = '';
var provedorCep = '';
var urlFuncoes = '';
var urlImg = '';

// Mascara de campos
jQuery(function() {
    $("#fkcustomers_cnpj").mask('99.999.999/9999-99');
    $("#fkcustomers_cpf").mask('999.999.999-99');
    $("#postcode_fk").mask('99999-999');
    $("#phone").mask('(99) 9999-9999');

    $('#phone_mobile').focusout(function(){
        var phone, element;
        element = $(this);
        element.unmask();
        phone = element.val().replace(/\D/g, '');
        if(phone.length > 10) {
            element.mask("(99) 99999-999?9");
        } else {
            element.mask("(99) 9999-9999?9");
        }
    }).trigger('focusout');
});

// Monitoramento automatico de acoes
$(document).ready(function(){

    // Grava valores nas variaveis recuperados dos cookies
    modoOper = readCookie('fkcustomes_modo_oper');
    provedorCep = readCookie('fkcustomes_provedor_cep');
    urlFuncoes = decodeURIComponent(readCookie('fkcustomes_url_funcoes'));
    urlImg = decodeURIComponent(readCookie('fkcustomes_url_img'));

    // CPF
    $(document).on('keyup', 'input[name=fkcustomers_cpf]', function(e) {
        $("#cpf_cnpj").val($(this).val());
    });

    $(document).on('blur', 'input[name=fkcustomers_cpf]', function(e) {
        var cpf = this.value;
        procCPF(cpf, 'front')
    });

    // CNPJ
    $(document).on('keyup', 'input[name=fkcustomers_cnpj]', function(e) {
        $("#cpf_cnpj").val($(this).val());
    });

    $(document).on('blur', 'input[name=fkcustomers_cnpj]', function(e) {
        var cnpj = this.value;
        procCNPJ(cnpj, 'front')
    });

    // RG
    $(document).on('keyup', 'input[name=fkcustomers_rg]', function(e) {
        $("#rg_ie").val($(this).val());
    });

    // IE
    $(document).on('keyup', 'input[name=fkcustomers_ie]', function(e) {
        $("#rg_ie").val($(this).val());
    });

    // CEP quando em Modo Completo
    $(document).on('keyup', 'input[name=postcode_fk]', function(e) {
        $("#postcode").val($(this).val());
    });

    $(document).on('blur', 'input[name=postcode_fk]', function(e) {
        var cep = this.value;
        procCep(cep, modoOper, 'front');
    });

    // CEP quando em Modo Compatibilidade
    $(document).on('blur', 'input[name=postcode]', function(e) {
        var cep = this.value;
        procCep(cep, modoOper, 'front');
    });

    // Numero do Endereco em Modo Compatibilidade
    $(document).on('blur', 'input[name=address1]', function(e) {
        if (modoOper == '2' && validaNumero == true) {
            var end = this.value;
            procEndereco(end);
        }
    });

});

function procRadioTipo(id) {

    $("#cpf_cnpj").val('');
    $("#rg_ie").val('');
    $("#fkcustomers_cpf").val('');
    $("#fkcustomers_cnpj").val('');
    $("#fkcustomers_rg").val('');
    $("#fkcustomers_ie").val('');

    if (id.value == "pf") {
        $("#fkcustomers_pf").css("display", "block");
        $("#fkcustomers_pj").css("display", "none");

        if ($("#fkcustomers_company").size() > 0) {
            $("#fkcustomers_company").css("display", "none");
        }
    }else {
        $("#fkcustomers_pf").css("display", "none");
        $("#fkcustomers_pj").css("display", "block");

        if ($("#fkcustomers_company").size() > 0) {
            $("#fkcustomers_company").css("display", "block");
        }
    }

}
