let global = {};
global.routes = {};
global.hadRedirection = false;
global.initialRoute = location.pathname;
global.initialDomain = location.origin;
global.domRoute = () => location.pathname;
global.domDomain = () => location.origin;
global.intervals = [];
global.stepperEvents = [];
let app = () => document.getElementById('app');
let next = () => document.getElementById('next');
global.pageContext = '<?php echo $context ?? ""; ?>';
let contextIs = context => context == global.pageContext;
global.version = '1.0.1';
global.onloadRoutines = [];
global.settings = {
    withdrawalFee: Number('<?php echo get_setting("withdrawal_fee"); ?>')
};
global.env = {
    SUBDOMAIN_INDEX: '<?php echo env("SUBDOMAIN_INDEX"); ?>'
};

String.prototype.toHex = function () {
    let hex, result = '';
    for (let i = 0; i < this.length; i++) {
        hex = this.charCodeAt(i).toString(16);
        result += (''.padStart(3, '0') + hex).slice(-4);
    }
    return result;
}

String.prototype.fromHex = function () {
    let hex = this.match(/.{1,4}/g) || [], result = '';
    for (let i = 0; i < hex.length; i++) result += String.fromCharCode(parseInt(hex[i], 16));
    return result;
}

let queryString = s => {
    let result = '';
    let search = document.location.search;
    let queryString = search.split('?')[1] || '';
    if (queryString) {
        let r = queryString.split('&').find(item => {
            let aux = item.split('=');
            let key = aux[0];
            let value = aux[1];
            return key == s;
        });
        result = r.substr(r.split('=')[0].length + 1);
    }
    return result;
}

/**
 * Se os dois valores existirem, entao faca a comparacao
 */
let cmp_both_valid = (arg1, cmp, arg2) => {
    if (arg1 && arg2) {
        switch (cmp) {
            case '=':
            case '==':
                return arg1 == arg2;

            case '===':
                return arg1 === arg2;

            case '!=':
                return arg1 != arg2;

            case '!==':
                return arg1 !== arg2;

            case '<':
                return arg1 < arg2;

            case '>':
                return arg1 > arg2;

            case '<=':
                return arg1 <= arg2;

            case '>=':
                return arg1 >= arg2;

            // ...
        }
    }

    return false;
}

/**
 * Convert number to currency
 */
let currency = number => Number(number).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&-').replace(/\./g, ',').replace(/\-/g, '.');

/**
 * Set currency symbol
 */
let currencySymbol = price => 'R$ ' + currency(price);

let currencyToNumber = currencyNumber => Number(currencyNumber.replace(/\./g, '').replace(/\,/, '.'));

let pascalCase = s => s.replace(/(\w)(\w*)/g, (g0, g1, g2) => g1.toUpperCase() + g2.toLowerCase())
    .replace(/\-/g, '')
    .toString()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .trim()
    .replace(/\s+/g, '')
    .replace(/[^\w-]+/g, '')
    .replace(/--+/g, '')
    ;

String.prototype.isEmail = function () {
    return this
        .toLowerCase()
        .match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)
        ?.length ? true : false;
};

String.prototype.isCNPJ = function () {
    let value = this;
    if (!value) return false;

    // Limita ao máximo de 18 caracteres, para CNPJ formatado
    if (value.length > 18) return false;

    // Teste Regex para veificar se é uma string apenas dígitos válida
    const digitsOnly = /^\d{14}$/.test(value);
    // Teste Regex para verificar se é uma string formatada válida
    const validFormat = /^\d{2}.\d{3}.\d{3}\/\d{4}-\d{2}$/.test(value);

    // Se o formato é válido, usa um truque para seguir o fluxo da validação
    if (digitsOnly || validFormat) true;
    // Se não, retorna inválido
    else return false;

    // Guarda um array com todos os dígitos do valor
    const match = value.toString().match(/\d/g);
    const numbers = Array.isArray(match) ? match.map(Number) : [];

    // Valida a quantidade de dígitos
    if (numbers.length !== 14) return false;

    // Elimina inválidos com todos os dígitos iguais
    const items = [...new Set(numbers)]
    if (items.length === 1) return false;

    // Cálculo validador
    const calc = (x) => {
        const slice = numbers.slice(0, x);
        let factor = x - 7;
        let sum = 0;

        for (let i = x; i >= 1; i--) {
            const n = slice[x - i];
            sum += n * factor--;
            if (factor < 2) factor = 9;
        }

        const result = 11 - (sum % 11);

        return result > 9 ? 0 : result;
    }

    // Separa os 2 últimos dígitos de verificadores
    const digits = numbers.slice(12);

    // Valida 1o. dígito verificador
    const digit0 = calc(12);
    if (digit0 !== digits[0]) return false;

    // Valida 2o. dígito verificador
    const digit1 = calc(13);
    return digit1 === digits[1];
}

