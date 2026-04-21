<x-layouts.teacher :title="'Daftar Siswa'" :subtitle="'Siswa di kelas yang saya walii'">
    @foreach($classrooms as $classroom)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/30 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-900 text-lg">{{ $classroom->name }}</h3>
                <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider font-semibold">KODE: {{ $classroom->code }}</p>
            </div>
            <span class="inline-flex items-center justify-center min-w-8 h-8 px-2 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">{{ $classroom->students->count() }} Siswa</span>
        </div>
        
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest bg-gray-50/50 border-b border-gray-100">
                    <th class="px-6 py-4 w-12">{{ __('No') }}</th>
                    <th class="px-6 py-4">{{ __('Siswa') }}</th>
                    <th class="px-6 py-4">{{ __('Akademik & Akun') }}</th>
                    <th class="px-6 py-4">{{ __('Kontak Wali (WA)') }}</th>
                    <th class="px-6 py-4 text-right">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($classroom->students as $i => $student)
                <tr class="hover:bg-gray-50/80 transition-colors group">
                    <td class="px-6 py-4 text-gray-400 font-medium text-xs align-top pt-6">{{ $i + 1 }}</td>
                    
                    {{-- 1. Siswa --}}
                    <td class="px-6 py-4 align-top">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 shrink-0 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-md ring-2 ring-white">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                            <div>
                                <p class="font-bold text-gray-900 leading-none">{{ $student->name }}</p>
                                <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                                    <span class="truncate max-w-[200px]">{{ $student->email }}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    
                    {{-- 2. Akademik & Akun --}}
                    <td class="px-6 py-4 align-top">
                        <div class="flex flex-col gap-2 items-start pt-1">
                            @if($student->nis)
                                <span class="text-[13px] font-bold text-gray-700 border-b border-dashed border-gray-300 pb-0.5" title="NIS">{{ $student->nis }}</span>
                            @else
                                <span class="text-xs text-gray-400 italic">No NIS</span>
                            @endif
                            <span class="font-mono text-[10px] bg-gray-100 border border-gray-200 px-1.5 py-0.5 rounded text-gray-600">{{ $student->username }}</span>
                        </div>
                    </td>

                    {{-- 3. Kontak Wali --}}
                    <td class="px-6 py-4 align-top">
                        @if($student->studentProfile)
                            <div class="flex flex-col gap-2 pt-1">
                                <p class="text-[13px] font-semibold text-gray-800 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7-7h14a7 7 0 00-7-7z"/></svg>
                                    {{ $student->studentProfile->parent_name ?: '-' }}
                                </p>
                                @if($student->studentProfile->parent_phone)
                                <a href="https://wa.me/{{ $student->studentProfile->parent_phone }}" target="_blank" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-2 transition-colors w-fit bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                    {{ $student->studentProfile->parent_phone }}
                                </a>
                                @endif
                            </div>
                        @else
                            <div class="pt-1"><span class="text-amber-500 font-semibold text-[11px] flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Belum diset</span></div>
                        @endif
                    </td>

                    {{-- 4. Status --}}
                    <td class="px-6 py-4 align-top text-right">
                        <div class="flex justify-end pt-1">
                            @if($student->is_active)
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600"><span class="flex relative w-2 h-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full w-2 h-2 bg-emerald-500"></span></span>{{ __('Aktif') }}</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-400"><span class="w-1.5 h-1.5 bg-gray-300 rounded-full flex-shrink-0"></span>{{ __('Nonaktif') }}</span>
                            @endif
                        </div>
                        <div class="mt-2 text-[10px] text-gray-400 font-medium">Berdasar akun</div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400 font-medium">{{ __('Belum ada siswa di kelas ini') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endforeach

    @if($classrooms->isEmpty())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center"><p class="text-gray-400">Anda belum ditugaskan sebagai wali kelas.</p></div>
    @endif
</x-layouts.teacher>
