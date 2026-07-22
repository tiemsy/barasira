<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Resume;
use Illuminate\Http\Request;

class ProfileCredentialController extends Controller
{
    public function storeEducation(Request $request)
    {
        $this->resume($request)->educations()->create($this->educationData($request));

        return back()->with('success', __('profile.credentials.education_saved'));
    }

    public function updateEducation(Request $request, Education $education)
    {
        $this->authorizeItem($request, $education->resume_id);
        $education->update($this->educationData($request));

        return back()->with('success', __('profile.credentials.education_saved'));
    }

    public function destroyEducation(Request $request, Education $education)
    {
        $this->authorizeItem($request, $education->resume_id);
        $education->delete();

        return back()->with('success', __('profile.credentials.education_deleted'));
    }

    public function storeExperience(Request $request)
    {
        $this->resume($request)->experiences()->create($this->experienceData($request));

        return back()->with('success', __('profile.credentials.experience_saved'));
    }

    public function updateExperience(Request $request, Experience $experience)
    {
        $this->authorizeItem($request, $experience->resume_id);
        $experience->update($this->experienceData($request));

        return back()->with('success', __('profile.credentials.experience_saved'));
    }

    public function destroyExperience(Request $request, Experience $experience)
    {
        $this->authorizeItem($request, $experience->resume_id);
        $experience->delete();

        return back()->with('success', __('profile.credentials.experience_deleted'));
    }

    public function storeCertification(Request $request)
    {
        $this->resume($request)->certifications()->create($this->certificationData($request));

        return back()->with('success', __('profile.credentials.certification_saved'));
    }

    public function updateCertification(Request $request, Certification $certification)
    {
        $this->authorizeItem($request, $certification->resume_id);
        $certification->update($this->certificationData($request));

        return back()->with('success', __('profile.credentials.certification_saved'));
    }

    public function destroyCertification(Request $request, Certification $certification)
    {
        $this->authorizeItem($request, $certification->resume_id);
        $certification->delete();

        return back()->with('success', __('profile.credentials.certification_deleted'));
    }

    private function resume(Request $request): Resume
    {
        abort_unless($request->user()->role === 'prestataire', 403);

        return $request->user()->resume()->firstOrCreate([], [
            'title' => trim($request->user()->first_name.' '.$request->user()->last_name),
            'visibility' => 'public',
        ]);
    }

    private function authorizeItem(Request $request, int $resumeId): void
    {
        abort_unless(
            $request->user()->role === 'prestataire'
            && $request->user()->resume()->whereKey($resumeId)->exists(),
            403
        );
    }

    private function educationData(Request $request): array
    {
        return $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'degree' => ['required', 'string', 'max:255'],
            'field' => ['required', 'string', 'max:255'],
            'start_year' => ['nullable', 'integer', 'min:1950', 'max:'.now()->year],
            'end_year' => ['nullable', 'integer', 'min:1950', 'max:'.(now()->year + 10), 'gte:start_year'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function experienceData(Request $request): array
    {
        return $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date', 'before_or_equal:today'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function certificationData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'issuer' => ['required', 'string', 'max:255'],
            'issue_date' => ['required', 'date', 'before_or_equal:today'],
            'expiration_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'credential_url' => ['nullable', 'url', 'max:500'],
        ]);
    }
}
