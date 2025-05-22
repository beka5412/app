Events.Contexts.Form.Click = class Click extends EventClick {
    context = 'form';
    domain = siteUrl();

    onSubmit() {
        console.log('clickei2 ' + global.version);
    }

    reset() {
        console.log('teste')
        // let form = document.getElementById('filter-form')
        // form.reset();
    }
};