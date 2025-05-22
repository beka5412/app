App.User.Product.Upsell.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Product.Upsell.Index';

    view(loaded, link) {
        return super.find(`${link?.full ? 'full/' : ''}user/product/upsell/${this?.constructor?.name || ''}${this?.queryString || ''}`, loaded,
            () => this.coloringMenu('.menu_product_edit', 'upsell'));
    }
    
    productCopyUpsellSnippet(element, instance, methodName, ev) {
        element.select();
        document.execCommand('copy');
        toast('Snippet copiado.');
    }
    
    productUpsellOnSubmit() {
        const form = document.querySelector('.frm_edit_upsell');
        const has_upsell = form.querySelector('.inp_has_upsell');
        const upsell_link = form.querySelector('.inp_upsell_link');
        const has_upsell_rejection = form.querySelector('.inp_has_upsell_rejection');
        const upsell_text = form.querySelector('.inp_upsell_text');
        const upsell_rejection_link = form.querySelector('.inp_upsell_rejection_link');
        const upsell_rejection_text = form.querySelector('.inp_upsell_rejection_text');
        
        const data = {
            has_upsell: has_upsell?.checked ? 1 : 0,
            upsell_link: upsell_link?.value || '',
            has_upsell_rejection: has_upsell_rejection?.checked ? 1 : 0,
            upsell_text: upsell_text?.value || '',
            upsell_rejection_link: upsell_rejection_link?.value || '',
            upsell_rejection_text: upsell_rejection_text?.value || ''
        };

        const options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        const id = params().id;
        fetch(`/ajax/actions/user/product/${id}/upsell/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);
        });
    }

    productUpsellGenerateSnippet() {
        const {
            template,
            accept_button,
            reject_button
        } = tagJSON('snippet');
        
        const snippet = document.querySelector('.inp_snippet');
        const has_upsell_rejection = document.querySelector('.inp_has_upsell_rejection');
        const accept_text = document.querySelector('.inp_upsell_text');
        const rejection_text = document.querySelector('.inp_upsell_rejection_text');
        const rejection_link = document.querySelector('.inp_upsell_rejection_link');

        let html = template;
        html = html.replace('[accept_button]', accept_button);
        html = html.replace('[reject_button]', has_upsell_rejection.checked ? reject_button : '');
        html = html.replace('[accept_text]', accept_text?.value || '');
        html = html.replace('[reject_text]', rejection_text?.value || '');
        html = html.replace('[rejection_link]', rejection_link?.value || '');
        
        snippet.value = html;
    }
};
