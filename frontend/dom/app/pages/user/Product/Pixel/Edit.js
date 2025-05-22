App.User.Product.Pixel.Edit = class Edit extends Page {
    context = 'dashboard';
    title = 'Editar Pixel';
    className = 'App.User.Product.Pixel.Edit';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/product/pixel/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    pixelOnSubmit() {
        let form = document.querySelector('.frm_edit_pixel');
        let name = form.querySelector('.inp_pixel_name');
        let platform = form.querySelector('.inp_pixel_platform');
        let content = form.querySelector('.inp_pixel_content');
        let access_token = form.querySelector('.inp_pixel_access_token');
        let metatag = form.querySelector('.inp_pixel_metatag');
        let domain = form.querySelector('.inp_domain');
        
        let data = {
            name: name?.value || '',
            platform: platform?.value || '',
            content: content?.value || '',
            access_token: access_token?.value || '',
            metatag: metatag?.value || '',
            domain: domain?.value || '',
        };

        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH',
            body: JSON.stringify(data)
        };

        let id = params().id; // pixel
        let product_id = params().product_id;
        fetch(`/ajax/actions/user/product/${product_id}/pixel/${id}/edit`, options).then(response => response.json()).then(body => {
            if (body?.status == 'success') {
                toast(body.message);
                // let link = new Link;
                // link.to(siteUrl() + `/product/${product_id}/pixels`);
            }
            else toastError(body.message);
        });
    }
};