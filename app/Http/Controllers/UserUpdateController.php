<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UserUpdateRequest;


class UserUpdateController extends Controller
{

    public function saveStorage($image, $imageOld, $route): string
    {
        if ($imageOld !== null) {
            $oldImagePath = 'images/' . $route . '/' . basename($imageOld);
            if (Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }
        }
        $imageName = $image->getClientOriginalName();
        $nameRoute = 'images/'. $route .'/';
        $image->storeAs($nameRoute, $imageName, 'public');
        $url = Storage::disk('public')->url($nameRoute . $imageName);
        return $url;
    }

    public function update(UserUpdateRequest $userUpdateRequest, User $user): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user->name = $userUpdateRequest->name;
            $user->phone = $userUpdateRequest->phone;
            $image = $this->saveStorage($userUpdateRequest->images, $user->image, 'users');
            $user->image = $image;
            $user->save();
            DB::commit();
            $userResource = UserResource::make($user);
            $data = [
                'message' => 'Usuario actualizado correctamente',
                'user' => $userResource,
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
