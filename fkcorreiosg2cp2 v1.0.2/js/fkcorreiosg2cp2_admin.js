
jQuery(function() {
    $('.fkcorreiosg2cp2-mask-cep').mask('99999-999');
});

function fkcorreiosg2cp2ExcluirConf(msg, id) {

    if ($("#" + id).is(":checked")) {

        if (confirm(msg)) {
            return true;
        }

        $("#" + id).attr("checked", false);
        return false;
    }

    return true;

}