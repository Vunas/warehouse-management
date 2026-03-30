<?php 

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
        // $this->authorizeResource(CartItem::class, 'cart');
    }

    public function index(Request $request)
    {
        // View cho Admin xem danh sách giỏ hàng
        $query = CartItem::with(['user', 'product'])->orderBy('updated_at', 'desc');

        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $carts = $query->paginate($request->get('per_page', 15));
        
        return view('admin.carts.index', compact('carts'));
    }

    // Các hàm Store/Update/Destroy thường sẽ được gọi qua API/Frontend Controller thay vì Admin Controller
}