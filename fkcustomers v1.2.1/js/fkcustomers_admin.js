
// Define variaveis
var validaNumero = true;
var modoOper = '';
var provedorCep = '';
var urlFuncoes = '';
var urlImg = '';

// Mascara de campos
jQuery(function() {
    $("#postcode").mask('99999-999');
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

    // Tipo de pessoa
    maskTipoPessoa();

    $(document).on('click', 'input[name=tipo]', function(e) {
        $("#cpf_cnpj").val("");
        maskTipoPessoa();
    });

    // CPF e CNPJ
    $(document).on('blur', 'input[name=cpf_cnpj]', function(e) {
        var valor = this.value;

        if ($("input[name='tipo']:checked").val() == "pf") {
            procCPF(valor, 'back');
        }else {
            procCNPJ(valor, 'back')
        }
    });

    // CEP
    $(document).on('blur', 'input[name=postcode]', function(e) {
        var cep = this.value;
        procCep(cep, modoOper, 'back');
    });

    // Numero do Endereco em Modo Compatibilidade
    $(document).on('blur', 'input[name=address1]', function(e) {
        if (modoOper == '2' && validaNumero == true) {
            var end = this.value;
            procEndereco(end);
        }
    });

});

// Mascara Tipo Pessoa
function maskTipoPessoa() {
    if ($("input[name='tipo']:checked").val() == "pf") {
        $("#cpf_cnpj").mask('999.999.999-99');
    }else {
        $("#cpf_cnpj").mask('99.999.999/9999-99');
    }
}


