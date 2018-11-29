$ = jQuery;

var submitSearchBtn = $(".searchBarSearchIcon");
var searchBarInput  = $("#searchBarInput");
var clearSearchBtn  = $(".clearSearchBarField");
var searchForm = $("#searchForm");
var imageFlip  = ".image-flip";
var fsCard  =  ".frontside .card";
var bsCard  =  ".backside";
var colCard =  ".col .card";
var docCard =  ".doc .card";
var docMetaCard  =  ".doc-meta .card";
var colRedirect  =  colCard + " a.redirect";
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

    $(document).on( "click ontouchstart", bsCard, function(e) {
        href = $(this).prev($(fsCard)).find('a.redirect.desktop').attr('href');

        if ( href ) {
            window.location.href = href;
        }
    });

    $(document).on( "click ontouchstart", fsCard, function(e) {
        window.location.href = $(this).find('a.redirect').attr('href');
    });

    $(document).on( "click ontouchstart", colCard, function(e) {
        window.location.href = $(this).find('a.redirect').attr('href');
    });

    $(document).on( "click ontouchstart", docMetaCard, function(e) {
        window.location.href = $(this).find('a.full-text').attr('href');
    });

    $(document).on( "click ontouchstart", docCard, function(e) {
        window.location.href = $(this).find('a.full-text').attr('href');
    });

    $(document).on( "click ontouchstart", docRedirect, function(e) {
        e.stopImmediatePropagation();
    });

    $(document).on( "click ontouchstart", docMetaRedirect, function(e) {
        e.stopImmediatePropagation();
    });

    submitSearchBtn.on( "click ontouchstart", function(e) {
        searchForm.submit();
    });
});

$(function () {
    $('a[href=#top]').on( "click ontouchstart", function () {
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
