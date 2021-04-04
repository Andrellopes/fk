// Variaveis
var valorPedido = '';
var numCartao = '';
var cardBin = '';
var bandeira = '';

//Mascara de campos
jQuery(function() {
    $('#fkpagseguroct_cartao_nasc').mask('99/99/9999');
    $('#fkpagseguroct_cartao_cpf').mask('999.999.999-99');
    $('#fkpagseguroct_cartao_cnpj').mask('99.999.999/9999-99');

    $('#fkpagseguroct_cartao_tel').focusout(function(){
        var phone, element;
        element = $(this);
        element.unmask();
        phone = element.val().replace(/\D/g, '');
        if(phone.length > 10) {
            element.mask('(99) 99999-999?9');
        } else {
            element.mask('(99) 9999-9999?9');
        }
    }).trigger('focusout');
    
    $('#fkpagseguroct_boleto_cpf').mask('999.999.999-99');
    $('#fkpagseguroct_boleto_cnpj').mask('99.999.999/9999-99');
    
    $('#fkpagseguroct_boleto_tel').focusout(function(){
        var phone, element;
        element = $(this);
        element.unmask();
        phone = element.val().replace(/\D/g, '');
        if(phone.length > 10) {
            element.mask('(99) 99999-999?9');
        } else {
            element.mask('(99) 9999-9999?9');
        }
    }).trigger('focusout');
    
    $('#fkpagseguroct_transf_cpf').mask('999.999.999-99');
    $('#fkpagseguroct_transf_cnpj').mask('99.999.999/9999-99');
    
    $('#fkpagseguroct_transf_tel').focusout(function(){
        var phone, element;
        element = $(this);
        element.unmask();
        phone = element.val().replace(/\D/g, '');
        if(phone.length > 10) {
            element.mask('(99) 99999-999?9');
        } else {
            element.mask('(99) 9999-9999?9');
        }
    }).trigger('focusout');
});

// Monitoramento automatico de acoes
$(document).ready(function(){
	
    // Recupera Valor do Pedido
    valorPedido = Number($('#fkpagseguroct_valor_pedido').val());
    
    // Obtem Session Id
    fkpagseguroct_obterSessionId();

    // Radio CPF/CNPJ do boleto
    $(document).on('click', 'input[name=fkpagseguroct_boleto_cpf_cnpj]', function(e) {

        if ($("#fkpagseguroct_boleto_radio_cpf").is(":checked")) {
            $("#fkpagseguroct_boleto_cpf").css("display", "block");
            $("#fkpagseguroct_boleto_cnpj").css("display", "none");
            $("#fkpagseguroct_boleto_cpf").focus();
        }else {
            $("#fkpagseguroct_boleto_cpf").css("display", "none");
            $("#fkpagseguroct_boleto_cnpj").css("display", "block");
            $("#fkpagseguroct_boleto_cnpj").focus();
        }

    });

    // Radio CPF/CNPJ da transferencia
    $(document).on('click', 'input[name=fkpagseguroct_transf_cpf_cnpj]', function(e) {

        if ($("#fkpagseguroct_transf_radio_cpf").is(":checked")) {
            $("#fkpagseguroct_transf_cpf").css("display", "block");
            $("#fkpagseguroct_transf_cnpj").css("display", "none");
            $("#fkpagseguroct_transf_cpf").focus();
        }else {
            $("#fkpagseguroct_transf_cpf").css("display", "none");
            $("#fkpagseguroct_transf_cnpj").css("display", "block");
            $("#fkpagseguroct_transf_cnpj").focus();
        }

    });

    // Numero do cartao
    $(document).on('change', 'input[name=fkpagseguroct_cartao_numero]', function(e) {

		numCartao = $(this).val();
		numCartao = numCartao.replace(/[^0-9]/g,'');
		
		// Obtem bandeira
		fkpagseguroct_obterBandeira();
    });
    
    // Parcelamento
    $(document).on('change', '#fkpagseguroct_cartao_parcelas', function(e) {
	    
		var option = $(this).find('option:selected');
		if (option.length) {
		    $('#fkpagseguroct_cartao_valor_parcela').val(option.attr('dataPrice'));
		}
    });
    
    // Finaliza pagamento com Cartao de Credito
    $(document).on('click', '#btnCartao', function(e) {
    	fkpagseguroct_finalizarCartao();
    });
    
    // Finaliza pagamento com Boleto
    $(document).on('click', '#btnBoleto', function(e) {
    	fkpagseguroct_finalizarBoleto();
    });
    
    // Finaliza pagamento com Transferencia
    $(document).on('click', '#btnTransf', function(e) {
    	fkpagseguroct_finalizarTransf();
    });
	
});

