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
});
