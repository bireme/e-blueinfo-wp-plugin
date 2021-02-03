var $ = jQuery;
//  --------------------------------- Text
$(document).ready(function() {
  M.updateTextFields();
});
//  --------------------------------- Select
$('select').formSelect();
//  --------------------------------- Modal
$(document).ready(function(){
  $('.modal').modal();
});
//  --------------------------------- Modal
$(document).ready(function(){
  $('.sidenav').sidenav();
});
//  --------------------------------- Accordion
$(document).ready(function(){
  $('.collapsible').collapsible();
});
//  --------------------------------- Nav
$(document).ready(function(){
  $('.sidenav').sidenav();
});
//  --------------------------------- Data Picker
$('.datepicker').datepicker();
//  --------------------------------- Tradução DataPicker (calendário)
$('.datepicker').datepicker({
  i18n: {
    months: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
    monthsShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
    weekdays: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sabádo'],
    weekdaysShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
    weekdaysAbbrev: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'],
    today: 'Hoje',
    clear: 'Limpar',
    cancel: 'Sair',
    done: 'Confirmar',
    labelMonthNext: 'Próximo mês',
    labelMonthPrev: 'Mês anterior',
    labelMonthSelect: 'Selecione um mês',
    labelYearSelect: 'Selecione um ano',
    selectMonths: true,
    selectYears: 15,
  },
  format: 'dd mmmm, yyyy',
  container: 'body',
  minDate: new Date(),
});
// AOS
AOS.init();
/********************/
$('#radioThemes').click(function(){
  $('#themes').show();
  $('#categories').hide();
})
$('#radioCategories').click(function(){
  $('#themes').hide();
  $('#categories').show();
})
/********************/
/*
$('#cardLastVisited').click(function(){
  $('.cardLastVisited').show();
  $('.cardVisited').hide();
  $('.cardSingle').hide();
})
$('#cardVisited').click(function(){
 $('.cardVisited').show();
 $('.cardLastVisited').hide();
 $('.cardSingle').hide();
})
$('#cardSingle').click(function(){
 $('.cardVisited').show();
 $('.cardLastVisited').show();
 $('.cardSingle').show();
})
*/
//  --------------------------------- Cores
var theme = $.cookie('color'); // carrega cookie da cor
// Ao clicar Cor Padrão
$('#color0').click(function(){
  $.cookie("e-blueinfo-color","",{ path: '/', expires: 365 * 10 }); // seta cor vazia
  $('body').removeClass('bgColor1 + bgColor2'); // remover classes de cores
});
// Ao clicar Cor Dark
$('#color1').click(function(){
  $.cookie("e-blueinfo-color","bgColor1",{ path: '/', expires: 365 * 10 }); //seta cor 1
  $('body').addClass('bgColor1'); // insere no body cor selecionada (1)
  $('body').removeClass('bgColor2'); // remover segunda cor
});
// Ao clicar Cor Blue
$('#color2').click(function(){
  $.cookie("e-blueinfo-color","bgColor2",{ path: '/', expires: 365 * 10 }); // seta cor 2
  $('body').addClass('bgColor2'); // insere no body cor selecionada (2)
  $('body').removeClass('bgColor1'); // remover primeira cor
});

/* Busca por voz */
if ( $('#speakBtn').length ) {
  window.addEventListener('DOMContentLoaded', function() {
    var speakBtn = document.querySelector('#speakBtn');
    if (window.SpeechRecognition || window.webkitSpeechRecognition) {
      var SpeechRecognition = SpeechRecognition || webkitSpeechRecognition;
      var recognition = new SpeechRecognition();
      speakBtn.addEventListener('click', function(e) {
        recognition.start();
      }, false);
      recognition.addEventListener('result', function(e) {
        var result = e.results[0][0].transcript;
        document.getElementById("searchBarInput").value = result;
        document.getElementById("searchForm").submit();
      }, false);
    } else {
      // alert('Este navegador não suporta esta funcionalidade ainda!');
      jQuery('#speakBtn').css('display','none');
    }
  }, false);
}

// Mostar mais
$('#moreOptions').click(function(){
  $('#fieldSetOptions').toggle('1000');
})

