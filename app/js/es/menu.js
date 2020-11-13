function is_webview () {
    var userAgent = navigator.userAgent.toLowerCase(),
    wv = /wv/.test( userAgent ),
    safari = /safari/.test( userAgent ),
    ios = /iphone|ipod|ipad|macintosh/.test( userAgent );

    if ( ios ) {
        if ( safari ) {
            return false;
        } else {
            return true;
        }
    } else {
        if ( wv ) {
            return true;
        } else {
            return false;
        }
    }
}

$(function () {
  if ( is_webview() ) {
    var site = "http://sites.bvsalud.org/e-blueinfo";
    var country = $.cookie("e-blueinfo-country");
    var userData = $.cookie("userData");
    var json = [
                  {
                    "url": site + "es/app",
                    "label": "Comunidades",
                    "subLinks": []
                  },
                  {
                    "label": "Acerca",
                    "grouping": "[grouping]",
                    "isGrouping": true,
                    "isSubmenu": false,
                    "subLinks": [
                      {
                        "url": site + "/about-es/",
                        "label": "¿Por qué e-BlueInfo?",
                        "subLinks": []
                      },
                      {
                        "url": site + "/supporters-es/",
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
                        "url": "https://bvsalud.org/contactenos/",
                        "label": "Contacto",
                        "subLinks": []
                      },
                      {
                        "url": "https://e-blueinfo.bvsalud.org/es/tutorial-es/",
                        "label": "Tutorial",
                        "subLinks": []
                      }
                    ]
                  },
                  {
                    "label": "Idioma",
                    "grouping": "[grouping]",
                    "isGrouping": true,
                    "isSubmenu": false,
                    "subLinks": [
                      {
                        "url": site + "/pt/app?fcl=true",
                        "label": "Português",
                        "subLinks": []
                      },
                      {
                        "url": site + "/es/app?fcl=true",
                        "label": "Español",
                        "subLinks": []
                      },
                      {
                        "url": site + "/app?fcl=true",
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
      "BR": 'https://e-blueinfo.bvsalud.org/es/datos-de-brasil/',
      "SV": 'https://e-blueinfo.bvsalud.org/es/datos-de-el-salvador/',
      "GT": 'https://e-blueinfo.bvsalud.org/es/datos-de-guatemala/',
      "PE": 'https://e-blueinfo.bvsalud.org/es/datos-de-peru/'
    };

    if ( 'oc' == country ) {
      var _json = [
                    {
                      "url": site + "es/app/country",
                      "label": "País",
                      "subLinks": []
                    }
                  ];
    } else {
      if ( userData ) {
        _site = site.replace(/\/?$/, '/');
        var _json = [
                      {
                        "url": "https://platserv.bvsalud.org/client/controller/logout/control/business/origin/"+btoa(_site),
                        "label": "Logout",
                        "subLinks": []
                      },
                      {
                        "url": site + "es/app/favorites",
                        "label": "Favoritos",
                        "subLinks": []
                      },
                      {
                        "url": site + "es/app/visited",
                        "label": "Visitados",
                        "subLinks": []
                      },
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
                            "url": site + "es/app/country",
                            "label": "Cambiar país",
                            "subLinks": []
                          }
                        ]
                      }
                    ];
      } else {
        var _json = [
                      {
                        "url": site + "es/app/auth",
                        "label": "Login",
                        "subLinks": []
                      },
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
                            "url": site + "es/app/country",
                            "label": "Cambiar país",
                            "subLinks": []
                          }
                        ]
                      }
                    ];
      }
    }

    _json = _json.concat(json);

    var items = JSON.stringify(_json);

    window.location.href='gonative://sidebar/setItems?items=' + encodeURIComponent(items);
  }
});
