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
        if ($collector->identification_document !== null){
            $parsedUrl = parse_url($collector->identification_document, PHP_URL_PATH);
            $container = '/' . config('filesystems.disks.azure.container') . '/';
            $relativePath = ltrim(str_replace($container, '', $parsedUrl), '/');
            if (Storage::disk('azure')->exists($relativePath)) {
                Storage::disk('azure')->delete($relativePath);
            }
            $collector->identification_document = null;
        }
        if ($collector->driving_license_document !== null){
            $parsedUrl = parse_url($collector->driving_license_document, PHP_URL_PATH);
            $container = '/' . config('filesystems.disks.azure.container') . '/';
            $relativePath = ltrim(str_replace($container, '', $parsedUrl), '/');
            if (Storage::disk('azure')->exists($relativePath)) {
                Storage::disk('azure')->delete($relativePath);
            }
            $collector->driving_license_document = null;
        }
        $user = User::find($collector->user_id);
        if ($user->image !== null) {
            $parsedUrl = parse_url($user->image, PHP_URL_PATH);
            $container = '/' . config('filesystems.disks.azure.container') . '/';
            $relativePath = ltrim(str_replace($container, '', $parsedUrl), '/');
            if (Storage::disk('azure')->exists($relativePath)) {
                Storage::disk('azure')->delete($relativePath);
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
