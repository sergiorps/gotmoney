sap.ui.define([
    "sap/ui/core/UIComponent",
    "sap/ui/Device",
    "sap/ui/model/json/JSONModel"
], function(UIComponent, Device, JSONModel) {
    "use strict";

    return UIComponent.extend("com.mlauffer.gotmoneyappui5.Component", {

        metadata: {
            manifest: "json"
        },


        /**
         * The component is initialized by UI5 automatically during the startup of the app and calls the init method once.
         * @public
         * @override
         */
        init: function() {
            // call the base component's init function
            UIComponent.prototype.init.apply(this, arguments);

            this.setModels();

            // create the views based on the url/hash
            this.getRouter().initialize();
        },


        /**
         * The component is destroyed by UI5 automatically.
         * @public
         * @override
         */
        destroy: function() {
            // call the base component's destroy function
            UIComponent.prototype.destroy.apply(this, arguments);
        },


        setModels : function() {
            // set invoice model - local
            //var oConfig = this.getMetadata().getConfig();
            //var sNamespace = this.getMetadata().getManifestEntry("sap.app").id;

            // Main model
            //var oModel = new JSONModel(jQuery.sap.getModulePath(sNamespace, oConfig.appData));
            var oModel = new JSONModel();
            oModel.setDefaultBindingMode(sap.ui.model.BindingMode.OneWay);
            oModel.iSizeLimit = 1000;
            this.setModel(oModel);

            // Account Types Model
            oModel = new JSONModel();
            oModel.setDefaultBindingMode(sap.ui.model.BindingMode.OneWay);
            //var url = "../../../accounttyperest/";
            //oModel.loadData(url, {}, true, "GET", false, true);
            this.setModel(oModel, "accTypes");

            // set the device model
            oModel = new JSONModel(Device);
            oModel.setDefaultBindingMode(sap.ui.model.BindingMode.OneWay);
            this.setModel(oModel, "device");
        }
    });
});