String.prototype.isCPF = function () {
    let cpf = this;

    // Tirar formatação
    cpf = cpf.replace(/[^\d]+/g, '');

    // Validar se tem tamanho 11 ou se é uma sequência de digitos repetidos
    if (cpf.length !== 11 || !!cpf.match(/(\d)\1{10}/)) return false;

    // String para Array
    cpf = cpf.split('');

    const validator = cpf
        // Pegar os últimos 2 digitos de validação
        .filter((digit, index, array) => index >= array.length - 2 && digit)
        // Transformar digitos em números
        .map(el => +el);

    const toValidate = pop => cpf
        // Pegar Array de items para validar
        .filter((digit, index, array) => index < array.length - pop && digit)
        // Transformar digitos em números
        .map(el => +el);

    const rest = (count, pop) => (toValidate(pop)
        // Calcular Soma dos digitos e multiplicar por 10
        .reduce((soma, el, i) => soma + el * (count - i), 0) * 10)
        // Pegar o resto por 11
        % 11
        // transformar de 10 para 0
        % 10;

    return !(rest(10, 2) !== validator[0] || rest(11, 1) !== validator[1]);
}

// App is public
App.mask = {
    doc: selector => {
        let element = $(selector);
        let options = {
            placeholder: "",
            onKeyPress: function (value, e, field, options_) {
                let masks = ["000.000.000-0099999", "00.000.000/0000-00"],
                    digits = value.replace(/\D/g, "").length,
                    mask = digits <= 11 ? masks[0] : masks[1];

                element.mask(mask, options_);
            }
        };

        if ($(document.body)?.mask) element.mask("000.000.000-0099999", options);
    },

    phone: selector => {
        let element = $(selector);
        var options = {
            placeholder: "",
            onKeyPress: function (value, e, field, options_) {
                let masks = ["(00) 0000-00009", "(00) 0 0000-0000"],
                    digits = value.replace(/[^0-9]/g, "").length,
                    mask = digits <= 10 ? masks[0] : masks[1];

                element.mask(mask, options_);
            }
        };

        if ($(document.body)?.mask) element.mask("(00) 0000-0000", options);
    },

    birthdate: selector => {
        let element = $(selector);
        element.mask('00/00/0000');
    }
}

let changeUrl = (url) => window.history.pushState(null, '', url);

let findUrlStatement = (domain, url) => {
    // url_ = url da lista de rotas definidas
    // url = url requisitada
    let found = false;
    let foundRequestedUrlVars;
    if (global.routes[domain]) Object.entries(global.routes[domain]).forEach(item => {
        let url_ = item[0];
        let instance = item[1];

        let url_Arr = url_.split('/');
        let urlArr = url.split('/');
        let newUrlArr = [];
        let n = 0;
        let params = {};
        urlArr.forEach(p => {
            let i = url_Arr[n] || '';
            let hasVar = i.includes('{') && i.includes('}');
            if (hasVar) {
                newUrlArr.push(i);
                let key = i.replace(/\{/, '').replace(/\}/, '').trim();
                params[key] = p;
            }
            else
                newUrlArr.push(p);
            n++;
        });
        let requestedUrlVars = newUrlArr.join('/');
        if (!found && requestedUrlVars == url_) {
            instance.params = params;
            global.params = params;
            found = true;
            foundRequestedUrlVars = requestedUrlVars;
        }
    });
    return foundRequestedUrlVars;
}

let getPageByURL = (domain, url) => {
    let _instance = null;
    if (global.routes[domain]) {
        // se encontrar uma url normal antes de encontrar uma url com um parametro, priorizar a url normal
        Object.entries(global.routes[domain]).forEach(item => {

            let _url = item[0] || '';
            let instance = item[1];
            url = url.split('?')[0];

            if (!_instance) {
                if (url == _url) {
                    _instance = instance;
                }
            }
        });

        Object.entries(global.routes[domain]).forEach(item => {
            let _url = item[0] || '';
            let instance = item[1];
            url = url.split('?')[0];

            if (!_instance) {
                if (findUrlStatement(instance.pageDomain, url) == _url) {
                    _instance = instance;
                }
            }
        });
    }
    return _instance;
};

