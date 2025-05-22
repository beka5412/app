class EventClick {
    on(methodName) {
        let instance = this;
        let context = instance.context;
        $('body').on('click', `[click="${methodName}"]`, function(ev) {
            if (contextIs(context) && url() == instance?.domain) instance[methodName](this, instance, methodName);
        });
    }
}