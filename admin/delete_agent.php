<?php
session_start();
include_once '../config/database.php';
include_once '../includes/functions.php';
redirectIfNotLoggedIn();
$pageTitle = 'Delete Agent';

$database = new Database();
$db = $database->getConnection();

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $agentId = $_GET['id'];

    try {
        $db->beginTransaction();

        $deleteBranchesQuery = "DELETE FROM agent_branches WHERE agent_id = :agent_id";
        $deleteBranchesStmt = $db->prepare($deleteBranchesQuery);
        $deleteBranchesStmt->bindParam(':agent_id', $agentId, PDO::PARAM_STR);
        $deleteBranchesStmt->execute();

        $deleteAgentQuery = "DELETE FROM agents WHERE id = :agent_id";
        $deleteAgentStmt = $db->prepare($deleteAgentQuery);
        $deleteAgentStmt->bindParam(':agent_id', $agentId, PDO::PARAM_STR);
        $deleteAgentStmt->execute();

        $db->commit();

        $_SESSION['message'] = 'Agent deleted successfully.';

    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['message'] = 'Error deleting agent: ' . $e->getMessage();
    }

    header('Location: manage_agents.php');
    exit();
} else {
    $_SESSION['message'] = 'No agent ID provided.';
    header('Location: manage_agents.php');
    exit();
}
?>