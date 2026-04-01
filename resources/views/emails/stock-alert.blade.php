<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
        }

        .container {
            max-w-[700px] margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #1e293b;
            color: #ffffff;
            padding: 15px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
            font-size: 18px;
            text-transform: uppercase;
        }

        .section-title {
            margin-top: 25px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 16px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .table th,
        .table td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
        }

        .table th {
            background-color: #f8fafc;
            color: #475569;
        }

        .text-red {
            color: #dc2626;
            font-weight: bold;
        }

        .text-amber {
            color: #d97706;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            BÁO CÁO TỔNG HỢP CẢNH BÁO WMS
        </div>

        <p style="margin-top: 20px;">Xin chào Quản lý,</p>
        <p>Hệ thống tự động ghi nhận các vấn đề về Tồn kho và Hạn sử dụng như sau. Vui lòng kiểm tra và lên kế hoạch xử
            lý:</p>

        <!-- BẢNG 1: CẢNH BÁO TỒN KHO -->
        @if (count($alertData['low_stock']) > 0)
            <div class="section-title" style="color: #991b1b;">
                🚨 1. Danh sách Cần nhập hàng (Dưới ngưỡng an toàn)
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nhà Kho</th>
                        <th>Sản Phẩm</th>
                        <th>Tồn Kho</th>
                        <th>Ngưỡng</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alertData['low_stock'] as $item)
                        <tr>
                            <td><strong>{{ $item['warehouse_name'] }}</strong></td>
                            <td>{{ $item['product_name'] }} <br><span
                                    style="color:#64748b; font-size:11px;">SP-{{ $item['product_id'] }}</span></td>
                            <td class="text-red">{{ $item['current_stock'] }}</td>
                            <td>
                                <= {{ $item['threshold'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- BẢNG 2: CẢNH BÁO HẠN SỬ DỤNG -->
        @if (count($alertData['expiring']) > 0)
            <div class="section-title" style="color: #92400e;">
                ⚠️ 2. Danh sách Lô hàng Cận Date / Hết hạn
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Sản Phẩm</th>
                        <th>Mã Lô (Batch)</th>
                        <th>Hạn Sử Dụng</th>
                        <th>Tình Trạng</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alertData['expiring'] as $item)
                        <tr>
                            <td>{{ $item['product_name'] }} <br><span
                                    style="color:#64748b; font-size:11px;">SP-{{ $item['product_id'] }}</span></td>
                            <td><strong>{{ $item['batch_code'] }}</strong></td>
                            <td>{{ $item['expiry_date'] }}</td>
                            <td>
                                @if ($item['days_left'] < 0)
                                    <span class="text-red">Đã quá hạn {{ abs($item['days_left']) }} ngày!</span>
                                @elseif($item['days_left'] == 0)
                                    <span class="text-red">Hết hạn hôm nay!</span>
                                @else
                                    <span class="text-amber">Còn {{ $item['days_left'] }} ngày</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="footer">
            <p>Email này được tạo tự động từ Hệ thống Quản lý Kho (WMS) lúc
                {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}.</p>
            <p>Vui lòng không phản hồi lại địa chỉ email này.</p>
        </div>
    </div>
</body>

</html>
