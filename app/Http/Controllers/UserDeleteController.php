<?php

namespace App\Http\Controllers;

use App\Models\State;
use Carbon\Carbon;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;

class UserDeleteController extends Controller
{
    public function delete(User $user)
    {
        $user->deleted_at = Carbon::now()->format('Y-m-d');
        if ($user->image !== null) {
            $fileName = 'images/' . 'users' . '/' . basename($user->image);
            if (Storage::disk('public')->exists($fileName)) {
                Storage::disk('public')->delete($fileName);
            }
        }
        $user->image = null;
        $state = State::where('name', State::DISABLED)->first();
        $user->state_id = $state->id;
        $user->save();
        $data = [
            'message' => 'Usuario eliminado',
            'id' => $user->id,
            'code' => 200,
        ];
        return response()->json($data);
    }
}
