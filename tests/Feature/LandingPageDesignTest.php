<?php

use function Pest\Laravel\get;

it('uses the same palette as the dashboards', function (string $token) {
    $homepageValue = cssToken('homepage.css', $token);

    expect($homepageValue)->not->toBeNull();

    foreach (['student', 'teacher', 'admin'] as $role) {
        expect(cssToken("dashboard/{$role}_dashboard.css", $token))->toBe($homepageValue);
    }
})->with(['--blue', '--blue-dark', '--blue-mid', '--blue-light', '--green', '--orange', '--purple', '--bg', '--border', '--text', '--text-3']);

it('uses the sign-in page navy for the hero, cta band, and footer', function () {
    $css = file_get_contents(resource_path('css/homepage.css'));

    expect(cssToken('homepage.css', '--navy-1'))->toBe(cssToken('app.css', '--auth-bg-start'))
        ->and(cssToken('homepage.css', '--navy-2'))->toBe(cssToken('app.css', '--auth-bg-mid'))
        ->and(cssToken('homepage.css', '--navy-3'))->toBe(cssToken('app.css', '--auth-bg-end'))
        ->and(cssToken('homepage.css', '--navy-4'))->toBe(cssToken('app.css', '--auth-panel-top'))
        ->and(cssToken('homepage.css', '--navy-5'))->toBe(cssToken('app.css', '--auth-panel-bottom'));

    preg_match('/\.hero\s*\{[^}]*\}/', $css, $hero);
    preg_match('/\.cta__panel\s*\{[^}]*\}/', $css, $cta);
    preg_match('/\.footer\s*\{[^}]*\}/', $css, $footer);

    expect($hero[0] ?? '')->toContain('background: var(--navy-grad);')
        ->and($cta[0] ?? '')->toContain('background: var(--navy-grad);')
        ->and($footer[0] ?? '')->toContain('background: var(--navy-5);');
});

it('uses the dashboards font and the ai chat blue for primary buttons', function () {
    $css = file_get_contents(resource_path('css/homepage.css'));

    preg_match('/\.btn--primary\s*\{[^}]*\}/', $css, $primary);

    expect(cssToken('homepage.css', '--font'))->toStartWith("'plus jakarta sans'")
        ->and(cssToken('homepage.css', '--blue-grad'))->toBe('linear-gradient(135deg, var(--blue-mid), var(--blue))')
        ->and($primary[0] ?? '')->toContain('background: var(--blue-grad);');
});

it('leaves no blue-to-green brand gradient on the landing page', function () {
    $html = get('/')->getContent();

    preg_match('/<linearGradient id="brand-grad".*?<\/linearGradient>/s', $html, $brandGradient);

    expect($brandGradient[0] ?? '')
        ->toContain('stop-color="#60a5fa"')
        ->toContain('stop-color="#2563eb"');

    expect(file_get_contents(resource_path('css/homepage.css')))
        ->not->toMatch('/#(015b8a|0f6f36|06767a|16906e|197a86)\b/i')
        ->not->toContain('--accent-');
});

it('works a real Module 1 problem step by step on the hero board', function () {
    $html = get('/')->getContent();

    preg_match('/<section class="hero".*?<\/section>/s', $html, $hero);
    $hero = $hero[0] ?? '';

    expect($hero)
        ->toContain('Module 1 · Arithmetic Sequence')
        ->toContain('Find the 10th term of <span class="math">3, 7, 11, 15, …</span>')
        ->toContain('<var>d</var> = 7 − 3 = 4')
        ->toContain('<var>a</var><sub>10</sub> = 3 + (10 − 1)(4)')
        ->toContain('<span class="board__box"><var>a</var><sub>10</sub> = 39</span>')
        ->and(substr_count($hero, '<li class="board__step'))->toBe(4);

    // Math is set in a math serif the device already has, so it costs no download.
    expect(cssToken('homepage.css', '--font-math'))->toStartWith("'cambria math'");
});

