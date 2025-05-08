<h1>Add Category</h1>
<form action="<?= base_url('categories/create') ?>" method="POST">
    <div class="mb-3">
        <label for="name" class="form-label">Category Name</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Add Category</button>
    <a href="<?= base_url('categories') ?>" class="btn btn-secondary">Cancel</a>
</form>

<?php if (!empty($categories)): ?>
    <h2>Existing Categories</h2>
    <ul>
        <?php foreach ($categories as $category): ?>
            <li><?= htmlspecialchars($category['name']) ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>

<?php endif; ?>
