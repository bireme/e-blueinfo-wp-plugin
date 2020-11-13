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
                    "url": site + "/app",
                    "label": "Communities",
                    "subLinks": []
                  },
                  {
                    "label": "About",
                    "grouping": "[grouping]",
                    "isGrouping": true,
                    "isSubmenu": false,
                    "subLinks": [
                      {
                        "url": site + "/about-en/",
                        "label": "Why e-BlueInfo?",
                        "subLinks": []
                      },
                      {
                        "url": site + "/supporters-en/",
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
                        "url": "https://bvsalud.org/contact_us/",
                        "label": "Contact",
                        "subLinks": []
                      },
                      {
                        "url": "https://e-blueinfo.bvsalud.org/en/tutorial-en/",
                        "label": "Tutorial",
                        "subLinks": []
                      }
                    ]
                  },
                  {
                    "label": "Language",
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
      "BR": "Brazil",
      "SV": "El Salvador",
      "GT": "Guatemala",
      "PE": "Peru"
    };
    var c_pages = {
      "BR": 'https://e-blueinfo.bvsalud.org/en/data-from-brazil/',
      "SV": 'https://e-blueinfo.bvsalud.org/en/data-from-el-salvador/',
      "GT": 'https://e-blueinfo.bvsalud.org/en/data-from-guatemala/',
      "PE": 'https://e-blueinfo.bvsalud.org/en/data-from-peru/'
    };

    if ( 'oc' == country ) {
      var _json = [
                    {
                      "url": site + "/app/country",
                      "label": "Country",
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
                        "url": site + "/app/favorites",
                        "label": "Favorites",
                        "subLinks": []
                      },
                      {
                        "url": site + "/app/visited",
                        "label": "Visited",
                        "subLinks": []
                      },
                      {
                        "label": "Country (" + c_name[cc] + ")",
                        "grouping": "[grouping]",
                        "isGrouping": true,
                        "isSubmenu": false,
                        "subLinks": [
                          {
                            "url": c_pages[cc],
                            "label": "See more",
                            "subLinks": []
                          },
                          {
                            "url": site + "/app/country",
                            "label": "Change country",
                            "subLinks": []
                          }
                        ]
                      }
                    ];
      } else {
        var _json = [
                      {
                        "url": site + "/app/auth",
                        "label": "Login",
                        "subLinks": []
                      },
                      {
                        "label": "Country (" + c_name[cc] + ")",
                        "grouping": "[grouping]",
                        "isGrouping": true,
                        "isSubmenu": false,
                        "subLinks": [
                          {
                            "url": c_pages[cc],
                            "label": "See more",
                            "subLinks": []
                          },
                          {
                            "url": site + "/app/country",
                            "label": "Change country",
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
