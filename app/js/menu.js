if (navigator.userAgent.indexOf('gonative') > -1) {
  alert('xxx');
  var json = [
                {
                  "url": memoria_azul_script_vars.home,
                  "label": memoria_azul_script_vars.home_label,
                  "subLinks": []
                },
                {
                  "label": memoria_azul_script_vars.lang_label,
                  "grouping": "[grouping]",
                  "isGrouping": true,
                  "isSubmenu": false,
                  "subLinks": [
                    {
                      "url": memoria_azul_script_vars.languages.pt.url,
                      "label": memoria_azul_script_vars.languages.pt.label,
                      "subLinks": []
                    },
                    {
                      "url": memoria_azul_script_vars.languages.es.url,
                      "label": memoria_azul_script_vars.languages.es.label,
                      "subLinks": []
                    },
                    {
                      "url": memoria_azul_script_vars.languages.en.url,
                      "label": memoria_azul_script_vars.languages.en.label,
                      "subLinks": []
                    }
                  ]
                }
              ];

  var items = JSON.stringify(json);

  window.location.href='gonative://sidebar/setItems?items=' + encodeURIComponent(items);
}
