sap.ui.define([
"sap/m/MessageBox",
"sap/ui/model/Filter",
"sap/ui/model/FilterOperator",
"com/mlauffer/gotmoneyappui5/controller/BaseController",
"com/mlauffer/gotmoneyappui5/model/formatter"
], function (MessageBox, Filter, FilterOperator, BaseController, formatter) {
	"use strict";

	return BaseController.extend("com.mlauffer.gotmoneyappui5.controller.TransactionListLate", {
		formatter : formatter,

		/* =========================================================== */
		/* lifecycle methods                                           */
		/* =========================================================== */
		onInit: function() {
			try {
				this.getView().addEventDelegate({
					onAfterShow: function() {
						this.checkUserConnected(true);
						this._setFilterByYearMonth(new Date());
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

        /**
		 * Event handler when a table item gets pressed
		 * @param {sap.ui.base.Event} oEvent the table selectionChange event
		 * @public
		 */
        onItemPress: function(oEvent) {
			this.getRouter().navTo("transaction", {
				transactionId : this.extractIdFromPath(oEvent.getSource().getBindingContext().getPath())
			});
		},


        /**
		 * Triggered by the table's 'updateFinished' event: after new table
		 * data is available, this handler method updates the table counter.
		 * This should only happen if the update was successful, which is
		 * why this handler is attached to 'updateFinished' and not to the
		 * table's list binding's 'dataReceived' method.
		 * @param {sap.ui.base.Event} oEvent the update finished event
		 * @public
		 */
		onUpdateFinished: function(oEvent) {
			var sTitle,
				oTable = oEvent.getSource(),
				iTotalItems = oEvent.getParameter("total");
			// only update the counter if the length is final and
			// the table is not empty
			if (iTotalItems && oTable.getBinding("items").isLengthFinal()) {
				sTitle = this.getResourceBundle().getText("Transaction.count", [iTotalItems]);
			} else {
				sTitle = this.getResourceBundle().getText("Transaction.count");
			}
			this.getView().byId("countTitle").setText(sTitle);
		},


		onAddNew: function() {
			this.getRouter().navTo("transactionNew");
		},

		/* =========================================================== */
		/* begin: internal methods                                     */
		/* =========================================================== */

		_setFilterByYearMonth : function(oDateTo) {
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
