sap.ui.define([
"sap/m/MessageBox",
"sap/m/MessageToast",
"sap/ui/model/json/JSONModel",
"sap/ui/core/ValueState",
"com/mlauffer/gotmoneyappui5/controller/BaseController",
"com/mlauffer/gotmoneyappui5/controller/Validator",
"com/mlauffer/gotmoneyappui5/controller/ZString",
"com/mlauffer/gotmoneyappui5/model/ObjectFactory",
"com/mlauffer/gotmoneyappui5/model/formatter"
], function (MessageBox, MessageToast, JSONModel, ValueState, BaseController, Validator, ZString, ObjectFactory, formatter) {
	"use strict";

	return BaseController.extend("com.mlauffer.gotmoneyappui5.controller.User", {
		formatter: formatter,
		ZString: ZString,

		/* =========================================================== */
		/* lifecycle methods                                           */
		/* =========================================================== */

		onInit: function() {
			this.getView().setModel(new JSONModel(), "user");

			try {
				var oRouter = this.getRouter();
				oRouter.getRoute("profile").attachMatched(this._onRouteMatched, this);
				oRouter.getRoute("signup").attachMatched(this._onRouteMatchedNew, this);

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
			var oView = this.getView();
			// Create new validator instance
			var oValidator = new Validator();

			// Validate input fields
			oValidator.validate(oView.byId("userForm"));
			if (oValidator.isValid() === false) {
				return;
			}

			if (oView.byId("pwd").getValue()) {
				if (oView.byId("pwd").getValue() === oView.byId("pwdRepeat").getValue()) {
					oView.byId("pwd").setValueState(ValueState.None);
					oView.byId("pwdRepeat").setValueState(ValueState.None);
				} else {
					oView.byId("pwd").setValueState(ValueState.Error);
					oView.byId("pwdRepeat").setValueState(ValueState.Error);
					MessageToast.show(this.getResourceBundle().getText("Error.passwordNotEqual"));
					return;
				}
			}

			this.getView().setBusy(true);
			if (this.getView().getViewName() === "com.mlauffer.gotmoneyappui5.view.User") {
				this._saveEdit(oEvent);
			}
			else {
				this._saveNew(oEvent);
			}
			this.getView().setBusy(false);
		},


		/* =========================================================== */
		/* begin: internal methods                                     */
		/* =========================================================== */

		_onRouteMatched: function() {
			var sObjectPath = "/User/";
			this._bindView(sObjectPath);
		},


		_onRouteMatchedNew: function() {
			this.getView().getModel("user").setData(ObjectFactory.buildUser());
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
			var oView = this.getView();
			var mPayload = this._getPayload();
			mPayload.iduser = $.now();
			mPayload.tec = oView.byId("terms").getSelected();
			mPayload.captcha = oView.byId("captcha").getValue();

			try {
				//oView.getModel().getData().User.push(mPayload);
				console.dir(mPayload);

			} catch (e) {
				this.saveLog('E', e.message);
				MessageBox.error(e.message);
				return;
			}

			MessageToast.show(this.getResourceBundle().getText("Success.save"));
			oView.getModel().updateBindings();
			this.onNavBack();
		},


		_saveEdit: function(oEvent) {
			var that = this;
			var oContext = oEvent.getSource().getBindingContext();
			var oModel = this.getView().getModel();
			var mPayload = this._getPayload();
			mPayload.iduser = oModel.getProperty("iduser", oContext);

			$.ajax({
				url: this.getAjaxBaseURL() + "user/" + mPayload.iduser,
				async: false,
				data: mPayload,
				dataType: 'json',
				method: 'PUT'
			})
			.success(jQuery.proxy(that._editDone(mPayload, oContext), this))
			.error(jQuery.proxy(that._ajaxFail, this));

			/*try {
				oModel.setProperty("nome", mPayload.nome, oContext);
				oModel.setProperty("sexo", mPayload.sexo, oContext);
				oModel.setProperty("datanascimento", mPayload.datanascimento, oContext);
				oModel.setProperty("alert", mPayload.alert, oContext);

			} catch (e) {
				this.saveLog('E', e.message);
				MessageBox.error(e.message);
				return;
			}

			MessageToast.show(this.getResourceBundle().getText("Success.save"));
			oModel.updateBindings();
			this.onNavBack();*/
		},


		_newDone : function(mPayload) {
			try {
				//this.getView().getModel().getData().User.Category.push(mPayload);

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
				oModel.setProperty("nome", mPayload.nome, oContext);
				oModel.setProperty("sexo", mPayload.sexo, oContext);
				oModel.setProperty("datanascimento", mPayload.datanascimento, oContext);
				oModel.setProperty("alert", mPayload.alert, oContext);

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
			//TODO
			try {
				this.getView().getModel().getData().User.Category.splice(this.extractIdFromPath(sPath), 1);

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
			var mPayload = ObjectFactory.buildUser();

			//iduser : null,
			mPayload.email = oView.byId("email").getValue();
			mPayload.passwdold = oView.byId("pwdOld").getValue();
			mPayload.passwd = oView.byId("pwd").getValue();
			mPayload.passwdconf = oView.byId("pwdRepeat").getValue();
			mPayload.nome = oView.byId("name").getValue();
			mPayload.sexo = oView.byId("sex").getSelectedKey();
			mPayload.datanascimento = oView.byId("birthdate").getValue();
			mPayload.alert = oView.byId("alert").getState();
			mPayload.lastchange = $.now();
			//mPayload.lastsync : null
			return mPayload;
		}
	});
});
