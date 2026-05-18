<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">All Persons</h2>
        <small class="text-muted">
            <?php $count = $total ?? 0; ?>
            <?= $count ?> record<?= $count != 1 ? 's' : '' ?> in database
        </small>
    </div>
    <a href="<?= base_url('persons/form') ?>" class="btn btn-primary">
        + Add Person
    </a>
</div>

<?php if (isset($flash)): ?>
    <div class="alert alert-<?= $flash_type ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($flash) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (empty($persons)): ?>
    <div class="text-center p-5 border rounded bg-light">
        <h4 class="mt-3">No records yet</h4>
        <p class="text-muted">Get started by adding your first person.</p>
        <a href="<?= base_url('persons/form') ?>" class="btn btn-primary">Add First Person</a>
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Gender</th>
                            <th>State</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($persons as $i => $p): ?>
                        <tr>
                            <td class="text-muted"><?= $i + 1 ?></td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <strong><?= htmlspecialchars($p->name) ?></strong>
                                </div>
                            </td>

                            <td class="text-muted"><?= htmlspecialchars($p->email) ?></td>
                            <td><?= htmlspecialchars($p->mobile) ?></td>

                            <td>
                                <?php
                                    $genderClass = 'secondary';
                                    if ($p->gender == 'Male') $genderClass = 'primary';
                                    elseif ($p->gender == 'Female') $genderClass = 'danger';
                                    elseif ($p->gender == 'Other') $genderClass = 'dark';
                                ?>
                                <span class="badge bg-<?= $genderClass ?>">
                                    <?= $p->gender ?>
                                </span>
                            </td>

                            <td class="text-muted"><?= htmlspecialchars($p->state) ?></td>
                            <?php $enc_id = enc_id($p->id); ?>

                            <td class="text-end">
                                <a href="<?= base_url('persons/form/' . $enc_id) ?>" 
                                    class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                <button 
                                    class="btn btn-sm btn-danger"
                                    onclick="openDeleteModal('<?= $enc_id ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php if ($total_pages > 1): ?>
    <ul class="pagination mt-3 justify-content-center">
        <?php if ($current_page > 1): ?>
            <li class="page-item"><a class="page-link" href="<?= base_url('persons/1') ?>">&laquo;</a></li>
            <li class="page-item"><a class="page-link" href="<?= base_url('persons/'.($current_page-1)) ?>">&#8249;</a></li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                <a class="page-link" href="<?= base_url('persons/'.$i) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($current_page < $total_pages): ?>
            <li class="page-item"><a class="page-link" href="<?= base_url('persons/'.($current_page+1)) ?>">&#8250;</a></li>
            <li class="page-item"><a class="page-link" href="<?= base_url('persons/'.$total_pages) ?>">&raquo;</a></li>
        <?php endif; ?>
    </ul>
    <?php endif; ?>
<?php endif; ?>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p>This action cannot be undone. Are you sure?</p>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                <form method="POST" id="deleteForm" action="<?= base_url('persons/delete') ?>">
                    <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>

                    <input type="hidden" name="enc_id" id="enc_id">

                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(enc_id) {
        $('#enc_id').val(enc_id); 

        $('#deleteModal').modal('show');
    }
    $('#deleteForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(), 
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $('#deleteModal').modal('hide');
                    location.reload(); 
                } else {
                    alert(res.message);
                }
            },
            error: function(xhr) {
                // Log the error to see if it's a 403
                console.log(xhr.responseText);
                alert('Error: ' + xhr.status + ' Forbidden. Check CSRF token.');
            }
        });
    });
</script>