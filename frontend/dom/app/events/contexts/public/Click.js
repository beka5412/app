Events.Contexts.Public.Click = class Click extends EventClick {
    context = 'public';
    domain = siteUrl();

    onSubmit() {
        console.log('clickei2 ' + global.version);
    }
};