it('shows each module in its dashboard color with all twelve topics', function () {
    $html = get('/')->getContent();

    expect($html)
        ->toContain('class="module module--blue lift reveal"')
        ->toContain('class="module module--green lift reveal"')
        ->toContain('class="module module--orange lift reveal"');

    preg_match_all('/<ul class="module__topics"[^>]*>(.*?)<\/ul>/s', $html, $topicLists);

    expect($topicLists[1])->toHaveCount(3)
        ->and(substr_count(implode('', $topicLists[1]), '<li>'))->toBe(12);
});

it('introduces each role dashboard with its own sign-in link', function () {
    $html = get('/')->getContent();

    preg_match('/<section[^>]+id="dashboards".*?<\/section>/s', $html, $section);
    $dashboards = $section[0] ?? '';

    expect($dashboards)
        ->toContain('class="role role--blue lift reveal"')
        ->toContain('class="role role--green lift reveal"')
        ->toContain('class="role role--purple lift reveal"')
        ->toContain('href="'.route('student.login').'"')
        ->toContain('href="'.route('teacher.login').'"')
        ->toContain('href="'.route('admin.login').'"');

    expect(substr_count($dashboards, '<li>'))->toBe(12);
});

it('answers common questions in an accordion that matches how sign-up works', function () {
    $html = get('/')->getContent();

    preg_match('/<section[^>]+id="faq".*?<\/section>/s', $html, $section);
    $faq = $section[0] ?? '';

    expect(substr_count($faq, '<details class="faq__item">'))->toBe(6)
        ->and(substr_count($faq, '<summary>'))->toBe(6)
        ->and($faq)
        ->toContain('The teacher of the section you picked approves student accounts, and an administrator approves teacher accounts.')
        ->toContain('Continue with Google')
        ->toContain('href="#privacy"');
});

it('highlights the nav link for the section in view', function () {
    $html = get('/')->getContent();

    preg_match('/<ul class="nav__links">.*?<\/ul>/s', $html, $links);

    expect(substr_count($links[0] ?? '', 'data-spy'))->toBe(4)
        ->and($links[0] ?? '')->toContain('href="#dashboards"')->toContain('href="#faq"');

    expect(file_get_contents(resource_path('js/homepage.js')))
        ->toContain("document.querySelectorAll('[data-spy]')")
        ->toContain("link.setAttribute('aria-current', 'location')");

    expect(file_get_contents(resource_path('css/homepage.css')))
        ->toContain('.nav.is-scrolled .nav__links a.is-active');
});

it('writes the board steps one after another on load', function () {
    $html = get('/')->getContent();
    $css = file_get_contents(resource_path('css/homepage.css'));

    preg_match_all('/<li class="board__step[^"]*" data-enter="(\d)">/', $html, $steps);

    expect($steps[1])->toBe(['5', '6', '7', '8']);

    $delays = array_map(function (string $step) use ($css): int {
        preg_match('/\.hero \[data-enter="'.$step.'"\] \{ animation-delay: (\d+)ms; \}/', $css, $delay);

        return (int) ($delay[1] ?? -1);
    }, $steps[1]);

    expect($delays)->each->toBeGreaterThan(0)
        ->and($delays)->toBe(collect($delays)->sort()->values()->all());
});

it('keeps the stats cards below the hero instead of overlapping it', function () {
    preg_match('/\.stats\s*\{[^}]*\}/', file_get_contents(resource_path('css/homepage.css')), $stats);

    expect($stats[0] ?? '')
        ->toContain('padding-top:')
        ->not->toContain('margin-top: calc(-1');
});

it('keeps the hero column from growing wider than a phone screen', function () {
    preg_match('/\.hero__inner\s*\{[^}]*\}/', file_get_contents(resource_path('css/homepage.css')), $inner);

    expect($inner[0] ?? '')->toContain('grid-template-columns: minmax(0, 1fr);');
});

