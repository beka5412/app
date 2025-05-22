class Routes {
    use(domain, path, instance) {
        instance.pageDomain = domain;
        if (!global.routes[domain]) global.routes[domain] = {};        
        global.routes[domain][path] = instance;
    }

    watch() {
        // ...
    }
}