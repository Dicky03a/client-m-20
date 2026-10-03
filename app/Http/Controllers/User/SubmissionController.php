<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Submission;
use App\Models\UserFile;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubmissionController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('subcategories')->get();
        $userId = $request->user()->id;
        
        $submissionsCount = Submission::where('user_id', $userId)
            ->selectRaw('kriteria.id_kriteria as category_id, count(pengisian.id) as count')
            ->join('subkriteria', 'subkriteria.id_subkriteria', '=', 'pengisian.subcategory_id')
            ->join('kriteria', 'kriteria.id_kriteria', '=', 'subkriteria.category_id')
            ->groupBy('kriteria.id_kriteria')
            ->pluck('count', 'category_id');

        $categories->transform(function ($category) use ($submissionsCount) {
            $category->filled_count = $submissionsCount->get($category->id) ?? 0;
            return $category;
        });

        $recentSubmissions = Submission::where('user_id', $userId)
            ->with(['subcategory.category', 'userFile'])
            ->latest()
            ->get();

        return Inertia::render('user/categories/index', [
            'categories' => $categories,
            'recentSubmissions' => $recentSubmissions
        ]);
    }

    public function showCategory(Request $request, Category $category)
    {
        $subcategories = $category->subcategories;
        $userId = $request->user()->id;

        $submissions = Submission::where('user_id', $userId)
            ->whereIn('subcategory_id', $subcategories->pluck('id'))
            ->with('userFile')
            ->get()
            ->keyBy('subcategory_id');

        $subcategories->transform(function ($sub) use ($submissions) {
            $sub->is_filled = $submissions->has($sub->id);
            if ($sub->is_filled) {
                $sub->submission = $submissions->get($sub->id);
            }
            return $sub;
        });

        return Inertia::render('user/categories/show', [
            'category' => $category,
            'subcategories' => $subcategories
        ]);
    }

    public function showForm(Request $request, Subcategory $subcategory)
    {
        $subcategory->load('category');
        $userFiles = $request->user()->userFiles()->latest()->get();

        return Inertia::render('user/submissions/create', [
            'subcategory' => $subcategory,
            'library_files' => $userFiles
        ]);
    }

    public function store(Request $request, Subcategory $subcategory)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        if (Submission::where('user_id', $request->user()->id)->where('subcategory_id', $subcategory->id)->exists()) {
            return back()->with('error', 'You have already submitted a file for this subcategory.');
        }

        $file = $request->file('file');
        $path = $file->store('user_files', 'public');

        $userFile = UserFile::create([
            'user_id' => $request->user()->id,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);

        Submission::create([
            'user_id' => $request->user()->id,
            'subcategory_id' => $subcategory->id,
            'user_file_id' => $userFile->id,
        ]);

        return redirect()->route('user.categories.show', $subcategory->category_id)
            ->with('success', 'File successfully uploaded and submitted.');
    }

    public function storeFromLibrary(Request $request, Subcategory $subcategory)
    {
        $request->validate([
            'user_file_id' => 'required|exists:user_files,id',
        ]);

        $userFile = UserFile::findOrFail($request->user_file_id);
        if ($userFile->user_id !== $request->user()->id) {
            abort(403);
        }

        if (Submission::where('user_id', $request->user()->id)->where('subcategory_id', $subcategory->id)->exists()) {
            return back()->with('error', 'You have already submitted a file for this subcategory.');
        }

        Submission::create([
            'user_id' => $request->user()->id,
            'subcategory_id' => $subcategory->id,
            'user_file_id' => $userFile->id,
        ]);

        return redirect()->route('user.categories.show', $subcategory->category_id)
            ->with('success', 'Library file successfully submitted.');
    }

    public function preview(Request $request, $id)
    {
        $file = UserFile::findOrFail($id);
        
        if ($file->user_id !== $request->user()->id) {
            abort(403);
        }

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($file->path)) {
            abort(404);
        }

        return response()->file(storage_path('app/public/' . $file->path));
    }
}