function fkpagseguroct_obterSessionId() {

    var html = '';

    // Mensagem
    fkpagseguroct_msgFancyBox('1');
    
    $.ajax({
	url: urlFuncoes,
	type: 'POST',
	data: {func: '1'},
	cache: false,
	success: function(retorno) {
		    
	    if (retorno.length > 1) {

			var jRetorno = JSON.parse(retorno);
			
			if (jRetorno.erro == '0') {
			    // Retira msg de erro da pagina, se houver
			    fkpagseguroct_msgForm_off();
			    
			    // Informa Session Id para js
			    PagSeguroDirectPayment.setSessionId(jRetorno.dados);
			}else {
			    // Envia msg de erro para a pagina
			    fkpagseguroct_msgForm_on('<p>' + jRetorno.descricao + '</p>');
			}
	    }else {
    		// Envia msg de erro para a pagina
            html = '<p>Não foi possível obter o Session Id do Pagseguro.</p>';
            html += '<p>Clique F5 para tentar novamente.</p>';
    		fkpagseguroct_msgForm_on(html);
	    }
	},
	error: function() {
	    // Envia msg de erro para a pagina
	    html = '<p>Não foi possível obter o Session Id do Pagseguro.</p>';
        html += '<p>Clique F5 para tentar novamente.</p>';
    	fkpagseguroct_msgForm_on(html);
	},
	complete: function() {
	    $.fancybox.close();
	}
    });
	
}

function fkpagseguroct_obterBandeira() {
	
    if (String(numCartao).length >= 6 && numCartao.substring(0, 6) != cardBin) {
	    
		PagSeguroDirectPayment.getBrand({
			
		    cardBin: numCartao.substring(0, 6),
		    success: function(resposta) {
			    
    			// Retira msg de erro da pagina, se houver
    			fkpagseguroct_msgForm_off();

    			// Recupera a bandeira do cartao
    			bandeira = resposta.brand.name;

    			// Guarda cardBin
    			cardBin = numCartao.substring(0, 6)

    			// Mostra img da bandeira
    			fkpagseguroct_bandeira_on();

    			// Obtem parcelamento
    			fkpagseguroct_obterParcelamento();

    		    },
    		    error: function(resposta) {
    			// Retira img da bandeira
    			fkpagseguroct_bandeira_off();

    			// Envia msg de erro para a pagina
    			fkpagseguroct_msgForm_on('<p>Não foi possível obter a Bandeira do cartão. Verifique se o número digitado está correto.</p>');
		    },
		    complete: function(resposta) {
			    
		    }

		});
    }else {
		if (String(numCartao).length < 6) {
		    // Limpa cardBin
		    cardBin = '';
		    
		    // Retira img da bandeira
		    fkpagseguroct_bandeira_off();
		}
    }
	
}

