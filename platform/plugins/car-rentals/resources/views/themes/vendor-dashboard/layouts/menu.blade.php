<style>
    /* =========================================================
       VENDOR SIDEBAR: SOFT UI (GLASSMORPHISM) OVERRIDES
    ========================================================= */
    
    /* 1. Force the main background so the white glass pops */
    body, .page-wrapper, .vendor-dashboard { 
        background-color: #f8fafc !important; 
    }

    /* 2. Target the main sidebar wrapper (Floating Glass Effect) */
    aside, .sidebar, .vendor-sidebar, .page-sidebar {
        background-color: rgba(255, 255, 255, 0.7) !important; 
        backdrop-filter: blur(16px) saturate(200%) !important;
        -webkit-backdrop-filter: blur(16px) saturate(200%) !important;
        
        border-right: none !important; 
        border: 1px solid rgba(255, 255, 255, 0.9) !important;
        border-radius: 16px !important;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04) !important; 
        
        margin: 16px 0 16px 16px !important; 
        height: calc(100vh - 32px) !important; 
        z-index: 1040 !important;
    }

    /* Remove the default hard lines from the logo header */
    .sidebar-header, .logo {
        border-bottom: 1px solid rgba(0,0,0,0.05) !important;
    }

    /* =========================================================
       3. STYLING THE EXACT MENU ITEMS FROM YOUR BLADE FILE
    ========================================================= */
    ul.menu {
        padding: 10px 0 !important;
        list-style: none !important;
    }

    ul.menu > li {
        margin: 6px 16px !important; /* Creates space for the hover pill */
    }

    ul.menu > li > a {
        color: #64748b !important; /* Slate grey text */
        font-weight: 500 !important;
        font-size: 0.95rem !important;
        padding: 12px 16px !important;
        border-radius: 12px !important; /* Rounded pill shape */
        display: flex !important;
        align-items: center !important;
        background: transparent !important;
        text-decoration: none !important;
        transition: all 0.2s ease !important;
        border: none !important;
    }

    /* Menu Link Hover State */
    ul.menu > li > a:hover {
        background-color: #ffffff !important; 
        color: #d84a38 !important; /* Brand Red text */
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
    }

    /* 4. The Icons inside the links */
    ul.menu > li > a > svg,
    ul.menu > li > a > i {
        background-color: #ffffff !important; /* White box behind icon */
        color: #0f172a !important; /* Dark Slate Icon */
        width: 32px !important;
        height: 32px !important;
        min-width: 32px !important;
        padding: 7px !important; /* Sizes the SVG inside the box */
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 8px !important;
        margin-right: 14px !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
        transition: all 0.2s ease !important;
        stroke-width: 1.5 !important;
    }

    /* =========================================================
       5. ACTIVE STATE (THE RED GRADIENT HIGHLIGHT)
    ========================================================= */
    ul.menu > li > a.active {
        background-color: #ffffff !important; /* Solid white background for active tab */
        color: #0f172a !important; /* Dark text */
        font-weight: 700 !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05) !important;
    }

    /* The Active Icon gets the Red Gradient */
    ul.menu > li > a.active > svg,
    ul.menu > li > a.active > i {
        background: linear-gradient(310deg, #d84a38, #b93d2e) !important; /* Brand Red Gradient */
        color: #ffffff !important; 
        stroke: #ffffff !important; /* Make SVG lines white */
        box-shadow: 0 4px 10px rgba(216, 74, 56, 0.3) !important; /* Red glow */
    }

    /* =========================================================
       6. DARK MODE ADAPTATIONS
    ========================================================= */
    html[data-bs-theme="dark"] aside, 
    html[data-bs-theme="dark"] .sidebar {
        background-color: rgba(27, 37, 49, 0.65) !important;
        border-color: rgba(255, 255, 255, 0.05) !important;
    }
    
    html[data-bs-theme="dark"] ul.menu > li > a {
        color: rgba(255, 255, 255, 0.7) !important;
    }
    
    html[data-bs-theme="dark"] ul.menu > li > a > svg {
        background-color: rgba(0,0,0,0.25) !important;
        color: #ffffff !important;
        stroke: #ffffff !important;
    }

    html[data-bs-theme="dark"] ul.menu > li > a:hover,
    html[data-bs-theme="dark"] ul.menu > li > a.active {
        background-color: rgba(27, 37, 49, 0.9) !important;
        color: #ffffff !important;
    }
</style>

<ul class="menu">
    @foreach (DashboardMenu::getAll('vendor') as $item)
        @continue(! $item['name'])
        <li>
            <a
                href="{{ $item['url'] }}"
                @class(['active' => $item['active']])
            >
                <x-core::icon :name="$item['icon']" />

                {{ $item['name'] }}
            </a>
        </li>
    @endforeach
</ul>