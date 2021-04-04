
function procEndereco(endereco) {

    if (endereco.length == 0) {
        return true;
    }

    // Validando Endereco
    msgEndereco('1', endereco);

    var interval = window.setTimeout(function(){
        $.ajax({
            type: "POST",
            data: {func: "5", endereco: endereco},
            url: urlFuncoes,
            async: false,
            dataType: "html",
            success: function(retorno) {

                if (retorno == 0) {
                    // Confirmacao do Número
                    msgEndereco('2', endereco);
                }else {
                    // Fecha fancybox
                    $.fancybox.close();
                }
            }
        });
    }, 1000);

}

function msgEndereco(tipoMsg, endereco) {

    var html = '';
    var altura = '0';
    var largura = '0';
    var janela_modal = true;

    switch (tipoMsg) {

        case '1':
            html =  '<p><img src="' + urlImg + 'validando_48.gif" alt="" width="48" height="48" /></p>';
            html += '<p class="fkcustomers-fancybox-msg fkcustomers-color-verde">Validando Endereço</p>';
            largura = 200;
            break;

        case '2':
            html =  '<div class="fkcustomers-fancybox">';
            html += '<p><img src="' + urlImg + 'interrogacao_48.png" alt="" width="48" height="48" /></p>';
            html += '<p class="fkcustomers-fancybox-msg fkcustomers-color-azul">Confirme se o número de seu endereço está correto</p>';
            html += '<p class="fkcustomers-fancybox-submsg">' + endereco + '</p>';
            html += '<button class="fkcustomers-fancybox-button fkcustomers-btn-verde fkcustomers-float-left" name="btn_1" id="btn_1">Confirmo</button>';
            html += '<button class="fkcustomers-fancybox-button fkcustomers-btn-vermelho fkcustomers-float-right" name="btn_2" id="btn_2">Corrigir</button>';
            html += '</div>';
            largura = 420;
            break;

    }

    $.fancybox.open([{
            type: 'inline',
            modal: janela_modal,
            minHeight: 30,
            autoSize: false,
            autoHeight: true,
            width: largura,
            content: html,

            helpers: {
                overlay: {
                    closeClick: false,
                    lock: true
                }
            },

            afterShow: function () {
                $(".fkcustomers-fancybox #btn_1").click(function () {
                    validaNumero = false;
                    $.fancybox.close();
                });

                $(".fkcustomers-fancybox #btn_2").click(function () {
                    validaNumero = true;
                    $('#address1').focus();
                    $.fancybox.close();
                });
            }
        }],
        {
            padding: [20, 10, 10, 10]
        }
    );
}