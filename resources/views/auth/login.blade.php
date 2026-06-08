<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SiManKos</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-font-smoothing: antialiased; }
        .glass { background: rgba(255,255,255,.03); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,.08); }
        .input-field { width:100%; background:rgba(255,255,255,.06); border:1.5px solid rgba(255,255,255,.08); border-radius:.75rem; padding:.75rem 1rem; color:#F1F5F9; font-size:.9rem; font-family:inherit; outline:none; transition:border .2s,box-shadow .2s; }
        .input-field:focus { border-color:#38BDF8; box-shadow: 0 0 0 3px rgba(56,189,248,.15); }
        .input-field::placeholder { color:#475569; }
        .skyline { position:fixed; bottom:0; left:0; right:0; height:160px; pointer-events:none; }
    </style>
</head>
<body class="min-h-screen bg-[#0F172A] flex items-center justify-center p-4 relative overflow-hidden">

    {{-- Background decoration --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-sky-400/5 blur-3xl"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-indigo-500/5 blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-sky-400/3 blur-3xl"></div>
    </div>

    {{-- Skyline Buildings --}}
    <svg class="skyline" viewBox="0 0 1440 160" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0,160 L0,120 L60,120 L60,80 L80,80 L80,60 L100,60 L100,80 L120,80 L120,40 L130,40 L130,80 L150,80 L150,100 L180,100 L180,60 L190,60 L190,40 L200,40 L200,60 L210,60 L210,100 L240,100 L240,70 L260,70 L260,50 L270,50 L270,70 L290,70 L290,90 L320,90 L320,110 L380,110 L380,70 L400,70 L400,50 L410,50 L410,70 L430,70 L430,40 L440,40 L440,70 L460,70 L460,90 L500,90 L500,60 L520,60 L520,40 L530,40 L530,60 L550,60 L550,80 L580,80 L580,100 L620,100 L620,70 L640,70 L640,50 L650,50 L650,30 L660,30 L660,50 L670,50 L670,70 L700,70 L700,90 L730,90 L730,60 L750,60 L750,80 L770,80 L770,50 L790,50 L790,70 L820,70 L820,90 L860,90 L860,60 L880,60 L880,40 L890,40 L890,60 L910,60 L910,80 L940,80 L940,100 L980,100 L980,70 L1000,70 L1000,50 L1010,50 L1010,70 L1030,70 L1030,90 L1060,90 L1060,60 L1080,60 L1080,40 L1090,40 L1090,60 L1110,60 L1110,80 L1140,80 L1140,100 L1180,100 L1180,70 L1200,70 L1200,50 L1210,50 L1210,70 L1230,70 L1230,90 L1260,90 L1260,60 L1290,60 L1290,80 L1320,80 L1320,110 L1380,110 L1380,80 L1400,80 L1400,60 L1420,60 L1420,80 L1440,80 L1440,160 Z" fill="#1E293B" opacity="0.6"/>
        <path d="M0,160 L0,130 L100,130 L100,100 L120,100 L120,90 L130,90 L130,100 L160,100 L160,120 L220,120 L220,90 L240,90 L240,80 L250,80 L250,90 L270,90 L270,110 L330,110 L330,85 L350,85 L350,75 L360,75 L360,85 L380,85 L380,110 L440,110 L440,85 L460,85 L460,70 L470,70 L470,85 L490,85 L490,110 L560,110 L560,90 L580,90 L580,80 L590,80 L590,90 L620,90 L620,115 L680,115 L680,90 L700,90 L700,75 L710,75 L710,90 L740,90 L740,115 L800,115 L800,90 L820,90 L820,80 L830,80 L830,90 L860,90 L860,115 L920,115 L920,90 L940,90 L940,80 L950,80 L950,90 L980,90 L980,115 L1040,115 L1040,90 L1060,90 L1060,80 L1070,80 L1070,90 L1100,90 L1100,115 L1160,115 L1160,90 L1180,90 L1180,80 L1190,80 L1190,90 L1220,90 L1220,115 L1290,115 L1290,95 L1320,95 L1320,110 L1380,110 L1380,95 L1440,95 L1440,160 Z" fill="#1E293B" opacity="0.8"/>
        {{-- Windows --}}
        @for($i = 0; $i < 40; $i++)
        <rect x="{{ rand(50, 1400) }}" y="{{ rand(50, 140) }}" width="4" height="6" fill="#38BDF8" opacity="{{ rand(3, 8) / 10 }}"/>
        @endfor
    </svg>

    <div class="w-full max-w-sm relative z-10">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-[#38BDF8] to-[#0284C7] shadow-xl shadow-sky-500/20 mb-4">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <h1 class="text-white font-extrabold text-2xl tracking-tight">SiManKos</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola Kos, Lebih Mudah.</p>
        </div>

        {{-- Card --}}
        <div class="glass rounded-2xl p-7 shadow-2xl">
            <h2 class="text-white font-bold text-lg mb-1">Selamat Datang</h2>
            <p class="text-slate-500 text-sm mb-6">Masuk ke panel admin SiManKos</p>

            @if($errors->any())
            <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-3 mb-5">
                @foreach($errors->all() as $e)
                <p class="text-red-400 text-sm flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $e }}
                </p>
                @endforeach
            </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-slate-300 text-sm font-semibold mb-2">Email</label>
                    <input type="email" name="email" class="input-field" placeholder="admin@simanakos.com" value="{{ old('email') }}" required autocomplete="email">
                </div>
                <div>
                    <label class="block text-slate-300 text-sm font-semibold mb-2">Password</label>
                    <input type="password" name="password" class="input-field" placeholder="••••••••" required autocomplete="current-password">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/20 bg-white/10 text-sky-400">
                        <span class="text-slate-400 text-sm">Ingat saya</span>
                    </label>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-[#38BDF8] to-[#0EA5E9] text-[#0F172A] font-bold py-3 rounded-xl text-sm hover:opacity-90 active:scale-[.98] transition-all shadow-lg shadow-sky-500/20 mt-2">
                    Masuk ke Dashboard
                </button>
            </form>
        </div>

    </div>
</body>
</html>
