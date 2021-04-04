
jQuery(function() {
    $(".fkcarrier_text_cep").mask('99999-999');
});

function fkcarrierExcluir(msg) {
	
    if (confirm(msg)) {
    	return true;
    }
    
    return false;
};

function fkcarrierToggle(idDiv) {
	$("#" + idDiv).toggle("slow","linear");
};

function fkcarrierMarcar(idClass) {
	$("." + idClass).each(
	     function(){
	        $(this).attr("checked", true);
	     }
	);
};

function fkcarrierDesmarcar(idClass) {
	$("." + idClass).each(
	     function(){
	        $(this).attr("checked", false);
	     }
	);
};

function fkcarrierIncluirCepCidade() {

    var campo = "";

    campo = $("#fkcarrier_cep_cidade").val();
    campo += soNumeros($("#fkcarrier_cidade_cep1").val());
    campo += ":";
    campo += soNumeros($("#fkcarrier_cidade_cep2").val());
    campo += "/";

    $("#fkcarrier_cep_cidade").val(campo);
    $("#fkcarrier_cidade_cep1").val("");
    $("#fkcarrier_cidade_cep2").val("");
};

function fkcarrierIncluirCepCorreios(id) {
	
	var campo = "";
	
	campo = $("#fkcarrier_correios_intervalos_cep_" + id).val();
	campo += soNumeros($("#fkcarrier_correios_cep1_" + id).val());
	campo += ":";
	campo += soNumeros($("#fkcarrier_correios_cep2_" + id).val());
	campo += "/";
	
	$("#fkcarrier_correios_intervalos_cep_" + id).val(campo);
	$("#fkcarrier_correios_cep1_" + id).val("");
	$("#fkcarrier_correios_cep2_" + id).val("");
};

function fkcarrierIncluirCepTransp(id) {
	
	var campo = "";
	
	campo = $("#fkcarrier_transp_intervalos_cep_" + id).val();
	campo += soNumeros($("#fkcarrier_transp_cep1_" + id).val());
	campo += ":";
	campo += soNumeros($("#fkcarrier_transp_cep2_" + id).val());
	campo += "/";
	
	$("#fkcarrier_transp_intervalos_cep_" + id).val(campo);
	$("#fkcarrier_transp_cep1_" + id).val("");
	$("#fkcarrier_transp_cep2_" + id).val("");
};

function fkcarrierIncluirCepFreteGratis(id) {

    var campo = "";

    campo = $("#fkcarrier_frete_gratis_intervalos_cep_" + id).val();
    campo += soNumeros($("#fkcarrier_frete_gratis_cep1_" + id).val());
    campo += ":";
    campo += soNumeros($("#fkcarrier_frete_gratis_cep2_" + id).val());
    campo += "/";

    $("#fkcarrier_frete_gratis_intervalos_cep_" + id).val(campo);
    $("#fkcarrier_frete_gratis_cep1_" + id).val("");
    $("#fkcarrier_frete_gratis_cep2_" + id).val("");
};

function fkcarrierIncluirProdutosFreteGratis(id) {

    var campo = "";

    campo = $("#fkcarrier_frete_gratis_relacao_produtos_" + id).val();
    campo += soNumeros($("#fkcarrier_frete_gratis_produto_" + id).val());
    campo += "/";

    $("#fkcarrier_frete_gratis_relacao_produtos_" + id).val(campo);
    $("#fkcarrier_frete_gratis_produto_" + id).val("");
};

function fkcarrierTranspPreco1(id) {
	$('#fkcarrier_transp_preco1_' + id).show();
	$('#fkcarrier_transp_preco2_' + id).hide();
	$('#fkcarrier_transp_preco3_' + id).hide();
	
	$('#fkcarrier_preco_demais_itens_' + id).hide();
}

