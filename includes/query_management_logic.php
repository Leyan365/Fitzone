<?php
// This file contains shared PHP logic for handling queries for Admin and Management roles.
require_role($current_user, ['admin', 'management']);

$query_message = '';

// Handle query deletion
if (isset($_POST['delete_query_id']) && is_numeric($_POST['delete_query_id'])) {
    require_csrf();

    $delete_query_id = (int) $_POST['delete_query_id'];

    if ($user_role === 'admin') {
        $stmt = $conn->prepare("DELETE FROM queries WHERE id = ?");
        $stmt->bind_param("i", $delete_query_id);
    } else {
        $stmt = $conn->prepare("DELETE FROM queries WHERE id = ? AND recipient_id = ?");
        $stmt->bind_param("ii", $delete_query_id, $user_id);
    }

    if ($stmt->execute()) {
        $query_message = $stmt->affected_rows > 0
            ? "<div class='alert alert-success'>Query deleted successfully.</div>"
            : "<div class='alert alert-warning'>Query could not be found or is not assigned to you.</div>";
    }
    $stmt->close();
}

// Handle query reply
if (isset($_POST['reply_to_query']) && is_numeric($_POST['query_id'])) {
    require_csrf();

    $reply_text = trim($_POST['reply_text']);
    $query_id = (int) $_POST['query_id'];
    
    if (!empty($reply_text)) {
        if ($user_role === 'admin') {
            $stmt = $conn->prepare("UPDATE queries SET reply_text = ?, status = 'replied' WHERE id = ?");
            $stmt->bind_param("si", $reply_text, $query_id);
        } else {
            $stmt = $conn->prepare("UPDATE queries SET reply_text = ?, status = 'replied' WHERE id = ? AND recipient_id = ?");
            $stmt->bind_param("sii", $reply_text, $query_id, $user_id);
        }

        if ($stmt->execute()) {
            $query_message = $stmt->affected_rows > 0
                ? "<div class='alert alert-success'>Reply sent successfully.</div>"
                : "<div class='alert alert-warning'>Query could not be found or is not assigned to you.</div>";
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
    WHERE ? = 'admin' OR q.recipient_id = ?
    ORDER BY q.created_at DESC
");
$stmt->bind_param("si", $user_role, $user_id);
$stmt->execute();
$queries_for_staff = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>
