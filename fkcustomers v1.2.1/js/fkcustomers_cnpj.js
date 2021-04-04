
function procCNPJ(cnpj, local) {

    var i = 0;
    var strMul = "6543298765432";
    var iLenMul = 0;
    var iSoma = 0;
    var strNum_base = 0;
    var iLenNum_base = 0;

    cnpj = cnpj.replace(/[^0-9]/g,'');

    if (cnpj.length == 0) {
        return true;
    }

    if (cnpj.length != 14 || cnpj == "00000000000000") {
        // CNPJ invalido
        msgCNPJ(local);

        var interval = window.setTimeout(function(){
            $.fancybox.close();
        }, 1500);

        return false;
    }

    strNum_base = cnpj.substring(0, 12);
    iLenNum_base = strNum_base.length - 1;
    iLenMul = strMul.length - 1;

    for ( i = 0; i < 12; i++)
        iSoma = iSoma + parseInt(strNum_base.substring((iLenNum_base - i), (iLenNum_base - i) + 1), 10) * parseInt(strMul.substring((iLenMul - i), (iLenMul - i) + 1), 10);

    iSoma = 11 - (iSoma - Math.floor(iSoma / 11) * 11);

    if (iSoma == 11 || iSoma == 10)
        iSoma = 0;

    strNum_base = strNum_base + iSoma;
    iSoma = 0;
    iLenNum_base = strNum_base.length - 1;

    for ( i = 0; i < 13; i++)
        iSoma = iSoma + parseInt(strNum_base.substring((iLenNum_base - i), (iLenNum_base - i) + 1), 10) * parseInt(strMul.substring((iLenMul - i), (iLenMul - i) + 1), 10);

    iSoma = 11 - (iSoma - Math.floor(iSoma / 11) * 11);

    if (iSoma == 11 || iSoma == 10)
        iSoma = 0;

    strNum_base = strNum_base + iSoma;

    if (cnpj != strNum_base) {

        // CNPJ invalido
        msgCNPJ(local);

        var interval = window.setTimeout(function(){
            $.fancybox.close();
        }, 1500);

        return false;
    }

    return true;

}

function msgCNPJ(local) {

    var html = '';
    var largura = '0';
    var janela_modal = true;

    html =  '<div class="fkcustomers-fancybox-fechar">';
    html += '<p><img src="' + urlImg + 'erro_48.png" alt="" width="48" height="48" /></p>';
    html += '<p class="fkcustomers-fancybox-msg fkcustomers-color-vermelho">CNPJ Inválido</p>';
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
                    $('#fkcustomers_cnpj').val('');
                    $('#fkcustomers_cnpj').focus();
                }else {
                    $('#cpf_cnpj').focus();
                }
            }
        }]
    );

}
