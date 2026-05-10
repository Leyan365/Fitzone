<div class="card text-white dashboard-card mt-4">
    <div class="card-header dashboard-card-header"><h4>Submitted Customer Queries</h4></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover">
                <thead>
                    <tr>
                        <th>From Customer</th>
                        <th>Query</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($queries_for_staff)): ?>
                        <tr><td colspan="5" class="text-center text-white-50">No queries found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($queries_for_staff as $query): ?>
                        <tr>
                            <td><?php echo e($query['customer_name']); ?></td>
                            <td><?php echo e(substr($query['query_text'], 0, 50)) . '...'; ?></td>
                            <td><?php echo e(date("Y-m-d", strtotime($query['created_at']))); ?></td>
                            <td><span class="badge <?php echo ($query['status'] == 'replied') ? 'bg-success' : 'bg-warning'; ?>"><?php echo e(ucfirst($query['status'])); ?></span></td>
                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#replyQueryModal" data-query-id="<?php echo e($query['id']); ?>" data-query-text="<?php echo e($query['query_text']); ?>">
                                    <i class="bi bi-reply-fill"></i> View & Reply
                                </button>
                                <form action="dashboard.php" method="post" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="delete_query_id" value="<?php echo e($query['id']); ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="replyQueryModal" tabindex="-1" aria-labelledby="replyQueryModalLabel" aria-hidden="true" data-bs-theme="dark">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="dashboard.php" method="post">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="replyQueryModalLabel">Reply to Query</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="query_id" id="modal_query_id">
                    <div class="mb-3">
                        <label class="form-label">Customer's Query:</label>
                        <p class="form-control-plaintext fst-italic" id="modal_query_text"></p>
                    </div>
                    <div class="mb-3">
                        <label for="reply_text" class="form-label">Your Reply:</label>
                        <textarea class="form-control" name="reply_text" id="reply_text" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="reply_to_query" class="btn btn-primary">Send Reply</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// JavaScript to pass query data to the Reply Modal
document.addEventListener('DOMContentLoaded', function () {
    var replyModal = document.getElementById('replyQueryModal');
    replyModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var queryId = button.getAttribute('data-query-id');
        var queryText = button.getAttribute('data-query-text');

        var modalQueryIdInput = replyModal.querySelector('#modal_query_id');
        var modalQueryTextElement = replyModal.querySelector('#modal_query_text');

        modalQueryIdInput.value = queryId;
        modalQueryTextElement.textContent = queryText;
    });
});
</script>
