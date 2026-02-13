function toggleVisible(el) {
    if (el.css('display') == "none") {
        el.show();
    }
    else {
        el.hide();
    }	
}

function toggleVisible2(el, h) {
    if (el.css('height') == "0px") {
        el.css('height', h+"px");
    }
    else {
        el.css('height', "0px");
    }	
}