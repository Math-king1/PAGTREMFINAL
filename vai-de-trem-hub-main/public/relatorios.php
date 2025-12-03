<?php
require_once('../assets/config/auth.php');
require_once('../assets/config/db.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Relatórios - Vai de Trem</title>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
  <link href="../assets/css/styles.css" rel="stylesheet">
</head>

<body>

  <!-- HEADER -->
  <div class="top-header">
    <h1><img src="../assets/images/grafico_icone.png" alt="Relatórios" class="icon-img" style="width:22px;height:22px;">
      Relatórios</h1>
  </div>

  <div class="container" style="padding-bottom: 80px;">
    <!-- KPI GRID -->
    <div class="kpi-grid">
      <div class="kpi-card">
        <div class="kpi-label">Viagens/Mês</div>
        <div class="kpi-value">185</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Pontualidade</div>
        <div class="kpi-value">94%</div>
      </div>
      <div class="kpi-card">
        <div class="kpi-label">Satisfação</div>
        <div class="kpi-value">4.7</div>
      </div>
    </div>

    <!-- CHARTS -->
    <div class="chart-section">

      <div class="chart-card">
        <strong>Passageiros e Viagens por Mês</strong>
        <div class="chart-sub">Últimos 6 meses</div>
        <canvas id="chartPassageiros"></canvas>
      </div>

      <div class="chart-card">
        <strong>Distribuição por Rota</strong>
        <div class="chart-sub">Percentual de uso</div>
        <canvas id="chartRotas"></canvas>
      </div>

    </div>
  </div>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Chart 1: Passageiros e Viagens
    const ctx1 = document.getElementById('chartPassageiros').getContext('2d');
    new Chart(ctx1, {
      type: 'line',
      data: {
        labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
        datasets: [{
          label: 'Passageiros (k)',
          data: [8.5, 9.2, 8.8, 9.5, 10.1, 10.2],
          borderColor: '#2563eb',
          backgroundColor: 'rgba(37, 99, 235, 0.1)',
          tension: 0.4,
          fill: true
        }, {
          label: 'Viagens',
          data: [150, 165, 160, 175, 180, 185],
          borderColor: '#10b981',
          backgroundColor: 'rgba(16, 185, 129, 0.1)',
          tension: 0.4,
          fill: true,
          yAxisID: 'y1'
        }]
      },
      options: {
        responsive: true,
        interaction: {
          mode: 'index',
          intersect: false,
        },
        scales: {
          y: {
            type: 'linear',
            display: true,
            position: 'left',
          },
          y1: {
            type: 'linear',
            display: true,
            position: 'right',
            grid: {
              drawOnChartArea: false,
            },
          },
        }
      }
    });

    // Chart 2: Distribuição por Rota
    const ctx2 = document.getElementById('chartRotas').getContext('2d');
    new Chart(ctx2, {
      type: 'doughnut',
      data: {
        labels: ['Linha 1 - Azul', 'Linha 2 - Verde', 'Linha 3 - Vermelha', 'Linha 4 - Amarela'],
        datasets: [{
          data: [35, 25, 20, 20],
          backgroundColor: [
            '#2563eb',
            '#10b981',
            '#ef4444',
            '#f59e0b'
          ],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom',
          }
        }
      }
    });
  </script>

  <!-- NAV MOBILE -->
  <?php include '_partials/bottom_nav.php'; ?>

</body>

</html>