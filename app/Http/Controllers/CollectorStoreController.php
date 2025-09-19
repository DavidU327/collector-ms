<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use App\Models\State;
use App\Models\Collector;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CollectorResource;
use App\Http\Requests\CollectorStoreRequest;
use App\Http\Requests\CollectorStoreBackOfficeRequest;

class CollectorStoreController extends Controller
{

    public function saveStorage($image, $route): string
    {
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

    public function saveDocumentStorage($image, $route, $document): string
    {
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
            $collector->state_id = State::where('name', State::PENDING_USER)->value('id');
            $identification = $this->saveDocumentStorage($collectorStoreRequest->identification_document, 'collectors', 'identification');
            $collector->identification_document = $identification;
            $driving = $this->saveDocumentStorage($collectorStoreRequest->driving_license_document, 'collectors', 'driving_license');
            $collector->driving_license_document = $driving;
            $collector->save();
            DB::commit();
            $collectorResource = CollectorResource::make($collector);
            $data = [
                'message' => 'Recolector creado correctamente',
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

    public function createUserBackOffice($request) : User
    {
        $user = New User();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->identification = $request->identification;
        $user->type_identification_id = $request->type_identification;
        $user->email = $request->email;
        $user->password = 'change';
        $image = $this->saveStorage($request->images, 'collectors');
        $user->state_id = State::where('name', State::ENABLED)->value('id');
        $user->rol_id = Rol::where('name', Rol::RECYCLER)->value('id');
        $user->image = $image;
        $user->save();

        return $user;
    }

    public function createBackOffice(CollectorStoreBackOfficeRequest $collectorStoreBackOfficeRequest): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $this->createUserBackOffice($collectorStoreBackOfficeRequest);
            $collector = new Collector();
            $collector->user_id = $user->id;
            if($collectorStoreBackOfficeRequest->identification_document){
                $identification = $this->saveDocumentStorage($collectorStoreBackOfficeRequest->identification_document, 'collectors', 'identification');
                $collector->identification_document = $identification;
            }
            if($collectorStoreBackOfficeRequest->driving_license_document){
                $driving = $this->saveDocumentStorage($collectorStoreBackOfficeRequest->driving_license_document, 'collectors', 'driving_license');
                $collector->driving_license_document = $driving;
            }
            if($collectorStoreBackOfficeRequest->identification_document && $collectorStoreBackOfficeRequest->driving_license_document){
                $collector->state_id = State::where('name', State::ENABLED)->value('id');
            }else{
                $collector->state_id = State::where('name', State::PENDING_USER)->value('id');
            }
            $collector->save();
            DB::commit();
            $collectorResource = CollectorResource::make($collector);
            $data = [
                'message' => 'Se ha creado el recolector correctamente',
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
