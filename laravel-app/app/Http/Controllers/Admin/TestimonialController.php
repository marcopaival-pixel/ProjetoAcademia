<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the testimonials.
     */
    public function index(Request $request): View
    {
        $query = Testimonial::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('testimonial', 'like', "%{$search}%")
                  ->orWhere('profession', 'like', "%{$search}%");
            });
        }

        // Approval status filter
        if ($request->filled('status')) {
            $status = $request->query('status');
            if ($status === 'approved') {
                $query->whereNotNull('approved_at');
            } elseif ($status === 'pending') {
                $query->whereNull('approved_at');
            }
        }

        // Highlight/Featured filter
        if ($request->filled('featured')) {
            $query->where('featured', $request->query('featured') === '1');
        }

        $testimonials = $query->latest()->paginate(15)->withQueryString();

        return view('admin.testimonials.index', compact('testimonials'));
    }

    /**
     * Show the form for creating a new testimonial.
     */
    public function create(): View
    {
        return view('admin.testimonials.create');
    }

    /**
     * Store a newly created testimonial in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'profession' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|size:2',
            'rating' => 'required|integer|min:1|max:5',
            'testimonial' => 'required|string|max:1000',
            'avatar' => 'nullable|image|max:1024',
        ]);

        $data = $request->only([
            'name', 'profession', 'city', 'state', 'rating', 'testimonial'
        ]);

        $data['featured'] = $request->has('featured');
        $data['is_public'] = $request->has('is_public');
        $data['created_by'] = auth()->id();

        if ($request->has('approved')) {
            $data['approved_at'] = now();
        }

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('testimonials', 'public');
        }

        Testimonial::create($data);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Depoimento cadastrado com sucesso!');
    }

    /**
     * Show the form for editing the specified testimonial.
     */
    public function edit(Testimonial $testimonial): View
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    /**
     * Update the specified testimonial in storage.
     */
    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'profession' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|size:2',
            'rating' => 'required|integer|min:1|max:5',
            'testimonial' => 'required|string|max:1000',
            'avatar' => 'nullable|image|max:1024',
        ]);

        $data = $request->only([
            'name', 'profession', 'city', 'state', 'rating', 'testimonial'
        ]);

        $data['featured'] = $request->has('featured');
        $data['is_public'] = $request->has('is_public');

        if ($request->hasFile('avatar')) {
            if ($testimonial->avatar_path) {
                Storage::disk('public')->delete($testimonial->avatar_path);
            }
            $data['avatar_path'] = $request->file('avatar')->store('testimonials', 'public');
        }

        $testimonial->update($data);

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Depoimento atualizado com sucesso!');
    }

    /**
     * Remove the specified testimonial from storage.
     */
    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        if ($testimonial->avatar_path) {
            Storage::disk('public')->delete($testimonial->avatar_path);
        }

        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Depoimento excluído com sucesso!');
    }

    /**
     * Toggle approval status.
     */
    public function toggleApprove(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update([
            'approved_at' => $testimonial->approved_at ? null : now()
        ]);

        $msg = $testimonial->approved_at ? 'Depoimento aprovado com sucesso!' : 'Aprovação removida!';

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeature(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update([
            'featured' => !$testimonial->featured
        ]);

        $msg = $testimonial->featured ? 'Depoimento destacado com sucesso!' : 'Destaque removido!';

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Toggle public visibility.
     */
    public function toggleVisibility(Testimonial $testimonial): RedirectResponse
    {
        $testimonial->update([
            'is_public' => !$testimonial->is_public
        ]);

        $msg = $testimonial->is_public ? 'Depoimento agora está público!' : 'Depoimento agora está privado!';

        return redirect()->back()->with('success', $msg);
    }
}
