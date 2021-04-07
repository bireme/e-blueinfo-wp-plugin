$ = jQuery;

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
    var app_site = "http://sites.bvsalud.org/e-blueinfo";
    var json = [
                  {
                    "label": "About",
                    "grouping": "[grouping]",
                    "isGrouping": true,
                    "isSubmenu": false,
                    "subLinks": [
                      {
                        "url": site + "/en/why-e-blueinfo/",
                        "label": "Why e-BlueInfo?",
                        "subLinks": []
                      },
                      {
                        "url": site + "/en/institutional-supporters/",
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
                        "url": app_site + "/app/contact",
                        "label": "Contact",
                        "subLinks": []
                      },
                      // {
                      //   "url": site + "/en/tutorials/",
                      //   "label": "Tutorial",
                      //   "subLinks": []
                      // }
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

    window.location='gonative://sidebar/setItems?items=' + encodeURIComponent(items);
  }
});
