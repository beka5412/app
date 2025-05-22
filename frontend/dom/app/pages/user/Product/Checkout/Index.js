App.User.Product.Checkout.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Product.Checkout.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/product/checkouts/${this?.constructor?.name||''}${this?.queryString||''}`, loaded, 
        () => this.coloringMenu('.menu_product_edit', 'checkouts'));     
    }
    
    // jogar isso aqui no controller Index do checkout
    checkoutDestroy(srcElement) {
        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'DELETE'
        };

        let product_id = srcElement.getAttribute('data-product-id');
        let checkout_id = srcElement.getAttribute('data-checkout-id');
        let row = $(srcElement).parents('.tr')[0];
        [].map.call(row.children, item => item.style.opacity = .5);
        
        fetch(`/ajax/actions/user/product/${product_id}/checkout/${checkout_id}/destroy`, options).then(response => response.json()).then(body => {
            [].map.call(row.children, item => item.style.opacity = 1);
            if (body?.status == "success") {
                toastSuccess(body.message);
                row.remove();
            }
            else
                toastError('Checkout não encontrado.');
        });
    }
};