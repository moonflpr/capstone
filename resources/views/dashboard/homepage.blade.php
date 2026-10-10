<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">document.documentElement.classList.add('js')</script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Math Learning is an AI-powered math learning platform for junior high school students, featuring interactive modules, quizzes, and progress tracking.">
    <meta name="theme-color" content="#1e4e7f">

    <title>Math Learning Assistant</title>

    <link rel="icon" type="image/png" href="{{ asset('image/587572187-777024998723535-6772324307557000990-n-fotor-20260519155328.png') }}">

    <!-- ================= SELF-HOSTED FONT (Plus Jakarta Sans, same as the dashboards and sign-in) ================= -->
    <link rel="preload" href="/fonts/plus-jakarta-sans-latin-400-800.woff2" as="font" type="font/woff2" crossorigin>

    <!-- ================= CSS / JS =================
         The hero no longer uses a photo, so the old image preload is gone.
         homepage.css stays inlined to keep it off the critical request path. -->
    {{-- Falls back to a linked tag while the Vite dev server is running. --}}
    @if (app(\Illuminate\Foundation\Vite::class)->isRunningHot())
        @vite('resources/css/homepage.css')
    @else
        <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">{!! \Illuminate\Support\Facades\Vite::content('resources/css/homepage.css') !!}</style>
    @endif

    @vite([
        'resources/js/homepage.js',
        'resources/js/nav-progress.js'
    ])

</head>

<body>

@php
    // Pass $stats from the controller to show real numbers. Until then these
    // defaults only state things that are true of the platform itself.
    // 'students' stays null (shows "24/7 AI tutor help" instead) until you
    // have a real student count to show.
    $stats = array_merge(['modules' => 3, 'topics' => 12, 'students' => null], $stats ?? []);

    // Social links: set real URLs here (or pass $socials from the controller).
    // Entries without a URL are not rendered.
    $socials = $socials ?? [
        ['label' => 'Facebook', 'icon' => 'i-facebook', 'url' => null],
        ['label' => 'YouTube',  'icon' => 'i-youtube',  'url' => null],
    ];
    // Teacher-dashboard preview. The homepage route passes $overview from
    // App\Services\PlatformOverview: platform-wide AGGREGATE numbers only
    // (no student names or personal progress, this page is public):
    //   $overview = [
    //       'students'         => 15,
    //       'avg_progress'     => 1,     // percent, 0-100
    //       'pending_feedback' => 0,
    //       'modules'          => [['name' => 'Sequences and Series', 'avg' => 20], ...],
    //   ];
    // It's null until a student account is approved; the preview then falls
    // back to clearly labelled sample data.
    $isLive = isset($overview);
    $overview = $overview ?? [
        'students' => 38,
        'avg_progress' => 82,
        'pending_feedback' => 4,
        'modules' => [
            ['name' => 'Sequences and Series', 'avg' => 88],
            ['name' => 'Polynomials and Polynomial Equations', 'avg' => 74],
            ['name' => 'Advanced Equations and Functions', 'avg' => 61],
        ],
    ];
@endphp

