sap.ui.define([
    "sap/m/MessageBox",
    "sap/ui/core/mvc/Controller"
], function (MessageBox, Controller) {
    "use strict";

    return Controller.extend("com.mlauffer.gotmoneyappui5.controller.FacebookLogin", {
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


        login: function (oViewController) {
            this._oViewController = oViewController;
            var that = this;

            try {
                FB.login(function (responseLogin) {
                    if (responseLogin.status === 'connected') {
                        var graphAPIQuery = "/" + responseLogin.authResponse.userID + "?fields=email,name,gender";
                        FB.api(graphAPIQuery, function (responseApi) {
                            responseApi.accessToken = responseLogin.authResponse.accessToken;
                            that.onSuccess(responseApi);
                        });
                    } else {
                        that.onFailure();
                    }
                }, {
                    scope: 'public_profile,email'
                });

            } catch (e) {
                this.onFailure();
            }
        },


        onSuccess: function (facebookUser) {
            var that = this;
            var mPayload = {
                login: "facebook",
                iduser: facebookUser.id,
                nome: facebookUser.name,
                sexo: facebookUser.gender,
                email: facebookUser.email,
                token: facebookUser.accessToken.substring(0, 20)
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
            MessageBox.error(this._oViewController.getResourceBundle().getText("Login.facebookError"));
        }
    });
});