function flag(cc) {
    var cc = cc.replace(/[^0-9]+/g, '');
    var flags = {
        visa: /^4[0-9]{12}(?:[0-9]{3})/,
        mastercard: /^((5(([1-2]|[4-5])[0-9]{8}|0((1|6)([0-9]{7}))|3(0(4((0|[2-9])[0-9]{5})|([0-3]|[5-9])[0-9]{6})|[1-9][0-9]{7})))|((508116)\\d{4,10})|((502121)\\d{4,10})|((589916)\\d{4,10})|(2[0-9]{15})|(67[0-9]{14})|(506387)\\d{4,10})/,
        amex: /^3[47][0-9]{13}/,
        diners: /^3(?:0[0-5]|[68][0-9])[0-9]{11}/,
        hipercard: /^(606282\d{10}(\d{3})?)|(3841\d{15})/,
        discover: /^6(?:011|5[0-9]{2})[0-9]{12}/,
        elo: /^4011(78|79)|^43(1274|8935)|^45(1416|7393|763(1|2))|^50(4175|6699|67[0-6][0-9]|677[0-8]|9[0-8][0-9]{2}|99[0-8][0-9]|999[0-9])|^627780|^63(6297|6368|6369)|^65(0(0(3([1-3]|[5-9])|4([0-9])|5[0-1])|4(0[5-9]|[1-3][0-9]|8[5-9]|9[0-9])|5([0-2][0-9]|3[0-8]|4[1-9]|[5-8][0-9]|9[0-8])|7(0[0-9]|1[0-8]|2[0-7])|9(0[1-9]|[1-6][0-9]|7[0-8]))|16(5[2-9]|[6-7][0-9])|50(0[0-9]|1[0-9]|2[1-9]|[3-4][0-9]|5[0-8]))/,
        aura: /^(5078\d{2})(\d{2})(\d{11})$/,
        jcb: /^(?:2131|1800|35\d{3})\d{11}/
    };
    for (var flag in flags) if (flags[flag].test(cc)) return flag;
    return '';
}

let params = () => global.params;

let getDomain = () => window.history.state?.domain || global.domDomain() || global.initialDomain;
let getUri = () => window.history.state?.url || global.domRoute() || global.initialRoute;
let controllerInfo = () => {
    const domain = getDomain().replace('https://', '').replace('http://', '');

    return getPageByURL(domain, getUri());
};
let pageIs = page => controllerInfo()?.constructor.name == page;

let render = (div, instance) => {
    let full = window.history?.state?.full;

    let nextElement_ = document.createElement('div');
    nextElement_.id = 'next';
    nextElement_.classList.add('app');

    if (full) {
        let html = document.querySelector('html');
        // html.innerHTML = div.innerHTML;
        html.removeChild(document.body);
        html.appendChild(div);
    }
    else {
        let htmlElement = div.children[0] ?? '';

        // se nao deu erro
        if (htmlElement) {
            app().parentNode.insertBefore(nextElement_, app());
            let titleElement = htmlElement.querySelector('title');
            if (titleElement) {
                document.title = titleElement.innerText;
                titleElement.parentNode.removeChild(titleElement);
            }
            next().appendChild(htmlElement);
            app().parentNode.removeChild(app());
            next().setAttribute('id', 'app');
        }
    }


    // let nextElement = document.createElement('div');
    // nextElement.id = 'next';
    // nextElement.classList.add('app');
    // app().appendChild(nextElement);

    // next().parentNode.removeChild(next());
}

let copy = val => {
    var el = document.createElement("textarea");
    el.style.position = 'fixed';
    el.style.left = '-2000px';
    el.style.top = '-2000px';
    document.body.appendChild(el);
    el.value = val;
    el.focus();
    el.select();
    document.execCommand("copy");
    el.select();
    el.setSelectionRange(0, 99999); /* For mobile devices */
    navigator.clipboard.writeText(el.value);
    document.body.removeChild(el);
};

let toast = message => {
    Toastify({
        text: message,
        duration: 7000,
        destination: "",
        newWindow: true,
        close: true,
        gravity: "bottom", // `top` or `bottom`
        position: "right", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
            background: "linear-gradient(to right, rgb(45 100 223), rgb(61 201 182))",
        },
        onClick: function () { } // Callback after click
    }).showToast();
}

