$ = jQuery;

var submitSearchBtn = $(".searchBarSearchIcon");
var searchBarInput  = $("#searchBarInput");
var clearSearchBtn  = $(".clearSearchBarField");
var searchForm = $("#searchForm");
var imageFlip  = ".image-flip";
var fsCard  =  ".frontside .card";
var bsCard  =  ".backside";
var docCard =  ".doc .card";
var docMetaCard  =  ".doc-meta .card";
var docRedirect  =  docCard + " a.redirect";
var docMetaRedirect = docMetaCard + " a.redirect";

$(document).ready(function() {
    searchBarInput.on('keyup focus', function() {
        if( $(this).val().length === 0 ) {
            clearSearchBtn.hide();
        } else {
            clearSearchBtn.show();
        }
    });

    $(document).on( "click", bsCard, function(e) {
        href = $(this).prev($(fsCard)).find('a.redirect.desktop').attr('href');

        if ( href ) {
            window.location.href = href;
        }
    });

    $(document).on( "click", fsCard, function(e) {
        window.location.href = $(this).find('a.redirect').attr('href');
    });

    $(document).on( "click", docMetaCard, function(e) {
        window.location.href = $(this).find('a.full-text').attr('href');
    });

    $(document).on( "click", docCard, function(e) {
        window.location.href = $(this).find('a.full-text').attr('href');
    });

    $(document).on( "click", docRedirect, function(e) {
        // e.stopPropagation();
        e.stopImmediatePropagation();
    });

    $(document).on( "click", docMetaRedirect, function(e) {
        // e.stopPropagation();
        e.stopImmediatePropagation();
    });

    submitSearchBtn.on( "click", function(e) {
        searchForm.submit();
    });
});

$(function () {
    $('a[href=#top]').click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 600);
        return false;
    });
});

$(window).scroll(function () {
    if ($(this).scrollTop() > 50) {
        $('.totop a').fadeIn();
    } else {
        $('.totop a').fadeOut();
    }
});

function resetInput() {
    clearSearchBtn.hide();
    searchBarInput.val('').focus();
}

function toggleCard(id) {
    $('body,html').find(imageFlip).removeClass('hover');
    $('#'+id).toggleClass('hover');
    $('#'+id).find('a.redirect').removeClass('desktop');
}
