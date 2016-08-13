sap.ui.define([
"sap/m/MessageBox",
"sap/m/MessageToast",
"sap/ui/model/json/JSONModel",
"com/mlauffer/gotmoneyappui5/controller/BaseController",
"com/mlauffer/gotmoneyappui5/controller/Validator",
"com/mlauffer/gotmoneyappui5/model/ObjectFactory",
"com/mlauffer/gotmoneyappui5/model/formatter"
], function (MessageBox, MessageToast, JSONModel, BaseController, Validator, ObjectFactory, formatter) {
	"use strict";

	return BaseController.extend("com.mlauffer.gotmoneyappui5.controller.Transaction", {
		formatter : formatter,

		/* =========================================================== */
		/* lifecycle methods                                           */
		/* =========================================================== */

		onInit: function() {
			this.getView().setModel(new JSONModel(), "transaction");

			try {
				var oRouter = this.getRouter();
				oRouter.getRoute("transaction").attachMatched(this._onRouteMatched, this);
				oRouter.getRoute("transactionNew").attachMatched(this._onRouteMatchedNew, this);

				this.getView().addEventDelegate({
					onAfterShow: function() {
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

		onSave: function(oEvent) {
            // Create new validator instance
			var oValidator = new Validator();

			// Validate input fields
			oValidator.validate(this.getView().byId("transactionForm"));
			if (oValidator.isValid() === false) {
				return;
			}

			this.getView().setBusy(true);
			if (this.getView().getViewName() === "com.mlauffer.gotmoneyappui5.view.Transaction") {
				this._saveEdit(oEvent);
			}
			else {
				this._saveNew(oEvent);
			}
			this.getView().setBusy(false);
		},


		onDelete : function(oEvent) {
			var that = this;
			var sPath = oEvent.getSource().getBindingContext().getPath();
			MessageBox.confirm(that.getResourceBundle().getText("Delete.message"), function(sAction) {
				if (MessageBox.Action.OK === sAction) {
					that.getView().setBusy(true);
					var oModel = that.getView().getModel();

					$.ajax({
						url: that.getAjaxBaseURL() + "transactions/" + oModel.getData().User.Transaction[that.extractIdFromPath(sPath)].idlancamento,
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

		onChangeOccur: function() {
			this._setOccurrenceVisibility();
		},


		/* =========================================================== */
		/* begin: internal methods                                     */
		/* =========================================================== */

		_onRouteMatched: function(oEvent) {
			var sObjectPath = "/User/Transaction/" + oEvent.getParameter("arguments").transactionId;
			this._bindView(sObjectPath);
		},


		_onRouteMatchedNew: function() {
			this.getView().getModel("transaction").setData(ObjectFactory.buildTransaction());
			//this.getView().getModel("transaction").refresh(true);
			//this._setOccurrenceVisibility();
		},


		_bindView : function(sPath) {
			var oView = this.getView();
			oView.unbindElement();
			oView.bindElement({
				path : sPath,
				events : {
					change : this._onBindingChange.bind(this),
					dataRequested : function(oEvent) {
						console.log("dataRequested");
						oView.setBusy(true);
					},
					dataReceived : function(oEvent) {
						console.log("dataReceived");
						oView.setBusy(false);
					}
				}
			});
		},


		_onBindingChange : function() {
			// No data for the binding
			if (!this.getView().getBindingContext()) {
				this.getRouter().getTargets().display("notFound");
			}
		},


		_saveNew: function() {
			//TODO: Validation
			var that = this;
			//var mPayload = this._getPayload();
			//mPayload.idlancamento = $.now();

			var mPayload = this._createRepetition(this.getView().byId("occurrence").getSelectedKey());
			var data = {
				data: mPayload
			};

			$.ajax({
				url: this.getAjaxBaseURL() + "transactions",
				async: false,
				contentType: 'application/json',
				//data: { "data" : mPayload },
				data: JSON.stringify(data),
				dataType: 'json',
				method: 'POST'
			})
			.success(jQuery.proxy(that._newDone(mPayload), this))
			.error(jQuery.proxy(that._ajaxFail, this));
		},


		_saveEdit: function(oEvent) {
			//TODO: Validation
			var that = this;
			var oContext = oEvent.getSource().getBindingContext();
			var oModel = this.getView().getModel();
			var mPayload = this._getPayload();
			mPayload.idlancamento = oModel.getProperty("idlancamento", oContext);

			/*if (this.getView().byId("editrecurrency").getSelected()) {
				mPayload.editrecurrency = true;
				console.log(99999);
			}*/

			$.ajax({
				url: this.getAjaxBaseURL() + "transactions/" + mPayload.idlancamento,
				async: false,
				data: mPayload,
				dataType: 'json',
				method: 'PUT'
			})
			.success(jQuery.proxy(that._editDone(mPayload, oContext), this))
			.error(jQuery.proxy(that._ajaxFail, this));
		},


		_newDone : function(mPayload) {
			var oView = this.getView();

			try {
				$.each(mPayload, function(i, item){
					console.dir(item);
					oView.getModel().getData().User.Transaction.push(item);
				});

			} catch (e) {
				this.saveLog('E', e.message);
				MessageBox.error(e.message);
				return;
			}

			this.onFinishBackendOperation();
			MessageToast.show(this.getResourceBundle().getText("Success.save"));
			this.getView().setBusy(false);
		},


		_editDone : function(mPayload, oContext) {
			var oModel = this.getView().getModel();

			try {
				oModel.setProperty("idconta", mPayload.idconta, oContext);
				oModel.setProperty("tipo", mPayload.tipo, oContext);
				oModel.setProperty("idstatus", mPayload.idstatus, oContext);
				oModel.setProperty("descricao", mPayload.descricao, oContext);
				oModel.setProperty("valor", mPayload.valor, oContext);
				oModel.setProperty("datalancamento", mPayload.datalancamento, oContext);
				oModel.setProperty("datavencimento", mPayload.datavencimento, oContext);

			} catch (e) {
				this.saveLog('E', e.message);
				MessageBox.error(e.message);
				return;
			}

			this.onFinishBackendOperation();
			MessageToast.show(this.getResourceBundle().getText("Success.save"));
			this.getView().setBusy(false);
		},


		_deleteDone : function(sPath) {
			try {
				this.getView().getModel().getData().User.Transaction.splice(this.extractIdFromPath(sPath), 1);

			} catch (e) {
				this.saveLog('E', e.message);
				MessageBox.error(e.message);
				return;
			}

			this.onFinishBackendOperation();
			MessageToast.show(this.getResourceBundle().getText("Success.delete"));
			this.getView().setBusy(false);
		},


		_getPayload : function() {
			var oView = this.getView();

			// Format values
			var fNumber;
			fNumber = oView.byId("amount").getValue();
			oView.byId("amount").setValue(parseFloat(fNumber).toFixed(2));

			var mPayload = ObjectFactory.buildTransaction();
			//idlancamento : null,
			mPayload.idconta = oView.byId("idaccount").getSelectedKey();
			mPayload.idlancamentopai = null;
			mPayload.idstatus = (oView.byId("idstatus").getSelected()) ? "001" : "000";
			mPayload.descricao = oView.byId("description").getValue();
			mPayload.parcela = oView.byId("split").getValue();
			mPayload.valor = oView.byId("amount").getValue();
			mPayload.tipo = oView.byId("type").getSelectedKey();
			mPayload.datalancamento = oView.byId("startdate").getValue();
			//mPayload.datalancamento = formatter.dateMySQLFormat(oView.byId("startdate").getDateValue());
			mPayload.datavencimento = oView.byId("duedate").getValue();
			//mPayload.datavencimento = formatter.dateMySQLFormat(oView.byId("duedate").getDateValue());
			//tag = null,

			mPayload.tag = oView.byId("category").getSelectedKeys();
			return mPayload;
		},

		_setOccurrenceVisibility: function() {
			var oView = this.getView();
			var bShow = (oView.byId("occurrence").getSelectedKey() === "U") ? false : true;

			// Show?
			oView.byId("startdate").setVisible(bShow);
			oView.byId("startdateLabel").setVisible(bShow);
			oView.byId("split").setVisible(bShow);
			oView.byId("splitLabel").setVisible(bShow);

			// Do not show?
			bShow = (bShow) ? false : true;
			oView.byId("duedate").setVisible(bShow);
			oView.byId("duedateLabel").setVisible(bShow);
		},


		_createRepetition : function(sOccurrence) {
			var oView = this.getView();
			var oStartDate = oView.byId("startdate").getDateValue();
			var sSplit = oView.byId("split").getValue() || 1;
			var aPayloads = [];
			var mPayloadReference = this._getPayload();
			var sLastId;
			mPayloadReference.idlancamento = $.now();

			for(var i=0; i < sSplit; i++) {
				var mPayload;
				mPayload = $.extend(true, {}, mPayloadReference);
				var oDueDate = new Date(oStartDate.getFullYear(), oStartDate.getMonth(), oStartDate.getDate());
				switch (sOccurrence) {
				case 'U': //Once
					break;

				case 'D': //Dayly
					oDueDate.setDate(oDueDate.getDate() + i);
					break;

				case 'W': //Weekly
					var iDays = i * 7;
					oDueDate.setDate(oDueDate.getDate() + iDays);
					break;

				case 'M': //Monthly
					oDueDate.setMonth(oDueDate.getMonth() + i);
					break;

				case 'Y': //Annualy
					oDueDate.setFullYear(oDueDate.getFullYear() + i);
					break;

				default:
					break;
				}

				//Set ID
				do {
					mPayload.idlancamento = $.now();
				}
				while (sLastId == $.now());

				sLastId = mPayload.idlancamento;

				//Set parent ID
				if (i > 0) {
					mPayload.idlancamentopai = mPayloadReference.idlancamento;
				} else {
					//Get parent ID
					mPayloadReference.idlancamento = mPayload.idlancamento;
				}

				mPayload.parcela = (i + 1) + "" + "/" + "" + sSplit;
				//mPayload.datavencimento = oDueDate.toJSON().substring(0, 10);
				mPayload.datavencimento = formatter.dateMySQLFormat(oDueDate);
				aPayloads.push(mPayload);
			}

			return aPayloads;
		}
	});
});
