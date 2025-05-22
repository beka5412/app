App.User.MarketPlace.Show = class Show extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.MarketPlace.Show';

    view(loaded, link) {
        let product_id = params().id;
        return super.find(`${link?.full?'full/':''}user/marketplace/${product_id}/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
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

        fetch(`/ajax/actions/user/marketplace/product/${id}/promote`, options).then(response => response.json()).then(body => {
            toast(body.message);
        });
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

        fetch(`/ajax/actions/user/marketplace/product/${id}/demote`, options).then(response => response.json()).then(body => {
            toast(body.message);
        });
    }
};