it('sets the at-a-glance numbers on ruled lines instead of icon cards', function () {
    $html = get('/')->getContent();
    $css = file_get_contents(resource_path('css/homepage.css'));

    preg_match('/<section class="stats".*?<\/section>/s', $html, $stats);
    preg_match('/\.stats__grid\s*\{[^}]*\}/', $css, $grid);
    preg_match('/\.stat\s*\{[^}]*\}/', $css, $stat);

    expect($stats[0] ?? '')->not->toContain('stat__icon')->not->toContain('data-count')
        ->and($grid[0] ?? '')->toContain('border-block: 1px solid var(--rule);')
        ->and($stat[0] ?? '')->toContain('grid-template-columns: 5rem minmax(0, 1fr);')
        ->and($css)->toMatch('/@media \(min-width: 768px\) \{\s*\.stats__grid \{ grid-template-columns: repeat\(3, minmax\(0, 1fr\)\); \}/');
});

it('drops the gradient icon tiles for ruled lists and small role color markers', function () {
    $html = get('/')->getContent();
    $css = file_get_contents(resource_path('css/homepage.css'));

    expect($html)->not->toContain('tile__icon')->not->toContain('stat__icon')
        ->and($css)->not->toContain('.tile__icon')
        ->and($css)->toMatch('/\.role h3::before \{[^}]*background: var\(--role\);/')
        ->and($css)->toMatch('/\.role__list li::before \{[^}]*content: "–";/');

    preg_match('/<ul class="points">(.*?)<\/ul>/s', $html, $points);

    expect(substr_count($points[1] ?? '', 'class="point reveal"'))->toBe(4);
});

it('explains the four phases of every topic on a number line', function () {
    $html = get('/')->getContent();
    $css = file_get_contents(resource_path('css/homepage.css'));

    preg_match('/<section[^>]+id="how-it-works".*?<\/section>/s', $html, $section);
    preg_match_all('/<li class="step">.*?<h3>(.*?)<\/h3>/s', $section[0] ?? '', $phases);

    expect($phases[1])->toBe(['Pre-test', 'Lesson', 'Activity', 'Post-test'])
        ->and($section[0] ?? '')->toContain('leaving the page submits your answers')
        ->and($css)->toMatch('/\.steps::after \{[^}]*border-left: 11px solid var\(--navy-1\);/');
});

it('sets the modules as a numbered table of contents', function () {
    $html = get('/')->getContent();
    $css = file_get_contents(resource_path('css/homepage.css'));

    preg_match_all('/<span class="module__num" aria-hidden="true">(\d+)<\/span>/', $html, $numbers);

    expect($numbers[1])->toBe(['01', '02', '03'])
        ->and($html)->toContain('<li>The Remainder Theorem and Factor Theorem</li>')
        ->and($css)->toContain('content: counter(module) "." counter(topic);')
        ->and($css)->toMatch('/\.module__art \{ display: none; \}/');
});

it('keeps the hero title on two lines on small laptops', function () {
    expect(file_get_contents(resource_path('css/homepage.css')))
        ->toMatch('/@media \(min-width: 1024px\) \{\s*\.hero__title \{ font-size: clamp\(3\.25rem, 0\.5rem \+ 4\.4vw, 4\.25rem\); \}/');
});

it('stacks the account and legal footer links beside explore on phones', function () {
    $css = file_get_contents(resource_path('css/homepage.css'));

    expect($css)->toContain('.footer__grid > nav:first-of-type { grid-row: span 2; }')
        ->toContain('.footer__grid > nav:first-of-type { grid-row: auto; }')
        ->toContain('p { text-wrap: pretty; }');
});

it('shows the school seal in the call to action like the sign-in page', function () {
    $html = get('/')->getContent();

    preg_match('/<div class="cta__brand">.*?<\/div>\s*<\/div>/s', $html, $brand);

    expect($brand[0] ?? '')
        ->toContain('alt="Bubog National High School seal"')
        ->toContain('loading="lazy"')
        ->toContain('Bubog National High School');
});
