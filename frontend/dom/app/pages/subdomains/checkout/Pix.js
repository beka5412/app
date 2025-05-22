App.Subdomains.Checkout.Pix = class Pix extends Page {
    context = 'public';
    title = 'Pix';

    view(loaded) {
        return super.find(`subdomains/checkout/${this?.constructor?.name||''}${this?.queryString||''}`, loaded);
    }

    onSubmit() {
        alert('Controller Pix');
    }

    pixCopyCode(element, instance, methodName, ev) {
        copy(element.getAttribute('data-code'));
        toast('Código copiado!');
    }

    watchPix(transaction_id, order_uuid) {
        if (!transaction_id)
        {
            Swal.fire('Erro!', 'Ocorreu um erro ao gerar o QR Code.', 'error');
            return;
        }

        global.intervals.filter(item => {
            if (typeof item?.watchPix != "undefined") {
                clearInterval(item.watchPix);
            }
            else return item;
        });

        let token = JSON.stringify(tagJSON('customer')).toHex();

        if (location.pathname == '/pix') {
            let url = `${getSubdomainSerialized('checkout')}/ajax/actions/subdomains/checkout/watchPix`;
            let interval = setInterval(() => {
                fetch(`${url}?transaction_id=${transaction_id}`).then(response => response.json()).then(body => {
                    if (body.status == 'approved') {
                        let u = '';
                        if (pixThanksEnabled.value == 1) {
                            let url_ = pixThanksUrl.value;
                            let concat = /\?/.test(url_) ? '&' : '?';
                            u = url_ + concat + 'k=' + token;
                        }
                        else {
                            u = `${getSubdomainSerialized('checkout')}/pix/paid?id=${order_uuid}`;
                        }
                        if (order_uuid && u) document.location = u;
                        clearInterval(interval);
                    }
                });
            }, 1000);
            global.intervals.push({ watchPix: interval });
        }
    };
};