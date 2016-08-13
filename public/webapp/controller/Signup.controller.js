sap.ui.define([
	"sap/m/MessageBox",
	"sap/m/MessageToast",
	"sap/ui/model/json/JSONModel",
	"sap/ui/core/ValueState",
	"com/mlauffer/gotmoneyappui5/controller/BaseController",
	"com/mlauffer/gotmoneyappui5/controller/FacebookLogin",
	"com/mlauffer/gotmoneyappui5/controller/GoogleLogin",
	"com/mlauffer/gotmoneyappui5/controller/Validator",
	"com/mlauffer/gotmoneyappui5/controller/ZString",
	"com/mlauffer/gotmoneyappui5/model/ObjectFactory",
	"com/mlauffer/gotmoneyappui5/model/formatter"
], function (MessageBox, MessageToast, JSONModel, ValueState, BaseController, FacebookLogin, GoogleLogin, Validator, ZString, ObjectFactory, formatter) {
	"use strict";

	return BaseController.extend("com.mlauffer.gotmoneyappui5.controller.Signup", {
		formatter: formatter,
		ZString: ZString,

		/* =========================================================== */
		/* lifecycle methods                                           */
		/* =========================================================== */

		onInit: function() {
			var that = this;
			this.getView().setModel(new JSONModel(), "user");

			try {
				var oRouter = this.getRouter();
				oRouter.getRoute("signup").attachMatched(this._onRouteMatchedNew, this);

				this.getView().addEventDelegate({
					onAfterShow: function() {
						var oGoogleLogin = new GoogleLogin();
						oGoogleLogin.renderButton(that, that.getView().byId("btGoogle").getDomRef().id);
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
			} else {
				this._saveNew(oEvent);
			}
			this.getView().setBusy(false);
		},

		onFacebookLogin : function() {
			var oFacebookLogin = new FacebookLogin();
			oFacebookLogin.login(this);
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


		_loginDone : function() {
			this._loadBackendData();
			sap.ui.getCore().byId("__component0---rootApp").getController()._toogleButtonsVisible();
			this.getRouter().navTo("home");
			MessageToast.show(this.getResourceBundle().getText("Success.login"));
		},


		_saveNew: function() {
			var that = this;
			var mPayload = this._getPayload();
			mPayload.iduser = $.now();

			$.ajax({
				url: this.getAjaxBaseURL() + "/user",
				async: false,
				contentType: 'application/json',
				data: JSON.stringify(mPayload),
				dataType: 'json',
				method: 'POST'
			})
			.success(jQuery.proxy(that._newDone(mPayload), this))
			.error(jQuery.proxy(that._ajaxFail, this));
		},


		_newDone : function(mPayload) {
			try {
				this.getView().getModel().getData().User = mPayload;

			} catch (e) {
				this.saveLog('E', e.message);
				MessageBox.error(e.message);
				return;
			}

			//this.onFinishBackendOperation();
			this._loginDone();
			//MessageToast.show(this.getResourceBundle().getText("Success.save"));
			this.getView().setBusy(false);
		},


		_getPayload : function() {
			var oView = this.getView();
			var mPayload = ObjectFactory.buildUser();

			//iduser : null,
			mPayload.email = oView.byId("email").getValue();
			mPayload.passwd = oView.byId("pwd").getValue();
			mPayload.passwdconf = oView.byId("pwdRepeat").getValue();
			mPayload.nome = oView.byId("name").getValue();
			mPayload.sexo = oView.byId("sex").getSelectedKey();
			mPayload.datanascimento = oView.byId("birthdate").getValue();
			mPayload.alert = oView.byId("alert").getState();
			mPayload.tec = oView.byId("terms").getSelected();
			mPayload.captcha = oView.byId("captcha").getValue();
			mPayload.lastchange = $.now();
			//mPayload.lastsync : null
			return mPayload;
		}
	});
});
