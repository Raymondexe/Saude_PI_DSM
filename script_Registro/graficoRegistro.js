const app = angular.module('monitorApp', []);

app.controller('monitorCtrl', function ($scope) {

    const ctx = document
        .getElementById('graficoMonitoramento')
        .getContext('2d');

    let grafico = null;

    $scope.tipoSelecionado = 'temperatura';

    function criarDataset(label, dados) {

        return {
            label: label,
            data: dados,
            borderWidth: 2,
            tension: 0.3
        };

    }

    function gerarGrafico(tipo) {

        const registros = dadosGrafico[tipo];

        if (!registros || registros.length === 0) {

            if (grafico) {
                grafico.destroy();
            }

            return;
        }

        const labels = registros.map(r => r.data);

        let datasets = [];

        switch (tipo) {

            case 'batimentos':

                datasets.push(
                    criarDataset(
                        'Batimentos',
                        registros.map(r => r.valor)
                    )
                );

                break;

            case 'temperatura':

                datasets.push(
                    criarDataset(
                        'Temperatura',
                        registros.map(r => r.valor)
                    )
                );

                break;

            case 'glicemia':

                datasets.push(
                    criarDataset(
                        'Glicemia',
                        registros.map(r => r.valor)
                    )
                );

                break;

            case 'pressao':

                datasets.push(
                    criarDataset(
                        'Sistólica',
                        registros.map(r => r.sistolica)
                    )
                );

                datasets.push(
                    criarDataset(
                        'Diastólica',
                        registros.map(r => r.diastolica)
                    )
                );

                break;

        }

        if (grafico) {
            grafico.destroy();
        }

        grafico = new Chart(ctx, {

            type: 'line',

            data: {
                labels: labels,
                datasets: datasets
            },

            options: {

                responsive: true,

                scales: {
                    y: {
                        beginAtZero: false
                    }
                }

            }

        });

    }

    $scope.atualizarGrafico = function () {

        gerarGrafico($scope.tipoSelecionado);

    };

    gerarGrafico($scope.tipoSelecionado);

});