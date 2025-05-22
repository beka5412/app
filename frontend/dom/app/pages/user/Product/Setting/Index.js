App.User.Product.Setting.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Product.Setting.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/product/settings/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    productSettingsOnSubmit(element, instance, methodName, ev) {
        let form = document.querySelector('.frm_edit_product_tab_settings');
        
        let pixEnabled = form.querySelector('#pmPix');
        let creditCardEnabled = form.querySelector('#pmCreditCard');
        let billetEnabled = form.querySelector('#pmBillet');
        let pixDiscountEnabled = form.querySelector('#pixDiscountEnabled');
        let creditCardDiscountEnabled = form.querySelector('#creditCardDiscountEnabled');
        let billetDiscountEnabled = form.querySelector('#billetDiscountEnabled');
        let pixDiscountAmount = form.querySelector('#pixDiscountAmount');
        let creditCardDiscountAmount = form.querySelector('#creditCardDiscountAmount');
        let billetDiscountAmount = form.querySelector('#billetDiscountAmount');
        let installmentsQtySelect = form.querySelector('#installmentsQtySelect');
        let pixThanksPageEnabled = form.querySelector('#pixThanksPageEnabled');
        let pixThanksPageURL = form.querySelector('#pixThanksPageURL');
        let creditCardThanksPageEnabled = form.querySelector('#creditCardThanksPageEnabled');
        let creditCardThanksPageURL = form.querySelector('#creditCardThanksPageURL');
        let billetThanksPageEnabled = form.querySelector('#billetThanksPageEnabled');
        let billetThanksPageURL = form.querySelector('#billetThanksPageURL');
        
        let data = {
            pix_enabled: pixEnabled?.checked ? true : false,
            credit_card_enabled: creditCardEnabled?.checked ? true : false,
            billet_enabled: billetEnabled?.checked ? true : false,
            pix_discount_enabled: pixDiscountEnabled?.checked ? true : false,
            credit_card_discount_enabled: creditCardDiscountEnabled?.checked ? true : false,
            billet_discount_enabled: billetDiscountEnabled?.checked ? true : false,
            pix_discount_amount: currencyToNumber(pixDiscountAmount?.value || ''),
            credit_card_discount_amount: currencyToNumber(creditCardDiscountAmount?.value || ''),
            billet_discount_amount: currencyToNumber(billetDiscountAmount?.value || ''),
            max_installments: installmentsQtySelect?.value || '',
            pix_thanks_page_enabled: pixThanksPageEnabled?.checked ? true : false,
            pix_thanks_page_url: pixThanksPageURL?.value || '',
            credit_card_thanks_page_enabled: creditCardThanksPageEnabled?.checked ? true : false,
            credit_card_thanks_page_url: creditCardThanksPageURL?.value || '',
            billet_thanks_page_enabled: billetThanksPageEnabled?.checked ? true : false,
            billet_thanks_page_url: billetThanksPageURL?.value || ''
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;

        fetch(`/ajax/actions/user/product/${id}/edit-settings`, options).then(response => response.json()).then(body => {
            toast(body.message);  
            // if (body?.status == 'success') {
            //     let link = new Link;
            //     link.to(siteUrl() + '/products');          
            // }
        });
    }
};