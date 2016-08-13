sap.ui.define([
    "sap/m/MessageBox",
    "sap/m/MessageToast",
    "sap/ui/model/json/JSONModel",
    "com/mlauffer/gotmoneyappui5/controller/BaseController",
    "com/mlauffer/gotmoneyappui5/controller/Validator",
    "com/mlauffer/gotmoneyappui5/model/ObjectFactory"
], function (MessageBox, MessageToast, JSONModel, BaseController, Validator, ObjectFactory) {
    "use strict";

    return BaseController.extend("com.mlauffer.gotmoneyappui5.controller.Account", {
        /* =========================================================== */
        /* lifecycle methods                                           */
        /* =========================================================== */

        onInit: function () {
            this.getView().setModel(new JSONModel(), "account");

            try {
                var oRouter = this.getRouter();
                oRouter.getRoute("account").attachMatched(this._onRouteMatched, this);
                oRouter.getRoute("accountNew").attachMatched(this._onRouteMatchedNew, this);

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

        onSave: function (oEvent) {
            // Create new validator instance
            var oValidator = new Validator();
            // Validate input fields
            oValidator.validate(this.getView().byId("accountForm"));
            if (oValidator.isValid() === false) {
                return;
            }

            this.getView().setBusy(true);
            if (this.getView().getViewName() === "com.mlauffer.gotmoneyappui5.view.Account") {
                this._saveEdit(oEvent);
            } else {
                this._saveNew(oEvent);
            }
            this.getView().setBusy(false);
        },


        onDelete: function (oEvent) {
            var that = this;
            var sPath = oEvent.getSource().getBindingContext().getPath();
            MessageBox.confirm(that.getResourceBundle().getText("Delete.message"), function (sAction) {
                if (MessageBox.Action.OK === sAction) {
                    that.getView().setBusy(true);
                    var oModel = that.getView().getModel();

                    $.ajax({
                        url: that.getAjaxBaseURL() + "account/" + oModel.getData().User.Account[that.extractIdFromPath(sPath)].idconta,
                        async: false,
                        contentType: 'application/json',
                        dataType: 'json',
                        method: 'DELETE'
                    })
                    .success(jQuery.proxy(that._deleteDone(sPath), this))
                    .error(jQuery.proxy(that._ajaxFail, this));
                }
            }, that.getResourceBundle().getText("Delete.title"));
        },


        onChangeType: function () {
            this._setInvoicedayVisibility();
        },


        /* =========================================================== */
        /* begin: internal methods                                     */
        /* =========================================================== */

        _onRouteMatched: function (oEvent) {
            var sObjectPath = "/User/Account/" + oEvent.getParameter("arguments").accountId;
            this._bindView(sObjectPath);
            this._setInvoicedayVisibility();
        },


        _onRouteMatchedNew: function () {
            this.getView().getModel("account").setData(ObjectFactory.buildAccount());
            //this.getView().getModel("account").refresh(true);
            this._setInvoicedayVisibility();
        },


        _bindView: function (sPath) {
            var oView = this.getView();
            oView.unbindElement();
            oView.bindElement({
                path: sPath,
                events: {
                    change: this._onBindingChange.bind(this),
                    dataRequested: function (oEvent) {
                        console.log("dataRequested");
                        oView.setBusy(true);
                    },
                    dataReceived: function (oEvent) {
                        console.log("dataReceived");
                        oView.setBusy(false);
                    }
                }
            });
        },


        _onBindingChange: function () {
            // No data for the binding
            if (!this.getView().getBindingContext()) {
                this.getRouter().getTargets().display("notFound");
            }
        },


        _saveNew: function () {
            var that = this;
            var mPayload = this._getPayload();
            mPayload.idconta = $.now();

            $.ajax({
                url: this.getAjaxBaseURL() + "account",
                async: false,
                contentType: 'application/json',
                data: JSON.stringify(mPayload),
                dataType: 'json',
                method: 'POST'
            })
            .success(jQuery.proxy(that._newDone(mPayload), this))
            .error(jQuery.proxy(that._ajaxFail, this));
        },


        _saveEdit: function (oEvent) {
            var that = this;
            var oContext = oEvent.getSource().getBindingContext();
            var oModel = this.getView().getModel();
            var mPayload = this._getPayload();
            mPayload.idconta = oModel.getProperty("idconta", oContext);

            $.ajax({
                url: this.getAjaxBaseURL() + "account/" + mPayload.idconta,
                //contentType: ,
                async: false,
                data: mPayload,
                dataType: 'json',
                method: 'PUT'
            })
            .success(jQuery.proxy(that._editDone(mPayload, oContext), this))
            .error(jQuery.proxy(that._ajaxFail, this));
        },


        _newDone: function (mPayload) {
            try {
                this.getView().getModel().getData().User.Account.push(mPayload);

            } catch (e) {
                this.saveLog('E', e.message);
                MessageBox.error(e.message);
                return;
            }

            this.onFinishBackendOperation();
            this.getView().setBusy(false);
            MessageToast.show(this.getResourceBundle().getText("Success.save"));
        },


        _editDone: function (mPayload, oContext) {
            var oModel = this.getView().getModel();
            try {
                oModel.setProperty("idtipo", mPayload.idtipo, oContext);
                oModel.setProperty("descricao", mPayload.descricao, oContext);
                //TODO: mPayload.balance = 1;
                oModel.setProperty("dataabertura", mPayload.dataabertura, oContext);
                oModel.setProperty("limitecredito", mPayload.limitecredito, oContext);
                oModel.setProperty("diafatura", mPayload.diafatura, oContext);

            } catch (e) {
                this.saveLog('E', e.message);
                MessageBox.error(e.message);
                return;
            }

            this.onFinishBackendOperation();
            MessageToast.show(this.getResourceBundle().getText("Success.save"));
            this.getView().setBusy(false);
        },


        _deleteDone: function (sPath) {
            try {
                this.getView().getModel().getData().User.Account.splice(this.extractIdFromPath(sPath), 1);

            } catch (e) {
                this.saveLog('E', e.message);
                MessageBox.error(e.message);
                return;
            }

            this.onFinishBackendOperation();
            MessageToast.show(this.getResourceBundle().getText("Success.delete"));
            this.getView().setBusy(false);
        },


        _getPayload: function () {
            var oView = this.getView();

            // Format values
            var fNumber;
            fNumber = oView.byId("creditlimit").getValue();
            oView.byId("creditlimit").setValue(parseFloat(fNumber).toFixed(2));
            //fNumber = parseInt(oView.byId("invoiceday").getValue());
            //fNumber = ($.isNumeric(fNumber)) ? fNumber : null;
            //oView.byId("invoiceday").setValue(fNumber);

            var mPayload = ObjectFactory.buildAccount();
            mPayload.idtipo = oView.byId("idtype").getSelectedKey();
            mPayload.descricao = oView.byId("description").getValue();
            mPayload.limitecredito = oView.byId("creditlimit").getValue();
            mPayload.saldo = 1;
            mPayload.dataabertura = oView.byId("opendate").getValue();
            mPayload.lastchange = $.now();
            mPayload.diafatura = oView.byId("invoiceday").getValue();
            //mPayload.diafatura = ($.isNumeric(mPayload.diafatura)) ? mPayload.diafatura : null;
            if (!$.isNumeric(mPayload.diafatura) || mPayload.diafatura == 0) {
                mPayload.diafatura = null;
            }
            return mPayload;
        },


        _setInvoicedayVisibility: function () {
            var bShow = (this.getView().byId("idtype").getSelectedKey() === "002") ? true : false;
            this.getView().byId("invoiceday").setVisible(bShow);
            this.getView().byId("invoicedayLabel").setVisible(bShow);
        }
    });
});
