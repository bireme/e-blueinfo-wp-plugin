function is_webview () {
    var userAgent = navigator.userAgent.toLowerCase(),
    wv = /wv/.test( userAgent ),
    gonative = /gonative/.test( userAgent ),
    safari = /safari/.test( userAgent ),
    ios = /iphone|ipod|ipad|macintosh/.test( userAgent );

    if ( ios ) {
        if ( safari ) {
            return false;
        } else {
            return true;
        }
    } else {
        if ( wv || gonative ) {
            return true;
        } else {
            return false;
        }
    }
}

$(function () {
  if ( is_webview() ) {
    var site = "https://e-blueinfo.bvsalud.org";
    var app_site = "https://sites.bvsalud.org/e-blueinfo";
    var country = $.cookie("e-blueinfo-country");
    var userData = $.cookie("userData");
    var json = [
                  {
                    "url": app_site + "/es/app",
                    "label": "Contenidos",
                    "subLinks": []
                  },
                  {
                    "label": "Acerca",
                    "grouping": "[grouping]",
                    "isGrouping": true,
                    "isSubmenu": false,
                    "subLinks": [
                      {
                        "url": site + "/es/por-que-e-blueinfo-es/",
                        "label": "¿Por qué e-BlueInfo?",
                        "subLinks": []
                      },
                      {
                        "url": site + "/es/apoyadores-institucionales/",
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
                        "url": app_site + "/es/app/contact",
                        "label": "Contacto",
                        "subLinks": []
                      },
                      // {
                      //   "url": site + "/es/tutoriales/",
                      //   "label": "Tutorial",
                      //   "subLinks": []
                      // }
                    ]
                  },
                  {
                    "label": "Idioma",
                    "grouping": "[grouping]",
                    "isGrouping": true,
                    "isSubmenu": false,
                    "subLinks": [
                      {
                        "url": app_site + "/pt/app?fcl=true",
                        "label": "Português",
                        "subLinks": []
                      },
                      {
                        "url": app_site + "/es/app?fcl=true",
                        "label": "Español",
                        "subLinks": []
                      },
                      {
                        "url": app_site + "/app?fcl=true",
                        "label": "English",
                        "subLinks": []
                      }
                    ]
                  }
                ];

    var cc = eblueinfo_script_vars.cc;
    var c_name = {
      "BR": "Brasil",
      "SV": "El Salvador",
      "GT": "Guatemala",
      "PE": "Perú"
    };
    var c_pages = {
      "BR": site + '/es/datos-de-brasil/',
      "SV": site + '/es/datos-de-el-salvador/',
      "GT": site + '/es/datos-de-guatemala/',
      "PE": site + '/es/datos-de-peru/'
    };

    if ( 'oc' == country ) {
      var _json = [
                    {
                      "url": app_site + "/es/app/country",
                      "label": "País",
                      "subLinks": []
                    }
                  ];
    } else {
      if ( userData ) {
        _app_site = app_site.replace(/\/?$/, '/');
        var _json = [
                      {
                        "url": "https://platserv.bvsalud.org/client/controller/logout/control/business/origin/"+btoa(_app_site),
                        "label": "Logout",
                        "subLinks": []
                      },
                      {
                        "url": app_site + "/es/app/favorites",
                        "label": "Favoritos",
                        "subLinks": []
                      },
                      {
                        "url": app_site + "/es/app/visited",
                        "label": "Visitados",
                        "subLinks": []
                      }
                    ];

        if ( '224' == country ) { // PAHO/WHO Guidelines
          var c_menu = [
                      {
                        "url": app_site + "/es/app/country",
                        "label": "País",
                        "subLinks": []
                      }
                    ];
        } else {
          var c_menu = [
                      {
                        "label": "País (" + c_name[cc] + ")",
                        "grouping": "[grouping]",
                        "isGrouping": true,
                        "isSubmenu": false,
                        "subLinks": [
                          {
                            "url": c_pages[cc],
                            "label": "Conozca más",
                            "subLinks": []
                          },
                          {
                            "url": app_site + "/es/app/country",
                            "label": "Cambiar país",
                            "subLinks": []
                          }
                        ]
                      }
                    ];
        }

        _json = _json.concat(c_menu);
      } else {
        var _json = [
                      {
                        "url": app_site + "/es/app/auth",
                        "label": "Login",
                        "subLinks": []
                      }
                    ];

        if ( '224' == country ) { // PAHO/WHO Guidelines
          var c_menu = [
                      {
                        "url": app_site + "/es/app/country",
                        "label": "País",
                        "subLinks": []
                      }
                    ];
        } else {
          var c_menu = [
                      {
                        "label": "País (" + c_name[cc] + ")",
                        "grouping": "[grouping]",
                        "isGrouping": true,
                        "isSubmenu": false,
                        "subLinks": [
                          {
                            "url": c_pages[cc],
                            "label": "Conozca más",
                            "subLinks": []
                          },
                          {
                            "url": app_site + "/es/app/country",
                            "label": "Cambiar país",
                            "subLinks": []
                          }
                        ]
                      }
                    ];
        }

        _json = _json.concat(c_menu);
      }
    }

    _json = _json.concat(json);

    var items = JSON.stringify(_json);

    window.location.href='gonative://sidebar/setItems?items=' + encodeURIComponent(items);
  }
});
