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
    /**
     * @OA\Post(
     *     path="/api/v1/upload",
     *     summary="Upload HTML File for Accessibility Check",
     *     description="Uploads an HTML file to be checked for accessibility compliance. Returns compliance score and issues.",
     *     tags={"File Upload"},
     *     @OA\RequestBody(
     *         required=true,
     *         content={
     *             @OA\MediaType(
     *                 mediaType="multipart/form-data",
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="file", type="string", format="binary", description="The HTML file to upload")
     *                 )
     *             )
     *         }
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully uploaded and analyzed the file",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="compliance_score",
     *                     type="integer",
     *                     example=0
     *                 ),
     *                 @OA\Property(
     *                     property="issues",
     *                     type="object",
     *                     @OA\Property(
     *                         property="text_alternatives",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="message", type="string", example="img element missing alt attribute."),
     *                             @OA\Property(property="element", type="string", example="&lt;img src=&quot;image.jpg&quot;&gt;"),
     *                             @OA\Property(property="suggested_fix", type="string", example="Add an alt attribute to your img element.")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="adaptable",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="message", type="string", example="Header nesting - header following <h1> is incorrect."),
     *                             @OA\Property(property="element", type="string", example="&lt;h3&gt;Subheading&lt;/h3&gt;"),
     *                             @OA\Property(property="suggested_fix", type="string", example="Modify the header levels. The <h1> heading should be followed by <h2>, not <h3>.")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="navigable",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="message", type="string", example="Anchor contains no text."),
     *                             @OA\Property(property="element", type="string", example="&lt;a href=&quot;https://goal.com&quot;&gt;&lt;/a&gt;"),
     *                             @OA\Property(property="suggested_fix", type="string", example="Add text to the element or the title attribute of the a element or, if an image is used within the anchor, add Alt text to the image.")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="distinguishable",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="message", type="string", example="The contrast between the colour of text and its background for the element is not sufficient to meet WCAG2.0 Level."),
     *                             @OA\Property(property="element", type="string", example="&lt;div style=&quot;color: #D3D3D3; background-color: #A9A9A9;&quot;&gt;This is a low-contrast text.&lt;/div&gt;"),
     *                             @OA\Property(property="suggested_fix", type="string", example="Use a colour contrast evaluator to determine if text and background colours provide a contrast ratio of 4.5:1 for standard text, or 3:1 for larger text. Change colour codes to produce sufficient contrast")
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Success")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request, invalid file",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid file format")
     *         )
     *     )
     * )
     * @throws \Exception
     */
    public function __invoke(UploadHtmlRequest $request): JsonResponse
    {
        $fileContent = file_get_contents($request->file('file')->getRealPath());

        $analysis = $this->accessibilityAnalyzerService->analyze($fileContent);

        return successResponse($analysis);
    }

}
