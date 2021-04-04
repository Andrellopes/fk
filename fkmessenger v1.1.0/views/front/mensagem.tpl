
<script type="text/javascript">

    $(document).ready(function(){

        var html =  '<div class="fkmessenger-fancybox">';
        html +=         '{$mensagem}';
        html +=         '<div>';
        html +=             '<a class="fkmessenger-fancybox-button {$class_botao_1}" href="{$link_botao_1}" target="{$nova_pagina_1}">{$nome_botao_1}</a>';
        html +=             '<a class="fkmessenger-fancybox-button {$class_botao_2}" href="{$link_botao_2}" target="{$nova_pagina_2}">{$nome_botao_2}</a>';
        html +=         '</div>';
        html +=     '</div>';

        // Altura
        var altura = {$altura};

        // Largura
        var largura = {$largura};

        // Dimensao
        var autoHeight = false;
        if (altura == 0){
            autoHeight = true;
        }

        var autoWidth = false;
        if (largura == 0){
            autoWidth = true;
        }

        // Tipo de janela
        var janela_modal = false;
        var modal = '{$modal}';

        if (modal == 'sim') {
            janela_modal = true;
        }

        $.fancybox.open([{
                type: 'inline',
                autoSize: false,
                modal: janela_modal,
                autoHeight: autoHeight,
                autoWidth: autoWidth,
                height: altura,
                width: largura,
                openEffect: 'elastic',
                closeEffect: 'elastic',
                content: html,

                helpers:  {
                    overlay : {
                        closeClick: false,
                        lock: true
                    }
                },

                afterShow: function() {
                    $(".fkmessenger-fancybox a").click(function(){
                        $.fancybox.close(true);
                    });
                }
            }]
        );

    });

</script>

