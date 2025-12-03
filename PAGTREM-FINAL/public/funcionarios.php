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
    // 2. Verifica se selecionou da galeria (se não houve upload)
    elseif (!empty($_POST['selected_photo'])) {
        $photo = $_POST['selected_photo'];
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
    $id = (int)$_GET['delete'];
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
    <title>Funcionários - Vai de Trem</title>

    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">

    <style>
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        .gallery-item {
            cursor: pointer;
            border: 2px solid transparent;
            border-radius: 50%;
            padding: 2px;
            transition: all 0.2s;
        }
        .gallery-item:hover {
            transform: scale(1.1);
        }
        .gallery-item.selected {
            border-color: var(--brand);
            transform: scale(1.1);
        }
        .gallery-item img {
            width: 100%;
            height: auto;
            border-radius: 50%;
            display: block;
        }
    </style>

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

    <!-- HEADER -->
    <div class="top-header">
        <h1><img src="../assets/images/icones_funcionarios.png" alt="Funcionários" class="icon-img" style="width:22px;height:22px;">
            Funcionários</h1>
    </div>

    <!-- LISTA DE FUNCIONÁRIOS -->
    <div class="container" style="padding-bottom: 80px;">
        
        <?php if ($feedback): ?>
            <div class="success-box"><?php echo $feedback; ?></div>
        <?php endif; ?>

        <div class="routes-grid">
            <?php
            $res = $mysqli->query("SELECT * FROM employees ORDER BY id DESC");
            while ($f = $res->fetch_assoc()) {
                // Lógica de exibição da foto:
                // 1. Se começar com "assets/", é caminho relativo direto (galeria)
                // 2. Se não, assume que está em uploads/funcionarios/
                // 3. Fallback
                
                $photoVal = $f['photo'];
                if ($photoVal && strpos($photoVal, 'assets/') === 0) {
                    $photoPath = '../' . $photoVal; // Ajuste pois estamos em public/
                } elseif ($photoVal) {
                    $photoPath = '../assets/uploads/funcionarios/' . $photoVal;
                } else {
                    $photoPath = '../assets/uploads/profile_photos/avatar-default.png';
                }
                
                // Dados para JS
                $jsonData = htmlspecialchars(json_encode($f), ENT_QUOTES, 'UTF-8');

                echo "
                <div class='route-card' onclick='editEmployee($jsonData)'>
                    <div style='display:flex; gap:15px; align-items:center;'>
                        <img src='$photoPath' style='width:60px; height:60px; border-radius:50%; object-fit:cover; border:1px solid #eee;'>
                        <div>
                            <div class='route-title' style='margin-bottom:4px;'>" . htmlspecialchars($f['name']) . "</div>
                            <div style='font-size:13px; color:var(--muted);'>" . htmlspecialchars($f['role']) . "</div>
                        </div>
                    </div>
                    
                    <div class='details' style='margin-top:15px;'>
                        <img src='../assets/images/local_icone.png' class='icon-img' style='width:16px;height:16px;'> " . htmlspecialchars($f['city']) . " - " . htmlspecialchars($f['uf']) . "
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
            <h2 id="modalTitle">Novo Funcionário</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="empId">
                <input type="hidden" name="selected_photo" id="selectedPhoto">

                <!-- Foto -->
                <div style="text-align:center; margin-bottom:15px;">
                    <img id="preview" src="../assets/uploads/profile_photos/avatar-default.png" style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:1px solid #ddd; margin-bottom:10px;">
                    <br>
                    
                    <!-- Opções de Foto -->
                    <div style="margin-bottom:10px;">
                        <span style="font-size:13px; color:var(--muted); display:block; margin-bottom:5px;">Escolha um avatar:</span>
                        <div class="gallery-grid">
                            <div class="gallery-item" onclick="selectGalleryPhoto('assets/images/funcionario1.png', this)">
                                <img src="../assets/images/funcionario1.png">
                            </div>
                            <div class="gallery-item" onclick="selectGalleryPhoto('assets/images/funcionario2.png', this)">
                                <img src="../assets/images/funcionario2.png">
                            </div>
                            <div class="gallery-item" onclick="selectGalleryPhoto('assets/images/funcionario3.png', this)">
                                <img src="../assets/images/funcionario3.png">
                            </div>
                            <div class="gallery-item" onclick="selectGalleryPhoto('assets/images/funcionario4.png', this)">
                                <img src="../assets/images/funcionario4.png">
                            </div>
                            <div class="gallery-item" onclick="selectGalleryPhoto('assets/images/funcionario5.png', this)">
                                <img src="../assets/images/funcionario5.png">
                            </div>
                        </div>
                    </div>

                    <div style="font-size:12px; color:var(--muted); margin:10px 0;">OU</div>

                    <label for="photoInput" class="btn secondary" style="display:inline-block; width:auto; padding:5px 10px; font-size:12px;">Upload do Computador</label>
                    <input type="file" name="photo" id="photoInput" accept="image/*" style="display:none;" onchange="handleFileUpload(this)">
                </div>

                <label>Nome Completo</label>
                <input class="input" name="name" id="empName" required>

                <label>Cargo</label>
                <input class="input" name="role" id="empRole" required>

                <!-- Campos de Login (Obrigatório para criação) -->
                <div id="loginFields">
                    <hr style="margin:15px 0; border:0; border-top:1px solid #eee;">
                    <p style="font-size:13px; font-weight:600; margin-bottom:10px; color:var(--brand);">Dados de Acesso (Obrigatório)</p>
                    
                    <label>Nível de Acesso</label>
                    <select class="input" name="access_level" id="empAccessLevel">
                        <option value="user">Usuário Comum</option>
                        <option value="admin">Administrador</option>
                    </select>

                    <label>E-mail</label>
                    <input class="input" name="email" id="empEmail" type="email" required>
                    <label>Senha</label>
                    <input class="input" name="password" id="empPassword" type="password" required>
                </div>

                <hr style="margin:15px 0; border:0; border-top:1px solid #eee;">
                
                <!-- Endereço -->
                <div style="display:flex; gap:10px; align-items:flex-end;">
                    <div style="flex:1;">
                        <label>CEP</label>
                        <input class="input" name="cep" id="empCep" onblur="buscarCEP()">
                    </div>
                    <button type="button" class="btn" onclick="buscarCEP()" style="width:auto; margin-bottom:2px;"><i class="ri-search-line"></i></button>
                </div>

                <div style="display:flex; gap:10px; margin-top:10px;">
                    <div style="flex:2;">
                        <label>Cidade</label>
                        <input class="input" name="city" id="empCity">
                    </div>
                    <div style="flex:1;">
                        <label>UF</label>
                        <input class="input" name="uf" id="empUf">
                    </div>
                </div>

                <div style="margin-top:10px;">
                    <label>Rua</label>
                    <input class="input" name="street" id="empStreet">
                </div>

                <div style="margin-top:10px;">
                    <label>Bairro</label>
                    <input class="input" name="neighborhood" id="empNeighborhood">
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="button" class="btn secondary" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn">Salvar</button>
                </div>
                
                <div id="deleteBtnContainer" style="margin-top:10px; text-align:center; display:none;">
                    <a href="#" id="deleteLink" class="btn" style="background:#fee2e2; color:#991b1b; border-color:#fca5a5;">Excluir Funcionário</a>
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
        const selectedPhotoInput = document.getElementById("selectedPhoto");
        const photoInput = document.getElementById("photoInput");
        
        const deleteBtnContainer = document.getElementById("deleteBtnContainer");
        const deleteLink = document.getElementById("deleteLink");

        function selectGalleryPhoto(path, element) {
            // Atualiza preview
            preview.src = "../" + path;
            // Define valor no input hidden
            selectedPhotoInput.value = path;
            // Limpa input file
            photoInput.value = "";
            
            // Visual selection
            document.querySelectorAll('.gallery-item').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
        }

        function handleFileUpload(input) {
            if (input.files && input.files[0]) {
                preview.src = window.URL.createObjectURL(input.files[0]);
                // Limpa seleção da galeria
                selectedPhotoInput.value = "";
                document.querySelectorAll('.gallery-item').forEach(el => el.classList.remove('selected'));
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
            preview.src = "../assets/uploads/profile_photos/avatar-default.png";
            selectedPhotoInput.value = "";
            photoInput.value = "";
            document.querySelectorAll('.gallery-item').forEach(el => el.classList.remove('selected'));
            
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
            selectedPhotoInput.value = "";
            photoInput.value = "";
            document.querySelectorAll('.gallery-item').forEach(el => el.classList.remove('selected'));

            if (data.photo) {
                if (data.photo.startsWith('assets/')) {
                     preview.src = "../" + data.photo;
                     // Tenta marcar na galeria se for um dos padrões
                     const galleryItem = document.querySelector(`.gallery-item[onclick*='${data.photo}']`);
                     if (galleryItem) galleryItem.classList.add('selected');
                     selectedPhotoInput.value = data.photo;
                } else {
                     preview.src = "../assets/uploads/funcionarios/" + data.photo;
                }
            } else {
                preview.src = "../assets/uploads/profile_photos/avatar-default.png";
            }

            // Esconder campos de login e remover obrigatoriedade
            loginFields.style.display = "none";
            document.getElementById("empEmail").required = false;
            document.getElementById("empPassword").required = false;

            // Configurar botão de excluir
            deleteLink.href = "?delete=" + data.id;
            deleteLink.onclick = function(e) {
                if(!confirm('Tem certeza que deseja excluir este funcionário?')) {
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