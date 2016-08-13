sap.ui.define([
    "sap/ui/core/mvc/Controller",
    "sap/ui/core/routing/History",
    "sap/m/MessageBox"
], function (Controller, History, MessageBox) {
    "use strict";

    return Controller.extend("com.mlauffer.gotmoneyappui5.controller.BaseController", {
        /**
         * Convenience method for accessing the router in every controller of the application.
         * @public
         * @returns {sap.ui.core.routing.Router} the router for this component
         */
        getRouter: function () {
            return this.getOwnerComponent().getRouter();
            //return sap.ui.core.UIComponent.getRouterFor(this);
        },


        /**
         * Convenience method for getting the resource bundle.
         * @public
         * @returns {sap.ui.model.resource.ResourceModel} the resourceModel of the component
         */
        getResourceBundle: function () {
            return this.getOwnerComponent().getModel("i18n").getResourceBundle();
        },


        /**
         * Event handler  for navigating back.
         * It checks if there is a history entry. If yes, history.go(-1) will happen.
         * If not, it will replace the current entry of the browser history with the master route.
         * @public
         */
        onNavBack: function () {
            var sPreviousHash = History.getInstance().getPreviousHash();
            if (sPreviousHash !== undefined) {
                // The history contains a previous entry
                history.go(-1);
            } else {
                // Otherwise we go backwards with a forward history
                var bReplace = true;
                this.getRouter().navTo("home", {}, bReplace);
            }
        },


        /**
         * Convenience method for getting the last part of a Binding Path.
         * @public
         * @returns {string} the last part of a Binding Path
         */
        onFinishBackendOperation: function () {
            this.getView().getModel().updateBindings();
            this.onNavBack();
        },


        /**
         * Convenience method for getting the AJAX base URL
         * @public
         * @returns {string} AJAX base URL
         */
        getAjaxBaseURL: function () {
            return "../../../";
        },


        /**
         * Convenience method for getting the last part of a Binding Path.
         * @public
         * @returns {string} the last part of a Binding Path
         */
        extractIdFromPath: function (sPath) {
            return sPath.slice(sPath.lastIndexOf("/") + 1);
        },


        _ajaxFail: function (oResult, textStatus, jqXHR) {
            var sText = this.getResourceBundle().getText("Error.internalServerError");
            //var sDetail = this.getResourceBundle().getText("Error.noDetails");
            try {
                if (oResult.responseJSON.messageCode) {
                    sText = oResult.responseJSON.messageCode;
                }

                //TODO
                /*if (oResult.responseJSON.messageCode = "Error.invalidInput") {
                 sDetail = oResult.responseJSON.message;
                 //MessageBox.error(this.getResourceBundle().getText(sText) + "\n\n", { details : sDetail });
                 } else {
                 MessageBox.error(this.getResourceBundle().getText(sText));
                 }*/

            } catch (e) {
                console.dir(e);
                console.dir(oResult);
            }

            MessageBox.error(this.getResourceBundle().getText(sText));

            this.getView().setBusy(false);
            try {
                if (oResult.responseJSON.messageCode === "Error.userNotLoggedIn") {
                    this.getRouter().navTo("index");
                }
            } catch (e) {
                console.dir(e);
            }
        },


        /**
         * Convenience method for logging actions in the system.
         * @public
         */
        saveLog: function (sType, sText) {
            //TODO
            // do something, i.e. send usage statistics to backend
            // in order to improve our app and the user experience (Build-Measure-Learn cycle)
            //console.dir($(window.location).attr('href'));
            //console.dir($(window.location).attr('hash'));
            sType = sType.toUpperCase();
            //var sUser = '';
            var sURL = $(window.location).attr('href').toString();
            var sLogMessage = sType + ": " + sText + ". User accessed route " + sURL + ", timestamp = " + $.now();
            switch (sType) {
                case 'E':
                    jQuery.sap.log.error(sLogMessage);
                    break;
                case 'I':
                    jQuery.sap.log.info(sLogMessage);
                    break;
                case 'S':
                    jQuery.sap.log.info(sLogMessage);
                    break;
                case 'W':
                    jQuery.sap.log.warning(sLogMessage);
                    break;
                default:
                    jQuery.sap.log.info(sLogMessage);
                    break;
            }
        },

        checkUserConnected: function (bRedirect) {
            var that = this;
            var connected = false;
            $.ajax({
                url: this.getAjaxBaseURL() + "session/" + $.now(),
                async: false,
                contentType: 'application/json',
                dataType: 'json',
                method: 'GET'
            })
            .success(function () {
                connected = true;
            })
            .error(function (jqXHR, textStatus, errorThrown) {
                connected = false;
                if (bRedirect) {
                    MessageBox.error(that.getResourceBundle().getText("Error.userNotConnected"), {
                        onClose: function (sAction) {
                            that.getRouter().navTo("index");
                        }
                    });
                }
            });
            return connected;
        },

        _loadBackendData: function () {
            var baseAjaxURL = this.getAjaxBaseURL();
            var that = this;
            var bError = false;
            var oModel = this.getView().getModel();
            $.ajax({
                //url: baseAjaxURL + "user/00000001407174213418",
                url: baseAjaxURL + "user/" + $.now(),
                contentType: 'application/json',
                dataType: 'json',
                method: 'GET',
                async: false
            })
            .success(function (response, textStatus, jqXHR) {//success(function(oResult) {
                var oData = {
                    User: response[0]
                };
                oData.User.Account = [];
                oData.User.Category = [];
                oData.User.Transaction = [];
                oModel.setData(oData);
                oModel.updateBindings();
            })
            .error(function (jqXHR, textStatus, errorThrown) {
                bError = true;
                that._ajaxFail(jqXHR, textStatus, errorThrown);
            });

            if (bError) {
                return;
            }

            $.ajax({
                url: baseAjaxURL + "category/",
                contentType: 'application/json',
                dataType: 'json',
                method: 'GET'
            })
            .success(function (response, textStatus, jqXHR) {//success(function(oResult) {
                oModel.getData().User.Category = response;
                oModel.updateBindings();
            })
            .error(function (jqXHR, textStatus, errorThrown) {
                bError = true;
                that._ajaxFail(jqXHR, textStatus, errorThrown);
            });

            $.ajax({
                url: baseAjaxURL + "account/",
                contentType: 'application/json',
                dataType: 'json',
                method: 'GET'
            })
            .success(function (response, textStatus, jqXHR) {//success(function(oResult) {
                oModel.getData().User.Account = response;
                oModel.updateBindings();
            })
            .error(function (jqXHR, textStatus, errorThrown) {
                bError = true;
                that._ajaxFail(jqXHR, textStatus, errorThrown);
            });

            $.ajax({
                url: baseAjaxURL + "transactions/",
                contentType: 'application/json',
                dataType: 'json',
                method: 'GET'
            })
            .success(function (response, textStatus, jqXHR) {//success(function(oResult) {
                oModel.getData().User.Transaction = response;
                oModel.updateBindings();
            })
            .error(function (jqXHR, textStatus, errorThrown) {
                bError = true;
                that._ajaxFail(jqXHR, textStatus, errorThrown);
            });

            // Account Types Model
            var url = baseAjaxURL + "accounttype/";
            this.getView().getModel("accTypes").loadData(url, {}, true, "GET", false, true);

        }
    });
});
