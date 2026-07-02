<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Collector;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CollectorResource;
use App\Http\Requests\CollectorUpdateBackOfficeRequest;

class CollectorUpdateController extends Controller
{

    public function saveStorage($image, $route, $oldFileUrl): string
    {
        if ($oldFileUrl) {
            $parsedUrl = parse_url($oldFileUrl, PHP_URL_PATH);
            $container = '/' . config('filesystems.disks.azure.container') . '/';
            $relativePath = ltrim(str_replace($container, '', $parsedUrl), '/');
            if (Storage::disk('azure')->exists($relativePath)) {
                Storage::disk('azure')->delete($relativePath);
            }
        }

        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $image->getClientOriginalExtension();
        $safeName = Str::slug($originalName, '_');
        $imageName = $safeName . '.' . $extension;
        $nameRoute = 'images/' . $route . '/';
        Storage::disk('azure')->putFileAs($nameRoute, $image, $imageName);
        $url = rtrim(config('filesystems.disks.azure.url'), '/') . '/' .
            config('filesystems.disks.azure.container') . '/' .
            $nameRoute . $imageName;
        return $url;
    }

    public function saveDocumentStorage($image, $route, $document, $oldFileUrl = null): string
    {
        if ($oldFileUrl) {
            $parsedUrl = parse_url($oldFileUrl, PHP_URL_PATH);
            $container = '/' . config('filesystems.disks.azure.container') . '/';
            $relativePath = ltrim(str_replace($container, '', $parsedUrl), '/');
            if (Storage::disk('azure')->exists($relativePath)) {
                Storage::disk('azure')->delete($relativePath);
            }
        }

        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $image->getClientOriginalExtension();
        $safeName = Str::slug($originalName, '_');
        $documentName = $safeName . '.' . $extension;
        $nameRoute = 'documents/'. $route. '-' . $document .'/';
        Storage::disk('azure')->putFileAs($nameRoute, $image, $documentName);
        $url = rtrim(config('filesystems.disks.azure.url'), '/') . '/' .
            config('filesystems.disks.azure.container') . '/' .
            $nameRoute . $documentName;
        return $url;
    }

    public function updateUserBackOffice($request, $user) : User
    {
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('identification')) {
            $user->identification = $request->identification;
        }
        if ($request->has('type_identification')) {
            $user->type_identification_id = $request->type_identification;
        }
        if ($request->hasFile('images')) {
            $image = $this->saveStorage($request->images, 'collectors', $user->image);
            $user->image = $image;
        }
        $user->save();

        return $user;
    }

    public function updateBackOffice(CollectorUpdateBackOfficeRequest $collectorUpdateBackOfficeRequest, Collector $collector): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($collector->user_id);
            $this->updateUserBackOffice($collectorUpdateBackOfficeRequest, $user);
            if($collectorUpdateBackOfficeRequest->hasFile('identification_document')){
                $identification = $this->saveDocumentStorage(
                    $collectorUpdateBackOfficeRequest->identification_document,
                    'collectors',
                    'identification',
                    $collector->identification_document,
                );
                $collector->identification_document = $identification;
            }
            if($collectorUpdateBackOfficeRequest->hasFile('driving_license_document')){
                $driving = $this->saveDocumentStorage(
                    $collectorUpdateBackOfficeRequest->driving_license_document,
                    'collectors',
                    'driving_license',
                    $collector->driving_license_document,
                );
                $collector->driving_license_document = $driving;
            }
            $collector->save();
            DB::commit();
            $collectorResource = CollectorResource::make($collector);
            $data = [
                'message' => 'Se ha actualizado el recolector correctamente',
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