window.toastSuccess = message => {
    Toastify({
        text: message,
        duration: 7000,
        destination: "",
        newWindow: true,
        close: true,
        gravity: "bottom", // `top` or `bottom`
        position: "right", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
            background: "linear-gradient(to right, rgb(45 100 223), rgb(61 201 182))",
        },
        onClick: function () { } // Callback after click
    }).showToast();
}

window.toastError = message => {
    Toastify({
        text: message,
        duration: 7000,
        destination: "",
        newWindow: true,
        close: true,
        gravity: "bottom", // `top` or `bottom`
        position: "right", // `left`, `center` or `right`
        stopOnFocus: true, // Prevents dismissing of toast on hover
        style: {
            background: "linear-gradient(to right, rgb(45 100 223), rgb(61 201 182))",
        },
        onClick: function () { } // Callback after click
    }).showToast();
}

let siteUrlFull = () => "<?php echo full_url() ?>";
let siteUrl = () => "<?php echo site_url() ?>";
let siteUrlBase = () => "<?php echo site_url_base() ?>";
let siteHost = () => "<?php echo site_host() ?>";
let siteProtocol = () => "<?php echo env('PROTOCOL') ?>";

let subdomain = () => {
    let envUrl = siteHost();
    let host = document.location.host;
    let aux = host.split(`.${envUrl}`);
    let subdomain = aux.length > 1 ? aux[0] : '';
    return subdomain;
}

let url = () => document.location.origin;

let getSubdomainSerialized = subdomain => {
    // translatedSubdomain(subdomain);
    return `${siteProtocol()}://${subdomain}.${siteHost()}`;
};

let getSubdomainSerializedWithoutProtocol = subdomain => `${subdomain}.${siteHost()}`

const subdomains = () => {
    return JSON.parse('<?= json_encode(env_subdomains()) ?>');
};

const translatedSubdomain = (subdomain) => {
    const key = 'SUBDOMAIN_' + subdomain.toUpperCase();
    const found = subdomains().find(row => row[key]);
    return found[key];
};

let getSubdomainTranslated = subdomain => {
    subdomain = translatedSubdomain(subdomain);
    return `${siteProtocol()}://${subdomain}.${siteHost()}`;
};

window.history.pushState = function () {
    Routes.prototype.watch({ arguments });

    History.prototype.pushState.apply(window.history, arguments);
}

let spinner = () => `&nbsp;<div class="spinner"><div></div><div></div><div></div><div></div></div>`;

let currentGlobalRoute = () => {
    let w_state = window.history?.state;
    let url = w_state?.url || '';
    let domain = w_state?.domain || '';
    let page = w_state?.page;
    let full = w_state?.full;
    url = url.split('?')[0];
    let routes = global?.routes;
    let route_domain = routes[domain] || {};
    let domain_url = route_domain[url] || {};
    return domain_url;
};

window.addEventListener('popstate', function (event, state) {
    Routes.prototype.watch({ arguments });

    let w_state = window.history?.state;
    let url = w_state?.url || global.domRoute() || global.initialRoute || '';
    let domain = w_state?.domain || global.domDomain() || global.initialDomain || '';
    let page = w_state?.page;
    let full = w_state?.full;
    url = url.split('?')[0];
    let urlStatement = findUrlStatement(domain, url);
    global.hadRedirection = true;

    // if (full) {
    //     let html = document.querySelector('html');
    //     html.innerHTML = div.innerHTML;
    // }
    // console.log(url);
    // console.log(global.routes[domain]);

    let queryString = location.search.split('?')[1] || '';
    if (queryString) queryString = '?' + queryString;


    // procura rota estatica, ex.: se existe http://sub.site.com/login
    if (global.routes[domain][url]) {
        let instance = global.routes[domain][url];
        if (queryString) instance.queryString = queryString;
        instance?.view();
    }

    // procura rota dinamica, ex.: se existe http://sub.site.com/{id}
    else if (urlStatement) {
        let instance = global.routes[domain][urlStatement];
        if (queryString) instance.queryString = queryString;
        instance?.view();
    }

    else {
        if (global.routes[domain][url]) {
            let instance = global.routes[domain][url];
            instance?.view();
        }
        else {
            let notfoundInstance = new App.Browser[pascalCase(global.pageContext)].NotFound;

            // if (url) {
            notfoundInstance.view(div => {
                // window.history.pushState({ ...state, url, page: notfoundInstance.constructor.name }, '', url);
                document.title = notfoundInstance.title;
            }, self);
            // }

            // else {
            //     window.history.pushState({ url: global.initialRoute }, '', global.initialRoute);
            //     global.routes[global.initialRoute]?.view();
            // }
        }
    }

    [].map.call(document.querySelectorAll('[load]'), element => {
        let load = element.getAttribute('load');
        eval(load);
    });

    global?.onloadRoutines.forEach(item => {
        item.callback();
    });
});

