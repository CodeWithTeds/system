<?php
require_once 'Views/includes/head.php';
require_once 'Views/includes/header.php';
require_once 'Views/includes/navbar.php';
?>

<div class="ml-[250px] p-8">
    <div class="mb-6 flex justify-between items-center mt-[80px]">
        <h1 class="text-2xl font-['Chilanka']">Edit Product</h1>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <!-- General Information -->
        <div class="col-span-2 bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium mb-6">General Information</h2>
            <form id="editProductForm" class="space-y-6">
                <input type="hidden" name="product_id" value="<?= $product->id ?>">
                
                <div>
                    <label class="block text-sm text-gray-700">Product Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($product->name) ?>" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#A1E95C] focus:ring-[#A1E95C]">
                </div>

                <div>
                    <label class="block text-sm text-gray-700">Category</label>
                    <select name="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#A1E95C] focus:ring-[#A1E95C]">
                        <option value="RICE" <?= $product->category === 'RICE' ? 'selected' : '' ?>>Rice</option>
                        <option value="CAT" <?= $product->category === 'CAT' ? 'selected' : '' ?>>Cat</option>
                        <option value="DOG" <?= $product->category === 'DOG' ? 'selected' : '' ?>>Dog</option>
                        <option value="FISH" <?= $product->category === 'FISH' ? 'selected' : '' ?>>Fish</option>
                        <option value="BIRD" <?= $product->category === 'BIRD' ? 'selected' : '' ?>>Bird</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-700">Description</label>
                    <textarea name="description" rows="4" 
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#A1E95C] focus:ring-[#A1E95C]"><?= htmlspecialchars($product->description) ?></textarea>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm text-gray-700">Base Price ($)</label>
                        <input type="number" name="base_price" value="<?= $product->base_price ?>" step="0.01"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#A1E95C] focus:ring-[#A1E95C]">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700">Brand</label>
                        <input type="text" name="brand" value="<?= htmlspecialchars($product->brand) ?>"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#A1E95C] focus:ring-[#A1E95C]">
                    </div>
                </div>

                <!-- Product Variants -->
                <div class="space-y-4">
                    <h2 class="text-lg font-medium">Product Variants</h2>
                    <div id="variantsContainer">
                        <?php foreach ($variants as $variant): ?>
                        <div class="variant-item grid grid-cols-3 gap-4 mb-4">
                            <input type="hidden" name="variant_ids[]" value="<?= $variant->id ?>">
                            <div>
                                <input type="text" name="variant_sizes[]" value="<?= htmlspecialchars($variant->size) ?>"
                                       placeholder="Size" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <input type="number" name="variant_prices[]" value="<?= $variant->price ?>"
                                       placeholder="Price ($)" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <input type="number" name="variant_stocks[]" value="<?= $variant->stock ?>"
                                       placeholder="Stock" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" onclick="addVariant()" 
                            class="px-4 py-2 bg-[#A1E95C] text-black rounded-md hover:bg-[#8fd152]">
                        + Add Size
                    </button>
                </div>
            </form>
        </div>

        <!-- Product Images -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-medium mb-6">Product Images</h2>
            
            <!-- Main Image -->
            <div class="mb-6">
                <label class="block text-sm text-gray-700 mb-2">Main Image</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-[#A1E95C]"
                     onclick="document.getElementById('mainImage').click()">
                    <img id="mainImagePreview" src="<?= $product->main_image ?>" 
                         class="mx-auto mb-2 <?= $product->main_image ? '' : 'hidden' ?>"
                         style="max-height: 200px; object-fit: contain;">
                    <div id="mainImagePlaceholder" class="<?= $product->main_image ? 'hidden' : '' ?>">
                        Click to upload main image
                    </div>
                    <input type="file" id="mainImage" name="main_image" class="hidden" accept="image/*">
                </div>
            </div>

            <!-- Additional Images -->
            <div>
                <label class="block text-sm text-gray-700 mb-2">Additional Images</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-[#A1E95C]"
                     onclick="document.getElementById('additionalImages').click()">
                    <div id="additionalImagesPreview" class="grid grid-cols-2 gap-2 mb-2">
                        <?php if (isset($product_images) && !empty($product_images)): ?>
                            <?php foreach ($product_images as $image): ?>
                                <img src="<?= $image->image_url ?>" class="w-full h-24 object-cover rounded">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div id="additionalImagesPlaceholder" class="<?= isset($product_images) && count($product_images) > 0 ? 'hidden' : '' ?>">
                        Click to upload additional images
                    </div>
                    <input type="file" id="additionalImages" name="additional_images[]" class="hidden" accept="image/*" multiple>
                </div>
            </div>

            <!-- Update Button -->
            <div class="mt-6">
                <button onclick="submitForm()" 
                        class="w-full px-6 py-2 bg-[#A1E95C] text-black rounded-md hover:bg-[#8fd152]">
                    Update Product
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Image preview handlers
document.getElementById('mainImage').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('mainImagePreview').src = e.target.result;
            document.getElementById('mainImagePreview').classList.remove('hidden');
            document.getElementById('mainImagePlaceholder').classList.add('hidden');
        }
        reader.readAsDataURL(file);
    }
});

document.getElementById('additionalImages').addEventListener('change', function(e) {
    const files = e.target.files;
    const preview = document.getElementById('additionalImagesPreview');
    preview.innerHTML = '';
    
    for (let file of files) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML += `
                <img src="${e.target.result}" class="w-full h-24 object-cover rounded">
            `;
        }
        reader.readAsDataURL(file);
    }
    
    document.getElementById('additionalImagesPlaceholder').classList.add('hidden');
});

function addVariant() {
    const container = document.getElementById('variantsContainer');
    const newVariant = `
        <div class="variant-item grid grid-cols-3 gap-4 mb-4">
            <input type="hidden" name="variant_ids[]" value="new">
            <div>
                <input type="text" name="variant_sizes[]" placeholder="Size" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <input type="number" name="variant_prices[]" placeholder="Price ($)" step="0.01"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <input type="number" name="variant_stocks[]" placeholder="Stock"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', newVariant);
}

async function submitForm() {
    const form = document.getElementById('editProductForm');
    const formData = new FormData(form);
    
    // Add images to formData
    const mainImage = document.getElementById('mainImage').files[0];
    if (mainImage) {
        formData.append('main_image', mainImage);
    }
    
    const additionalImages = document.getElementById('additionalImages').files;
    for (let file of additionalImages) {
        formData.append('additional_images[]', file);
    }
    
    try {
        const response = await fetch('/e-commerce/admin/update-product', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Product updated successfully');
            window.location.href = '/e-commerce/admin/all-products';
        } else {
            alert(data.message || 'Error updating product');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error updating product');
    }
}
</script>

<?php require_once 'Views/includes/scripts.php'; ?> 