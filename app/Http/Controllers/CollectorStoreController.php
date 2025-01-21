<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use App\Models\State;
use App\Models\Collector;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CollectorResource;
use App\Http\Requests\CollectorStoreRequest;

class CollectorStoreController extends Controller
{

    public function saveStorage($image, $route): string
    {
        $imageName = $image->getClientOriginalName();
        $nameRoute = 'images/'. $route .'/';
        $image->storeAs($nameRoute, $imageName, 'public');
        $url = Storage::disk('public')->url($nameRoute . $imageName);
        return $url;
    }

    public function createUser($request) : User
    {
        $user = New User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->identification = $request->identification;
        $user->type_identification_id = $request->type_identification;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);;
        $image = $this->saveStorage($request->images, 'collectors');
        $user->state_id = State::where('name', State::ENABLED)->value('id');
        $user->rol_id = Rol::where('name', Rol::RECYCLER)->value('id');
        $user->image = $image;
        $user->save();

        return $user;
    }

    public function create(CollectorStoreRequest $collectorStoreRequest): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $this->createUser($collectorStoreRequest);
            $collector = new Collector();
            $collector->user_id = $user->id;
            $collector->state_id = State::where('name', State::ENABLED)->value('id');
            $collector->save();
            DB::commit();
            $collectorResource = CollectorResource::make($collector);
            $data = [
                'message' => 'Usuario creado correctamente',
                'user' => $collectorResource,
                'code' => 200,
            ];
            return response()->json($data);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException  $exception) {
            DB::rollBack();
            $data = [
                'message' => $exception->getMessage(),
                'code' => 400,
            ];

            return response()->json($data);
        }
    }
}
