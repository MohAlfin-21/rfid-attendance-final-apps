<x-layouts.student :title="__('Leaderboard Kehadiran')" :subtitle="'Kompetisi kehadiran kelas ' . ($classroom?->name ?? '')">
<div class="space-y-6">

    {{-- My Streak Hero Card --}}
    @php $badge = $myStreak->badge(); @endphp
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-violet-700 rounded-2xl p-6 text-white shadow-xl shadow-indigo-500/30">
        {{-- Decorative circles --}}
        <div class="absolute -top-8 -right-8 w-40 h-40 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-6 -left-6 w-28 h-28 bg-white/5 rounded-full"></div>

        <div class="relative flex items-center justify-between gap-4">
            <div>
                <p class="text-indigo-200 text-xs font-semibold uppercase tracking-widest mb-1">Streak Kamu Saat Ini</p>
                <p class="text-6xl font-black leading-none">{{ $myStreak->current_streak }}<span class="text-2xl font-normal ml-2 text-indigo-200">hari</span></p>
                <div class="flex items-center gap-4 mt-3 text-sm text-indigo-200">
                    <span>🏆 Terpanjang: <strong class="text-white">{{ $myStreak->longest_streak }} hari</strong></span>
                    <span>·</span>
                    <span>⚡ Poin: <strong class="text-white">{{ number_format($myStreak->total_points) }}</strong></span>
                </div>
            </div>
            <div class="text-7xl select-none shrink-0 drop-shadow-lg">
                @if($myStreak->current_streak >= 100) 💎
                @elseif($myStreak->current_streak >= 30)  🥇
                @elseif($myStreak->current_streak >= 7)   ⭐
                @elseif($myStreak->current_streak >= 1)   🔥
                @else                                      😴
                @endif
            </div>
        </div>

        @if($badge)
        <div class="relative mt-4">
            <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm border border-white/20 rounded-full px-4 py-1.5 text-sm font-semibold">
                🎖️ {{ $badge['label'] }} — earned!
            </span>
        </div>
        @endif
    </div>

    {{-- Badge Milestones --}}
    <div class="grid grid-cols-3 gap-3">
        @foreach([['⭐', '7 Hari Berturut', 7, 'indigo'], ['🥇', '30 Hari Berturut', 30, 'amber'], ['💎', '100 Hari Berturut', 100, 'purple']] as [$icon, $lbl, $days, $color])
        <div class="bg-white rounded-2xl border {{ $myStreak->longest_streak >= $days ? "border-{$color}-300 ring-2 ring-{$color}-200 shadow-sm" : 'border-gray-100 opacity-50' }} p-4 text-center transition-all">
            <p class="text-3xl mb-2">{{ $icon }}</p>
            <p class="text-xs font-bold text-gray-700">{{ $lbl }}</p>
            @if($myStreak->longest_streak >= $days)
                <p class="text-xs text-emerald-600 font-semibold mt-1">✓ Diraih!</p>
            @else
                <p class="text-xs text-gray-400 mt-1">Target: {{ $days }} hari</p>
                <div class="mt-2 h-1 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-indigo-300 to-purple-400 rounded-full transition-all" style="width: {{ min(100, round($myStreak->longest_streak / $days * 100)) }}%"></div>
                </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Leaderboard Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800 flex items-center gap-2">
                🏆 Leaderboard — {{ $classroom?->name ?? 'Kelas Saya' }}
            </h2>
            <p class="text-xs text-gray-400 bg-white px-3 py-1 rounded-full border border-gray-100">Berdasarkan streak aktif</p>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($topStreaks as $i => $streak)
            @php
                $isMe   = $streak->user_id === auth()->id();
                $medals = ['🥇','🥈','🥉'];
                $rank   = $i < 3 ? $medals[$i] : ($i + 1);
            @endphp
            <div class="flex items-center gap-4 px-5 py-3.5 {{ $isMe ? 'bg-indigo-50 border-l-2 border-indigo-400' : 'hover:bg-gray-50' }} transition-colors">
                <div class="w-8 text-center shrink-0">
                    @if($i < 3)
                        <span class="text-xl">{{ $rank }}</span>
                    @else
                        <span class="text-sm font-bold text-gray-400">{{ $rank }}</span>
                    @endif
                </div>

                <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $isMe ? 'from-indigo-500 to-purple-600' : 'from-slate-400 to-slate-500' }} flex items-center justify-center text-white font-bold text-sm shrink-0">
                    {{ strtoupper(substr($streak->user->name ?? 'U', 0, 1)) }}
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">
                        {{ $streak->user->name ?? '—' }}
                        @if($isMe) <span class="text-xs text-indigo-500 font-normal ml-1">(kamu)</span> @endif
                    </p>
                    @if($streak->badge())
                    <span class="text-xs {{ $streak->badge()['color'] }} {{ $streak->badge()['bg'] }} px-2 py-0.5 rounded-full">
                        {{ $streak->badge()['label'] }}
                    </span>
                    @endif
                </div>

                <div class="text-right shrink-0">
                    <p class="text-lg font-black text-indigo-600">{{ $streak->current_streak }}<span class="text-xs font-normal text-gray-400 ml-1">hari</span></p>
                    <p class="text-xs text-gray-400">{{ number_format($streak->total_points) }} pts</p>
                </div>
            </div>
            @empty
            <div class="px-5 py-14 text-center text-gray-400">
                <p class="text-4xl mb-3">🚀</p>
                <p class="text-sm font-medium">Belum ada data streak.</p>
                <p class="text-xs mt-1">Rajin hadir untuk mulai membangun streak!</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
</x-layouts.student>
