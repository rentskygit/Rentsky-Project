<style>
    .translate-wrapper {
        position: relative;
        display: inline-flex;
    }

    .translate-toggle {
        background: none;
        border: none;
        font-size: 1.1rem;
        color: var(--brand-primary);
        cursor: pointer;
        padding: 0.4rem;
        transition: color 0.15s ease;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
        font-family: inherit;
    }
    .translate-toggle:hover { color: var(--brand-secondary); }

    .translate-toggle .lang-code {
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .translate-menu {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        background: white;
        border: 1px solid var(--border);
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        min-width: 240px;
        max-height: 420px;
        overflow-y: auto;
        z-index: 2000;
        padding: 0.35rem;
        display: none;
    }
    .translate-menu.open { display: block; }

    .translate-menu::-webkit-scrollbar { width: 4px; }
    .translate-menu::-webkit-scrollbar-thumb { background: var(--border-strong); border-radius: 2px; }

    .translate-item {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.6rem 0.75rem;
        border-radius: 4px;
        font-size: 0.83rem;
        font-weight: 500;
        color: var(--brand-primary);
        cursor: pointer;
        transition: background 0.12s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        font-family: inherit;
    }
    .translate-item:hover { background: var(--surface); }
    .translate-item.active {
        background: var(--surface-alt);
        font-weight: 600;
        color: var(--brand-secondary);
    }
    .translate-item .lang-flag {
        font-size: 1rem;
        flex-shrink: 0;
        width: 22px;
        text-align: center;
    }
    .translate-item .lang-check {
        margin-left: auto;
        color: var(--brand-secondary);
        font-size: 0.9rem;
        opacity: 0;
        transition: opacity 0.15s;
    }
    .translate-item.active .lang-check { opacity: 1; }

    /* Hide Google Translate's injected banner and iframe */
    body { top: 0 !important; }
    .skiptranslate iframe { display: none !important; }
    .goog-te-banner-frame { display: none !important; }
    #google_translate_element { display: none !important; }
    .goog-te-gadget { display: none !important; }
    .goog-logo-link { display: none !important; }
    .goog-te-gadget .goog-te-combo { display: none !important; }

    @media (max-width: 480px) {
        .translate-menu { right: -0.75rem; min-width: 200px; }
    }
</style>

<div class="translate-wrapper">
    <button type="button"
            class="translate-toggle"
            id="translateToggle"
            title="Change language"
            aria-label="Change language">
        <i class="bi bi-translate"></i>
        <span class="lang-code" id="currentLangCode">EN</span>
    </button>

    <div class="translate-menu" id="translateMenu" role="menu"></div>
</div>

<div id="google_translate_element" style="display:none;"></div>

<script>
(function () {
    const LANGUAGES = [
        { code: 'en',    name: 'English',           flag: '🇺🇸' },
        { code: 'es',    name: 'Español',           flag: '🇪🇸' },
        { code: 'pt',    name: 'Português',         flag: '🇵🇹' },
        { code: 'ht',    name: 'Kreyòl Ayisyen',    flag: '🇭🇹' },
        { code: 'zh-CN', name: '简体中文',           flag: '🇨🇳' },
        { code: 'vi',    name: 'Tiếng Việt',        flag: '🇻🇳' },
        { code: 'fr',    name: 'Français',          flag: '🇫🇷' },
        { code: 'de',    name: 'Deutsch',           flag: '🇩🇪' },
        { code: 'it',    name: 'Italiano',          flag: '🇮🇹' },
        { code: 'ar',    name: 'العربية',           flag: '🇸🇦' },
        { code: 'ru',    name: 'Русский',           flag: '🇷🇺' },
        { code: 'ja',    name: '日本語',            flag: '🇯🇵' },
        { code: 'ko',    name: '한국어',            flag: '🇰🇷' },
        { code: 'hi',    name: 'हिन्दी',             flag: '🇮🇳' },
        { code: 'tr',    name: 'Türkçe',            flag: '🇹🇷' },
        { code: 'pl',    name: 'Polski',            flag: '🇵🇱' },
        { code: 'nl',    name: 'Nederlands',        flag: '🇳🇱' },
        { code: 'el',    name: 'Ελληνικά',          flag: '🇬🇷' },
        { code: 'he',    name: 'עברית',             flag: '🇮🇱' },
        { code: 'th',    name: 'ไทย',               flag: '🇹🇭' },
        { code: 'id',    name: 'Bahasa Indonesia',  flag: '🇮🇩' },
        { code: 'uk',    name: 'Українська',        flag: '🇺🇦' },
    ];

    const $toggle = document.getElementById('translateToggle');
    const $menu   = document.getElementById('translateMenu');
    const $code   = document.getElementById('currentLangCode');

    if (!$toggle || !$menu) return;

    function getCurrentLang() {
        const match = document.cookie.match(/(?:^|;\s*)googtrans=\/en\/([^;]+)/);
        return match && match[1] ? decodeURIComponent(match[1]) : 'en';
    }

    function setCookie(name, value, days) {
        const expires = days
            ? '; expires=' + new Date(Date.now() + days * 864e5).toUTCString()
            : '';
        const domains = ['', location.hostname, '.' + location.hostname];
        domains.forEach((d) => {
            document.cookie = name + '=' + value + '; path=/' + expires + (d ? '; domain=' + d : '');
        });
    }

    function deleteCookie(name) {
        const domains = ['', location.hostname, '.' + location.hostname];
        domains.forEach((d) => {
            document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/' + (d ? '; domain=' + d : '');
        });
    }

    function setLanguage(lang) {
        if (lang === 'en') {
            deleteCookie('googtrans');
        } else {
            setCookie('googtrans', '/en/' + lang, 365);
        }
        location.reload();
    }

    function renderMenu() {
        const current = getCurrentLang();
        const shortCode = current.split('-')[0].toUpperCase();

        if ($code) $code.textContent = shortCode;

        $menu.innerHTML = LANGUAGES.map((lang) => {
            const isActive = lang.code === current;
            return `
                <button type="button"
                        class="translate-item ${isActive ? 'active' : ''}"
                        data-lang="${lang.code}"
                        role="menuitem">
                    <span class="lang-flag">${lang.flag}</span>
                    <span>${lang.name}</span>
                    <i class="bi bi-check2 lang-check"></i>
                </button>
            `;
        }).join('');

        $menu.querySelectorAll('.translate-item').forEach((btn) => {
            btn.addEventListener('click', function () {
                setLanguage(this.dataset.lang);
            });
        });
    }

    $toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        $menu.classList.toggle('open');
    });

    document.addEventListener('click', function (e) {
        if (!$menu.contains(e.target) && !$toggle.contains(e.target)) {
            $menu.classList.remove('open');
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') $menu.classList.remove('open');
    });

    renderMenu();

    if (getCurrentLang() !== 'en') {
        window.googleTranslateElementInit = function () {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: LANGUAGES.map((l) => l.code).filter((c) => c !== 'en').join(','),
                autoDisplay: false,
            }, 'google_translate_element');
        };

        const script = document.createElement('script');
        script.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
        script.async = true;
        document.head.appendChild(script);
    }
})();
</script>