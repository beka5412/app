class Link {
    to(url, instance, state) {
        let self = this;
        // app().innerHTML = '';
        let protocol = url.split('/')[0] || '';
        let host = url.split('/')[2] || '';
        let domain = '';
        if (protocol && host) {
            domain = protocol + '//' + host;
            url = url.replace(domain, '');
        }
        let queryString = url.split('?')[1] || '';
        if (queryString) queryString = '?' + queryString;

        // console.log(instance); && instance.pageDomain == domain

        // problema de usar propriedade no instance:
        // a rota /login do site principal e /login de checkout vao ser o mesmo objeto

        if (instance) {
            instance.link = self;
            global.hadRedirection = true; // diz que teve um redirecionamento
            if (queryString) instance.queryString = queryString;
            let view = instance.view(div => {
                let pushState = { ...state, url, domain, full: instance?.link?.full || false };
                window.history.pushState(pushState, '', url);
                if (instance.title) document.title = instance.title;
            }, self);
        }

        else {
            let found = false;
            
            if (global.routes[domain]) {
                                
                // procura rotas estaticas
                Object.entries(global.routes[domain]).forEach(item => {
                    // url_ = url na lista de rotas definidas
                    // url  = url requisitada
                    let url_ = item[0] || '';
                    let instance = item[1];
                    url = url.split('?')[0];
                    url_ = url_.split('?')[0];
                    if (!found && url == url_) {
                        instance.link = self;
                        global.hadRedirection = true; // diz que teve um redirecionamento
                        if (queryString) instance.queryString = queryString;
                        let view = instance.view(div => {
                            let pushState = { ...state, url, page: instance.constructor.name, domain, full: instance?.link?.full || false };
                            window.history.pushState(pushState, '', url + queryString);
                            if (instance.title) document.title = instance.title;
                        }, self);
                        found = true;
                    }
                });

                // procura rotas dinamicas (com parametros)
                if (!found) Object.entries(global.routes[domain]).forEach(item => {
                    // url_ = url na lista de rotas definidas
                    // url  = url requisitada
                    let url_ = item[0] || '';
                    let instance = item[1];
                    url = url.split('?')[0];
                    url_ = url_.split('?')[0];

                    // na url requisitada, substituir valores pelas variaveis para que a comparacao acerte a rota
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
                            let key = i.replace(/\{/, '').replace(/\}/,'').trim();
                            params[key] = p;
                        }
                        else 
                            newUrlArr.push(p);
                        n++;
                    });
                    let requestedUrlVars = newUrlArr.join('/');
                    
                    if (!found && requestedUrlVars == url_) {
                        instance.link = self;
                        instance.params = params;
                        global.params = params;

                        global.hadRedirection = true; // diz que teve um redirecionamento
                        if (queryString) instance.queryString = queryString;
                        let view = instance.view(div => {
                            let pushState = { ...state, url, page: instance.constructor.name, domain, full: instance?.link?.full || false };
                            window.history.pushState(pushState, '', url + queryString);
                            if (instance.title) document.title = instance.title;
                        }, self);
                        found = true;
                    }
                });
            }

            if (!found) {
                global.hadRedirection = true;
                let notfoundInstance = new App.Browser[pascalCase(global.pageContext)].NotFound;

                if (global.pageContext) notfoundInstance.view(div => {
                    window.history.pushState({ ...state, url: url + queryString, page: notfoundInstance.constructor.name, domain }, '', url);
                    document.title = notfoundInstance.title;
                }, self);

                else console.error('Nenhum contexto informado.');
            }
        }
    }
}