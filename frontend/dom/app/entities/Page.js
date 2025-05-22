class Page {
    specialKeys = ['Backspace', 'Tab', 'Control', 'Alt', 'Shift', 'ArrowLeft', 'ArrowUp', 'ArrowRight', 'ArrowDown', 'Home', 'End', 'Insert', 'Delete'];

    find(page, loaded, end) {
        let div = document.createElement('div');

        if (!page) page = ''; // garante a tipagem em string

        let headers = {'Content-Type': 'application/json', 'Client-Name': 'Pager' };
        let url = `${this.pageDomain || ''}/ajax/pages/${page}`;
        
        if (this.params && Object.keys(this.params).length) {
            global.params = this.params;
            let qs = url.split('?').length > 1 ? '&' : '?';
            url += `${qs}params=${JSON.stringify(this.params)}`;
        }

        app().style.opacity = .2;

        if(global.hadRedirection) fetch(url, {
            headers
        }).then(async response => {
            app().style.opacity = 1;

            let body = await response.text();
            
            let errorType = '';
            let allHeaders = [];
            for (var pair of response.headers.entries()) {
                let key = pair[0];
                let value = pair[1];
                allHeaders.push({ [key]: value });
                if (key == 'error-type') errorType = value;
            }

            // console.log();
            // console.log(response.headers.entries());
            // console.log(errorType);
            // console.log(allHeaders);
            
            // console.log('errorType: ' + errorType);
            // if (errorType == 'not_found')
            // {
            //     if (this?.notFound) this.notFound();
            // }

            if (errorType == 'unauthorized') {
                console.log(errorType);
                // console.log('global.pageContext');
                // console.log(global.pageContext);
                if (subdomain()) {
                    (new App.Browser.Subdomains[pascalCase(subdomain())][pascalCase(global.pageContext)].Unauthorized).view();
                    console.log(`App.Browser.Subdomains[${pascalCase(subdomain())}][${pascalCase(global.pageContext)}].Unauthorized`);
                }
                else 
                    (new App.Browser[pascalCase(global.pageContext)].Unauthorized).view();
                return;
            }

            div.innerHTML = body;
            let _templateCss = div.querySelector('#_templateCss');

            if (_templateCss) {
                let templateCss = document.querySelector('#templateCss');
                templateCss.innerHTML = _templateCss.innerHTML;
                _templateCss.parentNode.removeChild(_templateCss);
            }

            if (this.context) global.pageContext = this.context;

            if (loaded) loaded(div);

            render(div, this);
            
            [].map.call(document.querySelectorAll('[load]'), element => {
                let load = element.getAttribute('load');
                eval(load);
            });
            
            global?.onloadRoutines.forEach(item => {
                item.callback();
            });

            if (end) end();
            endStack(this);
        })
        .catch(r => console.log);
        else console.error('É necessário um redirecionameto single page anterior.');

        return div;
    }

    onEnded(methodName) {
        let self = this;

        let instance = this;
        let page = this.constructor.name;
        let method_ = instance[methodName];

        if (pageIs(page) && method_) instance[methodName](document.body, instance, methodName, window.event);
    }

    onEndedAlways(methodName) {
        let self = this;

        let instance = this;
        let page = this.constructor.name;
        let method_ = instance[methodName];

        if (pageIs(page) && method_) instance[methodName](document.body, instance, methodName, window.event);
    }

    onClick(methodName) {
        let instance = this;
        let page = this.constructor.name;
        let method_ = instance[methodName];

        
        
        $('body').on('click', `[click="${methodName}"]`, function(ev) {
            console.log(pageIs(page));
            if (pageIs(page) && method_) instance[methodName](this, instance, methodName, ev);
        });
    }

    onEnter(methodName) {
        let instance = this;
        let page = this.constructor.name;
        let method_ = instance[methodName];

        $('body').on('keyup', `[enter="${methodName}"]`, function(ev) {
            if (pageIs(page) && method_ && ev.keyCode == 13) instance[methodName](this, instance, methodName, ev);
        });
    }

    onChange(methodName) {
        let instance = this;
        let page = this.constructor.name;
        let method_ = instance[methodName];

        $('body').on('change', `[change="${methodName}"]`, function(ev) {
            if (pageIs(page) && method_) instance[methodName](this, instance, methodName, ev);
        });
    }

    onInput(methodName) {
        let instance = this;
        let page = this.constructor.name;
        let method_ = instance[methodName];

        $('body').on('input', `[change="${methodName}"]`, function(ev) {
            if (pageIs(page) && method_) instance[methodName](this, instance, methodName, ev);
        });
    }


    onKeyup(methodName) {
        let instance = this;
        let page = this.constructor.name;
        let method_ = instance[methodName];

        $('body').on('keyup', `[keyup="${methodName}"]`, function(ev) {
            if (pageIs(page) && method_) instance[methodName](this, instance, methodName, ev);
        });
    }

    onKeydown(methodName) {
        let instance = this;
        let page = this.constructor.name;
        let method_ = instance[methodName];

        $('body').on('keydown', `[keydown="${methodName}"]`, function(ev) {
            if (pageIs(page) && method_) instance[methodName](this, instance, methodName, ev);
        });
    }

    onKeypress(methodName) {
        let instance = this;
        let page = this.constructor.name;
        let method_ = instance[methodName];

        $('body').on('keypress', `[keypress="${methodName}"]`, function(ev) {
            if (pageIs(page) && method_) instance[methodName](this, instance, methodName, ev);
        });
    }

    onBlur(methodName) {
        let instance = this;
        let page = this.constructor.name;
        let method_ = instance[methodName];

        $('body').on('blur', `[blur="${methodName}"]`, function(ev) {
            if (pageIs(page) && method_) instance[methodName](this, instance, methodName, ev);
        });
    }

    onResize(methodName) {
        let instance = this;
        let page = this.constructor.name;
        let method_ = instance[methodName];

        $(window).on('resize', function(ev) {
            if (pageIs(page) && method_) instance[methodName](this, instance, methodName, ev);
        });
    }

    // apenas no carregamento da pagina 
    onReady(methodName) {
        let instance = this;
        let page = this.constructor.name;
        let method_ = instance[methodName];

        $(document).ready(function() {
            $(`[ready="${methodName}"]`).each((index, element) => {
                if (pageIs(page) && method_) instance[methodName](element, instance, methodName, window.event);
            });
        });
    }

    // onLoad: no carregamento da pagina e no carregamento das paginas que sao carregadas em single page

    onStepperNext(methodName) {
        let instance = this;
        let page = this.constructor.name;
        let method_ = instance[methodName];

        global.stepperEvents.push({ page, instance, methodName });
    }

    $onlyNumbers(element, instance, methodName, ev) {
        if (/\D/.test(ev.originalEvent.key) && !this.specialKeys.includes(ev.key)) {
            ev.preventDefault();
            return;
        }
    }

    $inputCurrency(element, instance, methodName, ev) {
        let aux = element.value.split(',');
        let dec = aux[1];
        if (dec) dec = dec.replace(/\,|\./g, '');
        let num = aux[0] || '';
        if (num) num = num.replace(/\,|\./g, '');

        let number = dec ? `${num}.${dec}` : num;
        if (dec && dec.length > 2) {
            let d_end = dec.substr(-2);
            let d_start = dec.substr(0, dec.length - 2);
            num = num + d_start;
            dec = d_end;
            number = num + '.' + dec;
        }

        if (!this.specialKeys.includes(ev.key)) {
            if (/\./.test(number)) {
                let aux4 = String(number).split('.');
                let num2 = aux4[0];
                let dec2 = aux4[1];
                if (Number(dec2) < 10 && dec2.length == 1) dec2 = '0' + dec2;
                number = num2 + '.' + dec2;
            }

            let aux2 = number.replace(/\./g, '');
            let left = aux2.substr(0, aux2.length - 2) || '0';
            let right = aux2.substr(-2) || '0';
            if (element.value.length == 1) right = '0' + element.value;
            let aux3 = Number(left) + '.' + right;
            number = aux3;

            element.setAttribute('data-value', Number(number));
            element.value = currency(number);
        }
    }

    $inputCurrencyAlways(element, instance, methodName, ev) {
        let value = element.value.replace(/\D/g, '');
        if (value.length == 1) value = `0.0${value}`;
        else if (value.length == 2) value = `0.${value}`;
        else {
            let dec = value.substr(-2);
            value = value.substr(0, value.length - dec.length) + '.' + dec;
        }
        console.log(value);

        element.setAttribute('data-value', Number(value));
        element.value = currency(value);
    }
    
    // faz as cores dos botoes do menu serem pintadas quando a pagina carregar
    coloringMenu(menu, item) {
        [].map.call(document.querySelectorAll(menu + ' [menu-button]'), button => button.classList.remove('active'));
        document.querySelector(`${menu} [menu-button="${item}"]`)?.classList.add('active');
    }

    e(html) {
        let text = ''; try { text = eval(html); } catch(e) {console.log(e)}
        if (typeof text != "string" && typeof text != "number") return '';
        return text;
    }

    re() {
        stackReload(this);
    }
}