App.User.App.AstronMembers.Index = class Index extends Page {
    context = 'dashboard';
    title = '';
    className = 'App.User.App.AstronMembers.Index';

    view(loaded, link) {
        return super.find(`${link?.full ? 'full/' : ''}user/app/astronmembers/${this?.constructor?.name || ''}${this?.queryString || ''}`, loaded);
    }

    destroyAstronmembersIntegration(srcElement) {
        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'DELETE'
        };

        let id = srcElement.getAttribute('data-id');
        let row = $(srcElement).parents('.tr')[0];
        [].map.call(row.children, item => item.style.opacity = .5);

        fetch(`/ajax/actions/user/app/astronmembers/${id}/destroy`, options).then(response => response.json()).then(body => {
            [].map.call(row.children, item => item.style.opacity = 1);
            if (body?.status == "success") {
                toastSuccess(body.message);
                row.remove();
            }
            else
                toastError(__('AstronMembers integration deleted successfully.'));
        });
    }
};