<!-- ================= ICON SPRITE ================= -->
<svg class="sprite" aria-hidden="true" focusable="false">
    <defs>
        <linearGradient id="brand-grad" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="#60a5fa"/>
            <stop offset="1" stop-color="#2563eb"/>
        </linearGradient>
    </defs>
    {{-- Same mark as the dashboards' .logo-icon: a book on the AI Chat blue gradient. --}}
    <symbol id="logo-mark" viewBox="0 0 32 32">
        <rect width="32" height="32" rx="8" fill="url(#brand-grad)"/>
        <g transform="translate(8 8) scale(0.6667)" fill="none" stroke="#fff" stroke-width="3.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
        </g>
    </symbol>
    <symbol id="i-trend" viewBox="0 0 24 24"><path d="M23 6l-9.5 9.5-5-5L1 18"/><path d="M17 6h6v6"/></symbol>
    <symbol id="i-file" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8"/></symbol>
    <symbol id="i-bot" viewBox="0 0 24 24"><rect x="4" y="8" width="16" height="12" rx="3"/><path d="M12 4v4M9 13v1M15 13v1M2 13v2M22 13v2"/></symbol>
    <symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></symbol>
    <symbol id="i-close" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></symbol>
    <symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14m0 0-6-6m6 6-6 6"/></symbol>
    <symbol id="i-check" viewBox="0 0 24 24"><path d="m5 12.5 4.5 4.5L19 7.5"/></symbol>
    <symbol id="i-users" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.2"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><path d="M16 5.2a3.2 3.2 0 0 1 0 5.6M18 14.3c2 .8 3.5 2.8 3.5 5.7"/></symbol>
    <symbol id="i-user-plus" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><path d="M19 8v6M16 11h6"/></symbol>
    <symbol id="i-chevron" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></symbol>
    <symbol id="i-facebook" viewBox="0 0 24 24"><path d="M14.5 21v-8h2.7l.5-3.2h-3.2V7.9c0-.9.4-1.6 1.7-1.6h1.6V3.4c-.3 0-1.3-.2-2.3-.2-2.4 0-4 1.5-4 4.1v2.5H8.8V13h2.7v8z"/></symbol>
    <symbol id="i-youtube" viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="12" rx="3.5"/><path d="m10.5 9.5 4 2.5-4 2.5z"/></symbol>
</svg>

<a class="skip" href="#main">Skip to content</a>

<!-- ================= NAV ================= -->
<header class="nav" data-nav>
    <div class="container nav__inner">

        <a href="#top" class="logo" aria-label="Math Learning, back to top">
            <svg class="logo__mark" viewBox="0 0 32 32" aria-hidden="true"><use href="#logo-mark"/></svg>
            <span>Math Learn</span>
        </a>

        <button class="nav__toggle" type="button" data-nav-toggle
                aria-expanded="false" aria-controls="nav-panel" aria-label="Open menu">
            <svg class="icon i-open" aria-hidden="true"><use href="#i-menu"/></svg>
            <svg class="icon i-close" aria-hidden="true"><use href="#i-close"/></svg>
        </button>

        <div class="nav__panel" id="nav-panel" data-nav-panel>
            <nav aria-label="Primary">
                <ul class="nav__links">
                    <li><a href="#features" data-spy>Features</a></li>
                    <li><a href="#modules" data-spy>Modules</a></li>
                    <li><a href="#dashboards" data-spy>Accounts</a></li>
                    <li><a href="#faq" data-spy>FAQ</a></li>
                </ul>
            </nav>

            <div class="nav__actions">
                <a href="{{ route('signin-signin') }}" class="btn btn--ghost btn--sm">Sign In</a>
                <a href="{{ route('signin-signup') }}" class="btn btn--primary btn--sm">
                    <svg class="icon" aria-hidden="true"><use href="#i-user-plus"/></svg>
                    Sign Up
                </a>
            </div>
        </div>

    </div>
</header>

<main id="main">

<!-- ================= HERO ================= -->
<section class="hero" id="top" aria-labelledby="hero-title">

    <div class="container hero__inner">

        <div class="hero__copy">

            <p class="hero__school" data-enter="1">Bubog National High School · Junior High Math</p>

            <h1 class="hero__title" id="hero-title">
                <span data-enter="1">Every problem</span>
                <span data-enter="2">has a next step.</span>
            </h1>

            <p class="hero__sub">
                {{ $platformDescription ?? 'Interactive lessons, quizzes with instant feedback, and an AI tutor that explains each step. Made for Bubog National High School students and the teachers who guide them.' }}
            </p>

            <div class="hero__cta" data-enter="3">
                <a href="{{ route('signin-signup') }}" class="btn btn--primary">
                    Create your account
                    <svg class="icon icon--go" aria-hidden="true"><use href="#i-arrow"/></svg>
                </a>
                <a href="{{ route('signin-signin') }}" class="btn btn--ghost-light">Sign in</a>
            </div>

            <p class="hero__note" data-enter="4">
                Students, teachers, and admins sign in with their school email or Google.
            </p>

        </div>

        <!-- Hero visual: a real Module 1 problem, solved on the board the way a
             teacher would write it (statement on the left, reason on the right).
             It's real text, so screen readers get the same solution. -->
        <figure class="board" data-enter="3" aria-labelledby="board-title">
            <figcaption class="board__head">
                <span class="board__topic">Module 1 · Arithmetic Sequence</span>
                <span class="board__title" id="board-title">Find the 10th term of <span class="math">3, 7, 11, 15, …</span></span>
            </figcaption>

            <ol class="board__steps">
                <li class="board__step" data-enter="5">
                    <span class="math"><var>d</var> = 7 − 3 = 4</span>
                    <span class="board__why">Common difference</span>
                </li>
                <li class="board__step" data-enter="6">
                    <span class="math"><var>a</var><sub><var>n</var></sub> = <var>a</var><sub>1</sub> + (<var>n</var> − 1)<var>d</var></span>
                    <span class="board__why">Formula</span>
                </li>
                <li class="board__step" data-enter="7">
                    <span class="math"><var>a</var><sub>10</sub> = 3 + (10 − 1)(4)</span>
                    <span class="board__why">Substitute <span class="math"><var>n</var> = 10</span></span>
                </li>
                <li class="board__step board__step--answer" data-enter="8">
                    <span class="math"><span class="board__box"><var>a</var><sub>10</sub> = 39</span></span>
                    <span class="board__why">Answer</span>
                </li>
            </ol>

            <p class="board__hint" data-enter="8">
                Stuck on a step? The Math AI Assistant explains it.
                <a href="#features">See how</a>
            </p>
        </figure>

    </div>

</section>

<!-- ================= AT A GLANCE =================
     Plain numbers on a ruled line, no cards or icons. -->
<section class="stats" aria-label="Math Learning at a glance">
    <div class="container stats__grid">

        <p class="stat">
            <b class="stat__num">{{ $stats['modules'] }}</b>
            <span class="stat__label">modules, from sequences to logarithms</span>
        </p>

        <p class="stat">
            <b class="stat__num">{{ $stats['topics'] }}</b>
            <span class="stat__label">topics, each with a pre-test and a post-test</span>
        </p>

        <p class="stat">
            @if (! empty($stats['students']))
                <b class="stat__num">{{ number_format($stats['students']) }}</b>
                <span class="stat__label">students learning</span>
            @else
                <b class="stat__num">24/7</b>
                <span class="stat__label">help from the AI tutor, even the night before a test</span>
            @endif
        </p>

    </div>
</section>

<!-- ================= FEATURES ================= -->
<section class="section" id="features" aria-labelledby="features-title">
    <div class="container">

        <div class="section__head reveal">
            <h2 id="features-title">What's inside</h2>
            <p>Lessons, quizzes, and a tutor for Junior High math, made to work on the phone in your pocket.</p>
        </div>

        <div class="exhibits">

            <article class="tile tile--big reveal">
                <div>
                    <h3>A tutor that shows its work</h3>
                    <p>Ask the way you'd ask a classmate. The Math AI Assistant walks you through the steps instead of only giving the answer.</p>
                </div>
                <div class="tile__visual mini-chat" aria-hidden="true">
                    <div class="mini-chat__head">
                        <span class="chat-card__avatar"><svg class="icon"><use href="#i-bot"/></svg></span>
                        <span><strong>Math AI Assistant</strong><span class="sub">Online</span></span>
                    </div>
                    <div class="mini-chat__body">
                        <p class="bubble bubble--user">Paano hanapin ang roots ng x² − 4x + 3?</p>
                        <p class="bubble bubble--ai">I-factor muna: <b>(x − 1)(x − 3) = 0</b>, kaya x = 1 o x = 3.</p>
                    </div>
                    <ul class="chips">
                        <li class="chip">Explain factoring</li>
                        <li class="chip">Check my answer</li>
                        <li class="chip">Show another example</li>
                    </ul>
                </div>
            </article>

            <article class="tile tile--big reveal">
                <div>
                    <h3>Quizzes that tell you why</h3>
                    <p>See right away which answers are right, so you know what to review before the post-test.</p>
                </div>
                <div class="tile__visual mini-quiz" aria-hidden="true">
                    <p class="mini-quiz__q">Which number comes next? 3, 7, 11, …</p>
                    <div class="opt"><span>13</span></div>
                    <div class="opt"><span>14</span></div>
                    <div class="opt opt--right">
                        <span>15</span>
                        <svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>
                    </div>
                    <p class="mini-quiz__fb"><b>Tama!</b> The pattern adds 4 each time.</p>
                </div>
            </article>

        </div>

        <ul class="points">
            <li class="point reveal">
                <h3>Topics open in order</h3>
                <p>Each topic opens after you finish the one before it, so you never skip a step you'll need later.</p>
            </li>
            <li class="point reveal">
                <h3>Your progress is saved</h3>
                <p>Your dashboard keeps your overall progress, the topics you've finished, and your daily streak.</p>
            </li>
            <li class="point reveal">
                <h3>Study without data</h3>
                <p>Download materials from Offline Materials and keep studying when your load runs out.</p>
            </li>
            <li class="point reveal">
                <h3>Notes from your teacher</h3>
                <p>Your teacher can send you feedback on your work, right on your dashboard.</p>
            </li>
        </ul>

    </div>
</section>

<!-- ================= HOW EACH TOPIC WORKS =================
     The four phases every topic has in the app, drawn on a number line. -->
<section class="section section--white" id="how-it-works" aria-labelledby="how-title">
    <div class="container">

        <div class="section__head reveal">
            <h2 id="how-title">How each topic works</h2>
            <p>All twelve topics follow the same four steps, so you always know what comes next.</p>
        </div>

        <ol class="steps reveal">
            <li class="step">
                <span class="step__num" aria-hidden="true">1</span>
                <h3>Pre-test</h3>
                <p>A short quiz shows what you already know before the lesson starts.</p>
            </li>
            <li class="step">
                <span class="step__num" aria-hidden="true">2</span>
                <h3>Lesson</h3>
                <p>Read the lesson and its worked examples at your own pace.</p>
            </li>
            <li class="step">
                <span class="step__num" aria-hidden="true">3</span>
                <h3>Activity</h3>
                <p>Practice problems. Pass the activity to open the post-test.</p>
            </li>
            <li class="step">
                <span class="step__num" aria-hidden="true">4</span>
                <h3>Post-test</h3>
                <p>The final check. Your first attempt is the one that counts, and you can practice again after.</p>
            </li>
        </ol>

        <p class="steps__note reveal">
            During a pre-test, activity, or post-test, leaving the page submits your answers. Stay on the quiz until you finish.
        </p>

    </div>
</section>

<!-- ================= MODULES =================
     A table of contents: the three modules in the app, with their real
     topic names. Topics inside each module unlock in order. -->
<section class="section" id="modules" aria-labelledby="modules-title">
    <div class="container">

        <div class="section__head reveal">
            <h2 id="modules-title">What you'll study</h2>
            <p>Three modules and twelve topics. Each module opens once you finish the one before it.</p>
        </div>

        <div class="modules">

            <a href="{{ route('signin-signin') }}" class="module module--blue lift reveal">
                <span class="module__num" aria-hidden="true">01</span>
                <div class="module__body">
                    <span class="module__label">Module 1 · 5 topics</span>
                    <h3>Sequences and Series</h3>
                    <p>Spot the pattern, find the next term, and add up a series.</p>
                    <span class="module__go">Sign in to start <svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></span>
                </div>
                <ul class="module__topics" aria-label="Topics">
                    <li>Arithmetic Sequence</li><li>Geometric Sequence</li><li>Harmonic Sequence</li><li>Fibonacci Sequence</li><li>Finite and Infinite Sequence</li>
                </ul>
                <div class="module__art">
                    <svg viewBox="0 0 200 125" aria-hidden="true" focusable="false">
                        <line class="art-axis" x1="16" y1="88" x2="184" y2="88"/>
                        <path class="art-soft" d="M30 80Q50 42 70 80"/>
                        <path class="art-soft" d="M70 80Q90 42 110 80"/>
                        <path class="art-soft" d="M110 80Q130 42 150 80"/>
                        <circle class="art-dot" cx="30" cy="88" r="5"/>
                        <circle class="art-dot" cx="70" cy="88" r="5"/>
                        <circle class="art-dot" cx="110" cy="88" r="5"/>
                        <circle class="art-dot" cx="150" cy="88" r="5"/>
                        <text class="art-txt" x="50" y="54" text-anchor="middle">+4</text>
                        <text class="art-txt" x="90" y="54" text-anchor="middle">+4</text>
                        <text class="art-txt" x="130" y="54" text-anchor="middle">+4</text>
                        <text class="art-txt" x="30" y="110" text-anchor="middle">3</text>
                        <text class="art-txt" x="70" y="110" text-anchor="middle">7</text>
                        <text class="art-txt" x="110" y="110" text-anchor="middle">11</text>
                        <text class="art-txt" x="150" y="110" text-anchor="middle">15</text>
                    </svg>
                </div>
            </a>

            <a href="{{ route('signin-signin') }}" class="module module--green lift reveal">
                <span class="module__num" aria-hidden="true">02</span>
                <div class="module__body">
                    <span class="module__label">Module 2 · 3 topics</span>
                    <h3>Polynomials and Polynomial Equations</h3>
                    <p>Divide polynomials, use the remainder and factor theorems, and solve polynomial equations.</p>
                    <span class="module__go">Sign in to start <svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></span>
                </div>
                <ul class="module__topics" aria-label="Topics">
                    <li>Division of Polynomials</li><li>The Remainder Theorem and Factor Theorem</li><li>Polynomial Equations</li>
                </ul>
                <div class="module__art">
                    <svg viewBox="0 0 200 125" aria-hidden="true" focusable="false">
                        <line class="art-axis" x1="16" y1="68" x2="184" y2="68"/>
                        <line class="art-axis" x1="100" y1="14" x2="100" y2="112"/>
                        <path class="art-line" d="M28 98C68 98 78 22 100 66S138 112 172 30"/>
                        <text class="art-txt" x="150" y="24">f(x)</text>
                    </svg>
                </div>
            </a>

            <a href="{{ route('signin-signin') }}" class="module module--orange lift reveal">
                <span class="module__num" aria-hidden="true">03</span>
                <div class="module__body">
                    <span class="module__label">Module 3 · 4 topics</span>
                    <h3>Advanced Equations and Functions</h3>
                    <p>Take on harder equations and see how functions behave on a graph.</p>
                    <span class="module__go">Sign in to start <svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></span>
                </div>
                <ul class="module__topics" aria-label="Topics">
                    <li>Rational Functions</li><li>Radical Equations</li><li>Exponential Functions</li><li>Logarithmic Functions</li>
                </ul>
                <div class="module__art">
                    <svg viewBox="0 0 200 125" aria-hidden="true" focusable="false">
                        <line class="art-axis" x1="16" y1="90" x2="184" y2="90"/>
                        <line class="art-axis" x1="100" y1="14" x2="100" y2="112"/>
                        <path class="art-line" d="M46 20Q100 152 154 20"/>
                        <path class="art-soft" d="M28 104L172 40"/>
                        <text class="art-txt" x="150" y="24">y</text>
                    </svg>
                </div>
            </a>

        </div>
    </div>
</section>

<!-- ================= TEACHERS ================= -->
<section class="section section--tint" id="teachers" aria-labelledby="teachers-title">
    <div class="container split">

        <div class="reveal">
            <h2 id="teachers-title">For teachers: see how every section is doing</h2>
            <p class="lead">Approve your students, follow their progress by module, and send feedback, without collecting a single paper.</p>

            <ul class="checklist">
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>See results as soon as students submit</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>See each student's progress by module</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Send personalized feedback to students</li>
                <li><svg class="icon" aria-hidden="true"><use href="#i-check"/></svg>Generate reports, keep a class record, and create AI-powered pre-tests and post-tests</li>
            </ul>

            <a href="{{ route('signin-signup') }}" class="btn btn--primary">
                Sign up as a teacher
                <svg class="icon icon--go" aria-hidden="true"><use href="#i-arrow"/></svg>
            </a>
        </div>

        <!-- Dashboard preview: real aggregates when $overview is passed, sample data otherwise -->
        <div class="dash reveal" role="img"
             aria-label="Preview of the teacher dashboard showing total students, average progress, pending feedback, and average progress by module.">

            <div class="dash__head" aria-hidden="true">
                <div>
                    <strong>{{ $isLive ? 'School overview' : 'Class overview' }}</strong>
                    <span class="sub">{{ $isLive ? 'All sections · refreshed every '.\App\Services\PlatformOverview::CACHE_MINUTES.' minutes' : 'Grade 10 · Rizal' }}</span>
                </div>
                @if ($isLive)
                    <span class="tag tag--live"><span class="tag__dot"></span>Live data</span>
                @else
                    <span class="tag">Sample data</span>
                @endif
            </div>

            <div class="dash__stats" aria-hidden="true">
                <div class="metric">
                    <div class="metric__head">
                        <span class="metric__label">Total students</span>
                        <span class="metric__icon metric__icon--blue"><svg class="icon"><use href="#i-users"/></svg></span>
                    </div>
                    <b class="metric__value">{{ number_format($overview['students']) }}</b>
                </div>
                <div class="metric">
                    <div class="metric__head">
                        <span class="metric__label">Avg. progress</span>
                        <span class="metric__icon metric__icon--green"><svg class="icon"><use href="#i-trend"/></svg></span>
                    </div>
                    <b class="metric__value">{{ (int) $overview['avg_progress'] }}%</b>
                </div>
                <div class="metric">
                    <div class="metric__head">
                        <span class="metric__label">Pending feedback</span>
                        <span class="metric__icon metric__icon--orange"><svg class="icon"><use href="#i-file"/></svg></span>
                    </div>
                    <b class="metric__value">{{ number_format($overview['pending_feedback']) }}</b>
                </div>
            </div>

            @if (! empty($overview['modules']))
                <p class="dash__title" aria-hidden="true">Average progress by module</p>
                <div class="rows" aria-hidden="true">
                    @foreach ($overview['modules'] as $module)
                        @php $avg = max(0, min(100, (int) $module['avg'])); @endphp
                        <div>
                            {{-- Same colors as the dashboards' progress bars: blue in progress, green complete. --}}
                            <div class="row__top"><span>{{ $module['name'] }}</span><span class="row__pct{{ $avg >= 100 ? ' row__pct--done' : '' }}">{{ $avg }}%</span></div>
                            {{-- Width is set by homepage.js from data-width (no inline style, CSP-safe). --}}
                            <div class="bar"><span class="bar__fill{{ $avg >= 100 ? ' bar__fill--done' : '' }}" data-width="{{ $avg }}"></span></div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

    </div>
</section>

<!-- ================= ACCOUNTS (one dashboard per role) =================
     Mirrors the three real dashboards: what each role sees after signing in.
     Set as three ruled columns, not cards. -->
<section class="section section--white" id="dashboards" aria-labelledby="dashboards-title">
    <div class="container">

        <div class="section__head reveal">
            <h2 id="dashboards-title">Three kinds of accounts</h2>
            <p>Each account opens its own dashboard. Students learn, teachers guide, and admins keep everything running.</p>
        </div>

        <div class="roles">

            <article class="role role--blue lift reveal">
                <h3>Student</h3>
                <p>Learn one topic at a time and see exactly how far you've come.</p>
                <ul class="role__list">
                    <li>Modules with pre-tests and post-tests</li>
                    <li>Math AI Assistant with a calculator</li>
                    <li>Progress, streak, and the summative test</li>
                    <li>Teacher feedback and offline materials</li>
                </ul>
                <a href="{{ route('student.login') }}" class="role__go">Sign in as a student <svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
            </article>

            <article class="role role--green lift reveal">
                <h3>Teacher</h3>
                <p>Guide each section, from new sign-ups to the final class record.</p>
                <ul class="role__list">
                    <li>Approve students and manage sections</li>
                    <li>Class record and reports as PDF or Excel</li>
                    <li>AI quiz generator for tests and activities</li>
                    <li>Upload modules and send feedback</li>
                </ul>
                <a href="{{ route('teacher.login') }}" class="role__go">Sign in as a teacher <svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
            </article>

            <article class="role role--purple lift reveal">
                <h3>Admin</h3>
                <p>Keep accounts, content, and the platform itself in good shape.</p>
                <ul class="role__list">
                    <li>Users, roles, and teacher approvals</li>
                    <li>Analytics on sign-ups and completion</li>
                    <li>Review and approve learning materials</li>
                    <li>Activity log, settings, and maintenance mode</li>
                </ul>
                <a href="{{ route('admin.login') }}" class="role__go">Admin sign in <svg class="icon" aria-hidden="true"><use href="#i-arrow"/></svg></a>
            </article>

        </div>
    </div>
</section>

<!-- ================= FAQ =================
     Answers match how the app actually works (approvals, Google sign-in,
     offline materials). Update them if those flows change. -->
<section class="section" id="faq" aria-labelledby="faq-title">
    <div class="container faq">

        <div class="section__head reveal">
            <h2 id="faq-title">Before you sign up</h2>
            <p>What students and teachers ask us most.</p>
        </div>

        <div class="faq__list reveal">

            <details class="faq__item">
                <summary>Is Math Learning free?<svg class="icon" aria-hidden="true"><use href="#i-chevron"/></svg></summary>
                <p>Yes. Creating a student or teacher account costs nothing. All you need is your email address or a Google account.</p>
            </details>

            <details class="faq__item">
                <summary>Why can't I sign in right after signing up?<svg class="icon" aria-hidden="true"><use href="#i-chevron"/></svg></summary>
                <p>New accounts are approved first. The teacher of the section you picked approves student accounts, and an administrator approves teacher accounts. You can sign in as soon as yours is approved.</p>
            </details>

            <details class="faq__item">
                <summary>Can I sign in with Google?<svg class="icon" aria-hidden="true"><use href="#i-chevron"/></svg></summary>
                <p>Yes. Choose "Continue with Google" on the sign-in or sign-up page. The first time, students also pick their section and enter their student ID.</p>
            </details>

            <details class="faq__item">
                <summary>Can I study without internet?<svg class="icon" aria-hidden="true"><use href="#i-chevron"/></svg></summary>
                <p>Partly. Offline Materials on your dashboard lets you download assessments and practice without data. The AI tutor, quizzes, and progress tracking need a connection.</p>
            </details>

            <details class="faq__item">
                <summary>How does the AI tutor help?<svg class="icon" aria-hidden="true"><use href="#i-chevron"/></svg></summary>
                <p>Ask it a math question and it explains the steps, not just the answer. It also has a calculator tab. AI can make mistakes, so check important work with your teacher.</p>
            </details>

            <details class="faq__item">
                <summary>Who can see my progress?<svg class="icon" aria-hidden="true"><use href="#i-chevron"/></svg></summary>
                <p>You, your teachers, and the school's administrators. Read the <a href="#privacy">Privacy Policy</a> for the details.</p>
            </details>

        </div>
    </div>
</section>

<!-- ================= FINAL CTA =================
     Full-bleed navy band, flush on every side (edge to edge, no gap above
     or below). Only the content sits in the container. -->
<section class="cta" aria-labelledby="cta-title">
    <div class="cta__panel">
        <div class="container cta__inner reveal">

            <div class="cta__copy">
                <h2 id="cta-title">Start with Module 1.</h2>
                <p>Create your account. Once your teacher approves it, your first topic is waiting: the arithmetic sequence.</p>

                <div class="cta__actions">
                    <a href="{{ route('signin-signup') }}" class="btn btn--primary">
                        Create your account
                        <svg class="icon icon--go" aria-hidden="true"><use href="#i-arrow"/></svg>
                    </a>
                    <a href="{{ route('signin-signin') }}" class="btn btn--ghost-light">Sign in</a>
                </div>

                <p class="cta__terms">By creating an account you agree to our <a href="#terms">Terms and Conditions</a> and <a href="#privacy">Privacy Policy</a>.</p>
            </div>

            {{-- Same brand panel as the sign-in page: the school seal on deep navy. --}}
            <div class="cta__brand">
                <div class="cta__seal">
                    <img src="{{ asset('image/587572187-777024998723535-6772324307557000990-n-fotor-20260519155328.png') }}"
                         alt="Bubog National High School seal" width="354" height="354" loading="lazy" decoding="async">
                </div>
                <p class="cta__school">Bubog National High School</p>
                <span class="cta__dots" aria-hidden="true"><i></i><i></i><i></i></span>
            </div>

        </div>
    </div>
</section>

<!-- ================= LEGAL =================
     Privacy Policy and Terms live on the homepage for now. #privacy and #terms
     (footer, CTA, sign-up page) open the matching panel via homepage.js.
     -->
@php
    // Optional: create config/legal.php (or set these keys) to show a real DPO contact.
    // Without it the text says "the school office".
    $legal = config('legal', []);
    $effective = $legal['effective_date'] ?? 'September 21, 2026';
    $legalEmail = $legal['contact_email'] ?? null;
    $contact = $legalEmail ? '<a href="mailto:'.e($legalEmail).'">'.e($legalEmail).'</a>' : 'the school office';
    $dpo = $legal['dpo_name'] ?? null;
@endphp
<section class="section legal-home" id="legal" aria-labelledby="legal-title">
    <div class="container">

        <div class="section__head reveal">
            <h2 id="legal-title">Privacy Policy and Terms and Conditions</h2>
            <p>How we handle your data, and the rules for using Math Learning. Select a heading to read it.</p>
        </div>

        <div class="legal-home__list">

            <details class="legal-doc" id="privacy" data-legal>
                <summary>
                    <span>Privacy Policy</span>
                    <svg class="icon" aria-hidden="true"><use href="#i-chevron"/></svg>
                </summary>
                <div class="legal legal--inline">
                    <p class="legal__meta">Effective {{ $effective }}. Applies to the Math Learning platform of Bubog National High School.</p>
                    <div class="legal__summary">
                        <strong>In short</strong>
                        We collect only what we need to run your account and help you learn math. Your teachers and the school's administrators can see your learning progress. We do not sell your personal data. You can ask us to show, correct, or delete your data at any time.
                    </div>
                    <h3 id="pp-who-we-are">1. Who we are</h3>
                    <p>Math Learning is an online math learning platform run by Bubog National High School in San Jose, Occidental Mindoro (the "school", "we", "us"). The school decides why and how personal data on this platform is used. Under the Data Privacy Act of 2012 (Republic Act No. 10173), the school is the personal information controller.</p>
                    <p>Our Data Protection Officer{{ $dpo ? ' ('.$dpo.')' : '' }} can be reached through {!! $contact !!}.</p>

                    <h3 id="pp-what-we-collect">2. What we collect</h3>
                    <ul class="legal__list">
                        <li><strong>Account details:</strong> your name, email address, role (student, teacher, or admin), and a password, which we store in a protected (hashed) form. If you sign in with Google, we receive your name and email address from Google.</li>
                        <li><strong>Learning data:</strong> the modules and topics you open or finish, your progress, your quiz and test answers and scores, and your learning streak.</li>
                        <li><strong>AI chatbot conversations:</strong> the questions you type and the answers you receive.</li>
                        <li><strong>Teacher feedback and class records:</strong> feedback your teachers send you, and the reports and class records they create.</li>
                        <li><strong>Activity and technical data:</strong> sign-in and activity records (for example, when you log in), basic technical data such as your browser type, and the cookies described below.</li>
                    </ul>
                    <p>We do not need sensitive information such as your home address, government ID numbers, or health details. Please do not type them into your profile or the chatbot.</p>

                    <h3 id="pp-why-we-use-it">3. Why we use your data</h3>
                    <ul class="legal__list">
                        <li>To create and manage your account and let you sign in.</li>
                        <li>To give you modules, quizzes, tests, the AI chatbot, and offline materials.</li>
                        <li>To record your progress and show it to you and your teachers.</li>
                        <li>To let teachers send feedback, make reports and class records, and generate quizzes.</li>
                        <li>To keep the platform secure and prevent misuse.</li>
                        <li>To improve lessons and the platform, using summarized results wherever possible.</li>
                        <li>To follow the law and school requirements.</li>
                    </ul>
                    <p>We rely on your consent (or your parent's or guardian's consent, if you are under 18) and on the school's legitimate educational purposes.</p>

                    <h3 id="pp-students-and-parents">4. Students and parents or guardians</h3>
                    <p>Many of our students are minors. For students under 18, the school asks for a parent or guardian to agree to the account. Parents and guardians can ask to see, correct, or delete their child's data. If you are a parent or guardian and did not agree to an account made for your child, contact us and we will act on it.</p>
                    <p>If you are a student and something here is unclear, please ask your teacher or a trusted adult to read it with you.</p>

                    <h3 id="pp-who-can-see-it">5. Who can see your data</h3>
                    <ul class="legal__list">
                        <li><strong>You</strong> can see your own account, progress, and results.</li>
                        <li><strong>Your teachers</strong> can see your progress, test results, and class records so they can guide you.</li>
                        <li><strong>School administrators</strong> manage accounts and the platform, and can see activity records and platform-level analytics.</li>
                        <li><strong>Service providers</strong> that help us run the platform, such as Google (if you use Google sign-in), our hosting provider, and the AI service that produces chatbot answers and generated quizzes. They may only use data to provide their service to us.</li>
                        <li><strong>Authorities</strong>, only when the law requires it or a court or government agency lawfully asks.</li>
                    </ul>
                    <p>We do not sell your personal data and we do not use it for advertising.</p>

                    <h3 id="pp-ai-chatbot">6. The AI chatbot</h3>
                    <p>The chatbot uses an AI service to answer math questions. Your messages are sent to that service so it can reply. AI answers can be wrong, so check important work with your teacher. Never share passwords, your address, phone number, or other private details in the chatbot.</p>

                    <h3 id="pp-cookies">7. Cookies</h3>
                    <p>We use only the cookies the platform needs to work:</p>
                    <ul class="legal__list">
                        <li>a session cookie, which keeps you signed in while you use the platform;</li>
                        <li>a security token, which protects forms from being sent by someone else; and</li>
                        <li>a "remember me" cookie, which keeps you signed in on your device for up to 30 days if you tick "Remember me for 30 days".</li>
                    </ul>
                    <p>We do not use advertising cookies. You can delete cookies in your browser settings, but you will not be able to sign in without them.</p>

                    <h3 id="pp-how-long">8. How long we keep your data</h3>
                    <p>We keep your data while your account is active and for as long as the school needs it for its learning records. When it is no longer needed, we delete it or make it anonymous. You can ask us to delete your account (see "Your rights"). We may keep some records where the law or school policy requires it.</p>

                    <h3 id="pp-security">9. How we protect your data</h3>
                    <p>We use reasonable organizational, physical, and technical safeguards. For example, passwords are stored in hashed form, what you can see depends on your role, and the platform is meant to be used over secure connections. No system is perfectly secure. If a data breach happens that puts you at real risk, we will notify the National Privacy Commission and the people affected as the law requires.</p>

                    <h3 id="pp-your-rights">10. Your rights</h3>
                    <p>Under the Data Privacy Act, you have the right to:</p>
                    <ul class="legal__list">
                        <li>be informed about how your data is used;</li>
                        <li>access the personal data we hold about you;</li>
                        <li>object to how your data is used;</li>
                        <li>correct data that is wrong or out of date;</li>
                        <li>ask us to delete or block data that is no longer needed or was unlawfully collected;</li>
                        <li>receive a copy of your data in a common format (data portability);</li>
                        <li>claim damages if you were harmed by misuse of your data; and</li>
                        <li>file a complaint with the National Privacy Commission at <a href="https://privacy.gov.ph" rel="noopener noreferrer">privacy.gov.ph</a>.</li>
                    </ul>
                    <p>To use any of these rights, contact us using the details below. Parents and guardians may do this for their child.</p>

                    <h3 id="pp-changes">11. Changes to this policy</h3>
                    <p>We may update this policy. The date at the top shows when it last changed. If the changes are important, we will also tell you on the platform.</p>

                    <h3 id="pp-contact">12. Contact us</h3>
                    <p>Questions or requests about your data? Contact the Data Protection Officer of Bubog National High School through {!! $contact !!}. Please read our <a href="#terms">Terms and Conditions</a> too.</p>
                </div>
            </details>

            <details class="legal-doc" id="terms" data-legal>
                <summary>
                    <span>Terms and Conditions</span>
                    <svg class="icon" aria-hidden="true"><use href="#i-chevron"/></svg>
                </summary>
                <div class="legal legal--inline">
                    <p class="legal__meta">Effective {{ $effective }}. Applies to the Math Learning platform of Bubog National High School.</p>
                    <div class="legal__summary">
                        <strong>In short</strong>
                        Use Math Learning to learn, be honest and respectful, and keep your password to yourself. The AI chatbot can make mistakes, so check with your teacher. The school can suspend accounts that break these rules.
                    </div>
                    <h3 id="tc-agreeing">1. Agreeing to these terms</h3>
                    <p>By creating an account or using Math Learning, you agree to these Terms and Conditions. If you are under 18, your parent or guardian must also agree to them for you. If you do not agree, please do not use the platform.</p>

                    <h3 id="tc-the-platform">2. About the platform</h3>
                    <p>Math Learning is run by Bubog National High School to support Junior High School math. It includes learning modules (Sequences and Series, Polynomials and Polynomial Equations, and Advanced Equations and Functions), quizzes and summative tests, an AI chatbot, progress tracking, offline materials, and tools for teachers and administrators.</p>

                    <h3 id="tc-accounts">3. Your account</h3>
                    <ul class="legal__list">
                        <li>Give accurate information when you sign up. Some accounts, such as teacher accounts, may need approval from the school.</li>
                        <li>Keep your password private. You are responsible for what happens under your account.</li>
                        <li>One person, one account. Do not share your account or pretend to be someone else.</li>
                        <li>Tell your teacher or the school right away if you think someone else used your account.</li>
                    </ul>

                    <h3 id="tc-acceptable-use">4. How to use the platform</h3>
                    <p>Please use Math Learning for learning, and be honest and respectful. You agree not to:</p>
                    <ul class="legal__list">
                        <li>cheat, share test answers, or use the chatbot on any test or assessment your teacher says you must do on your own;</li>
                        <li>bully, harass, or send harmful or inappropriate content to anyone;</li>
                        <li>try to break, hack, overload, or copy data from the platform;</li>
                        <li>look at, change, or collect other people's accounts or data;</li>
                        <li>upload viruses or anything else meant to cause damage; or</li>
                        <li>use the platform for anything unlawful or for making money.</li>
                    </ul>

                    <h3 id="tc-ai-content">5. The AI chatbot and generated content</h3>
                    <p>The chatbot and the quiz generator use artificial intelligence. They can make mistakes or give incomplete explanations. Treat their answers as help, not as final truth, and check important work with your teacher. Teachers should review AI-generated quizzes before giving them to students. Do not share private information in the chatbot.</p>

                    <h3 id="tc-teachers-admins">6. Teachers and administrators</h3>
                    <p>If you are a teacher or administrator, you agree to use student information only for teaching and school administration, to keep it confidential, and to follow school rules and the Data Privacy Act of 2012. Give feedback that is fair and respectful. Manage accounts and platform settings responsibly.</p>

                    <h3 id="tc-materials">7. Learning materials and offline downloads</h3>
                    <p>The lessons, quizzes, and other materials belong to the school or the people who made them. You may use them for your own learning, including offline. Please do not copy, sell, or post them publicly. The work you create, such as your answers and messages, stays yours. You allow the school to use it to run the platform, show your progress, and (without your name) improve lessons.</p>

                    <h3 id="tc-privacy">8. Privacy</h3>
                    <p>Our <a href="#privacy">Privacy Policy</a> explains what personal data we collect, how we use it, and your rights. It is part of these terms.</p>

                    <h3 id="tc-availability">9. Availability and changes to the platform</h3>
                    <p>We try to keep Math Learning working, but we cannot promise it will always be available or error-free. Maintenance or internet problems can interrupt it. Offline downloads may not include the latest updates. We may add, change, or remove features.</p>

                    <h3 id="tc-suspension">10. Suspension and closing accounts</h3>
                    <p>The school may suspend or remove an account that breaks these terms or school rules. You can ask to close your account at any time by contacting us.</p>

                    <h3 id="tc-liability">11. Limits of responsibility</h3>
                    <p>Math Learning is an educational support tool, provided "as is". To the extent the law allows, the school is not responsible for indirect losses that come from using the platform, not being able to use it, or relying on AI-generated content. Nothing in these terms limits any right you have under Philippine law, including the Data Privacy Act of 2012.</p>

                    <h3 id="tc-governing-law">12. Governing law</h3>
                    <p>These terms are governed by the laws of the Republic of the Philippines.</p>

                    <h3 id="tc-changes">13. Changes to these terms</h3>
                    <p>We may update these terms. The date at the top shows when they last changed. If you keep using the platform after an update, you agree to the new terms. If the changes are important, we will tell you on the platform.</p>

                    <h3 id="tc-contact">14. Contact us</h3>
                    <p>Questions about these terms? Contact Bubog National High School through {!! $contact !!}.</p>
                </div>
            </details>

        </div>
    </div>
</section>

</main>

<!-- ================= FOOTER ================= -->
<footer class="footer">
    <div class="container">

        <div class="footer__grid">

            <div class="footer__brand">
                <a href="#top" class="logo" aria-label="Math Learning, back to top">
                    <svg class="logo__mark" viewBox="0 0 32 32" aria-hidden="true"><use href="#logo-mark"/></svg>
                    <span>Math Learn</span>
                </a>
                <p>A math learning platform made for the students and teachers of Bubog National High School.</p>

                @if (collect($socials)->contains(fn ($s) => ! empty($s['url'])))
                    <ul class="socials">
                        @foreach ($socials as $social)
                            @if (! empty($social['url']))
                                <li>
                                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}">
                                        <svg class="icon" aria-hidden="true"><use href="#{{ $social['icon'] }}"/></svg>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </div>

            <nav aria-label="Explore">
                <h3>Explore</h3>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#how-it-works">How a topic works</a></li>
                    <li><a href="#modules">Modules</a></li>
                    <li><a href="#teachers">For teachers</a></li>
                    <li><a href="#dashboards">Accounts</a></li>
                    <li><a href="#faq">FAQ</a></li>
                </ul>
            </nav>

            <nav aria-label="Account">
                <h3>Account</h3>
                <ul>
                    <li><a href="{{ route('signin-signin') }}">Sign In</a></li>
                    <li><a href="{{ route('signin-signup') }}">Sign Up</a></li>
                </ul>
            </nav>

            <nav aria-label="Legal">
                <h3>Legal</h3>
                <ul>
                    <li><a href="#privacy">Privacy Policy</a></li>
                    <li><a href="#terms">Terms and Conditions</a></li>
                </ul>
            </nav>


        </div>

        <div class="footer__legal">
            <p>© {{ now()->year }} Math Learning Assistant</p>
            <p>Bubog National High School · San Jose, Occidental Mindoro</p>
        </div>

    </div>
</footer>

<!-- ================= CSRF ================= -->
<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    window.Laravel = {
        csrfToken: '{{ csrf_token() }}'
    };
</script>

</body>
</html>