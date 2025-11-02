require_once '../models/Product.php';
class ProductController {
public function index($conn) {
$product = new Product();
$productos = $product->getAllProducts($conn);
include '../views/products.php';
}
}