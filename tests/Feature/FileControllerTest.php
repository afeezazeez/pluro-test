<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FileControllerTest extends TestCase
{

    public function test_that_it_can_upload_html_file_and_return_analysis_results_with_issues(): void
    {
        $htmlContent = '<html><body><img src="image.jpg"><h1>Title</h1><div style="color: #aaa; background-color: #ccc;">Low contrast text</div></body></html>';

        // Create a fake HTML file with known content
        $htmlFile = UploadedFile::fake()->create('test_with_issues.html', 100, 'text/html');

        // Write the HTML content into the fake file
        file_put_contents($htmlFile->getRealPath(), $htmlContent);

        $response = $this->json('POST', route('file.upload'), [
            'file' => $htmlFile
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            "success" => true,
            "message" => "Success",
            "data" => [
                "compliance_score" => 50,
                "issues" => [
                    "text_alternatives" => [
                        [
                            "message" => "img element missing alt attribute.",
                            "element" => '<img src="image.jpg">',
                            "suggested_fix" => "Add an alt attribute to your img element."
                        ]
                    ],
                    "adaptable" => [],
                    "navigable" => [],
                    "distinguishable" => [
                        [
                            "message" => "The contrast between the colour of text and its background for the element is not sufficient to meet WCAG2.0 Level.",
                            "element" => '<div style="color: #aaa; background-color: #ccc;">Low contrast text</div>',
                            "suggested_fix" => "Use a colour contrast evaluator to determine if text and background colours provide a contrast ratio of 4.5:1 for standard text, or 3:1 for larger text. Change colour codes to produce sufficient contrast"
                        ]
                    ]
                ]
            ]
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'compliance_score',
                'issues' => [
                    'text_alternatives',
                    'adaptable',
                    'navigable',
                    'distinguishable',
                ]
            ]
        ]);

    }

    public function test_that_it_returns_error_for_non_html_file()
    {
        $response = $this->json('POST', route('file.upload'), [
            'file' => UploadedFile::fake()->create('test.txt', 100, 'text/plain')
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'The file must be of type: .html.',
        ]);

        $response->assertJsonFragment([
            'errors' => [
                'file' => [
                    'The file must be of type: .html.'
                ]
            ]
        ]);
    }

    public function test_that_it_returns_error_for_missing_file()
    {
        $response = $this->json('POST', route('file.upload'), []);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'The HTML file is required.',
        ]);

        $response->assertJsonFragment([
            'errors' => [
                'file' => [
                    'The HTML file is required.'
                ]
            ]
        ]);
    }

    public function test_that_it_returns_file_size_limit_error()
    {
        $response = $this->json('POST', route('file.upload'), [
            'file' => UploadedFile::fake()->create('largefile.html', 5000) // 5MB file
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'The uploaded file size must not exceed 1MB.',
        ]);

        $response->assertJsonFragment([
            'errors' => [
                'file' => [
                    'The uploaded file size must not exceed 1MB.'
                ]
            ]
        ]);
    }
}
