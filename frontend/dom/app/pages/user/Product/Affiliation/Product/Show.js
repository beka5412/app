App.User.Product.Affiliation.Product.Show = class Show extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Product.Affiliation.Product.Show';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/product/affiliation/product/{id}/${this?.constructor?.name||''}${this?.queryString||''}`, loaded, 
        () => this.coloringMenu('.menu_aff_product_show', 'info'));
    }

    promoteOnClick() {
        let btnDemoteAffiliation = document.querySelector('.btn_demote_affiliation');
        let btnPromoteAffiliation = document.querySelector('.btn_promote_affiliation');
        btnDemoteAffiliation.classList.remove('d-none');
        btnPromoteAffiliation.classList.add('d-none');
        
        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'POST'
        };

        let id = params().id;

        fetch(`/ajax/actions/user/marketplace/product/${id}/promote`, options);
    }

    demoteOnClick() {
        let btnDemoteAffiliation = document.querySelector('.btn_demote_affiliation');
        let btnPromoteAffiliation = document.querySelector('.btn_promote_affiliation');
        btnDemoteAffiliation.classList.add('d-none');
        btnPromoteAffiliation.classList.remove('d-none');
        
        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'POST'
        };

        let id = params().id;

        fetch(`/ajax/actions/user/marketplace/product/${id}/demote`, options).then(response => response.json()).finally(body => {
            let link = new Link;
            link.to(siteUrl() + `/marketplace/${id}/view`);
        });
    }
};