App.User.Product.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Product.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/product/${this?.constructor?.name||''}${this?.queryString||''}`, loaded, this.load);
    }

    load() {
        textEllipisis();
    }

    productsReady() {
        this.load();
    }

    productsOnResize() {
        textEllipisis();
    }

    productDestroy(id) {
        console.log('teste')
        // let options = {
        //     headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
        //     method: 'DELETE'
        // };
        //
        // fetch(`/ajax/actions/user/product/${id}/destroy`, options).then(response => response.json()).then(body => {
        //     if (body?.status == "success") {
        //         window.location.href = `${siteUrl()}/products`
        //     }
        //     else
        //         toastError('Produto não encontrado.');
        // });
    }

    newProduct(srcElement) {
        let product_name = document.querySelector('.inp_product_name').value;
        let payment_type = document.querySelector('.inp_payment_type').value;
        let product_type = document.querySelector('.inp_product_type').value;

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'POST',
            body: JSON.stringify({
                product_name,
                payment_type,
                product_type
            })
        };
        let row = document.querySelector('.content_products_list');
        [].map.call(row.children, item => item.style.opacity = .5);
        $('#modalNewProduct').modal('hide');
        
        fetch(`/ajax/actions/user/product/new`, options).then(response => response.json()).then(body => {
            [].map.call(row.children, item => item.style.opacity = 1);
            let id;
            if (id = body?.id) {
                //let link = new Link;
                //link.to(`${siteUrl()}/product/${id}/edit`);
                window.location.href = `${siteUrl()}/product/${id}/edit`;
            }
        });
    }
};