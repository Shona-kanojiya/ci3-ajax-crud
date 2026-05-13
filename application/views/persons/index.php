<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
    <h2 class="mb-1">All Persons</h2>
    <small class="text-muted">
        <?= $total ?> record<?= $total != 1 ? 's' : '' ?> in database
    </small>
    </div>
    <a href="<?= base_url('persons/create') ?>" class="btn btn-primary">
    + Add Person
    </a>
</div>

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash_type ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($flash) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (empty($persons)): ?>

<div class="text-center p-5 border rounded bg-light">
    <h4 class="mt-3">No records yet</h4>
    <p class="text-muted">Get started by adding your first person.</p>
    <a href="<?= base_url('persons/create') ?>" class="btn btn-primary">Add First Person</a>
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

                <td class="text-end">
                <a href="<?= base_url('persons/edit/' . $p->id) ?>" 
                    class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil-square"></i>
                </a>

                <button 
                    class="btn btn-sm btn-danger"
                    onclick="openDeleteModal(<?= $p->id ?>)">
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

<?php if ($pagination): ?>
<div class="mt-3">
  <?= $pagination ?>
</div>
<?php endif; ?>

<?php endif; ?>

<!-- Bootstrap Modal -->
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

        <form method="POST" id="deleteForm">
          <?= form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()) ?>
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </form>
      </div>

    </div>
  </div>
</div>

<script>
function openDeleteModal(id) {
  const form = document.getElementById('deleteForm');
  form.action = "<?= base_url('persons/delete/') ?>" + id;

  var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
  modal.show();
}
</script>