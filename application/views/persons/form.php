<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
	<div>
		<h2><?= $action === 'edit' ? 'Edit Person' : 'Add New Person' ?></h2>
		<small class="text-muted">
		<?= $action === 'edit' ? 'Update details' : 'Create new record' ?>
		</small>
	</div>

	<a href="<?= base_url() ?>" class="btn btn-outline-secondary"><i class="bi bi-skip-backward-circle"></i> Back to Listing</a>
</div>

<?php
    $form_action = base_url('persons/save');
    echo form_open($form_action, ['id' => 'personForm']);
?>
<div id="alertBox"></div>

<div class="card shadow-sm">
    <div class="card-body">
        <input type="hidden" name="enc_id" id="enc_id" value="<?= $person ? enc_id($person->id) : '' ?>">

        <!-- Name -->
        <div class="mb-3">
            <label>Name *</label>
            <input type="text" name="name" class="form-control"
                placeholder="Enter your full name"
                value="<?= set_value('name', isset($person->name) ? $person->name : '') ?>">
            <div class="invalid-feedback"></div>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label>Email *</label>
            <input type="email" name="email" class="form-control"
                placeholder="Enter your email address"
                value="<?= set_value('email', isset($person->email) ? $person->email : '') ?>">
            <div class="invalid-feedback"></div>
        </div>

        <!-- Mobile -->
        <div class="mb-3">
            <label>Mobile *</label>
            <input type="text" name="mobile" class="form-control"
                placeholder="Enter your mobile number"
                value="<?= set_value('mobile', isset($person->mobile) ? $person->mobile : '') ?>">
            <div class="invalid-feedback"></div>
        </div>

        <div class="row">

            <!-- Gender -->
            <div class="col-md-6 mb-3">
                <label class="form-label d-block">Gender *</label>

                <?php 
                    $g = set_value('gender', $person->gender ?? 'Male');
                ?>

                <div class="form-check form-check-inline">
                    <label class="form-check-label">
                        <input class="form-check-input" type="radio" name="gender" value="Male"
                            <?= ($g == 'Male') ? 'checked' : '' ?>>
                        Male
                    </label>
                </div>

                <div class="form-check form-check-inline">
                    <label class="form-check-label">
                        <input class="form-check-input" type="radio" name="gender" value="Female"
                            <?= ($g == 'Female') ? 'checked' : '' ?>>
                        Female
                    </label>
                </div>

                <div class="form-check form-check-inline">
                    <label class="form-check-label">
                        <input class="form-check-input" type="radio" name="gender" value="Other"
                            <?= ($g == 'Other') ? 'checked' : '' ?>>
                        Other
                    </label>
                </div>

                <div class="invalid-feedback"></div>
            </div>

            <!-- State -->
            <div class="col-md-6 mb-3">
                <label>State *</label>

                <select name="state" class="form-select">
                    <option value="">Select State</option>
                    <?php foreach($states as $s): ?>
                        <option value="<?= $s ?>"
                            <?= set_value('state', $person->state ?? '') == $s ? 'selected' : '' ?>>
                            <?= $s ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="invalid-feedback"></div>
            </div>

        </div>

    </div>
</div>

<div class="mt-3 text-end">
    <button type="submit" class="btn btn-primary">Save</button>
</div>

<?= form_close(); ?>

<script>
    $(document).ready(function () {
        $.ajaxSetup({
            beforeSend: function (xhr, settings) {
                if (settings.type === 'POST') {
                    let csrfInput = $('input[name$="_token"]');
                    let tokenName  = csrfInput.attr('name');
                    let tokenValue = csrfInput.val();
                    if (tokenName && settings.data.indexOf(tokenName) === -1) {
                        settings.data += '&' + tokenName + '=' + encodeURIComponent(tokenValue);
                    }
                }
            }
        });

        // Setup validation
        $('#personForm').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                email: {
                    required: true,
                    email: true,
                    remote: {
                        url: "<?= base_url('persons/check_email') ?>",
                        type: "POST",
                        data: {
                            email: function () {
                                return $('#personForm input[name="email"]').val();
                            },
                            enc_id: function () {
                                return $('#enc_id').val();
                            }
                        },
                        dataFilter: function(response) {
                            return response === "true";
                        }
                    }
                },
                mobile: {
                    required: true,
                    minlength: 10,
                    maxlength: 10,
                    remote: {
                        url: "<?= base_url('persons/check_mobile') ?>",
                        type: "POST",
                        data: {
                            mobile: function () {
                                return $('#personForm input[name="mobile"]').val();
                            },
                            enc_id: function () {
                                return $('#enc_id').val();
                            }
                        },
                        dataFilter: function(response) {
                            return response === "true";
                        }
                    } 
                },
                gender: "required",
                state: "required"
            },
            messages: {
                name: {
                    required: "Enter name",
                    minlength: "Name must be at least 2 characters",
                    maxlength: "Name must be less than 100 characters"
                },
                email: {
                    required: "Enter valid email",
                    email:    "Enter valid email",
                    remote:   "Email already exists"
                },
                mobile: {
                    required: "Enter valid mobile",
                    minlength: "Enter valid mobile",
                    maxlength: "Enter valid mobile",
                    remote:   "Mobile already exists"
                },
                gender: "Select gender",
                state:  "Select state"
            },

            highlight: function (element) {
                let el = $(element);

                if (el.attr('type') === 'radio') {
                    // Mark all radios in the group + the wrapper
                    $('input[name="' + el.attr('name') + '"]').addClass('is-invalid');
                } else {
                    el.addClass('is-invalid');
                }
            },

            unhighlight: function (element) {
                let el = $(element);

                if (el.attr('type') === 'radio') {
                    $('input[name="' + el.attr('name') + '"]').removeClass('is-invalid');
                } else {
                    el.removeClass('is-invalid');
                }

                // Hide the message when field becomes valid
                el.closest('.mb-3, .col-md-6').find('.invalid-feedback')
                    .text('')
                    .css('display', 'none');
            },

            errorPlacement: function (error, element) {
                let msg = error.text();
                let wrapper = element.closest('.mb-3, .col-md-6');

                // Show text in the .invalid-feedback div
                wrapper.find('.invalid-feedback').text(msg).css('display', 'block');
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

                        // Update CSRF
                        if (res.csrf_token) {
                            $('input[name="' + res.csrf_name + '"]').val(res.csrf_token);
                        }

                        if (res.status === 'success') {
                            window.location.href = res.redirect;
                            return;
                        }

                        if (res.status === 'error') {

                            let html = `
                            <div class="alert alert-danger alert-dismissible fade show">
                                <strong>Please fix errors:</strong><br>
                            `;

                            $.each(res.errors, function(key, val) {
                            if (val) html += `• ${val}<br>`;
                            });

                            html += `
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            `;

                            $('#alertBox').html(html);
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