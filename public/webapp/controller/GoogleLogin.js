sap.ui.define([
    "sap/m/MessageBox",
    "sap/ui/core/mvc/Controller"
], function (MessageBox, Controller) {
    "use strict";

    return Controller.extend("com.mlauffer.gotmoneyappui5.controller.GoogleLogin", {
        _oViewController: null,

        /**
         * The component is destroyed by UI5 automatically.
         * @public
         * @override
         */
        destroy: function () {
            if (this._oViewController !== null) {
                this._oViewController.destroy();
            }
        },

        renderButton: function (oViewController, idButton) {
            this._oViewController = oViewController;

            /*gapi.signin2.render('Login--btGoogle', {
             'scope': 'profile email',
             'width': 240,
             'height': 40,
             'longtitle': true,
             'theme': 'dark',
             'onsuccess': jQuery.proxy(this.onSuccess, this),
             'onfailure': jQuery.proxy(this.onFailure, this)
             });*/

            if (Google.auth2) {
                Google.auth2.attachClickHandler(idButton, {},
                    jQuery.proxy(this.onSuccess, this),
                    jQuery.proxy(this.onFailure, this)
                );
            } else {
                //this.onFailure();
            }
        },

        onSuccess: function (googleUser) {
            var that = this;
            var mPayload = {
                login: "google",
                iduser: googleUser.getBasicProfile().getId(),
                nome: googleUser.getBasicProfile().getName(),
                email: googleUser.getBasicProfile().getEmail(),
                token: googleUser.getAuthResponse().access_token
            };

            $.ajax({
                url: this._oViewController.getAjaxBaseURL() + "session/",
                async: false,
                contentType: 'application/json',
                data: JSON.stringify(mPayload),
                dataType: 'json',
                method: 'POST'
            })
            .success(function (response, textStatus, jqXHR) {
                that._oViewController._loginDone();
            })
            .error(function (jqXHR, textStatus, errorThrown) {
                that._oViewController._ajaxFail(jqXHR, textStatus, errorThrown);
            });
        },


        onFailure: function () {
            MessageBox.error(this._oViewController.getResourceBundle().getText("Login.googleError"));
        }
    });
});
