App.User.Upsell.Edit = class Edit extends Page {
    context = 'dashboard';
    title = 'Editar upsell';
    className = 'App.User.Upsell.Edit';

    view(loaded, link) {
        console.log('EDIT');
        return super.find(`${link?.full?'full/':''}user/upsell/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    upsellOnSubmit() {
        let form = document.querySelector('.frm_edit_upsell');
        let name = form.querySelector('.inp_name');
        let status = form.querySelector('.inp_status');
        let product = form.querySelector('.inp_product');
        let price_var = form.querySelector('.sel_price_var');
        let accept_redirect = form.querySelector('.sel_accept_redirect');
        let accept_page = form.querySelector('.inp_accept_page');
        let refuse_redirect = form.querySelector('.sel_refuse_redirect');
        let refuse_page = form.querySelector('.inp_refuse_page');
        let accept_text = form.querySelector('.inp_accept_text');
        let refuse_text = form.querySelector('.inp_refuse_text');
        
        let data = {
            name: name?.value || '',
            status: status?.value || '',
            product: product?.value || '',
            price_var: price_var?.value || '',
            accept_redirect: accept_redirect?.value || '',
            accept_page: accept_page?.value || '',
            refuse_redirect: refuse_redirect?.value || '',
            refuse_page: refuse_page?.value || '',
            accept_text: accept_text?.value || '',
            refuse_text: refuse_text?.value || '',
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;

        fetch(`/ajax/actions/user/upsell/${id}/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);  
            if (body?.status == 'success') {
                let link = new Link;
                link.to(siteUrl() + '/upsells');          
            }
        });
    }

    setPriceVariations(element, instance, methodName, ev) {
        let value = element.value;
        [].map.call(element.children, option => {
            let valueAttr = option.value;
            let data = option.hasAttribute('data') ? option.getAttribute('data') : '';
            if (!data) return;
            // let targetElement = document.querySelector(target);
            if (value == valueAttr)
            {
                let priceVarElement = document.querySelector('.sel_price_var');
                priceVarElement.innerHTML = '';
                let json = JSON.parse(data);
                let { links, product } = json;
                priceVarElement.innerHTML += `<option value="0">R$ ${currency(product.price_promo || product.price)} - Principal</option>`;
                if (links?.length) {
                    links.forEach(link => {
                        priceVarElement.innerHTML += `<option value="${link.id}">R$ ${currency(link.amount)} - ${link.slug}</option>`;
                    });
                }
            }
                // targetElement.classList.remove(class_);
        });
    }
};