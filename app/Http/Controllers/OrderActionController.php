<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderActionController extends Controller
{
    // Xác nhận đã nhận hàng
    public function confirmReceived(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        if ($order->status !== 'completed') {
            return back()->with('error', 'Đơn hàng chưa giao thành công!');
        }

        $order->update(['status' => 'finished']);

        return back()->with('success', 'Cảm ơn bạn đã xác nhận nhận hàng!');
    }

    // Form yêu cầu trả hàng
    public function returnRequestForm(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        if (! in_array($order->status, ['completed', 'finished'])) {
            return back()->with('error', 'Chỉ có thể yêu cầu đổi trả khi đơn hàng đã giao thành công.');
        }

        // Kiểm tra xem đã có yêu cầu đang xử lý chưa
        if (ReturnRequest::where('order_id', $order->id)->whereIn('status', ['pending', 'approved', 'completed'])->exists()) {
            return back()->with('error', 'Bạn đã gửi yêu cầu trả hàng cho đơn này rồi.');
        }

        return view('storefront.order_return', compact('order'));
    }

    // Xử lý lưu trả hàng
    public function returnRequestStore(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        if (! in_array($order->status, ['completed', 'finished'])) {
            return back()->with('error', 'Chỉ có thể yêu cầu đổi trả khi đơn hàng đã giao thành công.');
        }

        if (ReturnRequest::where('order_id', $order->id)->whereIn('status', ['pending', 'approved', 'completed'])->exists()) {
            return back()->with('error', 'Đơn hàng này đã có yêu cầu đổi trả đang được xử lý.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
            'note' => 'nullable|string|max:1000',
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:5120',
            'bank_name' => 'required|string|max:100',
            'bank_account_holder' => 'required|string|max:100',
            'bank_account_number' => 'required|string|min:6|max:40',
        ], [
            'reason.required' => 'Vui lòng chọn lý do trả hàng.',
            'bank_name.required' => 'Vui lòng nhập tên ngân hàng nhận tiền hoàn.',
            'bank_account_holder.required' => 'Vui lòng nhập tên chủ tài khoản ngân hàng.',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản ngân hàng.',
            'image.file' => 'Tệp tải lên không hợp lệ.',
            'image.image' => 'Tệp tải lên phải là hình ảnh hợp lệ.',
            'image.mimes' => 'Định dạng hình ảnh phải là JPEG, PNG, JPG hoặc WebP.',
            'image.max' => 'Dung lượng ảnh tối đa cho phép là 5MB.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            if (! $file->isValid()) {
                return back()->withInput()->with('error', 'Tệp ảnh tải lên bị lỗi hoặc bị gián đoạn. Vui lòng thử lại.');
            }

            $targetDir = public_path('uploads/returns');
            File::ensureDirectoryExists($targetDir);

            // Xác thực và chuẩn hóa phần mở rộng an toàn dựa trên MIME type thực tế
            $detectedExt = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
            if (! in_array($detectedExt, ['jpeg', 'jpg', 'png', 'webp'])) {
                $detectedExt = 'jpg';
            }

            $fileName = 'return_'.date('Ymd_His').'_'.Str::random(16).'.'.$detectedExt;

            try {
                $file->move($targetDir, $fileName);
                $imagePath = 'uploads/returns/'.$fileName;
            } catch (\Throwable $e) {
                Log::error('Upload return evidence failed: '.$e->getMessage());

                return back()->withInput()->with('error', 'Không thể lưu tệp ảnh bằng chứng lên máy chủ. Vui lòng thử lại.');
            }
        }

        try {
            DB::transaction(function () use ($validated, $order, $imagePath) {
                // Chuẩn hóa tên chủ tài khoản và số tài khoản
                $cleanHolder = mb_strtoupper(trim($validated['bank_account_holder']));
                $cleanNumber = preg_replace('/[^0-9A-Za-z]/', '', $validated['bank_account_number']);

                ReturnRequest::create([
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'reason' => $validated['reason'],
                    'note' => $validated['note'] ?? null,
                    'image' => $imagePath,
                    'status' => 'pending',
                    'bank_name' => trim($validated['bank_name']),
                    'bank_account_holder' => $cleanHolder,
                    'bank_account_number' => $cleanNumber,
                    'refund_status' => 'pending',
                    'refund_amount' => (int) $order->total,
                ]);

                $order->update(['status' => 'returning']);
            });
        } catch (\Throwable $e) {
            // Dọn dẹp file vừa tải lên nếu giao dịch cơ sở dữ liệu thất bại
            if ($imagePath && File::exists(public_path($imagePath))) {
                File::delete(public_path($imagePath));
            }
            Log::error('Create return request transaction failed: '.$e->getMessage());

            return back()->withInput()->with('error', 'Có lỗi xảy ra khi tạo yêu cầu hoàn hàng. Vui lòng thử lại.');
        }

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Đã gửi yêu cầu trả hàng / hoàn tiền thành công. LensStore sẽ liên hệ và xử lý trong thời gian sớm nhất!');
    }

    // Form đánh giá
    public function reviewForm(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        if (! in_array($order->status, ['completed', 'finished'])) {
            return back()->with('error', 'Bạn chỉ có thể đánh giá khi đơn hàng đã hoàn tất.');
        }

        $order->load('items.product');

        return view('storefront.order_review', compact('order'));
    }

    // Xử lý lưu đánh giá
    public function reviewStore(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'reviews' => 'required|array',
            'reviews.*.product_id' => 'required|exists:products,id',
            'reviews.*.rating' => 'required|integer|min:1|max:5',
            'reviews.*.comment' => 'nullable|string',
        ]);

        foreach ($request->reviews as $rev) {
            // Check if reviewed
            if (! Review::where('order_id', $order->id)->where('product_id', $rev['product_id'])->exists()) {
                Review::create([
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'product_id' => $rev['product_id'],
                    'rating' => $rev['rating'],
                    'comment' => $rev['comment'],
                ]);
            }
        }

        return redirect()->route('orders.show', $order->id)->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}
