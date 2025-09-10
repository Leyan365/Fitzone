<?php
// This file contains shared PHP logic for handling queries for Admin and Management roles.

$query_message = '';

// Handle query deletion
if (isset($_POST['delete_query_id']) && is_numeric($_POST['delete_query_id'])) {
    $delete_query_id = $_POST['delete_query_id'];
    $stmt = $conn->prepare("DELETE FROM queries WHERE id = ?");
    $stmt->bind_param("i", $delete_query_id);
    if ($stmt->execute()) {
        $query_message = "<div class='alert alert-success'>Query deleted successfully.</div>";
    }
    $stmt->close();
}

// Handle query reply
if (isset($_POST['reply_to_query']) && is_numeric($_POST['query_id'])) {
    $reply_text = trim($_POST['reply_text']);
    $query_id = $_POST['query_id'];
    
    if (!empty($reply_text)) {
        $stmt = $conn->prepare("UPDATE queries SET reply_text = ?, status = 'replied' WHERE id = ?");
        $stmt->bind_param("si", $reply_text, $query_id);
        if ($stmt->execute()) {
            $query_message = "<div class='alert alert-success'>Reply sent successfully.</div>";
        }
        $stmt->close();
    }
}

// Retrieve all queries for management/admin view
$queries_for_staff = [];
$stmt = $conn->prepare("
    SELECT q.id, q.query_text, q.status, q.created_at, u.name AS customer_name
    FROM queries q
    JOIN users u ON q.customer_id = u.id
    WHERE q.recipient_id = ? OR 'admin' = ?
    ORDER BY q.created_at DESC
");
$stmt->bind_param("is", $user_id, $user_role);
$stmt->execute();
$queries_for_staff = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>