jQuery(function ($) {
    const Scripts = {
        varInit() {
            this.header = $("header");
            this.document = $(document);
        },

        someFunction() {
            console.log('TODO: Don\'t forget to change/delete this code!!!');
        },

        init() {
            Scripts.varInit();
            Scripts.someFunction();
        },
    };

    $(document).ready(function () {
        Scripts.init();
    });
});