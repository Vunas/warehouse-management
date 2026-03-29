<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerCartController extends Controller
{
    /**
     * Hiển thị giỏ hàng
     */
    public function index()
    {
        $cartItems = CartItem::where('user_id', Auth::id())
            ->with('product')
            ->get();
        
        $total = $cartItems->sum(function ($item) {
            return ($item->product->price ?? 0) * $item->quantity;
        });

        return view('customer.cart.index', compact('cartItems', 'total'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity;
        $userId = Auth::id();

        // Kiểm tra tồn kho
        $stock = Inventory::where('product_id', $product->id)->sum('quantity');
        if ($stock < $quantity) {
            return back()->withErrors(['quantity' => 'Số lượng yêu cầu vượt quá tồn kho. Còn lại: ' . $stock . ' sản phẩm']);
        }

        // Kiểm tra sản phẩm đã trong giỏ chưa
        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Cập nhật số lượng
            $newQuantity = $cartItem->quantity + $quantity;
            if ($stock < $newQuantity) {
                return back()->withErrors(['quantity' => 'Tổng số lượng vượt quá tồn kho']);
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Thêm mới
            CartItem::create([
                'user_id' => $userId,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return back()->with('success', "Đã thêm {$quantity} {$product->name} vào giỏ hàng!");
    }

    /**
     * Cập nhật số lượng
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorize('update', $cartItem);

        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        $quantity = $request->quantity;
        $stock = Inventory::where('product_id', $cartItem->product_id)->sum('quantity');

        if ($stock < $quantity) {
            return back()->withErrors(['quantity' => 'Số lượng vượt quá tồn kho']);
        }

        $cartItem->update(['quantity' => $quantity]);

        return back()->with('success', 'Cập nhật số lượng thành công!');
    }

    /**
     * Xóa khỏi giỏ hàng
     */
    public function remove(CartItem $cartItem)
    {
        $this->authorize('delete', $cartItem);
        
        $productName = $cartItem->product->name ?? 'Sản phẩm';
        $cartItem->delete();

        return back()->with('success', "Đã xóa $productName khỏi giỏ hàng!");
    }

    /**
     * Thanh toán
     */
    public function checkout(Request $request)
    {
        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();

        if ($cartItems->isEmpty()) {
            return back()->withErrors(['cart' => 'Giỏ hàng trống!']);
        }

        // Kiểm tra địa chỉ
        $address = $user->addresses()->first();
        if (!$address) {
            return redirect()->route('customer.address.create')
                ->with('error', 'Vui lòng thêm địa chỉ giao hàng trước khi đặt hàng!');
        }

        try {
            DB::beginTransaction();

            // Tính tổng tiền
            $total = $cartItems->sum(function ($item) {
                return ($item->product->price ?? 0) * $item->quantity;
            });

            // Kiểm tra tồn kho một lần nữa
            foreach ($cartItems as $item) {
                $stock = Inventory::where('product_id', $item->product_id)->sum('quantity');
                if ($stock < $item->quantity) {
                    throw new \Exception("Sản phẩm {$item->product->name} không đủ số lượng!");
                }
            }

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $address->id,
                'total_price' => $total,
                'status' => 'pending',
                'order_date' => now(),
            ]);

            // Thêm items vào đơn hàng
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);

                // Trừ tồn kho
                $inventory = Inventory::where('product_id', $cartItem->product_id)
                    ->orderBy('quantity', 'desc')
                    ->first();
                
                if ($inventory) {
                    $inventory->decrement('quantity', $cartItem->quantity);
                }
            }

            // Tạo payment
            Payment::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'amount' => $total,
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
            ]);

            // Xóa items khỏi giỏ hàng
            CartItem::where('user_id', $user->id)->delete();

            DB::commit();

            return redirect()->route('customer.order.show', $order->id)
                ->with('success', 'Đặt hàng thành công! Vui lòng chuyển khoản để hoàn tất thanh toán.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['checkout' => $e->getMessage()]);
        }
    }
}