function fkcarrierTranspPreco2(id) {
	$('#fkcarrier_transp_preco1_' + id).hide();
	$('#fkcarrier_transp_preco2_' + id).show();
	$('#fkcarrier_transp_preco3_' + id).hide();
	
	$('#fkcarrier_preco_demais_itens_' + id).show();
}

function fkcarrierTranspPreco3(id) {
	$('#fkcarrier_transp_preco1_' + id).hide();
	$('#fkcarrier_transp_preco2_' + id).hide();
	$('#fkcarrier_transp_preco3_' + id).show();
	
	$('#fkcarrier_preco_demais_itens_' + id).show();
}

function fkcarrierTranspIncluiPreco2(id) {
	
	var campo = "";
	
	campo = $("#fkcarrier_transp_preco_2_" + id).val();
	campo += $("#fkcarrier_transp_preco2_kilo_" + id).val();
	campo += ":";
	campo += $("#fkcarrier_transp_preco2_valor_" + id).val();
	campo += "/";
	
	$("#fkcarrier_transp_preco_2_" + id).val(campo);
	$("#fkcarrier_transp_preco2_kilo_" + id).val("");
	$("#fkcarrier_transp_preco2_valor_" + id).val("");
};

function fkcarrierTranspIncluiPreco3(id) {
	
	var campo = "";
	
	campo = $("#fkcarrier_transp_preco_3_" + id).val();
	campo += $("#fkcarrier_transp_preco3_kilo_" + id).val();
	campo += ":";
	campo += $("#fkcarrier_transp_preco3_valor_" + id).val();
	campo += "/";
	
	$("#fkcarrier_transp_preco_3_" + id).val(campo);
	$("#fkcarrier_transp_preco3_kilo_" + id).val("");
	$("#fkcarrier_transp_preco3_valor_" + id).val("");
};

