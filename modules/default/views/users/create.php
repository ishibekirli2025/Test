<h1>Create User</h1>
<form action="<?= base_url('users/create') ?>" method="POST">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" name="username" id="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="firstname" class="form-label">First Name</label>
        <input type="text" name="first_name" id="first_name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="lastname" class="form-label">Last Name</label>
        <input type="text" name="last_name" id="last_name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" name="phone" id="phone" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Create User</button>
    <a href="<?= base_url('users') ?>" class="btn btn-secondary">Cancel</a>
</form>
