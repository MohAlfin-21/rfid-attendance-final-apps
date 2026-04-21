<?php

namespace Tests\Feature;

use App\Models\AbsenceRequest;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AbsenceRequestAttachmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cache.default', 'array');
        Storage::fake('public');
    }

    public function test_student_attachment_is_stored_in_student_folder(): void
    {
        $student = $this->makeUser('student', ['nis' => '112233']);

        $response = $this->actingAs($student)->post(route('student.absence-requests.store'), [
            'type' => 'sick',
            'date_start' => now()->addDay()->toDateString(),
            'date_end' => now()->addDay()->toDateString(),
            'reason' => 'Surat keterangan sakit dari klinik.',
            'attachment' => UploadedFile::fake()->create('surat-sakit.pdf', 600, 'application/pdf'),
        ]);

        $response->assertRedirect(route('student.absence-requests.index'));

        $absenceRequest = AbsenceRequest::query()->firstOrFail();

        $this->assertStringStartsWith("absence-attachments/user-{$student->id}/", $absenceRequest->attachment_path);
        Storage::disk('public')->assertExists($absenceRequest->attachment_path);
    }

    public function test_teacher_and_secretary_can_view_allowed_student_attachment(): void
    {
        $teacher = $this->makeUser('teacher');
        $secretary = $this->makeUser('secretary');
        $student = $this->makeUser('student', ['nis' => '445566']);

        $classroom = Classroom::query()->create([
            'code' => 'XI-RPL-1',
            'name' => 'XI RPL 1',
            'grade' => 11,
            'major' => 'RPL',
            'homeroom_teacher_id' => $teacher->id,
            'is_active' => true,
        ]);

        $classroom->students()->attach($student->id, [
            'academic_year' => '2025/2026',
            'semester' => 1,
            'is_active' => true,
        ]);

        $absenceRequest = $this->makeAbsenceRequestWithAttachment($student);

        $this->actingAs($teacher)
            ->get(route('absence-requests.attachment', $absenceRequest))
            ->assertOk();

        $this->actingAs($secretary)
            ->get(route('absence-requests.attachment', $absenceRequest))
            ->assertOk();
    }

    public function test_teacher_cannot_view_attachment_outside_homeroom_classroom(): void
    {
        $teacher = $this->makeUser('teacher');
        $student = $this->makeUser('student', ['nis' => '778899']);
        $absenceRequest = $this->makeAbsenceRequestWithAttachment($student);

        $this->actingAs($teacher)
            ->getJson(route('absence-requests.attachment', $absenceRequest))
            ->assertForbidden();
    }

    private function makeUser(string $role, array $attributes = []): User
    {
        Role::findOrCreate($role, 'web');

        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }

    private function makeAbsenceRequestWithAttachment(User $student): AbsenceRequest
    {
        $path = "absence-attachments/user-{$student->id}/2026/04/surat.jpg";
        Storage::disk('public')->put($path, 'fake image content');

        return AbsenceRequest::query()->create([
            'user_id' => $student->id,
            'type' => 'sick',
            'status' => 'pending',
            'date_start' => now()->addDay()->toDateString(),
            'date_end' => now()->addDay()->toDateString(),
            'reason' => 'Sakit.',
            'attachment_path' => $path,
        ]);
    }
}
