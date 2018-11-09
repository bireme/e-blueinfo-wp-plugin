if (navigator.userAgent.indexOf('gonative') > -1) {
  var json = [
                {
                  "label": "About",
                  "grouping": "[grouping]",
                  "isGrouping": true,
                  "isSubmenu": false,
                  "subLinks": [
                    {
                      "url": "http://sites.teste.bvsalud.org/e-blueinfo/about-en/",
                      "label": "Why e-BlueInfo?",
                      "subLinks": []
                    },
                    {
                      "url": "http://sites.teste.bvsalud.org/e-blueinfo/supporters-en/",
                      "label": "Institutional Supporters",
                      "subLinks": []
                    }
                  ]
                },
                {
                  "label": "Help",
                  "grouping": "[grouping]",
                  "isGrouping": true,
                  "isSubmenu": false,
                  "subLinks": [
                    {
                      "url": "http://sites.teste.bvsalud.org/e-blueinfo/pdf-en/",
                      "label": "How to improve the readability of PDF files",
                      "subLinks": []
                    },
                    {
                      "url": "http://feedback.bireme.org/feedback/e-blueinfo?version=2.10-77&site=app&lang=en",
                      "label": "Leave comment",
                      "subLinks": []
                    },
                    {
                      "url": "http://feedback.bireme.org/feedback/e-blueinfo?version=2.10-77&error=1&site=app&lang=en",
                      "label": "Report error",
                      "subLinks": []
                    }
                  ]
                },
                {
                  "label": "Service Terms",
                  "grouping": "[grouping]",
                  "isGrouping": true,
                  "isSubmenu": false,
                  "subLinks": [
                    {
                      "url": "http://politicas.bireme.org/terminos/en/",
                      "label": "Terms of Use",
                      "subLinks": []
                    },
                    {
                      "url": "http://politicas.bireme.org/privacidad/en/",
                      "label": "Privacy Policy",
                      "subLinks": []
                    }
                  ]
                }
              ];

  var items = JSON.stringify(json);

  window.location.href='gonative://sidebar/setItems?items=' + encodeURIComponent(items);
}