$('body').on('click', '[to]', function (e) {
    if (this.tagName == 'A') {
        e.preventDefault();
    }
    let url = this.hasAttribute('to') ? this.getAttribute('to') : '';
    let full = this.hasAttribute('to:full');
    let link = new Link;
    link.element = this;
    link.full = full;
    link.to(url);
});

function startSpin(element) {
    let val = element.innerHTML;
    element.setAttribute('data-initial-value', val);
    element.innerHTML = spinner();
}

function stopSpin(element) {
    element.innerHTML = element.hasAttribute('data-initial-value') ? element.getAttribute('data-initial-value') : '';
}

function uniqid(length) {
    var dec2hex = [];
    for (var i = 0; i <= 15; i++) {
        dec2hex[i] = i.toString(16);
    }

    var uuid = '';
    for (var i = 1; i <= 36; i++) {
        if (i === 9 || i === 14 || i === 19 || i === 24) {
            uuid += '-';
        } else if (i === 15) {
            uuid += 4;
        } else if (i === 20) {
            uuid += dec2hex[(Math.random() * 4 | 0 + 8)];
        } else {
            uuid += dec2hex[(Math.random() * 16 | 0)];
        }
    }

    if (length) uuid = uuid.substring(0, length);
    return uuid;
}

// let toggleStatement = (toggle, not) => {
//     let selectors = toggle.getAttribute(not ? '!toggle' : 'toggle');
//     let checked = toggle.checked;
//     let cond = not ? !checked : checked;
//     [].map.call(document.querySelectorAll(selectors), element => {
//         console.log(element);
//         if (cond) {
//             element.classList.remove('d-none');
//             element.classList.add('d-block');
//         }
//         else {
//             element.classList.add('d-none');
//             element.classList.remove('d-block');
//         }
//     });
// };

let toggleStatement = (toggle, not) => {
    let selectors = toggle.getAttribute(not ? '!toggle' : 'toggle');
    let checked = toggle.checked;
    let cond = not ? !checked : checked;
    [].map.call(document.querySelectorAll(selectors), element => {
        if (cond) {
            element.classList.remove('d-none');
            element.classList.add('d-block');
        }
        else {
            element.classList.add('d-none');
            element.classList.remove('d-block');
        }
    });
};

$('body').on('change', '[toggle]', function (e) {
    toggleStatement(this);
});

$('body').on('change', '[\\!toggle]', function (e) {
    toggleStatement(this, true);
});

let ifCheckedShow = function () {
    let selectors = this.getAttribute('if(checked)show');
    let checked = this.checked;
    [].map.call(document.querySelectorAll(selectors), element => {
        if (checked) {
            element.classList.remove('d-none');
            element.classList.add('d-block');
        }
    });
};

let ifCheckedHide = function () {
    let selectors = this.getAttribute('if(checked)hide');
    let checked = this.checked;
    [].map.call(document.querySelectorAll(selectors), element => {
        if (checked) {
            element.classList.remove('d-block');
            element.classList.add('d-none');
        }
    });
};
let ifUncheckedShow = function () {
    let selectors = this.getAttribute('if(unchecked)show');
    let checked = this.checked;
    [].map.call(document.querySelectorAll(selectors), element => {
        if (!checked) {
            element.classList.remove('d-none');
            element.classList.add('d-block');
        }
    });
};

let ifUncheckedHide = function () {
    let selectors = this.getAttribute('if(unchecked)hide');
    let checked = this.checked;
    [].map.call(document.querySelectorAll(selectors), element => {
        if (!checked) {
            element.classList.remove('d-block');
            element.classList.add('d-none');
        }
    });
};

$('body').on('change', '[if\\(checked\\)show]', ifCheckedShow);
$('body').on('change', '[if\\(checked\\)hide]', ifCheckedHide);
$('body').on('change', '[if\\(unchecked\\)show]', ifUncheckedShow);
$('body').on('change', '[if\\(unchecked\\)hide]', ifUncheckedHide);

