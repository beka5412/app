App.Subdomains.Purchase.Subscription.Index = class Index extends Page {
    context = 'dashboard';
    title = 'Assinaturas';
    className = 'App.Subdomains.Purchase.Subscription.Index';

    view(loaded) {
        return super.find(`subdomains/purchase/subscriptions/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    async cancelSubscriptionOnClick(element) {
        let id = element.getAttribute('data-id');
        let url = getSubdomainTranslated('purchase') + `/ajax/actions/subdomains/purchase/subscription/${id}/cancel`;
        console.log(url)
        
        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'PATCH'
        };
        
        let response = await fetch(url, options);
        let { status, message } = await response.json();
        
        if (status == 'success')
            Swal.fire('Sucesso!', 'Sua assinatura foi cancelada. Na próxima data de pagamento você não será mais cobrado.', 'success')
                .then(({ isConfirmed }) => isConfirmed ? location.reload() : '');

        else
            Swal.fire('Erro!', message || 'Ocorreu um erro ao cancelar sua assinatura. Por favor, tente novamente mais tarde.', 'error');
    }
};