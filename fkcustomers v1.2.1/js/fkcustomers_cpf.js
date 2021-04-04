
function procCPF(cpf, local) {

    var soma;
    var resto;
    var i;

    cpf = cpf.replace(/[^0-9]/g,'');

    if (cpf.length == 0) {
        return true;
    }

    if ((cpf.length != 11) || (cpf == "00000000000") || (cpf == "11111111111")
        || (cpf == "22222222222") || (cpf == "33333333333")
        || (cpf == "44444444444") || (cpf == "55555555555")
        || (cpf == "66666666666") || (cpf == "77777777777")
        || (cpf == "88888888888") || (cpf == "99999999999")) {

        // CPF invalido
        msgCPF(local);

        var interval = window.setTimeout(function(){
            $.fancybox.close();
        }, 1500);

        return false;
    }

    soma = 0;

    for ( i = 1; i <= 9; i++) {
        soma += Math.floor(cpf.charAt(i - 1)) * (11 - i);
    }

    resto = 11 - (soma - (Math.floor(soma / 11) * 11));

    if ((resto == 10) || (resto == 11)) {
        resto = 0;
    }

    if (resto != Math.floor(cpf.charAt(9))) {

        // CPF invalido
        msgCPF(local);

        var interval = window.setTimeout(function(){
            $.fancybox.close();
        }, 1500);

        return false;
    }

    soma = 0;

    for ( i = 1; i <= 10; i++) {
        soma += cpf.charAt(i - 1) * (12 - i);
    }

    resto = 11 - (soma - (Math.floor(soma / 11) * 11));

    if ((resto == 10) || (resto == 11)) {
        resto = 0;
    }

    if (resto != Math.floor(cpf.charAt(10))) {

        // CPF invalido
        msgCPF(local);

        var interval = window.setTimeout(function(){
            $.fancybox.close();
        }, 1500);

        return false;
    }

    return true;

}

function msgCPF(local) {

    var html = '';
    var largura = '0';
    var janela_modal = true;

    html =  '<div class="fkcustomers-fancybox-fechar">';
    html += '<p><img src="' + urlImg + 'erro_48.png" alt="" width="48" height="48" /></p>';
    html += '<p class="fkcustomers-fancybox-msg fkcustomers-color-vermelho">CPF Inválido</p>';
    html += '</div>';
    largura = 200;

    $.fancybox.open([{
            type: 'inline',
            modal: janela_modal,
            minHeight: 30,
            autoSize: false,
            autoHeight: true,
            width: largura,
            content: html,

            helpers:  {
                overlay : {
                    closeClick: false,
                    lock: true
                }
            },

            afterShow: function() {
                $(".fkcustomers-fancybox-fechar").click(function(){
                    $.fancybox.close();
                });
            },

            afterClose: function() {
                $('#cpf_cnpj').val('');

                if (local == 'front') {
                    $('#fkcustomers_cpf').val('');
                    $('#fkcustomers_cpf').focus();
                }else {
                    $('#cpf_cnpj').focus();
                }
            }
        }]

    );

}
