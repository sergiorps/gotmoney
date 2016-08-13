sap.ui.define([], function () {
    "use strict";

    return {
        /**
         * Factory method for User
         *
         * @public
         * @returns {object} User
         */
        buildUser: function () {
            return {
                iduser: null,
                email: null,
                nome: null,
                sexo: null,
                datanascimento: null,
                alert: null,
                lastchange: null,
                facebook: null,
                google: null,
                twitter: null,
                passwdold: null,
                passwd: null,
                passwdconf: null,
                lastsync: null
            };
        },

        /**
         * Factory method for Category
         *
         * @public
         * @returns {object} Category
         */
        buildCategory: function () {
            return {
                idcategoria: null,
                descricao: null,
                origem: "W",
                lastchange: null
            };
        },

        /**
         * Factory method for Account
         *
         * @public
         * @returns {object} Account
         */
        buildAccount: function () {
            return {
                idconta: null,
                idtipo: null,
                descricao: null,
                limitecredito: null,
                saldo: null,
                dataabertura: null,
                diafatura: null,
                origem: "W",
                lastchange: null
            };
        },

        /**
         * Factory method for Account
         *
         * @public
         * @returns {object} Account
         */
        buildTransaction: function () {
            return {
                /*idtransaction : null,
                 idaccount : null,
                 idparent : null,
                 idstatus : null,
                 description : null,
                 split : null,
                 amount : null,
                 type : null,
                 duedate : null,
                 tag : null,
                 origin : "W",
                 lastchange : null*/
                idlancamento: null,
                idconta: null,
                idlancamentopai: null,
                idstatus: null,
                descricao: null,
                parcela: null,
                valor: null,
                tipo: null,
                datalancamento: null,
                datavencimento: null,
                tag: null,
                origem: "W",
                lastchange: null
            };
        }
    };
});