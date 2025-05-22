App.User.Upsell.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Upsell.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/upsell/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
    
    destroyUpsell(srcElement) {
        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'DELETE'
        };

        let id = srcElement.getAttribute('data-id');
        let row = $(srcElement).parents('.tr')[0];
        [].map.call(row.children, item => item.style.opacity = .5);

        fetch(`/ajax/actions/user/upsell/${id}/destroy`, options).then(response => response.json()).then(body => {
            [].map.call(row.children, item => item.style.opacity = 1);
            if (body?.status == "success") {
                toastSuccess(body.message);
                row.remove();
            }
            else
                toastError('Cupom não encontrado.');
        });
    }

    copyHTML(element, instance, methodName, ev) {
        // let textUpsellCode = document.getElementById('textUpsellCode');
        // // textUpsellCode.value
        // x = `
        // `;
        // console.log(x);
        let textUpsellCode = document.getElementById('textUpsellCode');
        if (!textUpsellCode) return console.error("O elemento #textUpsellCode não existe.");

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'GET',
        };

        let body = {
            accept_text: element.getAttribute('data-accept-text'),
            refuse_text: element.getAttribute('data-refuse-text'),
            accept_page: element.getAttribute('data-accept-page'),
            refuse_page: element.getAttribute('data-refuse-page')
        };

        fetch(`/ajax/actions/user/upsell/template?data=${encodeURIComponent(JSON.stringify(body))}`, options).then(response => response.text()).then(body => {
            textUpsellCode.value = body.trim();
        });
    }

    copyHTMLModal() {
        let textUpsellCode = document.getElementById('textUpsellCode');
        copy(textUpsellCode.value);
        toast('Código copiado!');
    }
}