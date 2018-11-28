if (navigator.userAgent.indexOf('gonative') > -1) {
  var json = [
                {
                  "label": "Sobre",
                  "grouping": "[grouping]",
                  "isGrouping": true,
                  "isSubmenu": false,
                  "subLinks": [
                    {
                      "url": "http://sites.bvsalud.org/e-blueinfo/about-es/",
                      "label": "¿Por qué e-BlueInfo?",
                      "subLinks": []
                    },
                    {
                      "url": "http://sites.bvsalud.org/e-blueinfo/supporters-es/",
                      "label": "Apoyadores Institucionales",
                      "subLinks": []
                    }
                  ]
                },
                {
                  "label": "Ayuda",
                  "grouping": "[grouping]",
                  "isGrouping": true,
                  "isSubmenu": false,
                  "subLinks": [
                    {
                      "url": "http://sites.bvsalud.org/e-blueinfo/pdf-es/",
                      "label": "Cómo mejorar la lectura de los archivos PDF",
                      "subLinks": []
                    },
                    {
                      "url": "http://feedback.bireme.org/feedback/e-blueinfo?version=2.10-77&site=app&lang=es",
                      "label": "Enviar comentario",
                      "subLinks": []
                    },
                    {
                      "url": "http://feedback.bireme.org/feedback/e-blueinfo?version=2.10-77&error=1&site=app&lang=es",
                      "label": "Informar error",
                      "subLinks": []
                    }
                  ]
                },
                {
                  "label": "Términos de Servicio",
                  "grouping": "[grouping]",
                  "isGrouping": true,
                  "isSubmenu": false,
                  "subLinks": [
                    {
                      "url": "http://politicas.bireme.org/terminos/es/",
                      "label": "Términos y Condiciones de Uso",
                      "subLinks": []
                    },
                    {
                      "url": "http://politicas.bireme.org/privacidad/es/",
                      "label": "Políticas de Privacidad",
                      "subLinks": []
                    }
                  ]
                },
                {
                  "url": "http://sites.bvsalud.org/e-blueinfo/es/app/country",
                  "label": "Cambiar País",
                  "subLinks": []
                }
              ];

  var items = JSON.stringify(json);

  window.location.href='gonative://sidebar/setItems?items=' + encodeURIComponent(items);
}
