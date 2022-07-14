$(document).ready(function () {
    // Start Age Chart
    if ($('#homeAge').length ){
        var data = {
            labels: [ "0-11", "11-18", "19-22", "23-27", "28-32", "33-40", "40-50", "50+"],
            datasets: [{
                label: "",
                data: age_data,
                backgroundColor: "#ffffff",
            }]
        }
        var ctx = document.getElementById("homeAge");
        new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                "hover": {
                    "animationDuration": 0
                },
                "animation": {
                    "duration": 1,
                    "onComplete": function() {
                        var chartInstance = this.chart,
                            ctx = chartInstance.ctx;
                        ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';

                        this.data.datasets.forEach(function(dataset, i) {
                            var meta = chartInstance.controller.getDatasetMeta(i);
                            meta.data.forEach(function(bar, index) {
                                var data = dataset.data[index];
                                ctx.fillText(data, bar._model.x, bar._model.y - 5);
                            });
                        });
                    }
                },
                legend: {
                    "display": false
                },
                tooltips: {
                    "enabled": false
                },
                scales: {
                    yAxes: [{
                        display: true,
                        gridLines: {
                            display: true
                        },
                        ticks: {
                            // stepSize: 1500,
                            max: Math.max(...data.datasets[0].data) + (Math.max(...data.datasets[0].data) * 30/100),
                            display: true,
                            beginAtZero: true
                        },
                    }],
                    xAxes: [{
                        gridLines: {
                            display: true
                        },
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    }
    // End Age Chart


    if ($('#dayOfWeeks').length ){
        var ctx = document.getElementById("dayOfWeeks");
        var data = {
            labels:  labels,
            datasets: [{
                label: "Customers",
                backgroundColor: "rgba(0,0,0,0)",
                borderColor: "#ffffff",
                pointBorderColor: "rgba(38, 185, 154, 0.7)",
                pointBackgroundColor: "rgba(38, 185, 154, 0.7)",
                pointHoverBackgroundColor: "#fff",
                pointHoverBorderColor: "rgba(220,220,220,1)",
                pointBorderWidth: 1,
                data: data
            }]
        };
        var dayOfWeeks = new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                "hover": {
                    "animationDuration": 0
                },
                "animation": {
                    "duration": 1,
                    "onComplete": function() {
                        var chartInstance = this.chart,
                            ctx = chartInstance.ctx;

                        ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';

                        this.data.datasets.forEach(function(dataset, i) {
                            var meta = chartInstance.controller.getDatasetMeta(i);
                            meta.data.forEach(function(bar, index) {
                                var data = dataset.data[index];
                                ctx.fillText(data, bar._model.x, bar._model.y - 5);
                            });
                        });
                    }
                },
                legend: {
                    "display": false
                },
                tooltips: {
                    "enabled": false
                },
                scales: {
                    yAxes: [{
                        display: true,
                        gridLines: {
                            display: true
                        },
                        ticks: {
                            max: Math.max(...data.datasets[0].data) + 100,
                            display: true,
                            beginAtZero: true
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false
                        },
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    }
});