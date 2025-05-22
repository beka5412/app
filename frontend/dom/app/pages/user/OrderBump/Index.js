App.User.OrderBump.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.OrderBump.Index';

    view(loaded, link) {
        console.log('fdsafds');
        
        return super.find(`${link?.full?'full/':''}user/orderbump/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    orderbumpDestroy(srcElement) {
        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'DELETE'
        };

        let id = srcElement.getAttribute('data-id');
        let row = $(srcElement).parents('.tr')[0];
        [].map.call(row.children, item => item.style.opacity = .5);

        fetch(`/ajax/actions/user/orderbump/${id}/destroy`, options).then(response => response.json()).then(body => {
            [].map.call(row.children, item => item.style.opacity = 1);
            if (body?.status == "success") {
                toastSuccess(body.message);
                row.remove();
            }
            else
                toastError('Produto não encontrado.');
        });
    }
};