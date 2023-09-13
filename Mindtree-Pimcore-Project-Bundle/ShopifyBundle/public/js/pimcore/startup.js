pimcore.registerNS("pimcore.plugin.ShopifyBundle");

pimcore.plugin.ShopifyBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // alert("ShopifyBundle ready!");
    }
});

var ShopifyBundlePlugin = new pimcore.plugin.ShopifyBundle();
