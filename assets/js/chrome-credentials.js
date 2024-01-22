jQuery( function() {
  "use strict";
  jQuery('.wk-wc-chrome-login').on( 'click', function(e) {

    e.preventDefault();

    if ( wk_cal_ajax_var.enable_disable == 'enable' ) {

      jQuery.ajax( {
        url : wk_cal_ajax_var.url,
        data: {
          'action':'wk_woo_user_logged_in'
        },
        success : function( is_logged_in ) {

          var clientIdValue = wk_cal_ajax_var.client_id;

          if ( is_logged_in == 'no' ) {

            if ( typeof googleyolo != "undefined" ) {

              const retrievePromise = googleyolo.retrieve( {

                supportedAuthMethods: [
                  "https://accounts.google.com",
                  "googleyolo://id-and-password"
                ],
                supportedIdTokenProviders: [
                  {
                    uri: "https://accounts.google.com",
                    clientId: clientIdValue
                  }
                ]

              } );
              retrievePromise.then( ( credential ) => {
                if ( credential.password ) {

                  wkChromeSignInWithEmailAndPassword( credential.id, credential.password );
                  googleyolo.disableAutoSignIn();

                } else {

                  jQuery(this).trigger( e.type );

                }
              }, (error) => {

                window.location.href = wk_cal_ajax_var.redirect_val;

                if ( error.type === 'noCredentialsAvailable' ) {
                  window.location.href = wk_cal_ajax_var.redirect_val;
                }

              });
            }
          } else {
            window.location.href = is_logged_in;
          }
        }
      });
    } else {
      window.location.href = wk_cal_ajax_var.redirect_val;
    }
  } );
} );

function wkChromeSignInWithEmailAndPassword ( username, password ) {
  jQuery.ajax( {
    url     :   wk_cal_ajax_var.url,
    type    :   "POST",
    showLoader: true,
    data: {
      'action' : 'wk_woo_user_login',
      'username' : username,
      'password' : password
    },
    complete:function( error_msg ) {
      console.log( error_msg );
    },
    success : function( WooPro ){
      window.location.href = WooPro;
    }
  } );
}
