<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Http\Requests\Certificate\StoreCertificateRequest;
use App\Http\Requests\Certificate\UpdateCertificateRequest;
use App\Services\CertificateGeneratorService;
use App\Http\Resources\CertificateResource;
use Illuminate\Http\Response;
use Throwable;

class CertificateController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateGeneratorService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    public function index()
    {
        try {
            $certificates = Certificate::with(['user', 'course'])->get();

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'data' => CertificateResource::collection($certificates),
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to retrieve certificates',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function store(StoreCertificateRequest $request)
    {
        try {
            $validated = $request->validated();

            $certificate = Certificate::create([
                'user_id' => $validated['user_id'],
                'course_id' => $validated['course_id'],
                'issue_date' => $validated['issue_date'] ?? now(),
                'expiry_date' => $validated['expiry_date'] ?? null,
            ]);

            if ($request->hasFile('template')) {
                $certificate->setTemplate($request->file('template'));
            }

            // Generate certificate image
            $userData = [
                'name' => $certificate->user->name,
                'course_name' => $certificate->course->title,
                'issue_date' => $certificate->issue_date,
            ];

            $templatePath = $certificate->getTemplate();
            $generatedImagePath = $this->certificateService->generateCertificateImage($templatePath, $userData);

            // Save generated image
            $certificate->setGeneratedCertificate(public_path($generatedImagePath));

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_CREATED,
                'message' => 'Certificate created successfully',
                'data' => new CertificateResource($certificate->refresh()),
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to create certificate',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function show(Certificate $certificate)
    {
        try {
            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'data' => new CertificateResource($certificate->load(['user', 'course'])),
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to retrieve certificate',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function update(UpdateCertificateRequest $request, Certificate $certificate)
    {
        try {
            $validated = $request->validated();

            $certificate->update([
                'user_id' => $validated['user_id'] ?? $certificate->user_id,
                'course_id' => $validated['course_id'] ?? $certificate->course_id,
                'issue_date' => $validated['issue_date'] ?? $certificate->issue_date,
                'expiry_date' => $validated['expiry_date'] ?? $certificate->expiry_date,
            ]);

            if ($request->hasFile('template')) {
                $certificate->setTemplate($request->file('template'));
            }

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'message' => 'Certificate updated successfully',
                'data' => new CertificateResource($certificate),
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to update certificate',
                'error' => $th->getMessage(),
            ]);
        }
    }

    public function destroy(Certificate $certificate)
    {
        try {
            $certificate->delete();

            return response()->json([
                'success' => true,
                'code' => Response::HTTP_OK,
                'message' => 'Certificate deleted successfully',
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Failed to delete certificate',
                'error' => $th->getMessage(),
            ]);
        }
    }
}
