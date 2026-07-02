<?php

namespace App\Http\Controllers;

use App\Models\Collector;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CollectorUploadDocumentRequest;

class CollectorUploadDocumentController extends Controller
{

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

    public function uploadDocument(CollectorUploadDocumentRequest $collectorUploadDocumentRequest, Collector $collector ): JsonResponse
    {
        DB::beginTransaction();
        try {
            if($collectorUploadDocumentRequest->identification_document){
                $identification = $this->saveDocumentStorage($collectorUploadDocumentRequest->identification_document, 'collectors', 'identification');
                $collector->identification_document = $identification;
            }
            if($collectorUploadDocumentRequest->driving_license_document){
                $driving = $this->saveDocumentStorage($collectorUploadDocumentRequest->driving_license_document, 'collectors', 'driving_license');
                $collector->driving_license_document = $driving;
            }
            $collector->save();
            DB::commit();
            $data = [
                'message' => 'Documento cargado correctamente',
                'data' => [
                    'url' => $collectorUploadDocumentRequest->identification_document ? $identification : $driving,
                ],
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