$('body').on('click', '[stepper-control=prev]', function () {
    let parent = $(this).parents('[stepper-control]')[0];
    let next = parent.querySelector('[stepper-control=next]');
    next.style.display = 'block';

    let stepperID = this.hasAttribute('stepper-for') ? this.getAttribute('stepper-for') : '';
    let stepper = document.querySelector(`[stepper="${stepperID}"]`);
    let current = stepper.querySelector('[step].active')
    let previous = current.previousElementSibling;
    if (previous) {
        current.classList.remove('active');
        previous.classList.add('active');
    }

    if (!previous?.previousElementSibling) {
        this.style.display = 'none';
    }
});

$('body').on('click', '[stepper-control=next]', function (ev) {
    let this_ = this;
    let parent = $(this).parents('[stepper-control]')[0];
    let previous = parent.querySelector('[stepper-control=prev]');

    let stepperID = this.hasAttribute('stepper-for') ? this.getAttribute('stepper-for') : '';
    let stepper = document.querySelector(`[stepper="${stepperID}"]`);
    let current = stepper.querySelector('[step].active')
    let next = current.nextElementSibling;

    let methodNameCondition = this.hasAttribute('stepper-validation') ? this.getAttribute('stepper-validation') : '';
    let validations = [];
    global.stepperEvents.forEach(item => {
        if (pageIs(item.page) && item.methodName == methodNameCondition)
            validations.push(item.instance[item.methodName](this_, item.instance, item.methodName, ev));
    });
    let validated = validations.every(item => item);
    if (!validated) return;

    previous.style.display = 'block';

    if (next) {
        current.classList.remove('active');
        next.classList.add('active');
    }

    if (!next?.nextElementSibling) {
        this.style.display = 'none';
    }
});

var countdown = function (selector, separatedIntoElements = []) {
    let countdownElement = document.querySelector(selector);
    if (!countdownElement) return;

    let arr = [], cancel = false;

    if (separatedIntoElements?.length) {
        separatedIntoElements.forEach(element => {
            if (typeof element == "undefined" || !element) cancel = true;
            else arr.push(element.innerText.replace(/\n|\t|\r|\s/g, '').trim());
        });
    }

    else
        arr = countdownElement.innerHTML.split(':');

    if (cancel) return;

    let H, i, s;
    let aux = arr;
    if (aux.length == 1) {
        s = Number(aux[0] || 0);
    }
    if (aux.length == 2) {
        i = Number(aux[0] || 0);
        s = Number(aux[1] || 0);
    }
    if (aux.length == 3) {
        H = Number(aux[0] || 0);
        i = Number(aux[1] || 0);
        s = Number(aux[2] || 0);
    }

    if (typeof H == "undefined") H = 0;
    if (typeof i == "undefined") i = 0;

    if (typeof s == "undefined") return;

    const second = 1000,
        minute = second * 60,
        hour = minute * 60,
        day = hour * 24;

    let left = (H * 60 * 60) + (i * 60) + s;
    // let left = 5;

    var today = new Date();
    var tomorrow = new Date(today.getTime() + (left * 1000));
    let birthday = tomorrow,
        countDown = new Date(birthday).getTime(),
        x = setInterval(function () {
            let now = new Date().getTime(),
                distance = countDown - now;

            if (distance < 1000)
                clearInterval(x);

            let hourStr = Math.floor((distance % (day)) / (hour));
            let minStr = Math.floor((distance % (hour)) / (minute));
            let secStr = Math.floor((distance % (minute)) / second);

            if (secStr < 0 || minStr < 0 || hourStr < 0) return;

            hourStr = hourStr < 10 ? '0' + hourStr : hourStr;
            minStr = minStr < 10 ? '0' + minStr : minStr;
            secStr = secStr < 10 ? '0' + secStr : secStr;

            if (Number(hourStr)) {
                if (separatedIntoElements?.length) {
                    separatedIntoElements[0].innerHTML = hourStr;
                    separatedIntoElements[1].innerHTML = minStr;
                    separatedIntoElements[2].innerHTML = secStr;
                }
                else countdownElement.innerHTML = `${hourStr}:${minStr}:${secStr}`;
            }
            else {
                if (separatedIntoElements?.length) {
                    separatedIntoElements[0].innerHTML = minStr;
                    separatedIntoElements[1].innerHTML = secStr;

                }
                else countdownElement.innerHTML = `${minStr}:${secStr}`;
            }
        }, 0);
};

