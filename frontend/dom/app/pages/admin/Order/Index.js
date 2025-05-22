App.Admin.Order.Index = class Index extends Page {
    context = 'dashboard';
    title = 'Admin Order';
    className = 'App.Admin.Order.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}admin/order/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    async fetchOrderData() {
        const filterForm = document.querySelector("#filter-form");
        const filterFormData = new FormData(filterForm);
        const filterQueryString = new URLSearchParams(filterFormData).toString();

        const searchForm = document.querySelector("#search-form");
        const searchFormData = new FormData(searchForm);
        const searchQueryString = new URLSearchParams(searchFormData).toString();

        const combinedQueryString = `${filterQueryString}&${searchQueryString}`;

        try {
            const response = await fetch(`/ajax/pages/admin/order/Index?` + combinedQueryString);

            if (!response.ok) {
                throw new Error('Erro na requisição: ' + response.statusText);
            }

            let link = new Link;
            link.to(siteUrl() + `/admin/orders?` + combinedQueryString);
        } catch (error) {
            console.error('Erro:', error);
        }
    }
};