<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $query = Classroom::with('homeroomTeacher')->withCount('students');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%");
            });
        }

        $classrooms = $query->latest()->paginate(15)->withQueryString();
        return view('admin.classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        $teachers = User::role('teacher')->active()->get();
        return view('admin.classrooms.create', compact('teachers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:classrooms',
            'name' => 'required|string|max:255',
            'grade' => 'required|integer|min:10|max:13',
            'major' => 'nullable|string|max:100',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        Classroom::create($validated);

        return redirect()->route('admin.classrooms.index')->with('success', __('Kelas berhasil ditambahkan.'));
    }

    public function show(Classroom $classroom)
    {
        $classroom->load(['homeroomTeacher', 'students' => fn ($q) => $q->withPivot('academic_year', 'semester', 'is_active')]);
        $availableStudents = User::role('student')->active()
            ->whereDoesntHave('classrooms', fn ($q) => $q->where('classrooms.id', $classroom->id))
            ->get();
        return view('admin.classrooms.show-localized', compact('classroom', 'availableStudents'));
    }

    public function edit(Classroom $classroom)
    {
        $teachers = User::role('teacher')->active()->get();
        return view('admin.classrooms.edit', compact('classroom', 'teachers'));
    }

    public function update(Request $request, Classroom $classroom): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('classrooms')->ignore($classroom)],
            'name' => 'required|string|max:255',
            'grade' => 'required|integer|min:10|max:13',
            'major' => 'nullable|string|max:100',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
            'add_student_id' => 'nullable|exists:users,id',
            'remove_student_id' => 'nullable|exists:users,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->filled('add_student_id')) {
            $classroom->students()->syncWithoutDetaching([
                $request->input('add_student_id') => [
                    'academic_year' => config('attendance.academic_year'),
                    'semester' => config('attendance.semester'),
                    'is_active' => true,
                ],
            ]);
            return redirect()->route('admin.classrooms.show', $classroom)->with('success', __('Siswa berhasil ditambahkan ke kelas.'));
        }

        if ($request->filled('remove_student_id')) {
            $classroom->students()->detach($request->input('remove_student_id'));
            return redirect()->route('admin.classrooms.show', $classroom)->with('success', __('Siswa berhasil dikeluarkan dari kelas.'));
        }

        $classroom->update($validated);
        return redirect()->route('admin.classrooms.index')->with('success', __('Kelas berhasil diperbarui.'));
    }

    public function destroy(Classroom $classroom): RedirectResponse
    {
        $classroom->delete();
        return redirect()->route('admin.classrooms.index')->with('success', __('Kelas berhasil dihapus.'));
    }
}