function fkpagseguroct_obterParcelamento() {
	
    PagSeguroDirectPayment.getInstallments({
	    
	amount: valorPedido,
    maxInstallmentNoInterest: parcelasSemJuros,
	brand:  bandeira,
	success: function(resposta) {
		
	    // Array com o parcelamento
	    var parcelamento = resposta.installments[bandeira];
	    
	    var options = '';
	    for (var i in parcelamento) {
		    
		    var optionItem     		= parcelamento[i];
		    var optionQuantidade 	= optionItem.quantity; // Obtendo a quantidade
		    var optionValor   		= optionItem.installmentAmount; // Obtendo o valor

            // String informando se a parcela e com ou sem juros
            if (optionItem.interestFree == true) {
                var strJuros = ' (sem juros)';
            }else {
                var strJuros = '';
            }

            var optionLabel    	    = (optionQuantidade + ' x ' + formatMoney(optionValor) + strJuros); // Label do option
		    var valor          		= Number(optionValor).toMoney(2,'.',',');
		    
		    options += ('<option value="' + optionItem.quantity + '" dataPrice="' + valor + '">'+ optionLabel +'</option>');
		    
	    };
	    
	    // Atualizando dados do select de parcelamento
	    $('#fkpagseguroct_cartao_parcelas').html(options);
	    
	    // Valor inicial do parcelamento
	    $('#fkpagseguroct_cartao_valor_parcela').val($('#fkpagseguroct_valor_pedido').val());
		
	},
	error: function(resposta) {
	    // Envia msg de erro para a pagina
	    fkpagseguroct_msgForm_on('<p>Não foi possível obter os dados de parcelamento.</p>');
	},
	complete: function(resposta) {
		
	}
    });
	
}

function fkpagseguroct_finalizarCartao() {
	
    if (fkpagseguroct_validarCartao()) {
	    
		// Mensagem
		fkpagseguroct_msgFancyBox('1');
		
		// Obtem token do cartao
		PagSeguroDirectPayment.createCardToken({
			
		    cardNumber: numCartao,
		    brand: bandeira,
		    cvv: $('#fkpagseguroct_cartao_codigo').val(),
		    expirationMonth: $('#fkpagseguroct_cartao_venc_mes').val(),
		    expirationYear: $('#fkpagseguroct_cartao_venc_ano').val(),
		    
		    success: function(resposta) {
			    
	    		// Recupera token do cartao e grava campo hidden com o token
	    		$('#fkpagseguroct_cartao_token').val(resposta.card.token);
	
	    		// Obtem Hash do comprador
	    		var hashComprador = PagSeguroDirectPayment.getSenderHash();
	
	    		// Grava campo hidden com o hash
	    		$('#fkpagseguroct_cartao_hash').val(hashComprador);
	
	    		// Submit no form
	    		$('#fkpagseguroct_form_cartao').submit();
	    	},
		    error: function(resposta) {
	    		// Envia msg de erro para a pagina
	    		var msgErro = fkpagseguroct_trataErro(resposta.errors)
	    		fkpagseguroct_msgForm_on(msgErro);
	
	    		$.fancybox.close();
	
		    },
		    complete: function(resposta) {
			    
		    }
			
		});
    }
	
}

function fkpagseguroct_finalizarBoleto() {
    
    if (fkpagseguroct_validarBoleto()) {
	
		// Mensagem
		fkpagseguroct_msgFancyBox('1');
		
		// Obtem Hash do comprador
		var hashComprador = PagSeguroDirectPayment.getSenderHash();
		
		// Grava campo hidden com o hash
		$('#fkpagseguroct_boleto_hash').val(hashComprador);
		
		// Submit no form
		$('#fkpagseguroct_form_boleto').submit();
	
    }
    
}

function fkpagseguroct_finalizarTransf() {
    
    if (fkpagseguroct_validarTransf()) {
	
		// Mensagem
		fkpagseguroct_msgFancyBox('1');
		
		// Obtem Hash do comprador
		var hashComprador = PagSeguroDirectPayment.getSenderHash();
		
		// Grava campo hidden com o hash
		$('#fkpagseguroct_transf_hash').val(hashComprador);
		
		// Submit no form
		$('#fkpagseguroct_form_transf').submit();
	
    }
    
}

