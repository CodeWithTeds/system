<?php
require_once 'Views/includes/auth_middleware.php';
checkStaffAuth(); // Check if staff is logged in

require_once 'Views/includes/head.php';
require_once 'Views/includes/header.php';
require_once 'Views/includes/navbar.php';

// Initialize ProductController
require_once $_SERVER['DOCUMENT_ROOT'] . '/e-commerce/Controllers/ProductController.php';
$productController = new ProductController();
$products = $productController->getProducts();
?>


<div class="ml-[250px] p-8">
    <div class="mb-6 flex justify-between items-center mt-[80px]">

        <!-- Category Filter Buttons -->
        <div class="flex gap-4">
            <button class="filter-button px-4 py-2 rounded-lg bg-gradient-to-r from-[#A1E95C] to-[#8fd152] text-white hover:shadow-md transition-all active shadow-sm" data-filter="*">
                <div class="flex items-center gap-2">
                    <iconify-icon icon="mdi:view-grid" class="w-5 h-5"></iconify-icon>
                    ALL
                </div>
            </button>
            <button class="filter-button px-4 py-2 rounded-lg hover:bg-gradient-to-r from-[#A1E95C] to-[#8fd152] text-gray-700 hover:text-white transition-all shadow-sm" data-filter="RICE">
                <div class="flex items-center gap-2">
                    <iconify-icon icon="mdi:rice" class="w-5 h-5"></iconify-icon>
                    RICE
                </div>
            </button>
            <button class="filter-button px-4 py-2 rounded-lg hover:bg-gradient-to-r from-[#A1E95C] to-[#8fd152] text-gray-700 hover:text-white transition-all shadow-sm" data-filter="CAT">
                <div class="flex items-center gap-2">
                    <iconify-icon icon="mdi:cat" class="w-5 h-5"></iconify-icon>
                    CAT
                </div>
            </button>
            <button class="filter-button px-4 py-2 rounded-lg hover:bg-gradient-to-r from-[#A1E95C] to-[#8fd152] text-gray-700 hover:text-white transition-all shadow-sm" data-filter="FISH">
                <div class="flex items-center gap-2">
                    <iconify-icon icon="mdi:fish" class="w-5 h-5"></iconify-icon>
                    FISH
                </div>
            </button>
            <button class="filter-button px-4 py-2 rounded-lg hover:bg-gradient-to-r from-[#A1E95C] to-[#8fd152] text-gray-700 hover:text-white transition-all shadow-sm" data-filter="DOG">
                <div class="flex items-center gap-2">
                    <iconify-icon icon="mdi:dog" class="w-5 h-5"></iconify-icon>
                    DOG
                </div>
            </button>
            <button class="filter-button px-4 py-2 rounded-lg hover:bg-gradient-to-r from-[#A1E95C] to-[#8fd152] text-gray-700 hover:text-white transition-all shadow-sm" data-filter="BIRD">
                <div class="flex items-center gap-2">
                    <iconify-icon icon="mdi:bird" class="w-5 h-5"></iconify-icon>
                    BIRD
                </div>
            </button>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <?php
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <?php
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Products Table with modern design -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="relative">
                    <input type="text"
                        id="searchInput"
                        placeholder="Search products..."
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <iconify-icon icon="mdi:magnify" class="w-5 h-5 text-gray-400 absolute left-3 top-2.5"></iconify-icon>
                </div>
                <a href="../admin/add-products" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:shadow-md transition-all">
                    <iconify-icon icon="mdi:plus" class="w-5 h-5 mr-2"></iconify-icon>
                    Add New Product
                </a>
            </div>
        </div>

        <!-- Rest of your table code with enhanced styling -->
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-20">Image</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-28">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Brand</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase w-24">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($products['products'] as $product): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <img src="<?= htmlspecialchars($product->main_image) ?>"
                                alt="<?= htmlspecialchars($product->name) ?>"
                                class="w-12 h-12 object-cover rounded">
                        </td>
                        <td class="px-4 py-2">
                            <p class="text-sm truncate max-w-[300px]" title="<?= htmlspecialchars($product->name) ?>">
                                <?= htmlspecialchars($product->name) ?>
                            </p>
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded-full
                                <?= strtolower($product->category) === 'cat' ? 'bg-blue-100 text-blue-800' : (strtolower($product->category) === 'dog' ? 'bg-green-100 text-green-800' : (strtolower($product->category) === 'fish' ? 'bg-yellow-100 text-yellow-800' :
                                            'bg-purple-100 text-purple-800')) ?>">
                                <?= htmlspecialchars($product->category) ?>
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm">
                            ₱<?= number_format($product->base_price, 2) ?>
                        </td>
                        <td class="px-4 py-2 text-sm">
                            <?= htmlspecialchars($product->brand) ?>
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex justify-center gap-3">
                                <a href="/e-commerce/admin/edit-product/<?= $product->id ?>"
                                    class="text-blue-600 hover:text-blue-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </a>
                                <button onclick="confirmDelete(<?= $product->id ?>)"
                                    class="text-red-600 hover:text-red-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination section at the bottom -->
    <div class="mt-4 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Showing 1 to 10 of <?= $products['total'] ?> results
        </div>
        <div class="flex justify-end gap-2">
            <?php for ($i = 1; $i <= ceil($products['total'] / 10); $i++): ?>
                <a href="?page=<?= $i ?>"
                    class="px-3 py-1 text-sm rounded-lg <?= ($i == ($products['currentPage'] ?? 1)) ? 'bg-[#A1E95C] text-black' : 'bg-gray-100' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Product</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to delete this product? This action cannot be undone.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="deleteButton"
                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Delete
                    </button>
                    <button onclick="closeDeleteModal()"
                        class="ml-3 px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-button');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => {
                        btn.classList.remove('active');
                        btn.classList.remove('bg-gradient-to-r');
                        btn.classList.add('hover:bg-gradient-to-r');
                    });

                    // Add active class to clicked button
                    this.classList.add('active');
                    this.classList.add('bg-gradient-to-r');
                    this.classList.remove('hover:bg-gradient-to-r');

                    // Your existing filter logic
                    const category = this.getAttribute('data-filter');
                    const tableRows = document.querySelectorAll('tbody tr');

                    tableRows.forEach(row => {
                        row.style.transition = 'all 0.3s ease';
                        const rowCategory = row.querySelector('td:nth-child(3) span').textContent.trim();
                        if (category === '*' || category === rowCategory) {
                            row.style.display = '';
                            row.style.opacity = '1';
                        } else {
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.style.display = 'none';
                            }, 300);
                        }
                    });
                });
            });
        });

        let productIdToDelete = null;

        function confirmDelete(productId) {
            productIdToDelete = productId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            productIdToDelete = null;
        }

        document.getElementById('deleteButton').addEventListener('click', async function() {
            if (productIdToDelete) {
                try {
                    const response = await fetch(`/e-commerce/admin/delete-product/${productIdToDelete}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Show success message
                        alert('Product deleted successfully');
                        window.location.reload();
                    } else {
                        alert(data.message || 'Error deleting product');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error deleting product');
                }
                closeDeleteModal();
            }
        });

        function viewVariants(productId) {
            window.location.href = `/e-commerce/admin/product-variants/${productId}`;
        }
    </script>

    <?php require_once 'Views/includes/scripts.php'; ?>

    <style>
        .filter-button.active {
            background-image: linear-gradient(to right, #A1E95C, #8fd152);
            color: white;
        }

        .filter-button:hover {
            transform: translateY(-1px);
        }
    </style>