var $ = jQuery;

function change_count(elem) {
    var form = document.searchForm;
    form.count.value = elem.value;
    $("#searchForm").submit();
}

function change_format(elem) {
    var form = document.searchForm;
    form.format.value = elem.value;
    $("#searchForm").submit();
}

function change_sort(obj){
    var sort = obj.options[obj.selectedIndex].value;
    var form = document.searchForm;
    form.sort.value = sort;
    $("#searchForm").submit();
}

function showHideFilters(){
	$('#filters').toggle();
}

function animateMenu(obj) {
    obj.classList.toggle("change");
}

function show_more_list(){
	$('.more-items a').click(function() {
		var element = $(this).parent().prev().children('.hide');
		if ( element.length ) {
			element.each(function( index ) {
				if ( index < 5 ) {
  				$(this).removeClass('hide');
        }
        else {
          return false;
        }
			});

      var el = $(this).parent().prev().children('.hide');

      if ( !el.length ) {
        $(this).parent().hide();
      }
		}
	});
}

function remove_filter(id) {
    // remove hidden field
    $("#"+id).remove();
    var filter = '';

    $('.apply_filter').each(function(i){
        filter += this.value + ' AND ';
    });
    // remove last AND of string
    filter = filter.replace(/\sAND\s$/, "");

    $('#filter').val(filter);
    $("#formFilters").submit();
}

function setCookie(name,value,days) {
    var expires = "";

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    } else {
        var date = new Date();
        date.setFullYear(date.getFullYear()+10); // 10 years
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

function change_code_text(sel){
   $("#labelCode span").text(sel.options[sel.selectedIndex].text);
}

function isJSON(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }

    return true;
}

var cookieList = function(cookieName) {
// When the cookie is saved the items will be a comma seperated string
// So we will split the cookie by comma to get the original array
var cookie = $.cookie(cookieName);
// Load the items or a new array if null.
var items = cookie ? cookie.split(/,/) : new Array();
var args = { path: '/', expires: 365 * 10 };

// Return a object that we can use to access the array
// While hiding direct access to the declared items array
return {
    "add": function(val) {
        indx = items.indexOf(val);
        if(indx==-1){
            if(items.length==10) items.shift();
            // Add to the items.
            items.push(val);
            // Save the items to a cookie.
            $.cookie(cookieName, items.join(','), args);
        }
    },
    "remove": function (val) { 
        indx = items.indexOf(val);
        if(indx!=-1) items.splice(indx, 1);
        $.cookie(cookieName, items.join(','), args);        },
    "clear": function() {
        items = null;
        // Clear the cookie.
        $.cookie(cookieName, null);
    },
    "items": function() {
        // Get all the items.
        return items;
    }
  }
}