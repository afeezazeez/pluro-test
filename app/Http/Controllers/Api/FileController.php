<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadHtmlRequest;
use App\Services\AccessibilityAnalyzerService;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    protected AccessibilityAnalyzerService $accessibilityAnalyzerService;

    public function __construct(AccessibilityAnalyzerService $accessibilityAnalyzerService)
    {
        $this->accessibilityAnalyzerService = $accessibilityAnalyzerService;
    }


    /**
     * Upload and analyze HTML file and return results.
     * @param UploadHtmlRequest $request
     * @return JsonResponse
     */
    public function __invoke(UploadHtmlRequest $request): JsonResponse
    {
        $fileContent = file_get_contents($request->file('file')->getRealPath());

        $analysis = $this->accessibilityAnalyzerService->analyze($fileContent);

        return successResponse($analysis);
    }
}
