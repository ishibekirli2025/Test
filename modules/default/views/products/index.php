<h1>Products</h1>
<a href="<?= base_url('products/create') ?>" class="btn btn-primary mb-3">Add Product</a>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Stock Quantity</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= $product['name'] ?></td>
                <td>$<?= $product['price'] ?></td>
                <td><?= $product['stock_quantity'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
