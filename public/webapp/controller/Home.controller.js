sap.ui.define([
    "sap/m/MessageBox",
    "sap/m/MessageToast",
    "sap/ui/model/Filter",
    "sap/ui/model/FilterOperator",
    "sap/ui/core/Fragment",
    "sap/ui/core/ValueState",
    "com/mlauffer/gotmoneyappui5/controller/BaseController"
], function (MessageBox, MessageToast, Filter, FilterOperator, Fragment, ValueState, BaseController) {
    "use strict";

    return BaseController.extend("com.mlauffer.gotmoneyappui5.controller.Home", {
        /* =========================================================== */
        /* lifecycle methods                                           */
        /* =========================================================== */
        onInit: function () {
            try {
                this.getView().addEventDelegate({
                    onAfterShow: function () {
                        this.checkUserConnected(true);
                        this._getTitleTotalItems();
                    }
                }, this);

            } catch (e) {
                this.saveLog('E', e.message);
                MessageBox.error(e.message);
            }
        },

        /* =========================================================== */
        /* event handlers                                              */
        /* =========================================================== */

        onAccount: function () {
            this.getRouter().navTo("accountList");
        },

        onCategory: function () {
            this.getRouter().navTo("categoryList");
        },

        onTransaction: function () {
            this.getRouter().navTo("transactionList");
        },

        onTransactionLate: function () {
            this.getRouter().navTo("transactionListLate");
        },

        onReport: function () {
            //TODO:
        },

        onProfile: function () {
            this.getRouter().navTo("profile");
        },


        /* =========================================================== */
        /* begin: internal methods                                     */
        /* =========================================================== */

        _getTitleTotalItems: function () {
            var oView = this.getView();
            var oData = oView.getModel().getData();

            if (oData) {
                try {
                    //TODO : Late
                    oView.byId("accountTile").setValue(oData.User.Account.length);
                    oView.byId("categoryTile").setValue(oData.User.Category.length);

                    //this._getTransactionLateItems();
                    //oView.byId("transactionLateTile").setValue(12);
                } catch (e) {
                    console.dir(e);
                }
            }
        },

        _getTransactionLateItems: function () {
            var oDateTo = new Date();
            this.getView().byId("transactionTable").setBusy(true);
            var aFilters = [];
            var oFilter = new Filter("datavencimento", FilterOperator.LT, formatter.dateMySQLFormat(oDateTo));
            aFilters.push(oFilter);
            oFilter = new Filter("idstatus", FilterOperator.EQ, "000");
            aFilters.push(oFilter);
            this.getView().byId("transactionTable").getBinding("items").filter(aFilters);
            this.getView().byId("transactionTable").setBusy(false);
        }

    });
});
