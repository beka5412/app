App.User.Coupon.Index = class Index extends Page {
    context = 'dashboard';
    title = 'Coupon';
    className = 'App.User.Coupon.Index';
    
    view(loaded, link) {
        return super.find(`${link?.full?'full/':''}user/coupon/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    ready() {
        this.couponTypeHandler();
        this.submitHandler();
    }

    submitHandler() {
        document.querySelector('#create-coupon-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            $('#modalCreate').modal('hide');
            
            const response = await fetch('/ajax/actions/user/coupon/new', {
                headers: {
                    'Content-Type': 'application/json',
                    'Client-Name': 'Action'
                },
                method: 'POST',
                body: JSON.stringify({
                    code: formData.get('coupon-code'),
                    type: formData.get('coupon-type'),
                    discount: Number(formData.get('coupon-value').replaceAll('.', '').replaceAll(',', '.')),
                })
            })

            const body = await response.json();
            if (!!body?.id) {
                window.location.href = `${siteUrl()}/coupon/${body?.id}/edit`;
            }
        });
    }

    couponTypeHandler() {
        document.querySelector('select#coupon-type').addEventListener('change', ({ target: { value } }) => {
            if (value === 'price') {
                this.hideElement('#coupon-type-percent-form-group');
                this.showElement('#coupon-type-value-form-group');
            } else if (value === 'percent') {
                this.hideElement('#coupon-type-value-form-group');
                this.showElement('#coupon-type-percent-form-group');
            }
        })
    }

    showElement(selectors) {
        document.querySelector(selectors).classList.replace('d-none', 'd-block')
    }

    hideElement(selectors) {
        document.querySelector(selectors).classList.replace('d-block', 'd-none')
    }
    
    destroyCoupon(srcElement) {
        let options = {
            headers: {'Content-Type': 'application/json', 'Client-Name': 'Action'},
            method: 'DELETE'
        };

        let id = srcElement.getAttribute('data-id');
        let row = $(srcElement).parents('.tr')[0];
        [].map.call(row.children, item => item.style.opacity = .5);

        fetch(`/ajax/actions/user/coupon/${id}/destroy`, options).then(response => response.json()).then(body => {
            [].map.call(row.children, item => item.style.opacity = 1);
            if (body?.status == "success") {
                toastSuccess(body.message);
                row.remove();
            }
            else
                toastError('Cupom não encontrado.');
        });
    }
};