window.addEventListener('load', function () {
    global?.onloadRoutines.forEach(item => {
        item.callback();
    });
    [].map.call(document.querySelectorAll('[load]'), element => {
        let load = element.getAttribute('load');
        eval(load);
    });
});

global.onloadRoutines.push({
    name: "countdown", callback: function () {
        countdown('countdown');
    }
});

let toggleChangeHandler = function () {
    let name = this.getAttribute('toggle-change');
    let selected = this.value.trim().replace(/\n|\r|\t/g, '');
    [].map.call(document.querySelectorAll(`[toggle-change-item^="${name}-"]`), item => {
        item.style.display = 'none';
        item.classList.remove('d-block');
        item.classList.add('d-none');
    });

    [].map.call(document.querySelectorAll(`[toggle-change-item="${name}-${selected}"]`), current => {
        current.style.display = 'block';
        current.classList.add('d-block');
        current.classList.remove('d-none');
    });
    // [].map.call(document.querySelectorAll(`[\\!toggle-change-item="${name}-${selected}"]`), current => {
    //     current.style.display = 'none';
    // });
    // let currentNot = document.querySelector(`[\\!toggle-change-item="${name}-${selected}"]`);
    // if (currentNot) currentNot.style.display = 'none';
}

global.onloadRoutines.push({
    name: "toggleChange", callback: function () {
        [...document.querySelectorAll('[toggle-change]')].forEach(element => {
            if (element) {
                element.removeEventListener('change', toggleChangeHandler);
                element.addEventListener('change', toggleChangeHandler);
                if (!element.hasAttribute('no-dispatch-change-onload'))
                    element.dispatchEvent(new Event('change'));
            }
        });
    }
});

global.onloadRoutines.push({
    name: "coloringSidebar", callback: function () {
        [...document.querySelectorAll('.ez-sidebar a')].forEach(a => {
            a.addEventListener('click', function () {
                [...document.querySelectorAll('.ez-sidebar li')].forEach(li => {
                    li.classList.remove('active');
                });
                [...document.querySelectorAll('.ez-sidebar a')].forEach(a => {
                    a.classList.remove('active');
                });
                $(this).parents('li')[0].classList.add('active');
            });
        });
    }
});

function expiredWarranty(base, days) {
    days = parseInt(days);
    let baseDate = new Date(base);
    let now = new Date;
    let addedDaysTimestamp = baseDate.setDate(baseDate.getDate() + days);
    return now.getTime() > addedDaysTimestamp;
}

