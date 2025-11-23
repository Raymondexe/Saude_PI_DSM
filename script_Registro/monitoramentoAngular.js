const app = angular.module('monitorApp', []);

app.controller('monitorCtrl', function ($scope) {

    const ctx = document.getElementById('graficoMonitoramento').getContext('2d');

    $scope.tipoSelecionado = 'temperatura';
    let grafico = null;

    function carregarRegistros(tipo) {
        return JSON.parse(localStorage.getItem(tipo)) || [];
    }

    function criarDataset(label, valores) {
        return {
            label,
            data: valores,
            borderWidth: 2,
            tension: 0.3
        };
    }

    function formatarDados(tipo, registros) {
        const labels = registros.map(r => r.data || '—');

        let datasets = [];

        switch (tipo) {

            case 'batimentos':
                datasets.push(
                    criarDataset("Batimentos", registros.map(r => r.valor))
                );
                break;

            case 'glicemia':
                datasets.push(
                    criarDataset("Glicemia", registros.map(r => r.valorGlicemia))
                );
                break;

            case 'temperatura':
                datasets.push(
                    criarDataset("Temperatura", registros.map(r => r.valorTemperatura))
                );
                break;

            case 'pressao':
                datasets.push(
                    criarDataset("Sistólica", registros.map(r => r.sistolica)),
                    criarDataset("Diastólica", registros.map(r => r.diastolica))
                );
                break;
        }

        return { labels, datasets };
    }

    function desenharGrafico(labels, datasets) {
        if (grafico) grafico.destroy();

        grafico = new Chart(ctx, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: false }
                }
            }
        });
    }

    $scope.atualizarGrafico = function () {
        const tipo = $scope.tipoSelecionado;
        const registros = carregarRegistros(tipo);
        const { labels, datasets } = formatarDados(tipo, registros);
        desenharGrafico(labels, datasets);
    };

    // Inicializa
    $scope.atualizarGrafico();
});
