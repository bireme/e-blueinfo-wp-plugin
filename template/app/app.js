$(function () {
    $('.dropdown-language li').on( "click", function(e) {
        var lang = $(this).data('lang');
        $('.app-lang').val(lang);
        $('.app-lang').text($(this).text());
        $('.adv-lang').css('display', 'none');
        $('.adv-lang.'+lang).css('display', 'block');
    });

    $('select.languages').on( "change", function(e) {
        var lang = $(this).val();
        $('select.countries').css('display', 'none');
        $('select.countries.'+lang).css('display', 'block');
    });

    $('select.countries, select.countries-list').on( "change", function(e) {
        var country = $(this).find('option:selected').data('country');
        setCookie('e-blueinfo-country', country);
    });
});

function setCookie(name,value,days) {
    var expires = "";

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }

    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');

    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }

    return null;
}
