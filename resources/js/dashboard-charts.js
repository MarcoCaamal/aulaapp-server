import ApexCharts from "apexcharts";

document.addEventListener('DOMContentLoaded', () => {
    iniciarApp();
});

function iniciarApp() {
    iniciarGraficaAsistenciasPorMes();
    iniciarGraficaDePastel();
}

function iniciarGraficaAsistenciasPorMes() {
    const chartAsistenciasMes = document.querySelector('#chartAsistencias');
    const colors = Object.values(config.colors);
    const data = [
        {
            x: 'Algebra',
            y: 20,
        },
        {
            x: 'Química',
            y: 10,
        },
        {
            x: 'Calculo Diferencial',
            y: 40
        },
        {
            x: 'Química II',
            y: 13
        }
    ]

    const options = {
        series: [
            {
                data: data,
                name: 'Asesorias'
            }
        ],
        chart: {
            type: 'bar',
            // height: 350
            width: 400
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
                distributed: true
            }
        },
        dataLabels: {
            enabled: false,
        },
        colors: colors,
        tooltip: {
            y: {
                formatter: function (value, { series, seriesIndex, dataPointIndex, w }) {
                    return value
                },
                title: {
                    formatter: (seriesName) => {
                        return 'Alumnos'
                    }
                }
            }
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 300
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    const chart = new ApexCharts(chartAsistenciasMes, options);
    chart.render();
}

function iniciarGraficaDePastel() {
    const options = {
        series: [44, 55, 13, 43, 22],
        chart: {
            type: 'pie',
            width: 452
        },
        labels: ['Algebra', 'Química II', 'Calculo Diferencial', 'Calculo Integral', 'Química'],
        responsive: [
            {
                breakpoint: 480,
                options: {
                    chart: {
                        width: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        ]
    };

    const chart = new ApexCharts(document.querySelector("#chartPastel"), options);
    chart.render();
}