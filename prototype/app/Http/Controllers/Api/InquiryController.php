<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class InquiryController extends Controller
{
    public function index(Profile $profile): JsonResponse
    {
        Gate::authorize('update', $profile);

        $inquiries = $profile->inquiries()
            ->limit(50)
            ->get(['id', 'name', 'email', 'service', 'message', 'created_at']);

        return response()->json(['data' => $inquiries]);
    }
}
