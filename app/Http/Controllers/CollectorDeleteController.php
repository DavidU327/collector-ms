<?php

namespace App\Http\Controllers;

use App\Models\Collector;
use App\Models\State;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class CollectorDeleteController extends Controller
{
    public function delete(Collector $collector)
    {
        $collector->deleted_at = Carbon::now()->format('Y-m-d');
        $state = State::where('name', State::DISABLED)->first();
        $collector->state_id = $state->id;
        $user = User::find($collector->user_id);
        if ($user->image !== null) {
            $fileName = 'images/' . 'users' . '/' . basename($user->image);
            if (Storage::disk('public')->exists($fileName)) {
                Storage::disk('public')->delete($fileName);
            }
        }
        $user->image = null;
        $user->state_id = $state->id;
        $user->deleted_at = Carbon::now()->format('Y-m-d');
        $user->save();
        $collector->save();
        $data = [
            'message' => 'Recolector eliminado',
            'id' => $collector->id,
            'code' => 200,
        ];
        return response()->json($data);
    }
}
