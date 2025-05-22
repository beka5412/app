App.User.Recurrence.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.Recurrence.Index';

    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/recurrence/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }
    
    async fetchRecurrenceData() {
        const filterForm = document.querySelector("#filter-form");
        const filterFormData = new FormData(filterForm);
        const filterQueryString = new URLSearchParams(filterFormData).toString();

        const searchForm = document.querySelector("#search-form");
        const searchFormData = new FormData(searchForm);
        const searchQueryString = new URLSearchParams(searchFormData).toString();

        const combinedQueryString = `${filterQueryString}&${searchQueryString}`;

        try {
            const response = await fetch(`/ajax/pages/user/recurrence/Index?` + combinedQueryString);

            if (!response.ok) {
                throw new Error('Erro na requisição: ' + response.statusText);
            }

            let link = new Link;
            link.to(siteUrl() + `/recurrences?` + combinedQueryString);
        } catch (error) {
            console.error('Erro:', error);
        }
    }
    
};