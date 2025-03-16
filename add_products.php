<?php
require_once 'Views/includes/auth_middleware.php';
checkStaffAuth(); // Check if staff is logged in

require_once 'Views/includes/head.php';
require_once 'Views/includes/header.php';
require_once 'Views/includes/navbar.php';
?>

<div class="ml-[250px] p-8">
    <div class="mb-6 flex justify-between items-center mt-[80px]">
        <h1 class="text-2xl font-['Chilanka']">Add New Product</h1>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
    </div>

    <form id="addProductForm" action="/e-commerce/admin/add-products" method="POST" enctype="multipart/form-data" class="font-['Chilanka']">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - General Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl mb-4">General Information</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-gray-600 mb-2">Product Name</label>
                            <input type="text" name="name" class="w-full p-2 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-2">Category</label>
                            <select name="category" class="w-full p-2 border rounded-lg" required>
                                <option value="ALL">ALL</option>
                                <option value="RICE">RICE</option>
                                <option value="CAT">CAT</option>
                                <option value="FISH">FISH</option>
                                <option value="DOG">DOG</option>
                                <option value="BIRD">BIRD</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-2">Description</label>
                            <textarea name="description" class="w-full p-2 border rounded-lg" rows="4" required></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-600 mb-2">Base Price (₱)</label>
                                <input type="number" name="base_price" step="0.01" class="w-full p-2 border rounded-lg" required>
                            </div>
                            <div>
                                <label class="block text-gray-600 mb-2">Brand</label>
                                <input type="text" name="brand" class="w-full p-2 border rounded-lg" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Variants Section -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl">Product Variants</h2>
                        <button type="button" onclick="addNewVariant()" class="text-sm px-3 py-1 bg-gray-100 rounded-lg">+ Add Size</button>
                    </div>
                    <div id="variantsContainer" class="space-y-4">
                        <!-- Initial variant -->
                        <div class="variant-item grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-gray-600 mb-2">Size</label>
                                <select name="sizes[]" class="w-full p-2 border rounded-lg" required>
                                    <option value="">Select Size</option>
                                    <option value="1KG">1KG</option>
                                    <option value="2KG">2KG</option>
                                    <option value="3KG">3KG</option>
                                    <option value="4KG">4KG</option>
                                    <option value="5KG">5KG</option>
                                    <option value="10KG">10KG</option>
                                    <option value="25KG">25KG</option>
                                    <option value="50KG">50KG</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-600 mb-2">Price (₱)</label>
                                <input type="number" name="variant_prices[]" step="0.01" class="w-full p-2 border rounded-lg" required>
                            </div>
                            <div>
                                <label class="block text-gray-600 mb-2">Stock</label>
                                <input type="number" name="variant_stocks[]" class="w-full p-2 border rounded-lg" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Images -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl mb-4">Product Images</h2>
                    <div class="space-y-6">
                        <div>
                            <label class="block text-gray-600 mb-2">Main Image</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                <input type="file" name="main_image" accept="image/*" class="hidden" id="mainImageInput" required>
                                <label for="mainImageInput" class="cursor-pointer">
                                    <div id="mainImagePreview" class="min-h-[200px] flex items-center justify-center">
                                        <span class="text-gray-500">Click to upload main image</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-2">Additional Images</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                <input type="file" name="additional_images[]" accept="image/*" multiple class="hidden" id="additionalImagesInput">
                                <label for="additionalImagesInput" class="cursor-pointer">
                                    <div id="additionalImagesPreview" class="min-h-[100px] flex items-center justify-center">
                                        <span class="text-gray-500">Click to upload additional images</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center mt-6">
                    <button type="submit" class="px-6 py-2 bg-[#A1E95C] text-black rounded-lg hover:bg-[#8fd152] transition-colors duration-200 font-['Chilanka']">
                        Add Product
                    </button>
                </div>
            </div>

            
        </div>

       
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function addNewVariant() {
    const container = document.getElementById('variantsContainer');
    const variantCount = container.children.length;
    
    const newVariant = document.createElement('div');
    newVariant.className = 'variant-item grid grid-cols-3 gap-4 relative';
    
    const variantHTML = `
        <div>
            <label class="block text-gray-600 mb-2">Size</label>
            <select name="sizes[]" class="w-full p-2 border rounded-lg" required>
                <option value="">Select Size</option>
                <option value="1KG">1KG</option>
                <option value="2KG">2KG</option>
                <option value="3KG">3KG</option>
                <option value="4KG">4KG</option>
                <option value="5KG">5KG</option>
                <option value="10KG">10KG</option>
                <option value="25KG">25KG</option>
                <option value="50KG">50KG</option>
            </select>
        </div>
        <div>
            <label class="block text-gray-600 mb-2">Price (₱)</label>
            <input type="number" name="variant_prices[]" step="0.01" class="w-full p-2 border rounded-lg" required>
        </div>
        <div>
            <label class="block text-gray-600 mb-2">Stock</label>
            <input type="number" name="variant_stocks[]" class="w-full p-2 border rounded-lg" required>
        </div>
        ${variantCount > 0 ? '<button type="button" class="absolute right-0 top-0 text-red-500" onclick="removeVariant(this)">Remove</button>' : ''}
    `;
    
    newVariant.innerHTML = variantHTML;
    container.appendChild(newVariant);
}

function removeVariant(button) {
    button.closest('.variant-item').remove();
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addProductForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        Swal.fire({
            title: 'Adding Product...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Create FormData object
        const formData = new FormData(this);

        // Send AJAX request
        fetch(this.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    confirmButtonText: 'View Products'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/e-commerce/admin/add-products';
                    }
                });
            } else {
                throw new Error(data.message || 'Something went wrong');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error.message || 'Something went wrong!',
                confirmButtonText: 'Try Again'
            });
        });
    });

    // Image preview functionality
    function handleImagePreview(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" class="h-[200px] w-full object-cover rounded">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Main image preview
    document.getElementById('mainImageInput').addEventListener('change', function() {
        handleImagePreview(this, 'mainImagePreview');
    });

    // Additional images preview
    document.getElementById('additionalImagesInput').addEventListener('change', function() {
        const preview = document.getElementById('additionalImagesPreview');
        preview.innerHTML = '';
        
        if (this.files) {
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML += `<img src="${e.target.result}" class="h-[100px] w-[100px] object-cover rounded m-1">`;
                }
                reader.readAsDataURL(file);
            });
        }
    });
});
</script>

<?php require_once 'Views/includes/scripts.php'; ?>
</body>
</html>
