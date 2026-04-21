<x-layouts.admin :title="__('Analitik Kehadiran')" :subtitle="__('Statistik dan tren kehadiran seluruh siswa')">
<!-- ApexCharts CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.1/dist/apexcharts.min.js" defer></script>

<div class="space-y-6">
  {{-- Summary Stats --}}
  <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
    @php
      $statCards = [
        ['label' => 'Siswa Aktif', 'val' => $totalStudents, 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'indigo', 'bg' => 'bg-indigo-50/50'],
        ['label' => 'Hadir Hari Ini',    'val' => $todayStats?->present ?? 0, 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald', 'bg' => 'bg-emerald-50/50'],
        ['label' => 'Terlambat Hari Ini','val' => $todayStats?->late ?? 0,    'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'amber', 'bg' => 'bg-amber-50/50'],
        ['label' => 'Alpa Hari Ini',     'val' => $todayStats?->absent ?? 0,  'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'red', 'bg' => 'bg-red-50/50'],
      ];
    @endphp
    @foreach($statCards as $s)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 relative overflow-hidden group hover:shadow-md transition-shadow">
      <div class="absolute -right-4 -top-4 w-16 h-16 rounded-full {{ $s['bg'] }} mix-blend-multiply opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
      <div class="flex items-center gap-4 relative z-10">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-{{ $s['color'] }}-100 to-{{ $s['color'] }}-50 flex items-center justify-center shrink-0 border border-{{ $s['color'] }}-100/50">
          <svg class="w-6 h-6 text-{{ $s['color'] }}-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $s['icon'] }}"/></svg>
        </div>
        <div>
          <p class="text-sm font-semibold text-gray-500 mb-0.5">{{ $s['label'] }}</p>
          <p class="text-2xl font-bold text-gray-900 tracking-tight">{{ $s['val'] }}</p>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Heatmap --}}
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
      <div>
        <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Heatmap Tingkat Kehadiran (365 Hari)
        </h2>
      </div>
      <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 bg-gray-100 px-2 py-1 rounded-md">1 Tahun Terakhir</span>
    </div>
    <div class="p-6">
      <div id="chart-heatmap"></div>
      <div class="flex items-center justify-end gap-2 mt-2 pt-4 border-t border-gray-50">
          <span class="text-xs text-gray-400 font-medium tracking-wide">Lebih Sedikit</span>
          <div class="flex gap-1">
              <div class="w-3 h-3 rounded-sm bg-indigo-50"></div>
              <div class="w-3 h-3 rounded-sm bg-indigo-100"></div>
              <div class="w-3 h-3 rounded-sm bg-indigo-200"></div>
              <div class="w-3 h-3 rounded-sm bg-indigo-400"></div>
              <div class="w-3 h-3 rounded-sm bg-indigo-600"></div>
          </div>
          <span class="text-xs text-gray-400 font-medium tracking-wide">Lebih Banyak</span>
      </div>
    </div>
  </div>

  {{-- Monthly Trend --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
      <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-emerald-50/50 to-white flex items-center justify-between">
          <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>
            Tren Kehadiran Bulanan
          </h2>
          <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 bg-gray-100 px-2 py-1 rounded-md">12 Bulan</span>
      </div>
      <div class="p-5 flex-1 relative min-h-[280px]">
        <div id="chart-trend" class="absolute inset-0 p-5"></div>
      </div>
    </div>

    {{-- Classroom Ranking --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
      <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-purple-50/50 to-white flex items-center justify-between">
          <h2 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
            <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0"/></svg>
            Ranking Kehadiran Kelas
          </h2>
          <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 bg-gray-100 px-2 py-1 rounded-md">30 Hari</span>
      </div>
      <div class="p-5 flex-1 relative min-h-[280px]">
        <div id="chart-ranking" class="absolute inset-0 p-5 pt-7"></div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

  const fontConfig = { fontFamily: 'Inter, system-ui, sans-serif' };

  // ── Heatmap data processing ─────────────────────────────────────────────
  const rawHeatmap = @json($heatmapData);

  // Convert flat data per day-of-week for heatmap series
  const dayNames = ['Sen','Sel','Rab','Kam','Jum','Sab','Min'];
  const heatSeries = dayNames.map(function(dayName) {
    return {
      name: dayName,
      data: rawHeatmap
        .filter(function(item) {
          const d    = new Date(item.x);
          const days = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
          return days[d.getDay()] === dayName;
        })
        .slice(-52) // last 52 weeks
        .map(function(item) { return { x: item.x, y: item.y }; })
    };
  });

  new ApexCharts(document.getElementById('chart-heatmap'), {
    chart: { type: 'heatmap', height: '100%', toolbar: { show: false }, animations: { enabled: false }, fontFamily: fontConfig.fontFamily },
    series: heatSeries,
    colors: ['#4f46e5'],
    dataLabels: { enabled: false },
    tooltip: { 
        theme: 'light',
        y: { formatter: function(v) { return v + '% tingkat kehadiran'; } } 
    },
    xaxis: { 
        type: 'category', 
        labels: { show: false }, 
        axisBorder: { show: false }, 
        axisTicks: { show: false } 
    },
    yaxis: { 
        labels: { style: { fontSize: '11px', fontWeight: 500, colors: '#6b7280' } } 
    },
    grid: { show: false, padding: { top: 0, right: 0, bottom: 0, left: 10 } },
    plotOptions: { 
        heatmap: { 
            shadeIntensity: 0.6, 
            radius: 6, 
            useFillColorAsStroke: false,
            colorScale: {
                ranges: [
                    { from: -1, to: 0, color: '#f8fafc', name: 'N/A' },
                    { from: 1, to: 20, color: '#e0e7ff', name: '< 20%' },
                    { from: 21, to: 50, color: '#c7d2fe', name: '21-50%' },
                    { from: 51, to: 80, color: '#818cf8', name: '51-80%' },
                    { from: 81, to: 100, color: '#4f46e5', name: '> 80%' }
                ]
            }
        } 
    },
  }).render();

  // ── Monthly trend line chart ────────────────────────────────────────────
  const trend = @json($monthlyTrend);
  new ApexCharts(document.getElementById('chart-trend'), {
    chart: { type: 'area', height: '100%', width: '100%', toolbar: { show: false }, zoom: { enabled: false }, fontFamily: fontConfig.fontFamily, parentHeightOffset: 0 },
    series: [{ name: 'Kehadiran', data: trend.map(t => t.rate) }],
    xaxis: { 
        categories: trend.map(t => t.label), 
        labels: { style: { fontSize: '11px', fontWeight: 500, colors: '#6b7280' }, offsetY: 5 },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: { 
        min: 0, 
        max: 100, 
        labels: { formatter: v => v + '%', style: { fontSize: '11px', fontWeight: 500, colors: '#6b7280' }, offsetX: -10 },
        tickAmount: 5
    },
    grid: { borderColor: '#f3f4f6', strokeDashArray: 4, padding: { top: 0, right: 0, bottom: 0, left: 10 } },
    colors: ['#10b981'],
    fill: {
        type: 'gradient',
        gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] }
    },
    stroke: { curve: 'smooth', width: 3 },
    markers: { size: 0, hover: { size: 6, sizeOffset: 3 } },
    tooltip: {
      theme: 'light',
      y: { formatter: function(v, { dataPointIndex }) {
        return v + '% (' + trend[dataPointIndex].present + '/' + trend[dataPointIndex].total + ' absensi)';
      }}
    },
  }).render();

  // ── Classroom ranking bar chart ─────────────────────────────────────────
  const ranking = @json($classroomRanking);
  new ApexCharts(document.getElementById('chart-ranking'), {
    chart: { type: 'bar', height: '100%', width: '100%', toolbar: { show: false }, fontFamily: fontConfig.fontFamily, parentHeightOffset: 0 },
    series: [{ name: 'Kehadiran', data: ranking.map(r => r.rate) }],
    xaxis: { 
        categories: ranking.map(r => r.name), 
        labels: { style: { fontSize: '11px', fontWeight: 600, colors: '#4b5563' }, offsetY: 5 },
        axisBorder: { show: false },
        axisTicks: { show: false }
    },
    yaxis: { 
        min: 0, 
        max: 100, 
        labels: { show: false } 
    },
    grid: { show: false, padding: { top: -20, right: 0, bottom: 0, left: 0 } },
    colors: ['#8b5cf6'],
    plotOptions: { bar: { borderRadius: 6, columnWidth: '45%', dataLabels: { position: 'top' } } },
    dataLabels: { 
        enabled: true, 
        formatter: v => v + '%', 
        style: { fontSize: '10px', fontWeight: 700, colors: ['#6b7280'] },
        offsetY: -20
    },
    tooltip: {
      theme: 'light',
      y: { formatter: function(v, { dataPointIndex }) {
        return v + '% (' + ranking[dataPointIndex].present + '/' + ranking[dataPointIndex].total + ')';
      }}
    },
  }).render();
});
</script>
</x-layouts.admin>
