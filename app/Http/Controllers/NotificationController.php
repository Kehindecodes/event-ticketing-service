<?php

namespace App\Http\Controllers;

use App\Data\MagicLinkData;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            "user_id" => ["required", "string"],
            "user_name" => ["required", "string"],
            "link" => ["required", "string"],
            "expires_in_minutes" => ["required", "integer"],
        ]);

        $data = new MagicLinkData(
            userName: $validated["user_name"],
            link: $validated["link"],
            expiresInMinutes: $validated["expires_in_minutes"],
        );
        // check user
        $user = User::find($validated["user_id"]);
        if (!$user) {
            return response()->json(["status" => "user_not_found"], 404);
        }
        $this->notificationService->send($data, $validated["user_id"]);

        return response()->json(["status" => "queued"], 200);
    }
}
