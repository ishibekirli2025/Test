<h1>Add Product</h1>
<form action="<?= base_url('products/create') ?>" method="POST">
    <div class="mb-3">
        <label for="name" class="form-label">Product Name</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" name="price" id="price" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="stock_quantity" class="form-label">Stock Quantity</label>
        <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="categories" class="form-label">Categories</label>
        <select name="categories[]" id="categories" class="form-control" multiple required>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
            <?php endforeach; ?>
        </select>

    </div>
    <button type="submit" class="btn btn-primary">Add Product</button>
    <a href="<?= base_url('products') ?>" class="btn btn-secondary">Cancel</a>
</form>
