sap.ui.define([
    "sap/m/MessageBox",
    "com/mlauffer/gotmoneyappui5/controller/BaseController",
    "com/mlauffer/gotmoneyappui5/model/formatter"
], function (MessageBox, BaseController, formatter) {
    "use strict";

    return BaseController.extend("com.mlauffer.gotmoneyappui5.controller.AccountList", {
        formatter: formatter,

        /* =========================================================== */
        /* lifecycle methods                                           */
        /* =========================================================== */
        onInit: function () {
            try {
                this.getView().addEventDelegate({
                    onAfterShow: function () {
                        this.checkUserConnected(true);
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

        onItemPress: function (oEvent) {
            this.getRouter().navTo("account", {
                accountId: this.extractIdFromPath(oEvent.getSource().getBindingContext().getPath())
            });
        },


        onAddNew: function () {
            this.getRouter().navTo("accountNew");
        }

        /* =========================================================== */
        /* begin: internal methods                                     */
        /* =========================================================== */
    });
});
