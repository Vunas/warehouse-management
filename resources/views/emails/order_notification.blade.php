<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo Đơn Hàng</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); border: 1px solid #e5e7eb; }
        .header { background-color: #ef4444; padding: 20px; text-align: center; color: #ffffff; }
        .header h1 { margin: 0; font-size: 20px; letter-spacing: 0.5px; text-transform: uppercase; }
        .content { padding: 30px; color: #374151; line-height: 1.6; font-size: 15px; }
        .alert-box { background-color: #fee2e2; border-left: 4px solid #ef4444; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-box strong { color: #b91c1c; }
        .order-details { background-color: #f9fafb; border: 1px solid #f3f4f6; padding: 20px; border-radius: 6px; margin-top: 20px; }
        .order-details h3 { margin-top: 0; margin-bottom: 15px; color: #111827; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; }
        .footer { background-color: #f9fafb; padding: 20px; text-align: center; font-size: 13px; color: #6b7280; border-top: 1px solid #e5e7eb; }
        .btn { display: inline-block; background-color: #4f46e5; color: #ffffff; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thông báo Đơn Đặt Hàng</h1>
        </div>
        <div class="content">
            <p>Kính chào <strong>{{ $customer->name ?? 'Quý khách' }}</strong>,</p>
            
            <p>Chúng tôi rất tiếc phải thông báo rằng Đơn đặt hàng <strong>#ORD-{{ $order->id }}</strong> của quý khách đã không thể được xử lý tiếp tục.</p>
            
            <div class="alert-box">
                <strong>Lý do từ chối:</strong><br>
                {{ $reason }}
            </div>

            <p><em>* Lưu ý: Toàn bộ sản phẩm trong đơn hàng này đã được hệ thống tự động hoàn trả lại vào <strong>Giỏ hàng</strong> của bạn. Quý khách có thể truy cập lại giỏ hàng để thay đổi số lượng hoặc chọn sản phẩm thay thế.</em></p>

            <div class="order-details">
                <h3>Chi tiết đơn hàng bị từ chối:</h3>
                <ul>
                    @foreach($order->items as $item)
                        <li>{{ $item->product->name }} - Số lượng: {{ $item->quantity }}</li>
                    @endforeach
                </ul>
                <p><strong>Tổng giá trị:</strong> {{ number_format($order->total_price, 0, ',', '.') }} đ</p>
            </div>

            {{-- <div style="text-align: center;">
                <a href="{{ config('app.url') }}/cart" class="btn">Đến Giỏ hàng của bạn</a>
            </div> --}}
        </div>
        <div class="footer">
            Đây là email tự động từ Hệ thống Quản lý Bán Hàng & WMS. Vui lòng không trả lời email này.
        </div>
    </div>
</body>
</html>