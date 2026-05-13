<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
	<div>
		<h2><?= $action === 'edit' ? 'Edit Person' : 'Add New Person' ?></h2>
		<small class="text-muted">
		<?= $action === 'edit' ? 'Update details' : 'Create new record' ?>
		</small>
	</div>

	<a href="<?= base_url('persons') ?>" class="btn btn-outline-secondary">Back</a>
</div>

<?php
$form_action = ($action === 'edit')
    ? base_url('persons/save/' . $person->id)
    : base_url('persons/save');

echo form_open($form_action, ['id' => 'personForm']);
?>

<div class="card shadow-sm">
  <div class="card-body">

    <!-- Name -->
    <div class="mb-3">
      <label>Name *</label>
      <input type="text" name="name" class="form-control"
             value="<?= set_value('name', $person ? $person->name : '') ?>">
    </div>

    <!-- Email -->
    <div class="mb-3">
      <label>Email *</label>
      <input type="email" name="email" class="form-control"
             value="<?= set_value('email', $person ? $person->email : '') ?>">
    </div>

    <!-- Mobile -->
    <div class="mb-3">
      <label>Mobile *</label>
      <input type="text" name="mobile" class="form-control"
             value="<?= set_value('mobile', $person ? $person->mobile : '') ?>">
    </div>

    <div class="row">
      <!-- Gender -->
      <div class="col-md-6 mb-3">
        <label>Gender *</label><br>

        <?php $g = set_value('gender', $person ? $person->gender : ''); ?>

        <input type="radio" name="gender" value="Male" <?= $g=='Male'?'checked':'' ?>> Male
        <input type="radio" name="gender" value="Female" <?= $g=='Female'?'checked':'' ?>> Female
        <input type="radio" name="gender" value="Other" <?= $g=='Other'?'checked':'' ?>> Other
      </div>

      <!-- State -->
      <div class="col-md-6 mb-3">
        <label>State *</label>
        <select name="state" class="form-select">
          <option value="">Select</option>
          <?php foreach($states as $s): ?>
          <option value="<?= $s ?>" <?= set_value('state', $person ? $person->state : '') == $s ? 'selected':'' ?>>
            <?= $s ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

  </div>
</div>

<div class="mt-3 text-end">
  <button type="submit" class="btn btn-primary">Save</button>
</div>

<?= form_close(); ?>


<!-- 🔥 AJAX + VALIDATION -->
<script>
$(document).ready(function () {

  // Setup validation
  $('#personForm').validate({
    rules: {
      name: "required",
      email: {
        required: true,
        email: true
      },
      mobile: {
        required: true,
        minlength: 10,
        maxlength: 15
      },
      gender: "required",
      state: "required"
    },

    messages: {
      name: "Enter name",
      email: "Enter valid email",
      mobile: "Enter valid mobile",
      gender: "Select gender",
      state: "Select state"
    },

    errorClass: "text-danger",
    errorPlacement: function(error, element) {
      if (element.attr("type") == "radio") {
        error.insertAfter(element.closest('div'));
      } else {
        error.insertAfter(element);
      }
    },

    submitHandler: function(form) {

      let url = $(form).attr('action');
      let btn = $(form).find('button[type="submit"]');

      btn.prop('disabled', true).text('Saving...');

      $.ajax({
        url: url,
        type: "POST",
        data: $(form).serialize(),
        dataType: "json",

        success: function (res) {

          if (res.status === 'success') {
            window.location.href = res.redirect;
          }

          // Update CSRF token
          if (res.csrf_token) {
            $('input[name="' + res.csrf_name + '"]').val(res.csrf_token);
          }

          btn.prop('disabled', false).text('Save');
        },

        error: function () {
          alert("Error occurred!");
          btn.prop('disabled', false).text('Save');
        }
      });

      return false;
    }
  });

});
</script>