<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/e-commerce/config/Database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/e-commerce/Models/Product.php';

class ProductController
{
    private $db;
    private $productModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->productModel = new Product();
    }

    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $products = $this->productModel->getAllProducts($limit, $offset);
        require_once 'Views/all_products.php';
    }

    public function add()
    {
        try {
            // Ensure clean output
            while (ob_get_level()) ob_end_clean();
            header('Content-Type: application/json');

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            // Validate required fields
            if (
                empty($_POST['name']) || empty($_POST['description']) ||
                empty($_POST['base_price']) || empty($_POST['brand'])
            ) {
                throw new Exception("All fields are required");
            }

            // Handle main image upload
            if (!isset($_FILES['main_image']) || $_FILES['main_image']['error'] !== 0) {
                throw new Exception("Main image is required");
            }

            // Create uploads directory with proper path and permissions
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/e-commerce/uploads/';
            if (!file_exists($uploadDir)) {
                $oldmask = umask(0);
                if (!@mkdir($uploadDir, 0777, true)) {
                    $error = error_get_last();
                    throw new Exception("Failed to create uploads directory: " . $error['message']);
                }
                chmod($uploadDir, 0777); // Ensure proper permissions
                umask($oldmask);
            }

            // Verify directory is writable
            if (!is_writable($uploadDir)) {
                throw new Exception("Uploads directory is not writable");
            }

            $mainImage = $this->handleFileUpload($_FILES['main_image'], 'main');
            if (!$mainImage) {
                throw new Exception("Failed to upload main image");
            }

            // Database operations in a transaction
            $this->db->beginTransaction();

            try {
                // Insert main product
                $sql = "INSERT INTO products (name, description, base_price, brand, category, main_image) 
                        VALUES (:name, :description, :base_price, :brand, :category, :main_image)";

                $this->db->query($sql);
                $this->db->bind(':name', htmlspecialchars($_POST['name']));
                $this->db->bind(':description', htmlspecialchars($_POST['description']));
                $this->db->bind(':base_price', filter_var($_POST['base_price'], FILTER_VALIDATE_FLOAT));
                $this->db->bind(':brand', htmlspecialchars($_POST['brand']));
                $this->db->bind(':category', strtoupper($_POST['category']));
                $this->db->bind(':main_image', '/e-commerce/' . $mainImage);

                $result = $this->db->execute();
                $productId = $this->db->lastInsertId();

                if (!$result || !$productId) {
                    throw new Exception("Failed to insert product data");
                }

                // Handle variants if they exist
                if (!empty($_POST['sizes'])) {
                    foreach ($_POST['sizes'] as $key => $size) {
                        $variantSql = "INSERT INTO product_variants (product_id, size, price, stock) 
                            VALUES (:product_id, :size, :price, :stock)";

                        $this->db->query($variantSql);
                        $this->db->bind(':product_id', $productId);
                        $this->db->bind(':size', $size);
                        $this->db->bind(':price', filter_var($_POST['variant_prices'][$key], FILTER_VALIDATE_FLOAT));
                        $this->db->bind(':stock', filter_var($_POST['variant_stocks'][$key], FILTER_VALIDATE_INT));
                        $this->db->execute();
                    }
                }

                // Handle additional images
                if (!empty($_FILES['additional_images']['name'][0])) {
                    $additionalImages = $this->handleMultipleFileUploads($_FILES['additional_images']);
                    foreach ($additionalImages as $image) {
                        $imageSql = "INSERT INTO product_images (product_id, image_path) 
                                VALUES (:product_id, :image_path)";

                        $this->db->query($imageSql);
                        $this->db->bind(':product_id', $productId);
                        $this->db->bind(':image_path', '/e-commerce/' . $image);
                        $this->db->execute();
                    }
                }

                $this->db->commit();

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Product added successfully!'
                ]);
                exit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
            exit();
        }
    }

    private function handleFileUpload($file, $prefix)
    {
        if ($file['error'] === 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception("Invalid file type. Allowed types: JPG, PNG, GIF, WEBP");
            }

            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/e-commerce/uploads/';

            // Ensure directory exists and is writable
            if (!file_exists($uploadDir)) {
                $oldmask = umask(0);
                mkdir($uploadDir, 0777, true);
                chmod($uploadDir, 0777);
                umask($oldmask);
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = $prefix . '_' . uniqid() . '.' . $extension;
            $targetPath = $uploadDir . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                $error = error_get_last();
                throw new Exception("Failed to move uploaded file: " . $error['message']);
            }

            return 'uploads/' . $fileName;
        }
        throw new Exception("File upload error: " . $file['error']);
    }

    private function handleMultipleFileUploads($files)
    {
        $uploadedFiles = [];
        if (!empty($files['name'][0])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            foreach ($files['name'] as $key => $name) {
                if ($files['error'][$key] === 0) {
                    if (!in_array($files['type'][$key], $allowedTypes)) {
                        continue;
                    }

                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/e-commerce/uploads/';
                    $extension = pathinfo($name, PATHINFO_EXTENSION);
                    $fileName = 'additional_' . uniqid() . '_' . $key . '.' . $extension;
                    $targetPath = $uploadDir . $fileName;

                    if (move_uploaded_file($files['tmp_name'][$key], $targetPath)) {
                        $uploadedFiles[] = 'uploads/' . $fileName;
                    }
                }
            }
        }
        return $uploadedFiles;
    }

    public function getProducts($page = 1, $perPage = 10)
    {
        try {
            // Calculate offset
            $offset = ($page - 1) * $perPage;
            
            // Get total count
            $this->db->query("SELECT COUNT(*) as total FROM products");
            $total = $this->db->single()->total;
            
            // Get paginated products
            $sql = "
                SELECT 
                    p.*,
                    MIN(pv.price) as min_price,
                    MAX(pv.price) as max_price
                FROM products p
                LEFT JOIN product_variants pv ON p.id = pv.product_id
                GROUP BY p.id
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset
            ";
            
            $this->db->query($sql);
            $this->db->bind(':limit', $perPage);
            $this->db->bind(':offset', $offset);
            
            return [
                'products' => $this->db->resultSet(),
                'total' => $total,
                'currentPage' => $page,
                'perPage' => $perPage,
                'totalPages' => ceil($total / $perPage)
            ];
        } catch (PDOException $e) {
            error_log("Error fetching products: " . $e->getMessage());
            return [];
        }
    }

    public function getProductDetails($productId)
    {
        try {
            $product = new Product();

            // Get basic product info
            $productDetails = $product->getProductDetails($productId);

            if (!$productDetails) {
                return null;
            }

            // Get variants
            $variants = $product->getProductVariants($productId);

            // Get additional images
            $images = $product->getProductImages($productId);

            return [
                'product' => $productDetails,
                'variants' => $variants,
                'images' => $images
            ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function getProductVariants($productId) {
        try {
            // Add debug logging
            error_log("Fetching variants for product ID: " . $productId);
            
            // Ensure productId is valid
            if (!$productId || !is_numeric($productId)) {
                throw new Exception("Invalid product ID");
            }

            $sql = "SELECT * FROM product_variants WHERE product_id = :product_id";
            $this->db->query($sql);
            $this->db->bind(':product_id', $productId);
            $variants = $this->db->resultSet();
            
            // Debug log the results
            error_log("Found variants: " . print_r($variants, true));

            // Set proper headers
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            
            // Return empty array if no variants found
            echo json_encode($variants ? $variants : []);
            
        } catch (Exception $e) {
            error_log("Error in getProductVariants: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function deleteProduct($id) {
        try {
            $productModel = new Product();
            $result = $productModel->deleteProduct($id);
            
            if($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Product deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to delete product'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false, 
                'message' => 'Error deleting product: ' . $e->getMessage()
            ]);
        }
        exit();
    }

    public function editProduct($id) {
        $productModel = new Product();
        $product = $productModel->getProductDetails($id);
        $variants = $productModel->getProductVariants($id);
        $product_images = $productModel->getProductImages($id);

        if (!$product) {
            $_SESSION['error'] = "Product not found";
            header('Location: /e-commerce/admin/all-products');
            exit();
        }
        
        require_once 'Views/edit_product.php';
    }

    public function updateProduct() {
        try {
            $productModel = new Product();
            $result = $productModel->updateProduct($_POST, $_FILES);
            
            if($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Product updated successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update product'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ]);
        }
        exit();
    }
}
