<?php
require_once('../assets/config/auth.php');
require_once('../assets/config/db.php');

// Helper para formatar duração
function formatDuration($minutes)
{
    if (!$minutes)
        return '0min';
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    if ($hours > 0) {
        return "{$hours}h {$mins}min";
    }
    return "{$mins}min";
}

// PROCESSAR FORMULÁRIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';

    // Novos campos
    $origin = trim($_POST['origin'] ?? '');
    $destination = trim($_POST['destination'] ?? '');

    // Duração agora vem em horas e minutos
    $hours = (int) ($_POST['duration_hours'] ?? 0);
    $minutes = (int) ($_POST['duration_minutes'] ?? 0);
    $duration_minutes = ($hours * 60) + $minutes;

    // Nome da rota é composto
    $name = "$origin → $destination";

    $stops = trim($_POST['stops'] ?? '');
    $extra_info = trim($_POST['extra_info'] ?? '');
    $status = $_POST['status'] ?? 'ativa';

    if ($action === 'create') {
        $stmt = $mysqli->prepare("INSERT INTO routes (name, origin, destination, stops, duration_minutes, extra_info, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssiss', $name, $origin, $destination, $stops, $duration_minutes, $extra_info, $status);
        $stmt->execute();
    } elseif ($action === 'update' && $id) {
        $stmt = $mysqli->prepare("UPDATE routes SET name=?, origin=?, destination=?, stops=?, duration_minutes=?, extra_info=?, status=? WHERE id=?");
        $stmt->bind_param('ssssissi', $name, $origin, $destination, $stops, $duration_minutes, $extra_info, $status, $id);
        $stmt->execute();
    } elseif ($action === 'delete' && $id) {
        $stmt = $mysqli->prepare("DELETE FROM routes WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }

    header('Location: rotas.php');
    exit;
}

// DELETE VIA GET
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $mysqli->query("DELETE FROM routes WHERE id=$id");
    header('Location: rotas.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rotas - Vai de Trem</title>

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">

</head>

<body>

    <!-- HEADER -->
    <div class="top-header">
        <h1><img src="../assets/images/rotas_icone.png" alt="Rotas" class="icon-img" style="width:22px;height:22px;">
            Rotas</h1>
    </div>

    <!-- LISTA DE ROTAS -->
    <div class="route-list" id="routeList" style="padding-bottom: 80px;">
        <?php
        $res = $mysqli->query("SELECT * FROM routes ORDER BY id DESC");
        while ($r = $res->fetch_assoc()) {
            $badgeClass = ($r['status'] === 'manutencao') ? 'red' : 'blue';
            $badgeText = ($r['status'] === 'manutencao') ? 'Manutenção' : 'Ativa';
            $durationFmt = formatDuration($r['duration_minutes']);

            // Prepara dados para o JS
            $jsonData = htmlspecialchars(json_encode($r), ENT_QUOTES, 'UTF-8');

            echo "
            <div class='route-card' onclick='editRoute($jsonData)'>
                <div class='route-title'>" . htmlspecialchars($r['name']) . "</div>
                <span class='badge $badgeClass'>$badgeText</span>

                <div class='details'>
                    <img src='../assets/images/local_icone.png' class='icon-img' style='width:16px;height:16px;'> Paradas: " . htmlspecialchars($r['stops'] ?? '') . "<br>
                    <img src='../assets/images/relogio_icone.png' class='icon-img' style='width:16px;height:16px;'> Duração: $durationFmt
                </div>

                <div class='live-info'>
                    <img src='../assets/images/notificacao_icone.png' class='icon-img' style='width:16px;height:16px;'> " . (!empty($r['extra_info']) ? htmlspecialchars($r['extra_info']) : 'Sem informações adicionais') . "
                </div>
            </div>";
        }
        ?>
    </div>

    <!-- BOTÃO "+" (FAB) -->
    <div class="fab" onclick="openCreateModal()">
        <i class="ri-add-line" style="font-size: 32px;"></i>
    </div>

    <!-- MODAL -->
    <div class="modal-bg" id="modal">
        <div class="modal" onclick="event.stopPropagation()">
            <h2 id="modalTitle">Nova Rota</h2>
            <form method="post">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="routeId">

                <div style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <label>Local de Embarque</label>
                        <input class="input" name="origin" id="routeOrigin" placeholder="Ex: São Paulo" required>
                    </div>
                    <div style="flex:1;">
                        <label>Destino</label>
                        <input class="input" name="destination" id="routeDestination" placeholder="Ex: Rio de Janeiro"
                            required>
                    </div>
                </div>

                <label>Paradas</label>
                <input class="input" name="stops" id="routeStops" placeholder="Separadas por vírgula" required>

                <label>Duração</label>
                <div style="display:flex; gap:10px; align-items:center;">
                    <div style="flex:1;">
                        <input type="number" class="input" name="duration_hours" id="routeHours" placeholder="Horas"
                            min="0" required>
                    </div>
                    <span>h</span>
                    <div style="flex:1;">
                        <input type="number" class="input" name="duration_minutes" id="routeMinutes"
                            placeholder="Minutos" min="0" max="59" required>
                    </div>
                    <span>min</span>
                </div>

                <label>Status</label>
                <select class="select" name="status" id="routeStatus">
                    <option value="ativa">Ativa</option>
                    <option value="manutencao">Manutenção</option>
                </select>

                <label>Informações Adicionais</label>
                <textarea class="textarea" name="extra_info" id="routeExtra" rows="2"
                    placeholder="Ex: Atrasos, previsões..."></textarea>

                <div style="display:flex; gap:10px; margin-top:15px;">
                    <button type="button" class="btn secondary" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn">Salvar</button>
                </div>

                <div id="deleteBtnContainer" style="margin-top:10px; text-align:center; display:none;">
                    <a href="#" id="deleteLink" class="btn"
                        style="background:#fee2e2; color:#991b1b; border-color:#fca5a5;">Excluir Rota</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const modalBg = document.getElementById("modal");
        const modalTitle = document.getElementById("modalTitle");
        const formAction = document.getElementById("formAction");
        const routeId = document.getElementById("routeId");

        const routeOrigin = document.getElementById("routeOrigin");
        const routeDestination = document.getElementById("routeDestination");

        const routeStops = document.getElementById("routeStops");

        const routeHours = document.getElementById("routeHours");
        const routeMinutes = document.getElementById("routeMinutes");

        const routeStatus = document.getElementById("routeStatus");
        const routeExtra = document.getElementById("routeExtra");
        const deleteBtnContainer = document.getElementById("deleteBtnContainer");
        const deleteLink = document.getElementById("deleteLink");

        function openCreateModal() {
            modalTitle.textContent = "Nova Rota";
            formAction.value = "create";
            routeId.value = "";

            routeOrigin.value = "";
            routeDestination.value = "";

            routeStops.value = "";

            routeHours.value = "";
            routeMinutes.value = "";

            routeStatus.value = "ativa";
            routeExtra.value = "";
            deleteBtnContainer.style.display = "none";
            modalBg.style.display = "flex";
        }

        function editRoute(data) {
            modalTitle.textContent = "Editar Rota";
            formAction.value = "update";
            routeId.value = data.id;

            // Tenta usar os campos novos, se não existirem, tenta extrair do nome
            if (data.origin && data.destination) {
                routeOrigin.value = data.origin;
                routeDestination.value = data.destination;
            } else {
                // Fallback: tenta quebrar "Origem → Destino"
                if (data.name && data.name.includes("→")) {
                    const parts = data.name.split("→");
                    routeOrigin.value = parts[0].trim();
                    routeDestination.value = parts[1].trim();
                } else {
                    routeOrigin.value = data.name;
                    routeDestination.value = "";
                }
            }

            routeStops.value = data.stops || "";

            // Converter minutos para horas e minutos
            let totalMin = parseInt(data.duration_minutes) || 0;
            let h = Math.floor(totalMin / 60);
            let m = totalMin % 60;

            routeHours.value = h;
            routeMinutes.value = m;

            routeStatus.value = data.status;
            routeExtra.value = data.extra_info || "";

            // Configurar botão de excluir
            deleteLink.href = "?delete=" + data.id;
            deleteLink.onclick = function (e) {
                if (!confirm('Tem certeza que deseja excluir esta rota?')) {
                    e.preventDefault();
                }
            };
            deleteBtnContainer.style.display = "block";

            modalBg.style.display = "flex";
        }

        function closeModal() {
            modalBg.style.display = "none";
        }

        window.addEventListener("click", function (e) {
            if (e.target === modalBg) {
                closeModal();
            }
        });
    </script>

    <?php include '_partials/bottom_nav.php'; ?>

</body>

</html>