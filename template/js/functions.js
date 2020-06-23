var $j = jQuery;

function change_count(elem) {
    var form = document.searchForm;
    form.count.value = elem.value;
    $j("#searchForm").submit();
}

function change_format(elem) {
    var form = document.searchForm;
    form.format.value = elem.value;
    $j("#searchForm").submit();
}

function change_sort(obj){
    var sort = obj.options[obj.selectedIndex].value;
    var form = document.searchForm;
    form.sort.value = sort;
    $j("#searchForm").submit();
}

function showHideFilters(){
	$j('#filters').toggle();
}

function animateMenu(obj) {
    obj.classList.toggle("change");
}

function show_more_list(){
	$j('.more-items a').click(function() {
		var element = $j(this).parent().prev().children('.hide');
		if ( element.length ) {
			element.each(function( index ) {
				if ( index < 5 ) {
  				$j(this).removeClass('hide');
        }
        else {
          return false;
        }
			});

      var el = $j(this).parent().prev().children('.hide');

      if ( !el.length ) {
        $j(this).parent().hide();
      }
		}
	});
}

function remove_filter(id) {
    // remove hidden field
    $j("#"+id).remove();
    var filter = '';

    $j('.apply_filter').each(function(i){
        filter += this.value + ' AND ';
    });
    // remove last AND of string
    filter = filter.replace(/\sAND\s$/, "");

    $j('#filter').val(filter);
    $j("#formFilters").submit();
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

function mudar(val){
   $("#labelCode").text('CODE '+val + ' *');
}
