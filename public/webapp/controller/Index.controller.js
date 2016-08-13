sap.ui.define([
	"com/mlauffer/gotmoneyappui5/controller/BaseController"
], function (BaseController) {
	"use strict";

	return BaseController.extend("com.mlauffer.gotmoneyappui5.controller.Index", {
		/* =========================================================== */
		/* lifecycle methods                                           */
		/* =========================================================== */


		/* =========================================================== */
		/* event handlers                                              */
		/* =========================================================== */

		onPrivacy: function() {
			this.getRouter().navTo("privacy");
		},

		onTerms: function() {
			this.getRouter().navTo("terms");
		},

		onAbout: function() {
			this.getRouter().navTo("about");
		}



		/* =========================================================== */
		/* begin: internal methods                                     */
		/* =========================================================== */

	});
});
