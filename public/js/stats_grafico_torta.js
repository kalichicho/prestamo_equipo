
    // Gráfico de torta mejorado
    const ctxTorta = document.getElementById('graficoTorta').getContext('2d');
    new Chart(ctxTorta, {
        type: 'doughnut',
        data: {
            labels: ['Activos', 'Dados de Baja', 'Prestados', 'Sin asignar'],
            datasets: [{
                label: 'Estado de Dispositivos',
                data: [
                    <?= $stats['activos'] ?? 0 ?>,
                    <?= $stats['bajas'] ?? 0 ?>,
                    <?= $stats['prestados'] ?? 0 ?>,
                    <?= $stats['sin_asignar'] ?? 0 ?>
                ],
                backgroundColor: [
                    '#198754', // Verde activo
                    '#dc3545', // Rojo baja
                    '#ffc107', // Amarillo prestado
                    '#6c757d'  // Gris sin asignar
                ],
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#555',
                        padding: 20,
                        boxWidth: 15
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true
            }
        }
    });

