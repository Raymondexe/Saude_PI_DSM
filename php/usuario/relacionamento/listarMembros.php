<?php
include("../../config/conexao.php");

$idFamilia = $_GET['idFamilia'] ?? null;

$stmt = $conn->prepare("
    SELECT
        fu.idFamiliaUsuario,
        u.nomeUsuario,
        u.foto,
        fu.papel,
        fu.statusMembro
    FROM tblFamiliaUsuario fu
    INNER JOIN tblUsuario u
        ON u.idUsuario = fu.Usuario_idUsuario
    WHERE fu.Familia_idFamilia = ?
");

$stmt->bind_param("i", $idFamilia);
$stmt->execute();

$result = $stmt->get_result();

while ($membro = $result->fetch_assoc()) {
    echo '
    <div class="membro-gerenciar">

        <div style="display:flex; align-items:center; gap:14px;">

            <img src="uploads/' . $membro['foto'] . '" class="foto-membro">

            <div>
                <strong>' . htmlspecialchars($membro['nomeUsuario']) . '</strong><br>
                <small>' . ucfirst($membro['papel']) . ' • ' . ucfirst($membro['statusMembro']) . '</small>
            </div>

        </div>

        <button onclick="removerMembro(' . $membro['idFamiliaUsuario'] . ')">
            Remover
        </button>

    </div>
';
}


$foto = !empty($membro['foto'])
    ? 'uploads/' . $membro['foto']
    : 'Img/defaultUser.png';
?>