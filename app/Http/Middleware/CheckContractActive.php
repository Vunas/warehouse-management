<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Contract;

class CheckContractActive
{
    public function handle(Request $request, Closure $next)
    {
        // Lấy contract_id từ request (thường gửi lên khi tạo phiếu)
        $contractId = $request->input('contract_id');

        if ($contractId) {
            $contract = Contract::find($contractId);

            if (!$contract) {
                return back()->withErrors(['contract_id' => 'Hợp đồng không tồn tại.']);
            }

            if ($contract->status !== 'active') {
                return back()->withErrors(['contract_id' => 'Hợp đồng này không hoạt động (Hết hạn hoặc bị đình chỉ).']);
            }
        }

        return $next($request);
    }
}