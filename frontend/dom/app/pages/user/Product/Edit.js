App.User.Product.Edit = class Edit extends Page {
    context = 'dashboard';
    title = 'Editar produto';
    className = 'App.User.Product.Edit';

    // getPaymentMethod(form) {
    //     let payment_method = '';
    //     let btnPaymentMethodCreditCard = form.querySelector('[checkout-radio="payment-method"] input[type="radio"][value="credit_card"]');
    //     let btnPaymentMethodPix = form.querySelector('[checkout-radio="payment-method"] input[type="radio"][value="pix"]');
    //     let btnPaymentMethodBillet = form.querySelector('[checkout-radio="payment-method"] input[type="radio"][value="billet"]');

    //     if (btnPaymentMethodCreditCard?.checked) payment_method = btnPaymentMethodCreditCard.value;
    //     else if (btnPaymentMethodPix?.checked) payment_method = btnPaymentMethodPix.value;
    //     else if (btnPaymentMethodBillet?.checked) payment_method = btnPaymentMethodBillet.value;

    //     return payment_method;
    // }

    view(loaded, link) {
        return super.find(`${link?.full ? 'full/' : ''}user/product/${this?.constructor?.name || ''}${this?.queryString || ''}`, 
            loaded,
            () => this.coloringMenu('.menu_product_edit', 'editProduct'));
    }

    end() {
        global.onloadRoutines.push({
            name: "productEdit", callback: function () {
                let is_free = document.querySelector('.inp_is_free');
                let payment_type = document.querySelector('.inp_payment_type');
                let div_product_price = document.querySelector('.div_product_price');
                let div_product_price_promo = document.querySelector('.div_product_price_promo');

                if (!is_free || !payment_type || !div_product_price || !div_product_price_promo) return;

                if (!is_free.checked && payment_type.value == 'unique') {
                    div_product_price.classList.add('d-block');
                    div_product_price.style.display = 'block';
                    div_product_price.classList.remove('d-none');

                    div_product_price_promo.classList.add('d-block');
                    div_product_price_promo.style.display = 'block';
                    div_product_price_promo.classList.remove('d-none');
                }
                else {
                    div_product_price.classList.add('d-none');
                    div_product_price.classList.remove('d-block');
                    div_product_price.style.display = 'none';

                    div_product_price_promo.classList.add('d-none');
                    div_product_price_promo.classList.remove('d-block');
                    div_product_price_promo.style.display = 'none';
                }
            }
        });
    }

    productOnSubmit(event) {
        const form = document.querySelector('.frm_edit_product');

        let name = form.querySelector('.inp_name');
        let price = form.querySelector('.inp_price');
        let description = form.querySelector('.inp_description');
        let image = form.querySelector('.inp_image');
        let is_free = form.querySelector('.inp_is_free');
        let price_promo = form.querySelector('.inp_price_promo');
        let stock_control = form.querySelector('.inp_stock_control');
        let stock_qty = form.querySelector('.inp_stock_qty');
        let landing_page = form.querySelector('.inp_landing_page');
        let support_email = form.querySelector('.inp_support_email');
        let author = form.querySelector('.inp_author');
        let warranty_time = form.querySelector('.inp_warranty_time');
        let type = form.querySelector('.inp_product_type');
        let delivery = form.querySelector('.inp_product_delivery');
        let payment_type = form.querySelector('.inp_payment_type');
        let category_id = form.querySelector('.inp_category');
        let attachment_url = form.querySelector('.inp_product_attachment_url');
        let attachment_file = form.querySelector('.inp_product_attachment_file');
        let recurrence_period = form.querySelector('.inp_recurrence_period');
        let shipping_cost = form.querySelector('.inp_shipping_cost');
        let has_upsell = form.querySelector('.inp_has_upsell');
        let upsell_link = form.querySelector('.inp_upsell_link');
        let currency = form.querySelector('.inp_currency');
        let lang = form.querySelector('.inp_lang');
        let links = [];
        [].map.call(document.querySelector('.div_product_diff_val').children, row => {
            let slug = row.querySelector('.inp_pd_slug')?.value || '';
            let val = Number(currencyToNumber(row.querySelector('.inp_pd_val')?.value || '0'));
            let qty = parseInt(row.querySelector('.inp_pd_qty')?.value || 0);
            let id = parseInt(row.querySelector('.inp_pd_id')?.value || 0);
            if (val > 0) {
                let obj = { slug, val, qty };
                if (id > 0) obj.id = id;
                links.push(obj);
            }
        });

        let data = {
            name: name?.value || '',
            price: price?.value || '',
            description: description?.value || '',
            image: image?.value || '',
            is_free: is_free?.checked ? 1 : 0,
            price_promo: price_promo?.value || '',
            stock_control: stock_control?.checked ? 1 : 0,
            stock_qty: stock_qty?.value || '',
            landing_page: landing_page?.value || '',
            support_email: support_email?.value || '',
            author: author?.value || '',
            warranty_time: warranty_time?.value || '',
            // type: type?.value || '',
            delivery: delivery?.value || '',
            category_id: category_id?.value || '',
            // payment_type: payment_type?.value || '',           
            attachment_url: attachment_url?.value || '',
            attachment_file: attachment_file?.value || '',
            payment_type: payment_type?.value || '',
            links,
            recurrence_period: recurrence_period?.value || '',
            shipping_cost: shipping_cost?.value || '',
            has_upsell: has_upsell?.checked ? 1 : 0,
            upsell_link: upsell_link?.value || '',
            currency: currency?.value || 'usd',
            lang: lang?.value || 'usd'
        };

        let options = {
            headers: { 'Content-Type': 'application/json', 'Client-Name': 'Action' },
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id;

        fetch(`/ajax/actions/user/product/${id}/edit`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                window.location.href = siteUrl() + '/products'
            }
            else toastError(body.message)
        });
    }

    productDestroy(srcElement) {
        let id = srcElement.getAttribute('data-id');

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'DELETE'
        };

        fetch(`/ajax/actions/user/product/${id}/destroy`, options).then(response => response.json()).then(body => {
            if (body?.status == "success") {
                window.location.href = `${siteUrl()}/products`
            }
            else
                toastError('Produto não encontrado.');
        });
    }

    productCopyUpsellSnippet(element, instance, methodName, ev) {
        element.select();
        document.execCommand('copy');
        toast('Snippet copiado.');
    }

    productUploadImage() {
        let id = params().id;

        let formData = new FormData();
        formData.append('image', document.getElementById('productImage')?.files[0] || '');

        let options = {
            headers: { 'Client-Name': 'Action' },
            method: 'POST',
            body: formData
        };

        fetch(`/ajax/actions/user/product/${id}/uploadImage`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                let area = document.querySelector('.img_product_image');
                area.innerHTML = `<img src="${body.data.image}" />`;
                let inp_image = document.querySelector('.inp_image');
                inp_image.value = body.data.image;
            }
            else toastError(body.message);
        });
    }

    productUploadAttachment(element, instance, methodName, ev) {
        let id = params().id;
        let currentPath = document.querySelector('.inp_product_attachment_file');
        let fileToUpload = element;

        const productUploading = document.getElementById('productUploading');
        if (productUploading) productUploading.style.display = 'block';

        let formData = new FormData();
        formData.append('file', fileToUpload?.files[0] || '');

        let options = {
            headers: { 'Client-Name': 'Action' },
            method: 'POST',
            body: formData
        };

        fetch(`/ajax/actions/user/product/${id}/uploadAttachment`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                currentPath.value = body.data.file;
                toast(body.message);
                if (body.data.icon_url) imgAttachmentPreview.src = body.data.icon_url;
            }
            else toastError(body.message);
            if (productUploading) productUploading.style.display = 'none';
        });
    }

    productRemoveAttachment(element, instance, methodName, ev) {
        document.querySelector('.inp_product_attachment_file').value = '';
        document.querySelector('#inp_product_upload_attachment').value = '';
        document.querySelector('#imgAttachmentPreview').src = '/images/extensions/file.png';
    }

    addVariation(element, instance, methodName, ev) {
        let list = document.querySelector('.div_product_diff_val');
        let item = list.children[list.children.length - 1];
        let clone = item.cloneNode(true);
        list.appendChild(clone);
        let slug; if (slug = clone.querySelector('.inp_pd_slug')) slug.value = '';
        let val; if (val = clone.querySelector('.inp_pd_val')) val.value = '';
        let qty; if (qty = clone.querySelector('.inp_pd_qty')) qty.value = '';
        // let del; if (del = clone.querySelector('.col_acts')) del.style.display = 'block';
        [].map.call(document.querySelectorAll('.div_product_diff_val .col_acts'), del => del.style.display = 'block');
        let dels = document.querySelectorAll('.div_product_diff_val .col_acts');
        let last = dels[dels.length - 1];
        if (last) last.style.display = 'none';
    }

    delVariation(element, instance, methodName, ev) {
        $(element).parents('.row')[0]?.remove();
    }
};