function fkpagseguroct_validarCartao() {
	
    var html = '';
    
    var titular = $('#fkpagseguroct_cartao_titular').val();
    titular = titular.trim();
    
    if (titular.length == 0) {
    	html += '<p>Titular do Cartão não preenchido.</p>';
    }
    
    var dataNasc = $('#fkpagseguroct_cartao_nasc').val();
    dataNasc = dataNasc.replace(/[^0-9]/g,'');
    
    if (dataNasc.length == 0) {
    	html += '<p>Data de Nascimento não preenchida.</p>';
    }
    
    var telefone = $('#fkpagseguroct_cartao_tel').val();
    telefone = telefone.replace(/[^0-9]/g,'');
    
    if (telefone.length == 0) {
    	html += '<p>Telefone não preenchido.</p>';
    }else if (!fkpagseguroct_validarDDD(telefone)) {
    	html += '<p>DDD do Telefone é inválido.</p>';
    }
    
    var cpf = $('#fkpagseguroct_cartao_cpf').val();
    cpf = cpf.replace(/[^0-9]/g,'');
    
    if (cpf.length == 0) {
    	html += '<p>CPF não preenchido.</p>';
    }else if (!fkpagseguroct_validarCPF(cpf)) {
    	html += '<p>CPF inválido.</p>';
    }

    var cnpj = $('#fkpagseguroct_cartao_cnpj').val();
    cnpj = cnpj.replace(/[^0-9]/g,'');

    if (cnpj.length > 0) {
        if (!fkpagseguroct_validarCNPJ(cnpj)) {
            html += '<p>CNPJ inválido.</p>';
        }
    }

    var numCartao = $('#fkpagseguroct_cartao_numero').val();
    numCartao = numCartao.replace(/[^0-9]/g,'');
    
    if (numCartao.length == 0) {
    	html += '<p>Número do Cartão não preenchido.</p>';
    }
    
    var mesVenc = $('#fkpagseguroct_cartao_venc_mes').val();
    mesVenc = mesVenc.replace(/[^0-9]/g,'');
    
    if (mesVenc.length == 0) {
    	html += '<p>Mês do Vencimento do Cartão não preenchido.</p>';
    }
    
    var anoVenc = $('#fkpagseguroct_cartao_venc_ano').val();
    anoVenc = anoVenc.replace(/[^0-9]/g,'');
    
    if (anoVenc.length == 0) {
    	html += '<p>Ano do Vencimento do Cartão não preenchido.</p>';
    }
    
    var codSeg = $('#fkpagseguroct_cartao_codigo').val();
    codSeg = codSeg.replace(/[^0-9]/g,'');
    
    if (codSeg.length == 0) {
    	html += '<p>Código de Segurança do Cartão não preenchido.</p>';
    }
    
    var parcelas = $('#fkpagseguroct_cartao_parcelas option:selected').text();
    parcelas = parcelas.trim();
    
    if (parcelas.length == 0) {
    	html += '<p>Quantidade de Parcelas não preenchida.</p>';
    }
    
    var endEntrega = $('#fkpagseguroct_cartao_endereco_entrega').val();
    endEntrega = endEntrega.trim();
    
    if (endEntrega.length == 0) {
    	html += '<p>Endereço de Entrega não preenchido.</p>';
    }
    
    var numEntrega = $('#fkpagseguroct_cartao_numero_entrega').val();
    numEntrega = numEntrega.trim();
    
    if (numEntrega.length == 0) {
    	html += '<p>Número do Endereço de Entrega não preenchido.</p>';
    }

    var bairroEntrega = $('#fkpagseguroct_cartao_bairro_entrega').val();
    bairroEntrega = bairroEntrega.trim();

    if (bairroEntrega.length == 0) {
        html += '<p>Bairro do Endereço de Entrega não preenchido.</p>';
    }

    var endCobranca = $('#fkpagseguroct_cartao_endereco_cobranca').val();
    endCobranca = endCobranca.trim();
    
    if (endCobranca.length == 0) {
    	html += '<p>Endereço de Cobrança não preenchido.</p>';
    }
    
    var numCobranca = $('#fkpagseguroct_cartao_numero_cobranca').val();
    numCobranca = numCobranca.trim();
    
    if (numCobranca.length == 0) {
    	html += '<p>Número do Endereço de Cobrança não preenchido.</p>';
    }

    var bairroCobranca = $('#fkpagseguroct_cartao_bairro_cobranca').val();
    bairroCobranca = bairroCobranca.trim();

    if (bairroCobranca.length == 0) {
        html += '<p>Bairro do Endereço de Cobrança não preenchido.</p>';
    }
    
    if (html.length > 0) {
		// Envia msg de erro para a pagina
		fkpagseguroct_msgForm_on(html);
		return false;
    }else {
		fkpagseguroct_msgForm_off()
		return true;
    }
	
}