function fkcarrierProcTabOffGeral(urlFuncoes, idCorreiosTransp) {

    var idTabelasOff = "";
    var erro = false;

    $("#fkcarrier_tabelas_off_status_" + idCorreiosTransp).html("");
    $("#fkcarrier_tabelas_off_status_" + idCorreiosTransp).css("display", "none");

    // Processa capital
    $(".fkcarrier_tabelas_off_capital_" + idCorreiosTransp).each(
        function(){

            // Recupera o id da tabela
            idTabelasOff = $(this).attr("id").toString();
            idTabelasOff = idTabelasOff.substr(30);

            $("#fkcarrier_tabelas_off_capital_" + idTabelasOff).focus();
            $("#fkcarrier_tabelas_off_capital_" + idTabelasOff).val("");

            $("#fkcarrier_tabelas_off_alert_capital_" + idTabelasOff).css("color","blue");
            $("#fkcarrier_tabelas_off_alert_capital_" + idTabelasOff).html("Processando...");

            $.ajax({
                type: "POST",
                async: false,
                url: urlFuncoes,
                data: {func: "1", idCorreiosTransp: idCorreiosTransp, idTabelasOff: idTabelasOff, tipoTabela: 'capital'}
            }).done(function(retorno) {

                    if (retorno.substr(0, 4) != "erro") {
                        $("#fkcarrier_tabelas_off_capital_" + idTabelasOff).val(retorno);

                        $("#fkcarrier_tabelas_off_alert_capital_" + idTabelasOff).css("color","green");
                        $("#fkcarrier_tabelas_off_alert_capital_" + idTabelasOff).html("Concluído");
                    }else {
                        $("#fkcarrier_tabelas_off_alert_capital_" + idTabelasOff).css("color","red");
                        $("#fkcarrier_tabelas_off_alert_capital_" + idTabelasOff).html("Erro na criação da tabela: " + retorno.substr(6));

                        erro = true;
                    }
                });
        }
    );

    $("html").animate({scrollTop:0},600);

    // Processa interior
    $(".fkcarrier_tabelas_off_interior_" + idCorreiosTransp).each(
        function(){

            // Recupera o id da tabela
            idTabelasOff = $(this).attr("id").toString();
            idTabelasOff = idTabelasOff.substr(31);

            $("#fkcarrier_tabelas_off_interior_" + idTabelasOff).focus();
            $("#fkcarrier_tabelas_off_interior_" + idTabelasOff).val("");

            $("#fkcarrier_tabelas_off_alert_interior_" + idTabelasOff).css("color","blue");
            $("#fkcarrier_tabelas_off_alert_interior_" + idTabelasOff).html("Processando...");

            $.ajax({
                type: "POST",
                async: false,
                url: urlFuncoes,
                data: {func: "1", idCorreiosTransp: idCorreiosTransp, idTabelasOff: idTabelasOff, tipoTabela: 'interior'}
            }).done(function(retorno) {

                    if (retorno.substr(0, 4) != "erro") {
                        $("#fkcarrier_tabelas_off_interior_" + idTabelasOff).val(retorno);

                        $("#fkcarrier_tabelas_off_alert_interior_" + idTabelasOff).css("color","green");
                        $("#fkcarrier_tabelas_off_alert_interior_" + idTabelasOff).html("Concluído");
                    }else {
                        $("#fkcarrier_tabelas_off_alert_interior_" + idTabelasOff).css("color","red");
                        $("#fkcarrier_tabelas_off_alert_interior_" + idTabelasOff).html("Erro na criação da tabela: " + retorno.substr(6));

                        erro = true;
                    }
                });
        }
    );

    $("#fkcarrier_tabelas_off_status_" + idCorreiosTransp).css("display", "inline");

    if (erro == false) {
        $("#fkcarrier_tabelas_off_status_" + idCorreiosTransp).css("color","green");
        $("#fkcarrier_tabelas_off_status_" + idCorreiosTransp).html("Tabelas processadas com sucesso.");
    }else {
        $("#fkcarrier_tabelas_off_status_" + idCorreiosTransp).css("color","red");
        $("#fkcarrier_tabelas_off_status_" + idCorreiosTransp).html("Existem tabelas com erros. Favor verificar e refazê-las.");
    }

    $("html").animate({scrollTop:0},600);
}

function fkcarrierProcTabOffEspecifica(urlFuncoes, idCorreiosTransp, idTabelasOff, tipo) {

    $("#fkcarrier_tabelas_off_" + tipo + "_" + idTabelasOff).val("");

    $("#fkcarrier_tabelas_off_alert_" + tipo + "_" + idTabelasOff).css("color","blue");
    $("#fkcarrier_tabelas_off_alert_" + tipo + "_" + idTabelasOff).html("Processando...");

    $.ajax({
        type: "POST",
        async: false,
        url: urlFuncoes,
        data: {func: "1", idCorreiosTransp: idCorreiosTransp, idTabelasOff: idTabelasOff, tipoTabela: tipo}
    }).done(function(retorno) {

            if (retorno.substr(0, 4) != "erro") {
                $("#fkcarrier_tabelas_off_" + tipo + "_" + idTabelasOff).val(retorno);

                $("#fkcarrier_tabelas_off_alert_" + tipo + "_" + idTabelasOff).css("color","green");
                $("#fkcarrier_tabelas_off_alert_" + tipo + "_" + idTabelasOff).html("Concluído");
            }else {
                $("#fkcarrier_tabelas_off_alert_" + tipo + "_" + idTabelasOff).css("color","red");
                $("#fkcarrier_tabelas_off_alert_" + tipo + "_" + idTabelasOff).html("Erro na criação da tabela: " + retorno.substr(6));
            }
        });

}

function soNumeros(str){  
    var i;
    var tmp="";

    for (i=0; i < str.length; i++){  

        if (str.substr(i,1) >= "0" && str.substr(i,1) <= "9") {
            tmp = tmp + str.substr(i,1);
        }
    }

    return tmp;      
}
