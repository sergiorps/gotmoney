sap.ui.define([
    "sap/m/MessageBox",
    "sap/m/MessageToast",
    "sap/ui/core/Fragment",
    "sap/ui/core/ValueState",
    "com/mlauffer/gotmoneyappui5/controller/BaseController",
    "com/mlauffer/gotmoneyappui5/controller/FacebookLogin",
    "com/mlauffer/gotmoneyappui5/controller/GoogleLogin"
], function (MessageBox, MessageToast, Fragment, ValueState, BaseController, FacebookLogin, GoogleLogin) {
    "use strict";

    return BaseController.extend("com.mlauffer.gotmoneyappui5.controller.App", {
        _oDialogLogin: null,
        _oDialogRecovery: null,
        _oGoogleLogin: null,


        /* =========================================================== */
        /* lifecycle methods                                           */
        /* =========================================================== */

        onInit: function () {
            var oRouter = this.getRouter();
            oRouter.attachBypassed(function (oEvent) {
                var sHash = oEvent.getParameter("hash");
                // do something here, i.e. send logging data to the backend for analysis
                // telling what resource the user tried to access...
                jQuery.sap.log.info("Sorry, but the hash '" + sHash + "' is invalid.", "The resource was not found.");
                this.saveLog("E", "notFound");
            }, this);
            /*oRouter.attachRouteMatched(function(oEvent) {
             var sRouteName = oEvent.getParameter("name");
             // do something, i.e. send usage statistics to backend
             // in order to improve our app and the user experience (Build-Measure-Learn cycle)
             jQuery.sap.log.info("User accessed route " + sRouteName + ", timestamp = " + new Date().getTime());
             this.saveLog("I", sRouteName);
             }, this);*/

            sap.ui.getCore().getMessageManager().registerObject(this.getView(), true);
        },

        onAfterRendering: function () {
            if (this.checkUserConnected()) {
                this._loadBackendData();
                this._toogleButtonsVisible();
            }
        },

        /**
         * The component is destroyed by UI5 automatically.
         * @public
         * @override
         */
        destroy: function () {
            if (this._oDialogLogin !== null) {
                this._oDialogLogin.destroy();
            }
            if (this._oDialogRecovery !== null) {
                this._oDialogRecovery.destroy();
            }
        },


        /* =========================================================== */
        /* event handlers                                              */
        /* =========================================================== */

        onPressHome: function () {
            this.getRouter().navTo("home");
        },

        onPressIndex: function () {
            this.getRouter().navTo("index");
        },

        onPressMenu: function () {
            this._toogleShellOverlay();
        },

        onClosedShellOverlay: function () {
            this._toogleShellOverlay();
        },

        onUserItemPressed: function () {
            this.getRouter().navTo("profile");
        },

        onAccount: function () {
            this._toogleShellOverlay();
            this.getRouter().navTo("accountList");
        },

        onCategory: function () {
            this._toogleShellOverlay();
            this.getRouter().navTo("categoryList");
        },

        onTransaction: function () {
            this._toogleShellOverlay();
            this.getRouter().navTo("transactionList");
        },

        onTransactionLate: function () {
            this._toogleShellOverlay();
            this.getRouter().navTo("transactionListLate");
        },

        onReport: function () {
            //TODO:
        },

        onProfile: function (oEvent) {
            this._toogleShellOverlay();
            this.getRouter().navTo("profile");
        },

        onLogin: function () {
            if (!this._oDialogLogin) {
                this._oDialogLogin = sap.ui.xmlfragment("Login", "com.mlauffer.gotmoneyappui5.view.Login", this);
                this.getView().addDependent(this._oDialogLogin);
            }
            this._oDialogLogin.open();

            var oGoogleLogin = new GoogleLogin();
            oGoogleLogin.renderButton(this, Fragment.byId("Login", "btGoogle").getDomRef().id);
        },


        onSystemLogin: function () {
            var that = this;
            var mPayload = {
                login: "system",
                email: Fragment.byId("Login", "email").getValue(),
                passwd: Fragment.byId("Login", "pwd").getValue()
            };

            $.ajax({
                url: this.getAjaxBaseURL() + "session/",
                async: false,
                contentType: 'application/json',
                data: JSON.stringify(mPayload),
                dataType: 'json',
                method: 'POST'
                //username: Fragment.byId("Login", "email").getValue(),
                //password: Fragment.byId("Login", "pwd").getValue()
            })
            .success(function () {
                that._loginDone();
            })
            .error(function (jqXHR, textStatus, errorThrown) {
                that._ajaxFail(jqXHR, textStatus, errorThrown);
            });
        },

        onCloseLogin: function () {
            this._oDialogLogin.close();
        },

        onAfterCloseLogin: function () {
            Fragment.byId("Login", "email").setValue();
            Fragment.byId("Login", "pwd").setValue();
            //oEvent.getSource().destroy();
        },

        onRecovery: function () {
            this.onCloseLogin();
            if (!this._oDialogRecovery) {
                this._oDialogRecovery = sap.ui.xmlfragment("Recovery", "com.mlauffer.gotmoneyappui5.view.Recovery", this);
                this.getView().addDependent(this._oDialogRecovery);
            }
            this._oDialogRecovery.open();
            var email = Fragment.byId("Login", "email").getValue();
            Fragment.byId("Recovery", "email").setValue(email);
        },

        onResetPassword: function () {
            var that = this;
            var mPayload = {
                email: Fragment.byId("Recovery", "email").getValue()
            };
            $.ajax({
                url: this.getAjaxBaseURL() + "session/" + $.now(),
                data: mPayload,
                dataType: 'json',
                method: 'PUT'
            })
            .success(function () {
                MessageBox.show(that.getResourceBundle().getText("Success.passwordRecovery"));
            })
            .error(function (jqXHR, textStatus, errorThrown) {
                that._ajaxFail(jqXHR, textStatus, errorThrown);
            });

            this.onCloseRecovery();
        },


        onCloseRecovery: function () {
            Fragment.byId("Recovery", "email").setValue();
            this._oDialogRecovery.close();
        },

        onAfterCloseRecovery: function () {
            Fragment.byId("Recovery", "email").setValue();
            //oEvent.getSource().destroy();
        },

        onSignup: function () {
            this.getRouter().navTo("signup");
        },

        onLogoff: function () {
            var that = this;
            $.ajax({
                url: this.getAjaxBaseURL() + "session/",
                async: false,
                contentType: 'application/json',
                dataType: 'json',
                method: 'DELETE'
            })
            .success(function () {
                that._logoffDone();
            })
            .error(function (jqXHR, textStatus, errorThrown) {
                that._ajaxFail(jqXHR, textStatus, errorThrown);
            });
        },

        onFacebookLogin: function () {
            var oFacebookLogin = new FacebookLogin();
            oFacebookLogin.login(this);
        },


        /* =========================================================== */
        /* begin: internal methods                                     */
        /* =========================================================== */

        _toogleShellOverlay: function () {
            var oItem = this.getView().byId("btMenu");
            var oShell = this.getView().byId("appUShell");
            var bState = oShell.getShowPane();
            oShell.setShowPane(!bState);
            oItem.setShowMarker(!bState);
            oItem.setSelected(!bState);
        },

        _toogleButtonsVisible: function () {
            var bState = this.checkUserConnected();
            this.getView().byId("btHome").setVisible(bState);
            this.getView().byId("btMenu").setVisible(bState);
            this.getView().byId("btIndex").setVisible(!bState);
            this.getView().byId("btLogin").setVisible(!bState);
            this.getView().byId("btSignup").setVisible(!bState);

            if (bState) {
                this._createShellUserButton();
            } else {
                this._destroyShellUserButton();
            }
        },

        _loginDone: function () {
            this._loadBackendData();
            this._toogleButtonsVisible();
            this.onCloseLogin();
            this.getRouter().navTo("home");
            MessageToast.show(this.getResourceBundle().getText("Success.login"));
        },

        _logoffDone: function () {
            this._toogleShellOverlay();
            this._toogleButtonsVisible();
            this.getRouter().navTo("index");
            MessageToast.show(this.getResourceBundle().getText("Success.logoff"));
        },


        _createShellUserButton: function () {
            var oUser = new sap.ui.unified.ShellHeadUserItem({
                image: "sap-icon://person-placeholder",
                username: "{/User/nome}",
                press: $.proxy(this.onUserItemPressed, this)
            });
            this.getView().byId("appUShell").setUser(oUser);
        },

        _destroyShellUserButton: function () {
            this.getView().byId("appUShell").setUser(null);
        }
    });
});
