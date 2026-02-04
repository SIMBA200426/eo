<?php
require_once __DIR__ . '/../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit;
}

// Handle Delete
if (isset($_POST['delete_id'])) {
    // Optionally delete image file
    // $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    // $stmt->execute([(int)$_POST['delete_id']]);
    // $img = $stmt->fetchColumn();
    // if($img && $img != 'default.jpg') ... unlink ...
    
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([(int)$_POST['delete_id']]);
    header("Location: products.php"); // Redirect to clear POST
    exit;
}

// Handle Add/Edit Product
if (isset($_POST['action'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    
    $image = null;
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image = uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../assets/images/' . $image);
    }
    
    if ($_POST['action'] === 'add') {
        $imgToSave = $image ?? 'default.jpg';
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, category_id, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $stock, $category_id, $imgToSave]);
        
    } elseif ($_POST['action'] === 'edit') {
        $id = (int)$_POST['product_id'];
        
        $sql = "UPDATE products SET name=?, description=?, price=?, stock=?, category_id=?";
        $params = [$name, $description, $price, $stock, $category_id];
        
        if ($image) {
            $sql .= ", image=?";
            $params[] = $image;
        }
        
        $sql .= " WHERE id=?";
        $params[] = $id;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }
    
    header("Location: products.php");
    exit;
}

$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

$pageTitle = "Manage Products";
include __DIR__ . '/../components/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Products</h1>
    <button onclick="openModal('add')" class="btn btn-primary">
        + Add Product
    </button>
</div>

<div class="card overflow-hidden p-0" style="border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
    <div class="table-container" style="border: none; box-shadow: none;">
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th style="width: 80px;">Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $p): ?>
                <tr>
                    <td style="color: var(--muted);">#<?= $p['id'] ?></td>
                    <td>
                        <div style="width: 48px; height: 48px; background: #f1f5f9; border-radius: 8px; overflow: hidden; border: 1px solid var(--border);">
                            <?php if($p['image'] && $p['image'] !== 'default.jpg'): ?>
                                <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($p['image']) ?>" style="width:100%; height:100%; object-fit:cover;" onerror="this.style.display='none'">
                            <?php else: ?>
                                <span style="display: flex; align-items: center; justify-content: center; height: 100%; color: #cbd5e1; font-size: 1.25rem;">📷</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td style="font-weight: 500;"><?= htmlspecialchars($p['name']) ?></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($p['category_name'] ?? 'N/A') ?></span></td>
                    <td>$<?= number_format($p['price'], 2) ?></td>
                    <td>
                        <span class="<?= $p['stock'] < 10 ? 'badge badge-danger' : 'badge badge-success' ?>">
                            <?= $p['stock'] ?> left
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <button onclick='openModal("edit", <?= json_encode($p) ?>)' class="btn btn-sm btn-secondary" style="margin-right: 0.5rem; padding: 0.4rem 0.8rem;">Edit</button>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');" style="display: inline;">
                            <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-sm" style="background: #fee2e2; color: #991b1b; padding: 0.4rem 0.8rem;">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="productModal" class="drawer-overlay flex items-center justify-center">
    <div class="card w-full m-4" style="max-width: 550px; background: white; z-index: 60; opacity: 0; transform: translateY(20px); transition: all 0.3s ease;" id="modalContent">
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h2 class="text-xl font-bold" id="modalTitle">Add Product</h2>
            <button onclick="closeModal()" class="text-2xl text-muted hover:text-red-500">&times;</button>
        </div>
        
        <form method="POST" enctype="multipart/form-data" id="productForm">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="product_id" id="productId">
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="label text-sm text-muted">Product Name</label>
                    <input type="text" name="name" id="pName" class="input" required placeholder="e.g. Wireless Headphones">
                </div>
                <div>
                    <label class="label text-sm text-muted">Category</label>
                    <select name="category_id" id="pCategory" class="input">
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="label text-sm text-muted">Price ($)</label>
                    <input type="number" step="0.01" name="price" id="pPrice" class="input" required placeholder="0.00">
                </div>
                <div>
                    <label class="label text-sm text-muted">Stock Quantity</label>
                    <input type="number" name="stock" id="pStock" class="input" required placeholder="0">
                </div>
            </div>

            <div class="mb-4">
                <label class="label text-sm text-muted">Description</label>
                <textarea name="description" id="pDesc" class="input" rows="3" placeholder="Product details..."></textarea>
            </div>

            <div class="mb-6">
                <label class="label text-sm text-muted">Product Image</label>
                <div style="border: 2px dashed var(--border); border-radius: 0.5rem; padding: 1.5rem; text-align: center; background: #f9fafb;">
                    <div id="imagePreview" style="margin-bottom: 1rem; display: none;">
                         <img src="" id="previewImg" style="max-height: 100px; margin: 0 auto; border-radius: 4px;">
                         <p class="text-xs text-muted mt-2">Current Image</p>
                    </div>
                    <input type="file" name="image" class="input" style="margin-bottom: 0;">
                    <p class="text-xs text-muted mt-2">Leave empty to keep current image (if editing)</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitBtn">Save Product</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(mode, data = null) {
    const modal = document.getElementById('productModal');
    const content = document.getElementById('modalContent');
    const form = document.getElementById('productForm');
    const title = document.getElementById('modalTitle');
    const btn = document.getElementById('submitBtn');
    const preview = document.getElementById('imagePreview');
    
    // Reset Form
    form.reset();
    preview.style.display = 'none';

    if (mode === 'edit' && data) {
        title.innerText = 'Edit Product';
        btn.innerText = 'Update Product';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('productId').value = data.id;
        
        document.getElementById('pName').value = data.name;
        document.getElementById('pPrice').value = data.price;
        document.getElementById('pStock').value = data.stock;
        document.getElementById('pCategory').value = data.category_id;
        document.getElementById('pDesc').value = data.description || '';
        
        if(data.image && data.image !== 'default.jpg') {
            preview.style.display = 'block';
            document.getElementById('previewImg').src = '<?= BASE_URL ?>/assets/images/' + data.image;
        }
    } else {
        title.innerText = 'Add New Product';
        btn.innerText = 'Create Product';
        document.getElementById('formAction').value = 'add';
    }

    // Show
    modal.classList.add('open');
    // Small animation delay
    setTimeout(() => {
        content.style.opacity = '1';
        content.style.transform = 'translateY(0)';
    }, 50);
}

function closeModal() {
    const modal = document.getElementById('productModal');
    const content = document.getElementById('modalContent');
    
    content.style.opacity = '0';
    content.style.transform = 'translateY(20px)';
    
    setTimeout(() => {
        modal.classList.remove('open');
    }, 300);
}

// Close on click outside
document.getElementById('productModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

<?php include __DIR__ . '/../components/footer.php'; ?>
