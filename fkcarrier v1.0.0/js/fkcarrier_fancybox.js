
$(document).ready(function(){
    
    $('.fkcarrier-fancybox').fancybox({
        'hideOnContentClick': false,
        'openEffect'        : 'elastic',
        'closeEffect'       : 'elastic'
    });
    
    $(".fkcarrier-fancybox").trigger("click");
    
});

function mostraRastreio(codRastreio) {

    if (codRastreio.length == 0) {
        return;
    }

    var urlRastreio = 'http://websro.correios.com.br/sro_bin/txect01%24.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=' + codRastreio;

    $.fancybox(
        urlRastreio,
        {
            width: 800,
            autoHeight: true,
            type: 'iframe'
        }
    );

}
