sap.ui.define([
	"sap/m/MessageBox",
	"sap/ui/model/Filter",
	"sap/ui/model/FilterOperator",
	"com/mlauffer/gotmoneyappui5/controller/BaseController",
	"com/mlauffer/gotmoneyappui5/model/formatter"
], function (MessageBox, Filter, FilterOperator, BaseController, formatter) {
	"use strict";

	return BaseController.extend("com.mlauffer.gotmoneyappui5.controller.TransactionList", {
		formatter : formatter,

		/* =========================================================== */
		/* lifecycle methods                                           */
		/* =========================================================== */
		onInit: function() {
			try {
				this.getView().addEventDelegate({
					onAfterShow: function() {
						this.checkUserConnected(true);
						//this._setFilterByYearMonth(new Date());
					}
				}, this);

			} catch (e) {
				this.saveLog('E', e.message);
				MessageBox.error(e.message);
			}
		},

		onAfterRendering : function() {
			//this._setFilterByYearMonth(new Date());
			var oDataRange = new sap.ui.unified.DateRange({ startDate : new Date()});
			this.getView().byId("calendar").addSelectedDate(oDataRange);
			this._setFilterByYearMonth(new Date());
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


		onAddNew: function() {
			this.getRouter().navTo("transactionNew");
		},


		onSelectDate : function(oEvent) {
			this._setFilterByYearMonth(oEvent.getSource().getSelectedDates()[0].getStartDate());
		},



		/**
		 * Triggered by the table's 'updateStarted' event: after new table
		 * data is available, this handler method updates the table counter.
		 * This should only happen if the update was successful, which is
		 * why this handler is attached to 'updateFinished' and not to the
		 * table's list binding's 'dataReceived' method.
		 * @param {sap.ui.base.Event} oEvent the update finished event
		 * @public
		 */
		onUpdateStarted: function(oEvent) {
			oEvent.getSource().setBusy(true);
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
			oTable.setBusy(false);
		},


		/* =========================================================== */
		/* begin: internal methods                                     */
		/* =========================================================== */

		_setFilterByYearMonth : function(oDateFrom) {
			this.getView().byId("transactionTable").setBusy(true);
			oDateFrom.setDate(1); //Define day
			var oDateTo = new Date(oDateFrom.getFullYear(), (oDateFrom.getMonth() + 1), 0);
			var aFilters = [];
			var oFilter = new Filter("datavencimento", FilterOperator.BT, formatter.dateMySQLFormat(oDateFrom), formatter.dateMySQLFormat(oDateTo));
			aFilters.push(oFilter);
			this.getView().byId("transactionTable").getBinding("items").filter(aFilters);
			this.getView().byId("transactionTable").setBusy(false);
		}
	});
});
