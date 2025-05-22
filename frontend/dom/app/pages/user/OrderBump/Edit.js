App.User.OrderBump.Edit = class Edit extends Page {
    context = 'dashboard';
    title = 'Editar Orderbump';
    className = 'App.User.OrderBump.Edit';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/orderbump/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    orderbumpOnSubmit(e) {
        let form = document.querySelector('.frm_edit_orderbump');
        let name = form.querySelector('.inp_orderbump_name');
        let status = form.querySelector('#orderbumpEnabled');
        let product = form.querySelector('.inp_orderbump_product');
        let price = form.querySelector('.inp_orderbump_price');
        let price_promo = form.querySelector('.inp_orderbump_price_promo');
        let text_button = form.querySelector('.inp_orderbump_text_button');
        let title = form.querySelector('.inp_orderbump_title');
        let description = form.querySelector('.inp_orderbump_description');
        let product_as_checkout = form.querySelector('.inp_orderbump_product_as_checkout');
        
        let data = {
            name: name?.value || '',
            status: status?.checked ? '<?php echo \Backend\Enums\Orderbump\EOrderbumpStatus::PUBLISHED->value; ?>' : '<?php echo \Backend\Enums\Orderbump\EOrderbumpStatus::DISABLED->value; ?>',
            product: product?.value || '',
            product_as_checkout: product_as_checkout?.value || '',
            price: currencyToNumber(price?.value || '0'),
            price_promo: currencyToNumber(price_promo?.value || '0'),
            text_button: text_button?.value || '',
            title: title?.value || '',
            description: description?.value || '',
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;

        fetch(`/ajax/actions/user/orderbump/${id}/edit`, options).then(response => response.json()).then(body => {
            toast(body.message);
            console.log(body)
            if (body?.status == 'success') {
                // let link = new Link;
                // link.to(siteUrl() + '/orderbumps');
                window.location.href = `${siteUrl()}/orderbumps`;
            }
        });
    }
};