// App.User.Profile = class Profile extends Page {
//     context = 'dashboard';
//     title = 'Perfil';
//     className = 'App.User.Profile';

//     view(loaded, link) {
//         console.log('to');
//         return super.find(`${link?.full?'full/':''}user/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
//     }

//     profileOnSubmit() {
//         alert('TESTE PERFIL');
//     }
// };