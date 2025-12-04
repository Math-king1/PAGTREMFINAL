<?php
require_once('../assets/config/auth.php');
require_once('../assets/config/db.php');

$feedback = "";

// PROCESSAR FORMULÁRIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';

    $name = trim($_POST['name'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $cep = trim($_POST['cep'] ?? '');
    $street = trim($_POST['street'] ?? '');
    $neighborhood = trim($_POST['neighborhood'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $uf = trim($_POST['uf'] ?? '');

    // Apenas para criação
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $access_level = trim($_POST['access_level'] ?? 'user');

    // FOTO
    $photo = null;

    // 1. Verifica se veio upload
    if (!empty($_FILES['photo']['name'])) {
        if ($_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($ext, $allowed)) {
                $fname = 'f_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $dest = '../assets/uploads/funcionarios/' . $fname;

                // Garantir que a pasta existe
                $dir = dirname($dest);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                    $photo = $fname;
                } else {
                    $feedback = "Erro ao mover arquivo de upload.";
                }
            } else {
                $feedback = "Formato de imagem inválido. Use JPG, PNG, GIF ou WEBP.";
            }
        } else {
            $feedback = "Erro no upload: Código " . $_FILES['photo']['error'];
        }
    }

    if ($action === 'create') {
        // Validação de campos obrigatórios para login
        if (empty($email) || empty($password)) {
            $feedback = "Erro: E-mail e Senha são obrigatórios para novos funcionários.";
        } else {
            // 1. Inserir Funcionário
            $stmt = $mysqli->prepare("INSERT INTO employees (name, role, cep, street, neighborhood, city, uf, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssssss', $name, $role, $cep, $street, $neighborhood, $city, $uf, $photo);

            if ($stmt->execute()) {
                // 2. Criar Usuário de Acesso (Obrigatório)
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt2 = $mysqli->prepare("INSERT INTO users (name, email, password, role, avatar, job_title) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt2->bind_param('ssssss', $name, $email, $hash, $access_level, $photo, $role);

                if ($stmt2->execute()) {
                    $feedback = "Funcionário e usuário de acesso cadastrados com sucesso!";
                } else {
                    $feedback = "Funcionário criado, mas erro ao criar usuário de acesso (E-mail já existe?).";
                }
            } else {
                $feedback = "Erro ao cadastrar funcionário.";
            }
        }

    } elseif ($action === 'update' && $id) {
        // Construir query dinâmica para update
        $sql = "UPDATE employees SET name=?, role=?, cep=?, street=?, neighborhood=?, city=?, uf=?";
        $params = [$name, $role, $cep, $street, $neighborhood, $city, $uf];
        $types = "sssssss";

        if ($photo) {
            $sql .= ", photo=?";
            $params[] = $photo;
            $types .= "s";
        }

        $sql .= " WHERE id=?";
        $params[] = $id;
        $types .= "i";

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $feedback = "Funcionário atualizado com sucesso!";
        } else {
            $feedback = "Erro ao atualizar funcionário.";
        }

    } elseif ($action === 'delete' && $id) {
        $stmt = $mysqli->prepare("DELETE FROM employees WHERE id=?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $feedback = "Funcionário excluído com sucesso!";
        }
    }
}

// DELETE VIA GET
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $mysqli->query("DELETE FROM employees WHERE id=$id");
    header('Location: funcionarios.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Funcionários - PagTrem</title>

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">

    <script>
        async function buscarCEP() {
            const cep = document.getElementById("empCep").value.replace(/\D/g, '');
            if (cep.length !== 8) {
                alert("CEP inválido! Digite 8 números.");
                return;
            }

            const url = `https://viacep.com.br/ws/${cep}/json/`;
            try {
                const data = await fetch(url).then(r => r.json());
                if (data.erro) {
                    alert("CEP não encontrado.");
                    return;
                }
                document.getElementById("empStreet").value = data.logradouro;
                document.getElementById("empNeighborhood").value = data.bairro;
                document.getElementById("empCity").value = data.localidade;
                document.getElementById("empUf").value = data.uf;
            } catch (e) {
                alert("Erro ao buscar CEP.");
            }
        }
    </script>
</head>

<body>

    <div class="layout-wrapper">
        <?php include '_partials/sidebar_admin.php'; ?>

        <div class="main-content">
            <!-- HEADER -->
            <div class="top-header">
                <h1><i class="ri-group-line"></i> Funcionários</h1>
            </div>

            <!-- LISTA DE FUNCIONÁRIOS -->
            <div class="container" style="padding-bottom: 120px;">

                <?php if ($feedback): ?>
                        <div class="badge success"
                            style="margin-bottom: 24px; width: 100%; justify-content: center; padding: 12px;">
                            <?php echo $feedback; ?>
                        </div>
                <?php endif; ?>

                <div class="route-list">
                    <?php
                    $res = $mysqli->query("SELECT * FROM employees ORDER BY id DESC");
                    while ($f = $res->fetch_assoc()) {
                        // Lógica de exibição da foto:
                        $photoVal = $f['photo'];
                        if ($photoVal && file_exists('../assets/uploads/funcionarios/' . $photoVal)) {
                            $photoPath = '../assets/uploads/funcionarios/' . $photoVal;
                            $imgTag = "<img src='$photoPath' style='width:64px; height:64px; border-radius:50%; object-fit:cover; border:2px solid var(--surface); box-shadow: var(--shadow-sm);'>";
                        } else {
                            $imgTag = "<div style='width:64px; height:64px; background:var(--brand-bg); color:var(--brand); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:24px; border:1px solid var(--brand-light);'><i class='ri-user-line'></i></div>";
                        }

                        // Dados para JS
                        $jsonData = htmlspecialchars(json_encode($f), ENT_QUOTES, 'UTF-8');

                        echo "
                        <div class='route-card' onclick='editEmployee($jsonData)'>
                            <div style='display:flex; gap:16px; align-items:center;'>
                                $imgTag
                                <div>
                                    <div class='route-title' style='margin-bottom:4px; font-size: 1.1rem;'>" . htmlspecialchars($f['name']) . "</div>
                                    <div style='font-size:0.9rem; color:var(--text-light);'>" . htmlspecialchars($f['role']) . "</div>
                                </div>
                            </div>
                            
                            <div class='details' style='margin-top:16px; padding-top: 16px; border-top: 1px solid var(--border);'>
                                <i class='ri-map-pin-line' style='color:var(--brand);'></i> " . htmlspecialchars($f['city']) . " - " . htmlspecialchars($f['uf']) . "
                            </div>
                        </div>";
                    }
                    ?>
                </div>
            </div>

            <!-- BOTÃO "+" (FAB) -->
            <div class="fab" onclick="openCreateModal()">
                <i class="ri-add-line" style="font-size: 32px;"></i>
            </div>

            <!-- MODAL -->
            <div class="modal-bg" id="modal">
                <div class="modal" onclick="event.stopPropagation()" style="max-height:90vh; overflow-y:auto;">
                    <h2 id="modalTitle" style="margin-bottom: 24px;">Novo Funcionário</h2>
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="id" id="empId">

                        <!-- Foto -->
                        <div style="text-align:center; margin-bottom:24px;">
                            <img id="preview" src="" style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:4px solid var(--surface); box-shadow: var(--shadow); margin-bottom:16px; display:none;">
                            <div id="defaultIcon" style="width:100px; height:100px; background:var(--brand-bg); color:var(--brand); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:40px; border:1px solid var(--brand-light); margin: 0 auto;">
                                <i class="ri-user-line"></i>
                            </div>
                            <br>
                            <label for="photoInput" class="btn secondary" style="display:inline-block; width:auto; padding:8px 16px; font-size:0.875rem; margin-top: 8px;">
                                Upload Foto
                            </label>
                            <input type="file" name="photo" id="photoInput" accept="image/*" style="display:none;" onchange="handleFileUpload(this)">
                        </div>

                        <label>Nome Completo</label>
                        <input class="input" name="name" id="empName" required>

                        <label>Cargo</label>
                        <input class="input" name="role" id="empRole" required>

                        <!-- Campos de Login (Obrigatório para criação) -->
                        <div id="loginFields">
                            <hr style="margin:24px 0; border:0; border-top:1px solid var(--border);">
                            <p style="font-size:0.95rem; font-weight:600; margin-bottom:16px; color:var(--brand);">Dados
                                de
                                Acesso (Obrigatório)</p>

                            <label>Nível de Acesso</label>
                            <select class="select" name="access_level" id="empAccessLevel">
                                <option value="user">Usuário Comum</option>
                                <option value="admin">Administrador</option>
                            </select>

                            <label>E-mail</label>
                            <input class="input" name="email" id="empEmail" type="email" required>
                            <label>Senha</label>
                            <input class="input" name="password" id="empPassword" type="password" required>
                        </div>

                        <hr style="margin:24px 0; border:0; border-top:1px solid var(--border);">

                        <!-- Endereço -->
                        <div style="display:flex; gap:12px; align-items:flex-end;">
                            <div style="flex:1;">
                                <label>CEP</label>
                                <input class="input" name="cep" id="empCep" onblur="buscarCEP()">
                            </div>
                            <button type="button" class="btn secondary" onclick="buscarCEP()"
                                style="width:auto; margin-bottom:2px; height: 50px;"><i
                                    class="ri-search-line"></i></button>
                        </div>

                        <div style="display:flex; gap:12px; margin-top:12px;">
                            <div style="flex:2;">
                                <label>Cidade</label>
                                <input class="input" name="city" id="empCity">
                            </div>
                            <div style="flex:1;">
                                <label>UF</label>
                                <input class="input" name="uf" id="empUf">
                            </div>
                        </div>

                        <div style="margin-top:12px;">
                            <label>Rua</label>
                            <input class="input" name="street" id="empStreet">
                        </div>

                        <div style="margin-top:12px;">
                            <label>Bairro</label>
                            <input class="input" name="neighborhood" id="empNeighborhood">
                        </div>

                        <div style="display:flex; gap:12px; margin-top:32px;">
                            <button type="button" class="btn secondary" style="flex:1;"
                                onclick="closeModal()">Cancelar</button>
                            <button type="submit" class="btn" style="flex:1;">Salvar</button>
                        </div>

                        <div id="deleteBtnContainer" style="margin-top:16px; text-align:center; display:none;">
                            <a href="#" id="deleteLink" class="btn secondary"
                                style="color:var(--danger); border-color:var(--danger-bg); width:100%;">Excluir
                                Funcionário</a>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                const modalBg = document.getElementById("modal");
                const modalTitle = document.getElementById("modalTitle");
                const formAction = document.getElementById("formAction");
                const empId = document.getElementById("empId");
                const empName = document.getElementById("empName");
                const empRole = document.getElementById("empRole");
                const loginFields = document.getElementById("loginFields");

                const empCep = document.getElementById("empCep");
                const empCity = document.getElementById("empCity");
                const empUf = document.getElementById("empUf");
                const empStreet = document.getElementById("empStreet");
                const empNeighborhood = document.getElementById("empNeighborhood");

                const preview = document.getElementById("preview");
                const defaultIcon = document.getElementById("defaultIcon");
                const photoInput = document.getElementById("photoInput");

                const deleteBtnContainer = document.getElementById("deleteBtnContainer");
                const deleteLink = document.getElementById("deleteLink");

                function handleFileUpload(input) {
                    if (input.files && input.files[0]) {
                        preview.src = window.URL.createObjectURL(input.files[0]);
                        preview.style.display = "block";
                        defaultIcon.style.display = "none";
                    }
                }

                function openCreateModal() {
                    modalTitle.textContent = "Novo Funcionário";
                    formAction.value = "create";
                    empId.value = "";
                    empName.value = "";
                    empRole.value = "";

                    // Limpar endereço
                    empCep.value = "";
                    empCity.value = "";
                    empUf.value = "";
                    empStreet.value = "";
                    empNeighborhood.value = "";

                    // Resetar foto
                    preview.src = "";
                    preview.style.display = "none";
                    defaultIcon.style.display = "flex";
                    photoInput.value = "";

                    // Mostrar campos de login e tornar obrigatórios
                    loginFields.style.display = "block";
                    document.getElementById("empEmail").required = true;
                    document.getElementById("empPassword").required = true;

                    deleteBtnContainer.style.display = "none";
                    modalBg.style.display = "flex";
                }

                function editEmployee(data) {
                    modalTitle.textContent = "Editar Funcionário";
                    formAction.value = "update";
                    empId.value = data.id;
                    empName.value = data.name;
                    empRole.value = data.role;

                    empCep.value = data.cep || "";
                    empCity.value = data.city || "";
                    empUf.value = data.uf || "";
                    empStreet.value = data.street || "";
                    empNeighborhood.value = data.neighborhood || "";

                    // Foto
                    photoInput.value = "";
                    if (data.photo) {
                        preview.src = "../assets/uploads/funcionarios/" + data.photo;
                        preview.style.display = "block";
                        defaultIcon.style.display = "none";
                    } else {
                        preview.src = "";
                        preview.style.display = "none";
                        defaultIcon.style.display = "flex";
                    }

                    // Esconder campos de login e remover obrigatoriedade
                    loginFields.style.display = "none";
                    document.getElementById("empEmail").required = false;
                    document.getElementById("empPassword").required = false;

                    // Configurar botão de excluir
                    deleteLink.href = "?delete=" + data.id;
                    deleteLink.onclick = function (e) {
                        if (!confirm('Tem certeza que deseja excluir este funcionário?')) {
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

        </div>
    </div>

</body>

</html>