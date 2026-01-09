

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    {{-- SECURITY NOTE:
         No CSP, no security headers → intentional vulnerabilities !!!!!!!!!!!! n--}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sandbox</title>

    {{-- Tailwind (from Breeze build) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100" style="min-height: 100vh; display: flex; flex-direction: column;">

{{-- Top navigation bar --}}
<nav class="bg-white shadow mb-6" style="flex-shrink: 0;">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between">

        <div class="flex space-x-4">
            <a href="{{ route('ideas.index') }}" class="font-bold">Ideas</a>
            <a href="{{ route('logs.index') }}">Logs</a>
            <a href="{{ route('redirect.vulnerable', ['url' => 'https://google.com']) }}">
                Open Redirect Test
            </a>
        </div>

        <div class="flex space-x-4">
            @auth
                <span>{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-red-600">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>

    </div>
</nav>

{{-- Main content section --}}
<main class="max-w-7xl mx-auto px-4" style="flex: 1; min-height: 0; padding-bottom: 48px; width: 100%;">
    @yield('content')
</main>

{{-- Footer --}}
<footer style="background-color: #1f2937; color: white; margin-top: 0; padding: 24px 0; flex-shrink: 0;">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">

            {{-- Copyright --}}
            <div>
                <p style="margin: 0; font-size: 14px; color: #9ca3af;">
                    &copy; {{ date('Y') }} Sandbox. Tous droits réservés.
                </p>
            </div>

            {{-- Cookie Toggle (visible seulement si authentifié et a fait un choix) --}}
            @auth
                @if(in_array(auth()->user()->User_Cookies, [1, 2]))
                    @php
                        $isAccepted = auth()->user()->User_Cookies == 1;
                        $statusText = $isAccepted ? 'Acceptés' : 'Refusés';
                    @endphp
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <span style="font-size: 14px; color: #d1d5db;">Cookies</span>

                        <form id="cookieToggleForm" method="POST" action="{{ route('cookie.toggle') }}" style="display: inline;">
                            @csrf
                            <label style="position: relative; display: inline-block; width: 56px; height: 28px; cursor: pointer;">
                                <input
                                    type="checkbox"
                                    id="cookieToggle"
                                    {{ $isAccepted ? 'checked' : '' }}
                                    onchange="document.getElementById('cookieToggleForm').submit()"
                                    style="opacity: 0; width: 0; height: 0;">

                                <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $isAccepted ? '#16a34a' : '#dc2626' }}; transition: 0.4s; border-radius: 28px;">
                                    <span style="position: absolute; content: ''; height: 20px; width: 20px; left: {{ $isAccepted ? '32px' : '4px' }}; bottom: 4px; background-color: white; transition: 0.4s; border-radius: 50%;"></span>
                                </span>
                            </label>
                        </form>

                        <span style="font-size: 12px; color: #9ca3af;">
                            {{ $statusText }}
                        </span>
                    </div>
                @endif
            @endauth

            {{-- Liens --}}
            <div style="display: flex; gap: 16px;">
                <a href="{{ route('privacy') }}" style="color: #9ca3af; text-decoration: none; font-size: 14px; transition: color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#9ca3af'">Vie privée / Charte RGPD</a>
                <a href="#" style="color: #9ca3af; text-decoration: none; font-size: 14px; transition: color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#9ca3af'">Contact</a>
            </div>
        </div>
    </div>
</footer>

{{-- Cookie Consent Pop-up (modale bloquante) --}}
@auth
    @if(in_array(auth()->user()->User_Cookies, [null, 0]) || session('cookie_just_set'))
        <div id="cookieModal" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; z-index: 9999;">
            <div style="background: white; border-radius: 20px; padding: 40px; max-width: 600px; width: 90%; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
                <h2 style="font-size: 32px; font-weight: bold; margin-bottom: 24px; color: #111827; text-align: center;">Consentement aux Cookies</h2>

                @if(session('cookie_just_set'))
                    {{-- Message de succès après soumission --}}
                    <div style="text-align: center;">
                        <div style="margin-bottom: 16px;">
                            <svg style="margin: 0 auto; width: 64px; height: 64px; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p style="font-size: 20px; color: #15803d; font-weight: 600;">
                            Votre choix a bien été pris en compte!
                        </p>
                        <p style="font-size: 14px; color: #6b7280; margin-top: 8px;">
                            Cette fenêtre va se fermer automatiquement...
                        </p>
                    </div>

                    <script>
                        setTimeout(function() {
                            document.getElementById('cookieModal').style.display = 'none';
                        }, 2500);
                    </script>
                @else
                    {{-- Formulaire de choix initial --}}
                    <p style="color: #6b7280; margin-bottom: 32px; text-align: center; font-size: 16px; line-height: 1.6;">
                        Nous utilisons des cookies pour améliorer votre expérience sur notre site.
                        Acceptez-vous l'utilisation de cookies?
                    </p>

                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <form method="POST" action="{{ route('cookie.consent') }}" style="flex: 1; min-width: 200px;">
                            @csrf
                            <input type="hidden" name="consent" value="1">
                            <button type="submit" style="width: 100%; padding: 16px 32px; background-color: #16a34a; color: white; border: none; border-radius: 12px; font-weight: bold; font-size: 18px; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); transition: all 0.2s;" onmouseover="this.style.backgroundColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a'">
                                Oui, j'accepte
                            </button>
                        </form>

                        <form method="POST" action="{{ route('cookie.consent') }}" style="flex: 1; min-width: 200px;">
                            @csrf
                            <input type="hidden" name="consent" value="2">
                            <button type="submit" style="width: 100%; padding: 16px 32px; background-color: #dc2626; color: white; border: none; border-radius: 12px; font-weight: bold; font-size: 18px; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); transition: all 0.2s;" onmouseover="this.style.backgroundColor='#b91c1c'" onmouseout="this.style.backgroundColor='#dc2626'">
                                Non, je refuse
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endauth

</body>
</html>
