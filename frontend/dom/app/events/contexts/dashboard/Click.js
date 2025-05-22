Events.Contexts.Dashboard.Click = class Click extends EventClick {
    context = 'dashboard';
    domain = siteUrl();

    onSubmit() {
        console.log('clickei1 ' + global.version);
    }

    reset() {
        let form = document.getElementById('filter-form')
        form.reset();
    }
};