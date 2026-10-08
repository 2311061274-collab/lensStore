<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        if (empty($data['is_default'])) {
            $data['is_default'] = false;
        }

        if ($data['is_default']) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        auth()->user()->addresses()->create($data);

        return back()->with('success', 'Thêm địa chỉ thành công.');
    }

    public function update(Request $request, \App\Models\UserAddress $address)
    {
        if ($address->user_id !== auth()->id()) abort(403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        if (empty($data['is_default'])) {
            $data['is_default'] = false;
        }

        if ($data['is_default'] && !$address->is_default) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        $address->update($data);

        return back()->with('success', 'Cập nhật địa chỉ thành công.');
    }

    public function destroy(\App\Models\UserAddress $address)
    {
        if ($address->user_id !== auth()->id()) abort(403);
        $address->delete();
        return back()->with('success', 'Xóa địa chỉ thành công.');
    }
}
