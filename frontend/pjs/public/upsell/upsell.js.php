(() => {
    let specialKeys = ['Backspace', 'Tab', 'Control', 'Alt', 'Shift', 'ArrowLeft', 'ArrowUp', 'ArrowRight', 'ArrowDown', 'Home', 'End', 'Insert', 'Delete'];
    let purchaseUrl = 'https://purchase.rocketpays.app';

    let currency = number => Number(number).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&-').replace(/\./g, ',').replace(/\-/g, '.');

    let currencySymbol = price => 'R$ ' + currency(price);

    let tagJSON = id => JSON.parse(document.querySelector(`script[json-id="${id}"]`)?.textContent || '');
    
    let toHex = function(str) {
        let hex, result = '';
        for (let i = 0; i < str.length; i++) {
            hex = str.charCodeAt(i).toString(16);
            result += (''.padStart(3, '0') + hex).slice(-4);
        }
        return result;
    }
    
    let fromHex = function(str) {
        let hex = str.match(/.{1,4}/g) || [], result = '';
        for (let i = 0; i < hex.length; i++) result += String.fromCharCode(parseInt(hex[i], 16));
        return result;
    }

    let qs = str => str.split('&').reduce(function(prev, current) {
        let _ = current.split('=');
        prev[decodeURIComponent(_[0])] = decodeURIComponent(_[1]);
        return prev;
    }, {});

    let inputValue = selector => document.querySelector(selector)?.value || '';
    let modalBuy = () => document.querySelector('.rocketpays_upsell_modal');
    let modalVerifyEmail = () => document.querySelector('.rocketpays_upsell_modal-verify_email');
    let modalError = () => document.querySelector('.rocketpays_upsell_modal-error');
    let closeModals = () => [].map.call(document.querySelectorAll('.rocketpays-modal'), modal => modal.classList.remove('active'));
    let titleError = () => document.querySelector('.rocketpays_error_title');
    let messageError = () => document.querySelector('.rocketpays_error_message');
    let spanAmount = () => document.querySelector('.rocketpays_amount');

    let button = () => document.querySelector('.rocketpays_btn_open_modal');
    let btnBuy = () => document.querySelector('.rocketpays_btn_buy');
    let btnVerifyEmail = () => document.querySelector('.rocketpays_btn_verify_email');

    let error = (title, message) => {
        closeModals();
        modalError().classList.add('active');
        titleError().innerHTML = title;
        messageError().innerHTML = message;
    }

    let getCustomerEncoded = () => {
        let queryString = document.location.search.replace('?', '');
        return qs(queryString)?.k || '';
    };

    let getCustomer = () => {
        let token = getCustomerEncoded();
        if (!token) return {};
        return JSON.parse(fromHex(token));
    };

    let Customer = getCustomer();

    let parents = (el, selector) => {
        el = typeof el == 'string' ? document.querySelector(el) : el;
        if (!el) return el;
        while ((el = el.parentElement) && !((el.matches || el.matchesSelector).call(el, selector)));
        return el;
    }

    let getPin = () => [].map.call(document.querySelectorAll('.rocketpays_pin input'), input => input.value).join('');
    let clearPin = () => [].map.call(document.querySelectorAll('.rocketpays_pin input'), input => input.value = '');
    let firstPin = () => document.querySelector('.rocketpays_pin input');

    let sendPin = async email => {
        let url = '<?php echo ajax_url() ?>/api/app/upsell/send-pin';
        let options = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email })
        };
        let response = await fetch(url, options);
        let json = await response.json();
        return json;
    };
    
    button().addEventListener('click', async function() {
        let { email, token } = Customer;
        
        clearPin();

        if (token) {
            modalBuy().classList.add('active');
        }
        else {
            modalVerifyEmail().classList.add('active');
            
            let { status, message } = await sendPin(btoa(email));
            if (status == 'error')
                error('Erro', message);
            else
                firstPin().focus();
        }
    });

    [].map.call(document.querySelectorAll('.rocketpays_pin input'), inputPin => {
        inputPin.addEventListener('keyup', function(ev) {
            let prev = this.previousElementSibling;
            let next = this.nextElementSibling;
            if (!specialKeys.includes(ev.code) && Number(ev.code) != NaN) (next || btnVerifyEmail()).focus();
            if (ev.code == "Backspace") {
                prev?.focus();
            }
            if (this.value && !specialKeys.includes(ev.code)) {
                this.value = this.value[0];
                ev.preventDefault();
                return false;
            }
        });
        inputPin.addEventListener('keydown', function(ev) {
            let prev = this.previousElementSibling;
            // let next = this.nextElementSibling;
            if (ev.code == "Backspace") {
                if (!this.value && prev) prev.value = '';
            }
            if (this.value && !specialKeys.includes(ev.code)) {
                if (ev.key.replace(/\D/g, '')) this.value = ev.key;
                // ev.preventDefault();
                // return false;
            }
        });
        inputPin.addEventListener('paste', function(ev) {
            ev.preventDefault();
            let paste = (ev.clipboardData || window?.clipboardData)?.getData("text");
            let index = 0;
            // se todos os 5 campos estao preenchidos, zerar
            if (getPin()?.length == 5) clearPin();
            this.value = '';
            paste.split('').forEach(char => {
                input = document.querySelectorAll('.rocketpays_pin input')[index];
                if (input) {
                    input.value = char;
                    input.focus();
                }
                index++;
            });
        });
    });
    
    [].map.call(document.querySelectorAll('.rocketpays_upsell_close_modal'), closeButton => {
        closeButton.addEventListener('click', function() {
            parents(closeButton, '.rocketpays-modal').classList.remove('active');
        });
    });

    btnVerifyEmail().addEventListener('click', async function() {
        let pin = getPin();
        let url = '<?php echo ajax_url() ?>/api/app/upsell/verify-email';
        let { email } = Customer;
        let options = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ pin, email })
        };
        let response = await fetch(url, options);
        let json = await response.json();
        let token = json?.data?.token || '';
        let message = json?.message || 'Internal error.';
        Customer.token = token;

        if (token) {
            modalVerifyEmail().classList.remove('active');
            modalBuy().classList.add('active');
        }

        else error('A verificação falhou', message);
    });

    btnBuy().addEventListener('click', async function() {        
        let url = '<?php echo ajax_url() ?>/api/app/upsell/pay';
        let { userAgent } = navigator;
        let installments = inputValue('.rocketpays_select_installments');
        let paymentMethod = inputValue('.rocketpays_payment_method');
        let customerKey = inputValue('.rocketpays_customer_key');
        let { token, order_id, upsell_id, price_var } = Customer;

        let info = tagJSON('rocketpays');
        console.log(info);
        console.log(Customer);

        let label = btnBuy().innerHTML;
        btnBuy().innerHTML = 'Aguarde...';
        btnBuy().setAttribute('disabled', true);
        

        let options = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                siteUrl: document.location.href,
                userAgent,
                installments,
                paymentMethod,
                customerKey,
                token,
                order_id,
                upsell_id,
                price_var
            })
        };

        let response = await fetch(url, options);
        let json = await response.json();

        let defUrl = purchaseUrl + '?k=' + getCustomerEncoded();

        document.location = json.status == 'success' ? info?.accept || defUrl : defUrl;
        btnBuy().innerHTML = label;
        btnBuy().removeAttribute('disabled');
    });

    (() => {
        let total = Number(Customer.total || 0);
        total = total != NaN ? total : 0;
        spanAmount().innerHTML = currency(total);
    })();
})();