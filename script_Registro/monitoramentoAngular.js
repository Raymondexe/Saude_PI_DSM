const app = angular.module('monitorApp', []);

app.controller('monitorCtrl', function($scope) {
    const ctx = document.getElementById('graficoMonitoramento').getContext('2d');

    // Tipo inicial
    $scope.tipoSelecionado = 'batimentos';

    let grafico = null;

    function carregarRegistros(tipo) {
        return JSON.parse(localStorage.getItem(tipo)) || [];
    }

    function formatarDados(tipo, registros) {
        const labels = registros.map(r => r.data || '—');
        let valores = [];

        switch (tipo) {
            case 'batimentos':
                valores = registros.map(r => r.valor || r);
                break;
            case 'glicemia':
                valores = registros.map(r => r.valorGlicemia);
                break;
            case 'pressao':
                valores = registros.map(r => `${r.sistolica}/${r.diastolica}`);
                break;
            case 'temperatura':
                valores = registros.map(r => r.valorTemperatura);
                break;
        }

        return { labels, valores };
    }

    function desenharGrafico(labels, valores, titulo) {
        if (grafico) grafico.destroy(); // limpa gráfico anterior

        grafico = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: titulo,
                    data: valores,
                    borderWidth: 2,
                    tension: 0.3
                }]
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

    $scope.atualizarGrafico = function() {
        const tipo = $scope.tipoSelecionado;
        const registros = carregarRegistros(tipo);
        const { labels, valores } = formatarDados(tipo, registros);
        desenharGrafico(labels, valores, tipo.charAt(0).toUpperCase() + tipo.slice(1));
    };

    // Inicializa
    $scope.atualizarGrafico();
});


