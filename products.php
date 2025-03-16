<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/e-commerce/Models/Product.php';

$productModel = new Product();
$products = $productModel->getAllProducts();
?>

<style>
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}

.modal {
    background-color: transparent;
}

.category-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    z-index: 1;
}

.category-badge.cat { background-color: #a8e6cf; }
.category-badge.dog { background-color: #ffd3b6; }
.category-badge.fish { background-color: #a8e6ff; }
.category-badge.bird { background-color: #ffb6b9; }
.category-badge.rice { background-color: #f8d775; }
</style>

<section id="foodies" class="my-5">
    <div class="container my-5 py-5">
        <div class="section-header d-md-flex justify-content-between align-items-center">
            <h2 class="display-3 fw-normal">Pet Foodies</h2>
            <div class="mb-4 mb-md-0">
                <p class="m-0">
                    <button class="filter-button me-4 active" data-filter="*">ALL</button>
                    <button class="filter-button me-4" data-filter=".cat">CAT</button>
                    <button class="filter-button me-4" data-filter=".fish">FISH</button>
                    <button class="filter-button me-4" data-filter=".dog">DOG</button>
                    <button class="filter-button me-4" data-filter=".bird">BIRD</button>
                </p>
            </div>
            <div>
                <a href="#" class="btn btn-outline-dark btn-lg text-uppercase fs-6 rounded-1">
                    shop now
                    <svg width="24" height="24" viewBox="0 0 24 24" class="mb-1">
                        <use xlink:href="#arrow-right"></use>
                    </svg>
                </a>
            </div>
        </div>

        <div class="isotope-container row">
            <?php foreach ($products as $product): ?>
                <div class="item <?= strtolower($product->category ?? '') ?> col-md-4 col-lg-3 my-4">
                    <!-- Category Badge -->
                    <div class="category-badge <?= strtolower($product->category ?? '') ?>">
                        <?= $product->category ?? '' ?>
                    </div>

                    <?php if ($product->created_at > date('Y-m-d H:i:s', strtotime('-7 days'))): ?>
                        <div class="z-1 position-absolute rounded-3 m-3 px-3 border border-dark-subtle">
                            New
                        </div>
                    <?php endif; ?>

                    <div class="card product-card position-relative">
                        <a href="javascript:void(0)" class="view-product" data-product-id="<?= $product->id ?>">
                            <img src="<?= !empty($product->main_image) ? $product->main_image : '/e-commerce/assets/images/placeholder.jpg' ?>"
                                class="img-fluid rounded-4"
                                alt="<?= htmlspecialchars($product->name ?? '') ?>">
                        </a>
                        <div class="card-body p-0">
                            <a href="javascript:void(0)" class="view-product" data-product-id="<?= $product->id ?>">
                                <h3 class="card-title pt-4 m-0"><?= htmlspecialchars($product->name ?? '') ?></h3>
                            </a>

                            <div class="card-text">
                                <span class="rating secondary-font">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <iconify-icon icon="clarity:star-solid" class="text-primary"></iconify-icon>
                                    <?php endfor; ?>
                                    5.0
                                </span>

                                <h3 class="secondary-font text-primary">
                                    <?php if (isset($product->min_price) && isset($product->max_price) && $product->min_price == $product->max_price): ?>
                                        ₱<?= number_format($product->min_price, 2) ?>
                                    <?php else: ?>
                                        ₱<?= number_format($product->min_price ?? 0, 2) ?> - ₱<?= number_format($product->max_price ?? 0, 2) ?>
                                    <?php endif; ?>
                                </h3>

                                <div class="d-flex flex-wrap mt-3 gap-2">
                                    <button type="button" 
                                            class="text-white bg-primary p-3 rounded-full hover:bg-primary-dark transition view-product"
                                            data-product-id="<?= $product->id ?>">
                                        <iconify-icon icon="carbon:view" class="text-xl"></iconify-icon>
                                    </button>

                                    <button type="button" 
                                            class="btn btn-primary add-to-cart-btn" 
                                            onclick="openProductModal(<?= $product->id ?>)"
                                            data-product-id="<?= $product->id ?>"
                                            data-product-name="<?= htmlspecialchars($product->name ?? '') ?>"
                                            data-product-price="<?= $product->base_price ?? 0 ?>">
                                        <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter functionality
        const filterButtons = document.querySelectorAll('.filter-button');
        const items = document.querySelectorAll('.item');

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');

                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                items.forEach(item => {
                    if (filter === '*' || item.classList.contains(filter.substring(1))) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // Cart Modal Functionality
        const cartModal = document.getElementById('cartModal');
        const modalProductId = document.getElementById('modalProductId');
        const addToCartForm = document.getElementById('addToCartForm');
        const confirmAddToCartBtn = document.getElementById('confirmAddToCart');

        cartModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            modalProductId.value = productId;
        });

        confirmAddToCartBtn.addEventListener('click', function() {
            const formData = new FormData(addToCartForm);
            
            // Send to cart endpoint
            fetch('/e-commerce/add-to-cart', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart successfully!');
                    bootstrap.Modal.getInstance(cartModal).hide();
                } else {
                    alert('Error adding product to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding product to cart');
            });
        });

        const productModal = document.getElementById('productModal');
        
        productModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            
            console.log('Loading variants for product:', productId); // Debug log
            
            // Update other modal content
            document.getElementById('modalProductName').textContent = button.getAttribute('data-product-name');
            document.getElementById('modalProductDescription').textContent = button.getAttribute('data-product-description');
            document.getElementById('modalProductImages').innerHTML = 
                `<img src="${button.getAttribute('data-product-image')}" class="img-fluid" alt="${button.getAttribute('data-product-name')}">`;

            // Fetch variants
            fetch(`/e-commerce/api/get-variants.php?product_id=${productId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(variants => {
                    console.log('Received variants:', variants); // Debug log
                    
                    const sizeOptions = document.getElementById('modalSizeOptions');
                    sizeOptions.innerHTML = '';
                    
                    if (variants && variants.length > 0) {
                        variants.forEach(variant => {
                            const sizeBtn = document.createElement('button');
                            sizeBtn.type = 'button';
                            sizeBtn.className = 'btn btn-outline-secondary me-2';
                            sizeBtn.textContent = variant.size;
                            sizeBtn.setAttribute('data-price', variant.price);
                            
                            sizeBtn.onclick = function() {
                                // Remove active class from all buttons
                                sizeOptions.querySelectorAll('.btn').forEach(btn => {
                                    btn.classList.remove('active');
                                });
                                // Add active class to clicked button
                                this.classList.add('active');
                                // Update price
                                document.getElementById('modalProductPrice').textContent = 
                                    Number(variant.price).toFixed(2);
                            };
                            
                            sizeOptions.appendChild(sizeBtn);
                        });
                    } else {
                        sizeOptions.innerHTML = '<p class="text-muted">No sizes available</p>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching variants:', error);
                    document.getElementById('modalSizeOptions').innerHTML = 
                        '<p class="text-danger">Error loading sizes</p>';
                });
        });

        // Updated addToCart function
        function addToCart() {
            const productId = document.querySelector('[data-product-id]').getAttribute('data-product-id');
            const quantity = document.getElementById('modalQuantity').value;
            const selectedSize = document.querySelector('#modalSizeOptions .btn.active');
            
            if (!selectedSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select a size first!'
                });
                return;
            }

            const variantSize = selectedSize.textContent;

            fetch('/e-commerce/api/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    variant_size: variantSize,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Product added to cart successfully!'
                    });
                    
                    // Properly close the modal and remove backdrop
                    const modal = document.getElementById('productModal');
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    modalInstance.hide();
                    
                    // Remove modal backdrop and restore scrolling
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                    
                } else {
                    throw new Error(data.message || 'Failed to add to cart');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message || 'Something went wrong!'
                });
            });
        }

        // Add click event listener to the Add to Cart button
        document.querySelector('#productModal .btn[onclick="addToCart()"]').onclick = addToCart;
    });
</script>
<!-- Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-2xl">
            <!-- Header -->
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title text-xl font-semibold" id="cartModalLabel">Add to Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-4">
                <form id="addToCartForm" class="space-y-6">
                    <input type="hidden" id="modalProductId" name="product_id">
                    
                    <!-- Quantity Input -->
                    <div>
                        <label for="quantity" class="mb-2 block text-sm font-medium text-gray-900">
                            Quantity
                        </label>
                        <div class="flex w-fit items-center rounded-lg border border-gray-200">
                            <button type="button" class="p-2 text-gray-600 hover:text-primary" onclick="decrementCartQuantity()">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <input type="number" 
                                   id="quantity" 
                                   name="quantity"
                                   class="w-16 border-x border-gray-200 p-2 text-center focus:outline-none" 
                                   value="1" 
                                   min="1">
                            <button type="button" class="p-2 text-gray-600 hover:text-primary" onclick="incrementCartQuantity()">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 p-4">
                <button type="button" 
                        class="rounded-lg bg-gray-100 px-6 py-2.5 text-gray-700 transition-colors hover:bg-gray-200" 
                        data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" 
                        class="rounded-lg bg-primary px-6 py-2.5 text-white transition-colors hover:bg-primary-dark" 
                        id="confirmAddToCart">
                    Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Quantity functions for cart modal
function incrementCartQuantity() {
    const input = document.getElementById('quantity');
    input.value = parseInt(input.value) + 1;
}

function decrementCartQuantity() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}
</script>