/* Feedback Tab */
$(function () {
    $('#feedbackIcone').click(function(){
        $('#feedback').toggleClass("feedback");
    })
    $('#feedbackFechar').click(function(){
        $('#feedback').removeClass("feedback");
    }) 
});

/* Main Script */
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
        var country = $('div.countries.'+lang+' select').find('option:selected').data('country');

        $('div.countries').css('display', 'none');
        $('div.countries.'+lang).css('display', 'block');

        if ( country && lang ) {
            $('#btn-submit').attr('disabled', false);
        } else {
            $('#btn-submit').attr('disabled', true);          
        }
    });

    $('div.countries select').on( "change", function(e) {
        var country = $(this).find('option:selected').data('country');
        var lang = $('select.languages').val();

        if ( country && lang ) {
            $('#btn-submit').attr('disabled', false);
        } else {
            $('#btn-submit').attr('disabled', true);          
        }
    });

    $('#btn-submit').on( "click", function(e) {
        var country = $('div.countries:visible select, div.countries-list select');
        var data = country.find('option:selected').data('country');
        var val = country.find('option:selected').val();
        $.cookie('e-blueinfo-country', data, { path: '/', expires: 365 * 10 });
        window.location.href = val;
    });
});

/* Favorite Documents */
$(function () {
    $(document).on('click', '.btn-favorites', function(){
        var lang = eblueinfo_script_vars.lang;
        var title = $(this).parents('article').find('.doc-title').text();
        var id = $(this).data('docid');
        var altid = $(this).data('altid');
        var url = eblueinfo_script_vars.portal+'/portal/resource/'+lang+'/'+altid;
        var source = eblueinfo_script_vars.site;
        var author = $(this).data('author');
        // author = author.replace(/[^ ]+/i,'');

        var obj = new Object();
        obj.url = $.trim(url);
        obj.source = $.trim(source);
        obj.author = $.trim(author).replace(/\s+/g, " ");
        obj.title = $.trim(title);
        obj.id = $.trim(id);

        // alert(JSON.stringify(obj, null, 4)); return false;

        obj.userTK = unescape($.cookie('userTK'));
        // obj.userTK = decodeURI($.cookie('userTK'));

        if ( obj.userTK == 'undefined' ) {
            $('#modal').modal('open');
        } else {
            $.post(eblueinfo_script_vars.servplat + '/client/controller/servicesplatform/control/business/task/addDoc', obj, function(data){
                if (isJSON(data)) {
                    response = $.parseJSON(data);
                } else {
                    response = data;
                }

                if (data == true) {
                    alert(eblueinfo_script_vars.fav_doc_success);
                } else if (typeof response == 'object') {
                    alert(eblueinfo_script_vars.fav_doc_exists);
                } else {
                    alert(eblueinfo_script_vars.fav_doc_error);
                }
            });
        }
    });
});

/* Ajax Plugin Data */
$(function () {
    $(document).on('mousedown', '.btn-ajax', function(){
        var docid = $(this).data('docid');
        $.ajax({ 
            type: "POST",
            url: eblueinfo_script_vars.ajaxurl,
            data:{
                action:'update_document_views',
                docid: docid
            },
            // success:function(result){ console.log(result); },
            // error:function(error){ console.log(error); }
        });
    });
});

/* Filter by information source */
$('select.info-source').on( "change", function(e) {
    var _is    = $(this).val();
    var _mt    = getUrlParam('mt') ? getUrlParam('mt') : '';
    var col    = getUrlParam('collection') ? getUrlParam('collection') : '';
    var com    = getUrlParam('community') ? getUrlParam('community') : '';
    var origin = window.location.origin;
    var path   = window.location.pathname;
    var href   = origin+path+'?community='+com+'&collection='+col+'&is='+_is+'&mt='+_mt;

    window.location.href = href;
});

/* Filter by media type */
$('select.media-type').on( "change", function(e) {
    var _mt    = $(this).val();
    var _is    = getUrlParam('is') ? getUrlParam('is') : '';
    var col    = getUrlParam('collection') ? getUrlParam('collection') : '';
    var com    = getUrlParam('community') ? getUrlParam('community') : '';
    var origin = window.location.origin;
    var path   = window.location.pathname;
    var href   = origin+path+'?community='+com+'&collection='+col+'&is='+_is+'&mt='+_mt;

    window.location.href = href;
});