function fkpagseguroct_validarBoleto() {
    
    var html = '';
    
    var telefone = $('#fkpagseguroct_boleto_tel').val();
    telefone = telefone.replace(/[^0-9]/g,'');
    
    if (telefone.length == 0) {
    	html += '<p>Telefone não preenchido.</p>';
    }else if (!fkpagseguroct_validarDDD(telefone)) {
    	html += '<p>DDD do Telefone é inválido.</p>';
    }
    
    var cpf = $('#fkpagseguroct_boleto_cpf').val();
    cpf = cpf.replace(/[^0-9]/g,'');

    var cnpj = $('#fkpagseguroct_boleto_cnpj').val();
    cnpj = cnpj.replace(/[^0-9]/g,'');

    if (cpf.length == 0 && cnpj.length == 0) {
        html += '<p>CPF ou CNPJ são obrigatórios.</p>';
    }else {
        if ($("#fkpagseguroct_boleto_radio_cpf").is(":checked")) {
            if (cpf.length > 0) {
                if (!fkpagseguroct_validarCPF(cpf)) {
                    html += '<p>CPF inválido.</p>';
                }
            }else {
                html += '<p>CPF não preenchido.</p>';
            }
        }else {
            if (cnpj.length > 0) {
                if (!fkpagseguroct_validarCNPJ(cnpj)) {
                    html += '<p>CNPJ inválido.</p>';
                }
            }else {
                html += '<p>CNPJ não preenchido.</p>';
            }
        }
    }
    
    var endEntrega = $('#fkpagseguroct_boleto_endereco_entrega').val();
    endEntrega = endEntrega.trim();
    
    if (endEntrega.length == 0) {
    	html += '<p>Endereço de Entrega não preenchido.</p>';
    }
    
    var numEntrega = $('#fkpagseguroct_boleto_numero_entrega').val();
    numEntrega = numEntrega.trim();
    
    if (numEntrega.length == 0) {
    	html += '<p>Número do Endereço de Entrega não preenchido.</p>';
    }

    var bairroEntrega = $('#fkpagseguroct_boleto_bairro_entrega').val();
    bairroEntrega = bairroEntrega.trim();

    if (bairroEntrega.length == 0) {
        html += '<p>Bairro do Endereço de Entrega não preenchido.</p>';
    }

    if (html.length > 0) {
    	// Envia msg de erro para a pagina
		fkpagseguroct_msgForm_on(html);
		return false;
    }else {
		fkpagseguroct_msgForm_off()
		return true;
    }
}    

function fkpagseguroct_validarTransf() {
    
    var html = '';
    
    if (!$("input[name='fkpagseguroct_transf_banco']:checked").val()) {
    	html += '<p>Banco não selecionado.</p>';
    }
    
    var telefone = $('#fkpagseguroct_transf_tel').val();
    telefone = telefone.replace(/[^0-9]/g,'');
    
    if (telefone.length == 0) {
    	html += '<p>Telefone não preenchido.</p>';
    }else if (!fkpagseguroct_validarDDD(telefone)) {
    	html += '<p>DDD do Telefone é inválido.</p>';
    }
    
    var cpf = $('#fkpagseguroct_transf_cpf').val();
    cpf = cpf.replace(/[^0-9]/g,'');

    var cnpj = $('#fkpagseguroct_transf_cnpj').val();
    cnpj = cnpj.replace(/[^0-9]/g,'');

    if (cpf.length == 0 && cnpj.length == 0) {
        html += '<p>CPF ou CNPJ são obrigatórios.</p>';
    }else {
        if ($("#fkpagseguroct_transf_radio_cpf").is(":checked")) {
            if (cpf.length > 0) {
                if (!fkpagseguroct_validarCPF(cpf)) {
                    html += '<p>CPF inválido.</p>';
                }
            }else {
                html += '<p>CPF não preenchido.</p>';
            }
        }else {
            if (cnpj.length > 0) {
                if (!fkpagseguroct_validarCNPJ(cnpj)) {
                    html += '<p>CNPJ inválido.</p>';
                }
            }else {
                html += '<p>CNPJ não preenchido.</p>';
            }
        }
    }

    var endEntrega = $('#fkpagseguroct_transf_endereco_entrega').val();
    endEntrega = endEntrega.trim();
    
    if (endEntrega.length == 0) {
    	html += '<p>Endereço de Entrega não preenchido.</p>';
    }
    
    var numEntrega = $('#fkpagseguroct_transf_numero_entrega').val();
    numEntrega = numEntrega.trim();
    
    if (numEntrega.length == 0) {
    	html += '<p>Número do Endereço de Entrega não preenchido.</p>';
    }

    var bairroEntrega = $('#fkpagseguroct_transf_bairro_entrega').val();
    bairroEntrega = bairroEntrega.trim();

    if (bairroEntrega.length == 0) {
        html += '<p>Bairro do Endereço de Entrega não preenchido.</p>';
    }

    if (html.length > 0) {
    	// Envia msg de erro para a pagina
		fkpagseguroct_msgForm_on(html);
		return false;
    }else {
		fkpagseguroct_msgForm_off()
		return true;
    }
}    

