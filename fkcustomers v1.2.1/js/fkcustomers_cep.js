
function procCep(cep, modo, local) {

    cep = cep.replace(/[^0-9]/g,'');

    if (cep.length == 0) {
        return true;
    }

    if (cep.length != 8) {
        // CEP Invalido
        msgCEP('2', modo, local);

        var interval = window.setTimeout(function(){
            $.fancybox.close();
        }, 1500);

        return;
    }

    // Validando CEP
    msgCEP('1', modo, local);

    switch (provedorCep) {

        case 'CO':
            $.post(urlFuncoes, {func: '6', cep: cep}, function(retorno) {

                if (retorno.length > 1) {

                    retorno = retorno.trim();

                    if (retorno.substring(0,1) == '{') {

                        var arRet = JSON.parse(retorno);

                        $('#address1').val($.trim(decodeURI(arRet.end)));
                        $('#address2').val($.trim(decodeURI(arRet.bairro)));
                        $('#city').val($.trim(decodeURI(arRet.cidade)));
                        procUF($.trim(decodeURI(arRet.uf)));

                        // Fecha fancybox
                        $.fancybox.close();
                    }else {
                        // CEP não localizado
                        msgCEP('3', modo, local);
                    }
                }else {
                    // CEP não localizado
                    msgCEP('3', modo, local);
                }

            });

            break;

        case 'RV':
            $.post(urlFuncoes, {func: '1', cep: cep}, function(retorno) {

                if (retorno.length > 1) {

                    retorno = retorno.trim();

                    if (retorno.substring(0,1) == '{') {

                        var arRet = JSON.parse(retorno);

                        if (arRet.resultado == 1) {
                            $('#address1').val($.trim(decodeURI(arRet.tipo_logradouro)) + ' ' + $.trim(decodeURI(arRet.logradouro)));
                            $('#address2').val($.trim(decodeURI(arRet.bairro)));
                            $('#city').val($.trim(decodeURI(arRet.cidade)));
                            procUF($.trim(decodeURI(arRet.uf)));

                            // Fecha fancybox
                            $.fancybox.close();
                        }else {
                            // CEP não localizado
                            msgCEP('3', modo, local);
                        }
                    }else {
                        // CEP não localizado
                        msgCEP('3', modo, local);
                    }
                }else {
                    // CEP não localizado
                    msgCEP('3', modo, local);
                }
            });

            break;

        case 'BY':
            $.post(urlFuncoes, {func: '2', cep: cep}, function(retorno) {

                if (retorno.length > 1) {

                    var arRet = retorno.split('|');

                    if (arRet[0] = 'OK') {

                        var arRet_1 = arRet[1].split(',');

                        if (!$.isEmptyObject(arRet_1[1])) {
                            $('#address1').val($.trim(decodeURI(arRet_1[0])));
                            $('#address2').val($.trim(decodeURI(arRet_1[1])));
                            $('#city').val($.trim(decodeURI(arRet_1[2])));
                            procUF($.trim(decodeURI(arRet_1[3])));

                            // Fecha fancybox
                            $.fancybox.close();
                        }else {
                            // CEP não localizado
                            msgCEP('3', modo, local);
                        }
                    }else {
                        // CEP não localizado
                        msgCEP('3', modo, local);
                    }
                }
            });

            break;

        case 'AC':
            $.post(urlFuncoes, {func: '3', cep: cep}, function(retorno) {

                if (retorno.length > 1) {

                    retorno = retorno.trim();

                    if (retorno.substring(0,1) == '{') {

                        var arRet = JSON.parse(retorno);

                        if (arRet.resultado == 1) {
                            $('#address1').val($.trim(decodeURI(arRet.tipo_logradouro)) + ' ' + $.trim(decodeURI(arRet.logradouro)));
                            $('#address2').val($.trim(decodeURI(arRet.bairro)));
                            $('#city').val($.trim(decodeURI(arRet.cidade)));
                            procUF($.trim(decodeURI(arRet.uf)));

                            // Fecha fancybox
                            $.fancybox.close();
                        }else {
                            // CEP não localizado
                            msgCEP('3', modo, local);
                        }
                    }else {
                        // CEP não localizado
                        msgCEP('3', modo, local);
                    }
                }else {
                    // CEP não localizado
                    msgCEP('3', modo, local);
                }

            });

            break;
    }

    return true;
}

function procUF(uf) {

    $.post(urlFuncoes, {func: '4', uf: uf}, function(retorno) {

        if (retorno.length > 1) {
            $('#id_state').val($.trim(decodeURI(retorno)));
            $('#id_state').click();
        }

    });
}

function msgCEP(tipoMsg, modo, local) {

    var html = '';
    var altura = '0';
    var largura = '0';
    var janela_modal = true;

    switch (tipoMsg) {

        case '1':
            html =  '<p><img src="' + urlImg + 'validando_48.gif" alt="" width="48" height="48" /></p>';
            html += '<p class="fkcustomers-fancybox-msg fkcustomers-color-verde">Validando CEP</p>';
            largura = 200;
            break;

        case '2':
            html =  '<div class="fkcustomers-fancybox-fechar">';
            html += '<p><img src="' + urlImg + 'erro_48.png" alt="" width="48" height="48" /></p>';
            html += '<p class="fkcustomers-fancybox-msg fkcustomers-color-vermelho">CEP Inválido</p>';
            html += '</div>';
            largura = 200;
            break;

        case '3':

            html =  '<p><img src="' + urlImg + 'aviso_48.png" alt="" width="48" height="48" /></p>';
            html += '<p class="fkcustomers-fancybox-msg fkcustomers-color-azul">CEP não localizado</p>';
            html += '<p class="fkcustomers-fancybox-submsg">Por favor, preencha os dados manualmente';
            janela_modal = false;
            largura = 300;
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
                $(".fkcustomers-fancybox-fechar").click(function () {
                    $.fancybox.close();
                });
            },

            afterClose: function () {

                switch (tipoMsg) {

                    case '1':
                        if (modo == '2') {
                            $('#address1').focus();
                        }

                        break;

                    case '2':
                        if (local == 'front') {
                            if (modo == 1) {
                                $('#postcode_fk').val('');
                                $('#postcode_fk').focus();
                            }else {
                                $('#postcode').val('');
                                $('#postcode').focus();
                            }
                        }else {
                            $('#postcode').focus();
                        }

                        break;

                    case '3':
                        $('#address1').val('');
                        $('#address2').val('');
                        $('#city').val('');
                }
            }
        }],
        {
            padding: [20, 10, 10, 10]
        }
    );
}