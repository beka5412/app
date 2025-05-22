App.Admin.Catalog.Index = class Index extends Page {
    context = 'dashboard';
    title = 'Admin Catalog';
    className = 'App.Admin.Catalog.Index';

    view(loaded, link) {
        return super.find(`${link?.full ? 'full/' : ''}admin/catalog/${this?.constructor?.name || ''}${this?.queryString || ''}`, loaded);
    }

    newCatalog(srcElement) {
        let title = document.getElementById('catalog_title').value;
        let price = document.getElementById('catalog_price').value;
        let category = document.getElementById('catalog_category').value;
        let description = document.getElementById('catalog_description').value;

        let formData = new FormData();
        formData.append('image', document.getElementById('inp_image')?.files[0] || '');
        formData.append('title', title);
        formData.append('price', price);
        formData.append('category', category);
        formData.append('description', description);

        let options = {
            headers: { 'Client-Name': 'Action' },
            method: 'POST',
            body: formData
        };


        console.log(options)
        fetch(`/ajax/actions/admin/catalogs/new`, options)
            .then(response => response.text())
            .then(body => {
                alert('Cadastrado com sucesso')
                location.reload()
            }).catch(err => console.log(err))
        // .then(body => {
        //     [].map.call(row.children, item => item.style.opacity = 1);
        //     let id;
        //     if (id = body?.id) {
        //         // let link = new Link;
        //         // link.to(`${siteUrl()}/product/${id}/edit`);
        //         alert(id)
        //     }
        // });
    }

    editCatalog(srcElement) {
        let id = document.getElementById('id_edit').value;
        let title = document.getElementById('title_edit').value;
        let price = document.getElementById('price_edit').value;
        let category = document.getElementById('category_edit').value;
        let description = document.getElementById('description_edit').value;

        let formData = new FormData();
        formData.append('image', document.getElementById('image_edit')?.files[0] || '');
        formData.append('id', id);
        formData.append('title', title);
        formData.append('price', price);
        formData.append('category', category);
        formData.append('description', description);

        let options = {
            headers: { 'Client-Name': 'Action' },
            method: 'POST',
            body: formData
        };


        console.log(options)
        fetch(`/ajax/actions/admin/catalogs/update`, options)
            .then(response => response.text())
            .then(body => {
                alert('Alterado com sucesso')
                location.reload()
            }).catch(err => console.log(err))
        // .then(body => {
        //     [].map.call(row.children, item => item.style.opacity = 1);
        //     let id;
        //     if (id = body?.id) {
        //         // let link = new Link;
        //         // link.to(`${siteUrl()}/product/${id}/edit`);
        //         alert(id)
        //     }
        // });
    }
};