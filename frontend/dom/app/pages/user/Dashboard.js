App.User.Dashboard = class Dashboard extends Page {
    context = 'dashboard';
    title = 'Dashboard';
    className = 'App.User.Dashboard';
    myChart;
    SalesChart = {
        startDate: null,
        endDate: null
    };

    view(loaded, link) {
        return super.find(`${link?.full ? 'full/' : ''}user/${this?.constructor?.name || ''}${this?.queryString || ''}`, (div) => {
            // global.onloadRoutines.push({
            //     name: "dashboardStats", callback: this.stats
            // });

            // global.onloadRoutines.push({
            //     name: "dashboardChart", callback: this.chart
            // });

            setTimeout(() => {
                this.pageLoaded();
            }, 0);
            
            return loaded(div);
        });
    }

    pageLoaded() {
        this.chart();
        this.filterDataPicker();
        this.stats();
        this.filterArea();
    }

    ready() {
        this.pageLoaded();
    }

    load() {
        // ...
    }

    end() {
        // ...
    }
    
    addAnnotations(data) {
        // Exemplo de anotações de linhas
        const average = data.reduce((a, b) => a + b) / data.length;
        return [
            {
                type: 'line',
                mode: 'horizontal',
                scaleID: 'y',
                value: average,
                borderColor: 'red',
                borderWidth: 2,
                label: {
                    content: 'Média',
                    enabled: true,
                    position: 'center'
                }
            },
            {
                type: 'line',
                mode: 'vertical',
                scaleID: 'x',
                value: 'Jul',
                borderColor: 'blue',
                borderWidth: 2,
                label: {
                    content: 'Ponto Médio',
                    enabled: true,
                    position: 'start'
                }
            }
        ];
    }

    filterDataPicker() {
        window.xyz=flatpickr(document?.getElementById('date-picker'), {
            mode: 'range',
            dateFormat: 'd/m/Y',
            locale: 'pt',
            onClose: (selectedDates) => {
                if (selectedDates.length === 2) {
                    const startDate = selectedDates[0];
                    const endDate = selectedDates[1];
                    this.SalesChart.startDate = startDate;
                    this.SalesChart.endDate = endDate;

                    // Example data generation for custom date range
                    const diffDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
                    const customLabels = Array.from({ length: diffDays + 1 }, (_, i) => {
                        const date = new Date(startDate);
                        date.setDate(date.getDate() + i);
                        return date.toISOString().split('T')[0];
                    });
                    const customData = Array.from({ length: diffDays + 1 }, () => Math.floor(Math.random() * 1000));
                    // this.createChart(customLabels, customData, this.addAnnotations(customData));
                }
            }
        });
    }
    
    createChart(labels, data, annotations = []) {
        const ctx = document.getElementById('myChart').getContext('2d');

        if (this.myChart) {
            this.myChart.destroy();
        }
        
        this.myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '',
                    data: data,
                    lineTension: 0,
                    backgroundColor: 'transparent',
                    borderColor: '#007bff',
                    borderWidth: 4,
                    pointBackgroundColor: '#007bff'
                }]
            },
            options: {
                responsive: true,
                tooltip: {
                    boxPadding: 3
                },
                annotation: {
                    annotations: annotations
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        display: false
                    }
                }
            }
        });
    }

    chart() {
        const { 
            last_7_days, 
            last_30_days,
            last_12_months,
            sales_last_week, 
            sales_last_month,
            sales_last_12_months
        } = tagJSON('dashboardInfo');

        const chartData = {
            yearly: {
                labels: last_12_months,
                data: sales_last_12_months
            },
            monthly: {
                labels: last_30_days,
                data: sales_last_month
            },
            weekly: {
                labels: last_7_days,
                data: sales_last_week
            }
        };

        document.getElementById('yearly')?.addEventListener('click', () => {
            this.createChart(chartData.yearly.labels, chartData.yearly.data, this.addAnnotations(chartData.yearly.data));
        });

        document.getElementById('monthly')?.addEventListener('click', () => {
            this.createChart(chartData.monthly.labels, chartData.monthly.data, this.addAnnotations(chartData.monthly.data));
        });

        document.getElementById('weekly')?.addEventListener('click', () => {
            this.createChart(chartData.weekly.labels, chartData.weekly.data, this.addAnnotations(chartData.weekly.data));
        });

        // Initialize with yearly data
        this.createChart(chartData.yearly.labels, chartData.yearly.data, this.addAnnotations(chartData.yearly.data));
        console.log(chartData);
    }

    stats() {
        let hideVendasButton = document.querySelector('#eye-button1');
        let vendasHoje = document.querySelector('#vendashoje');
        let hideSaldoButton = document.querySelector('#eye-button2');
        let saldoDisponivel = document.querySelector('#saldodisponivel');

        vendasHoje.style.opacity = 1;
        saldoDisponivel.style.opacity = 1;

        let originalVendasContent = vendasHoje.textContent;
        let isVendasMasked = false;
        let maskSymbol = '*';

        // vendasHoje.textContent = maskSymbol.repeat(originalVendasContent.length);
        
        hideVendasButton?.addEventListener('click', function () {
            if (isVendasMasked) {
                vendasHoje.textContent = originalVendasContent;
            } else {
                vendasHoje.textContent = maskSymbol.repeat(originalVendasContent.length);
            }
            isVendasMasked = !isVendasMasked;
        });

        let originalSaldoContent = saldoDisponivel.textContent;
        let isSaldoMasked = false;

        // saldoDisponivel.textContent = maskSymbol.repeat(originalSaldoContent.length);
        
        hideSaldoButton?.addEventListener('click', function () {
            if (isSaldoMasked) {
                saldoDisponivel.textContent = originalSaldoContent;
            } else {
                saldoDisponivel.textContent = maskSymbol.repeat(originalSaldoContent.length);
            }
            isSaldoMasked = !isSaldoMasked;
        });

        [].map.call(document.querySelectorAll('.btns_period_chart > button'), button => {
            button.addEventListener('click', function() {
                [].map.call(document.querySelectorAll('.btns_period_chart > button'), button => {
                    button.classList.remove('btn-period-chat-active');
                });

                this.classList.add('btn-period-chat-active');
            });
        });
    }
    
    async dashboardFilterChartOnClick() {
        const { 
            last_7_days, 
            last_30_days,
            last_12_months,
            sales_last_week, 
            sales_last_month,
            sales_last_12_months
         } = tagJSON('dashboardInfo')

        const chartData = {
            yearly: {
                labels: last_12_months,
                data: sales_last_12_months
            },
            monthly: {
                labels: last_30_days,
                data: sales_last_month
            },
            weekly: {
                labels: last_7_days,
                data: sales_last_week
            }
        };

        this.createChart(chartData.yearly.labels, chartData.yearly.data, this.addAnnotations(chartData.yearly.data));

        const dStart = new Date(this.SalesChart.startDate);
        const dEnd = new Date(this.SalesChart.endDate);

        const start = jsdateToDateTime(dStart, '00:00:00');
        const end = jsdateToDateTime(dEnd, '23:59:59');

        const payload = {
            start,
            end,
            product_id: document.getElementById('dashboardSelectProduct')?.value || ''
        }

        const response = await fetch(`/ajax/actions/user/dashboard/sales/filter?${$.param(payload)}`);
        const { data } = await response.json();
        
        this.createChart(data.labels, data.values, this.addAnnotations(data.values));
    }

    filterArea() {
        $('body').on('click', function(ev) {
            const element = $(ev.target).parents('.float-filters')[0];
            const floatFilter = document.querySelector('.float-filters');
            if (!floatFilter) return;
            const open = document.querySelector('.flatpickr-calendar')?.classList.contains('open');

            const visibility = floatFilter.hasAttribute('visible');
            if (!element && visibility && !open) {
                floatFilter.style.display = 'none';
                floatFilter.removeAttribute('float-filters');
            }
        });
        
        document.getElementById('btnFilters')?.addEventListener('click', function() {
            setTimeout(() => {
                const btnFilters = this;
                const floatFilter = document.querySelector('.float-filters');
                floatFilter.setAttribute('visible', true);
                floatFilter.style.display = 'block';
                floatFilter.style.left = btnFilters.getBoundingClientRect().left + 'px';
                floatFilter.style.marginTop = (btnFilters.getBoundingClientRect().height + 20) + 'px';
            }, 0);
        });
    }
};