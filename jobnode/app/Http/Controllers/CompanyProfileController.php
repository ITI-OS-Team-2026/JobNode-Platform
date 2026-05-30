<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class CompanyProfileController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048', // 2MB Max
        ]);

        $user = $request->user();
        // Get existing company or create a new instance
        $company = $user->company ?? new Company(['user_id' => $user->id]);

        $company->company_name = $validated['company_name'];
        $company->website = $validated['website'];

        if ($request->hasFile('logo')) {
            // Delete the old logo if it exists
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            // Save the new logo to storage/app/public/logos
            $company->logo_path = $request->file('logo')->store('logos', 'public');
        }

        $company->save();

        return back()->with('success', 'Company brand updated successfully.');
    }
}
