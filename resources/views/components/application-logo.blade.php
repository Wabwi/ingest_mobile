<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" {{ $attributes }} style="width: 80px; height: 80px;">
    <!-- Outer circle -->
    <circle cx="50" cy="50" r="45" fill="none" stroke="url(#logoGlow)" stroke-width="5" stroke-linecap="round" />
    
    <!-- Heart pulse line -->
    <path d="M25 50 H38 L43 32 L50 68 L57 45 L62 50 H75" fill="none" stroke="url(#logoGlow)" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />
    
    <defs>
        <linearGradient id="logoGlow" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#6366f1" />
            <stop offset="100%" stop-color="#d97706" />
        </linearGradient>
    </defs>
</svg>
