<h1>Categories</h1>
<a href="<?= base_url('categories/create') ?>" class="btn btn-primary mb-3">Add Category</a>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td><?= $category['name'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
