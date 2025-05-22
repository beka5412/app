App.User.Sample = class Sample extends Page {
    context = 'dashboard';
    title = 'Dashboard';

    view(loaded) {
        return super.find(`user/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    sampleOnSubmit() {
        alert('enviar1');
    }
};