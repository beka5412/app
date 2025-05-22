App.User.Product.Affiliation.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Product.Affiliation.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/product/affiliation/${this?.constructor?.name||''}${this?.queryString||''}`, loaded, 
        () => this.coloringMenu('.menu_product_edit', 'affiliation'));
    }

    affOnSubmit() {
        let form = document.querySelector('.frm_edit_affiliation');
        let enabled = form.querySelector('.inp_aff_enabled');
        let aff_payment_type = form.querySelector('.inp_aff_payment_type');
        let comission = form.querySelector('.inp_comission');
        let marketplace_enabled = form.querySelector('.inp_marketplace_enabled');
        let inp_cookie_duration = form.querySelector('.inp_cookie_duration');

        let cookie_mode = '';
        if (cookieModeFirstClick.checked) cookie_mode = 'first_click';
        if (cookieModeLastClick.checked) cookie_mode = 'last_click';

        let data = {
            enabled: enabled?.checked ? 1 : 0,
            marketplace_enabled: marketplace_enabled?.checked ? 1 : 0,
            aff_payment_type: aff_payment_type?.checked ? 1 : 0,
            comission: comission.value,
            cookie_mode,
            cookie_duration: Number(inp_cookie_duration.value) * 24 * 60 * 60
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;
        fetch(`/ajax/actions/user/product/${id}/affiliation/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);  
            // if (body?.status == 'success') {
            //     let link = new Link;
            //     link.to(siteUrl() + `/product/${product_id}/pixels`);          
            // }
        });
    }

    percentOrPriceOnChange(element) {
        let label_comission_type = document.querySelector('.label_comission_type');
        let span_comission = document.querySelector('.span_comission');
        span_comission.innerHTML = element.checked ? 'porcentagem' : 'preço';
        label_comission_type.innerHTML = element.checked ? 'Porcentagem' : 'Preço';
        this.re();
    }

    getComission() {
        return currencyToNumber(document.querySelector('.inp_comission')?.value);
    }

    calcAffComission() {
        $ = this;

        let isPercent = commission_type.checked;

        $.sellerComissionWithoutAff = $.price - $.fees;

        $.comission = (
            isPercent
                ? ($.price - $.fees) * ($.getComission() / 100) 
                : ( $.getComission() < $.sellerComissionWithoutAff ? $.getComission() : $.sellerComissionWithoutAff ) 
        );

        $.sellerComissionWithAff = $.sellerComissionWithoutAff - $.comission;
        $.affComission = $.comission;
    }
};