function fkpagseguroct_trataErro(erros) {
	
    var html = '';
    
    if (typeof erros == 'object') {
	    
		for (i in erros) {
		    html += ('<p>' + erros[i] + '</p>');
		}
	    
    }
    
    return html;
}

function fkpagseguroct_validarCPF(cpf) {

    var soma;
    var resto;
    var i;

    cpf = cpf.replace(/[^0-9]/g,'');

    if (cpf.length == 0) {
        return true;
    }

    if ((cpf.length != 11) || (cpf == '00000000000') || (cpf == '11111111111')
        || (cpf == '22222222222') || (cpf == '33333333333')
        || (cpf == '44444444444') || (cpf == '55555555555')
        || (cpf == '66666666666') || (cpf == '77777777777')
        || (cpf == '88888888888') || (cpf == '99999999999')) {

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
        return false;
    }

    return true;

}

function fkpagseguroct_validarCNPJ(cnpj) {

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
        return false;
    }

    return true;

}

function fkpagseguroct_validarDDD(telefone) {
	
    var ddd = '|' + telefone.substr(0, 2) + '|';
    
    if (dddValidos.indexOf(ddd) == -1) {
	return false;
    }
    
    return true;
}

function fkpagseguroct_msgFancyBox(tipoMsg) {

    var html = '';
    var largura = '0';
    var janela_modal = true;
    
    switch (tipoMsg) {
    
	case '1':
		html =  '<p><img src="' + urlImg + 'processando_48.gif" alt="" width="48" height="48" /></p>';
		html += '<p class="fkpagseguroct-fancybox-msg fkpagseguroct-color-verde">Aguarde...</p>';
		largura = 200;
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

	helpers:  {
	    overlay : {
		closeClick: false,
		lock: true
	    }
	},

	afterShow: function() {
	    
	},

	afterClose: function() {
	    
	}
    }]);

}

function fkpagseguroct_msgForm_on(msg) {
    $('#fkpagseguroct_msg_erro').css('display', 'block');
    $('#fkpagseguroct_msg_erro').html(msg);
}

function fkpagseguroct_msgForm_off() {
    $('#fkpagseguroct_msg_erro').css('display', 'none');
    $('#fkpagseguroct_msg_erro').html('');
}

function fkpagseguroct_bandeira_on() {
    $('#fkpagseguroct_img_bandeira').attr('src', urlImg + bandeira + '.png'); 
}

function fkpagseguroct_bandeira_off() {
    $('#fkpagseguroct_img_bandeira').attr('src', urlImg + 'bandeira.png'); 
}

var formatMoney = function(valor) {
    var valorAsNumber = Number(valor);
    return 'R$ ' + valorAsNumber.toMoney(2,',','.');
};

Number.prototype.toMoney = function(decimals, decimal_sep, thousands_sep) {
    var n = this,
    c = isNaN(decimals) ? 2 : Math.abs(decimals),
    d = decimal_sep || '.', 
    t = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    sign = (n < 0) ? '-' : '',
    i = parseInt(n = Math.abs(n).toFixed(c)) + '', 
    j = ((j = i.length) > 3) ? j % 3 : 0; 
    return sign + (j ? i.substr(0, j) + t : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : ''); 
};