global.onloadRoutines.push({
    name: "slugify", callback: function () {
        (function () {
            function Slug() {
                let fn = this;

                fn.slug = function () {
                    var reg = /\"|\'|\`|\´|\?|\!|\\|\[|\^|\~|\*|\]|ª|º|{|}|>|<|#|\$|\%|\(|\)|\§|\=/g;
                    var ciuri = this;
                    ciuri = ciuri.replace(reg, '');
                    ciuri = ciuri.replace(/\s/g, '-');
                    ciuri = ciuri.replace(/\:/g, '-');
                    ciuri = ciuri.replace(/\//g, '-');
                    ciuri = ciuri.replace(/ã/g, 'a');
                    ciuri = ciuri.replace(/õ/g, 'o');
                    ciuri = ciuri.replace(/á/g, 'a');
                    ciuri = ciuri.replace(/é/g, 'e');
                    ciuri = ciuri.replace(/í/g, 'i');
                    ciuri = ciuri.replace(/ó/g, 'o');
                    ciuri = ciuri.replace(/ú/g, 'u');
                    ciuri = ciuri.replace(/â/g, 'a');
                    ciuri = ciuri.replace(/ê/g, 'e');
                    ciuri = ciuri.replace(/î/g, 'i');
                    ciuri = ciuri.replace(/ô/g, 'o');
                    ciuri = ciuri.replace(/û/g, 'u');
                    ciuri = ciuri.replace(/à/g, 'a');
                    ciuri = ciuri.replace(/è/g, 'e');
                    ciuri = ciuri.replace(/ì/g, 'i');
                    ciuri = ciuri.replace(/ò/g, 'o');
                    ciuri = ciuri.replace(/ù/g, 'u');
                    ciuri = ciuri.replace(/ç/g, 'c');
                    ciuri = ciuri.replace(/\&/g, 'e');
                    ciuri = ciuri.toLowerCase().trim();
                    return ciuri;
                };

                fn.inputSlug = () => {
                    [].map.call(document.querySelectorAll('input[type=slug]'), input => {
                        input.addEventListener('keyup', function () {
                            return this.value = this.value.slug();
                        });
                        input.addEventListener('blur', function () {
                            let str = this.value.trim();
                            let lastChar = str.charAt(str.length - 1);
                            if (lastChar == '-') str = str.substring(0, str.length - 1);
                            return this.value = str;
                        });
                        input.setAttribute('spellcheck', false);
                    });
                    [].map.call(document.querySelectorAll('input[slug]'), input => {
                        let target = document.querySelector(input.getAttribute('slug'));
                        input.addEventListener('keyup', function () {
                            target.value = this.value.slug();
                        });
                        input.addEventListener('blur', function () {
                            let str = target.value.trim();
                            let lastChar = str.charAt(str.length - 1);
                            if (lastChar == '-') str = str.substring(0, str.length - 1);
                            target.value = str;
                            input.value = input.value.trim();
                        });
                        target.setAttribute('spellcheck', false);
                    });
                };

                fn.init = () => {
                    String.prototype.slug = fn.slug;
                    fn.inputSlug();
                };

                fn.init();
            };

            new Slug();
        }());
    }
});

const element = document.querySelector('[json-id]');
const id = element?.getAttribute('json-id');
console.log(id);
function tagJSON(id) {
    const textJson = document.querySelector(`[json-id="${id}"]`)?.textContent;
    
    if (!textJson) return {}
    return JSON.parse(textJson);
}

const updateTagJSON = (id, data) => (document.querySelector(`[json-id="${id}"]`).textContent = JSON.stringify(data));

const stacksExec = [];
const endStack = (instance) => {
    let n = 0;
    [].map.call(document.querySelectorAll('e'), element => {
        let html = element.innerHTML;
        stacksExec[n] = { html, element };
        let text = instance?.e(html);

        element.innerHTML = text;
        element.style.display = 'inherit';
        n++;
    });
}

const stackReload = instance => {
    stacksExec.forEach(stack => {
        stack.element.innerHTML = stack.html;
    });
    endStack(instance);
}

window.addEventListener('load', () => {
    endStack(controllerInfo());
});

window.SiteScope = {
    params
};

// const textEllipisis = () => {
//     [...document.querySelectorAll('[ellipsis]')].forEach(item => {
//         const text = item.getAttribute('ellipsis');
//         const initialHeight = item.getAttribute('initial-height');
//         const currentHeight = item.getBoundingClientRect().height;
//         const nextElement = item.nextElementSibling;
//         if (currentHeight > initialHeight) {
//             nextElement.style.display = 'block';
//         }
//         else {
//             nextElement.style.display = 'none';
//         }
//         console.log(`${initialHeight} ${currentHeight}\n`);
//     });
// };

const textEllipisis = () => {
    [...document.querySelectorAll('[ellipsis]')].forEach(item => {
        const text = item.getAttribute('ellipsis');
        const initialHeight = item.getAttribute('initial-height');
        const currentHeight = item.getBoundingClientRect().height;
        const currentWidth = item.getBoundingClientRect().width;
        const initialLength = text.length;
        const current = text.substring(0, currentWidth / 10.4).trim();
        const currentLength = current.length;

        if (currentLength < initialLength) {
            item.innerHTML = current + '...';
        }
        else {
            item.innerHTML = text;
        }
    });
};

// window.addEventListener('resize', function() {
//     textEllipisis();
// });

// window.addEventListener('load', function() {
//     textEllipisis();
// });

function jsdateToDateTime(date, time='') {
    let years = date.getFullYear();
    let months = date.getMonth() + 1; months = months < 10 ? '0' + months : months;
    let days = date.getDate(); days = days < 10 ? '0' + days : days;
    let hours = date.getHours(); hours = hours < 10 ? '0' + hours : hours;
    let minutes = date.getMinutes(); minutes = minutes < 10 ? '0' + minutes : minutes;
    let seconds = date.getSeconds(); seconds = seconds < 10 ? '0' + seconds : seconds;
    if (!time) time = `${hours}:${minutes}:${seconds}`;
    return `${years}/${months}/${days} ${time}`;
}

let __ = function (text, vars = []) {
    let list = <?= json_encode($GLOBALS["translate"]) ?: '{}'; ?>;
    let str = list[text] || text;
    for (let i in vars)
        str = str.replace(new RegExp(i, 'g'), vars[i]);
    return str;
}