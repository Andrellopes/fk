
function fkmessengerConfirma(msg) {

    if (confirm(msg)) {
        return true;
    }

    return false;
};

function fkmessengerShowId(id) {
    $("#" + id).css("display", "block");    
}

function fkmessengerHideId(id) {
    $("#" + id).css("display", "none");        
}

function fkmessengerToggle(id) {
    $("#" + id).toggle("slow","linear");
};