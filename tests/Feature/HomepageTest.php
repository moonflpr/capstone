<?php

use App\Models\PlatformSetting;

use function Pest\Laravel\get;

it('loads the homepage successfully', function () {
    get('/')->assertOk();
});

it('shows the default platform description when none has been saved', function () {
    get('/')->assertSee('Interactive learning platform for Junior High School Mathematics at Bubog National High School');
});

it('shows the admin-saved platform description on the homepage', function () {
    PlatformSetting::create(['key' => 'platform_desc', 'value' => 'A brand new custom description for MathLearn.']);

    get('/')->assertSee('A brand new custom description for MathLearn.');
});

it('renders the hero visual inline so no network image delays first paint', function () {
    $html = get('/')->getContent();

    preg_match('/<section class="hero".*?<\/section>/s', $html, $hero);

    // The hero visual is a worked problem on the board, written as real
    // HTML text rather than a screenshot.
    expect($hero[0] ?? '')
        ->toContain('<figure class="board"')
        ->toContain('<ol class="board__steps">')
        ->not->toContain('<img')
        ->not->toContain('loading="lazy"');
});

it('preloads the self-hosted font in the head without a stale hero image preload', function () {
    $html = get('/')->getContent();

    expect($html)
        ->toContain('rel="preload" href="/fonts/plus-jakarta-sans-latin-400-800.woff2" as="font" type="font/woff2" crossorigin')
        ->not->toContain('rel="preload" as="image"');

    expect(file_exists(public_path('fonts/plus-jakarta-sans-latin-400-800.woff2')))->toBeTrue();
});

it('declares font-display: optional for the self-hosted Plus Jakarta Sans face', function () {
    // "optional" (not "swap"): the font is preloaded, so it's normally ready
    // for first paint anyway; if it ever isn't, the browser commits to the
    // fallback for that whole render instead of swapping fonts in later —
    // guaranteeing the font can never cause a layout shift.
    preg_match('/@font-face\s*\{[^}]*\}/', file_get_contents(resource_path('css/homepage.css')), $face);

    expect($face[0] ?? '')
        ->toContain("font-family: 'Plus Jakarta Sans'")
        ->toContain('font-display: optional');
});

it('limits infinite animation to motion-safe, GPU-composited transforms', function () {
    $css = file_get_contents(resource_path('css/homepage.css'));

    preg_match_all('/animation:\s*([\w-]+)[^;]*\binfinite\b[^;]*;/', $css, $infinite);
    expect($infinite[0])->not->toBeEmpty();

    // Every looping animation (the live-data dot) only runs when the
    // visitor hasn't asked for reduced motion.
    preg_match_all('/@media \(prefers-reduced-motion: no-preference\)\s*\{(?:[^{}]|\{[^{}]*\})*\}/', $css, $motionSafe);
    preg_match_all('/animation:[^;]*\binfinite\b[^;]*;/', implode("\n", $motionSafe[0]), $motionSafeInfinite);
    expect($motionSafeInfinite[0])->toHaveCount(count($infinite[0]));

    foreach (array_unique($infinite[1]) as $name) {
        preg_match('/@keyframes '.preg_quote($name, '/').'\s*\{(?:[^{}]|\{[^{}]*\})*\}/', $css, $keyframes);

        expect($keyframes[0] ?? '')
            ->toContain('transform')
            ->not->toContain('opacity')
            ->not->toContain('width')
            ->not->toContain('box-shadow');
    }
});

it('never starts the LCP hero subheading at opacity zero', function () {
    $html = get('/')->getContent();
    $css = file_get_contents(resource_path('css/homepage.css'));

    // The subheading is the LCP element, so unlike the rest of the hero it
    // has no data-enter entrance animation.
    expect($html)->toContain('<p class="hero__sub">');

    preg_match('/\.hero__sub\s*\{[^}]*\}/', $css, $sub);
    expect($sub[0] ?? '')
        ->toContain('font-size')
        ->not->toContain('opacity')
        ->not->toContain('animation');
});

it('marks the document as js-capable so reveal sections degrade gracefully', function () {
    expect(get('/')->getContent())->toContain("classList.add('js')");
});

it('is written mobile-first with min-width layout breakpoints', function () {
    $css = file_get_contents(resource_path('css/homepage.css'));

    foreach (['640px', '768px', '1024px'] as $breakpoint) {
        expect($css)->toMatch('/@media\s*\(min-width:\s*'.preg_quote($breakpoint, '/').'\)/');
    }

    // The one max-width query stacks the hero buttons on small phones.
    preg_match_all('/@media\s*\(\s*max-width[^{]*\{(?:[^{}]|\{[^{}]*\})*\}/', $css, $maxWidth);
    expect($maxWidth[0])->toHaveCount(1);
    expect($maxWidth[0][0])->toContain('.hero__cta .btn');
});

it('constrains section content with a shared max-width container', function () {
    $html = get('/')->getContent();
    $css = file_get_contents(resource_path('css/homepage.css'));

    preg_match('/\.container\s*\{[^}]*\}/', $css, $container);
    expect($container[0] ?? '')->toContain('var(--container)')->toContain('margin-inline: auto');
    expect($css)->toMatch('/--container:\s*\d+px/');

    foreach (['features', 'dashboards', 'how-it-works', 'modules', 'teachers', 'faq'] as $section) {
        expect($html)->toMatch('/<section[^>]+id="'.$section.'"[^>]*>\s*<div class="container/');
    }
});

it('keeps long-form body copy at 16px or larger', function () {
    $css = file_get_contents(resource_path('css/homepage.css'));

    preg_match('/(?<![\w.-])body\s*\{[^}]*\}/', $css, $body);
    expect($body[0] ?? '')->toMatch('/font:\s*\d{3}\s+1rem\//');

    foreach (['.hero__sub', '.lead', '.cta__copy > p:not(.cta__terms)', '.legal p'] as $selector) {
        preg_match('/'.preg_quote($selector, '/').'\s*\{[^}]*\}/', $css, $rule);

        expect($rule[0] ?? '')->toMatch('/font-size:\s*(clamp\()?1(\.[0-9]+)?rem/');
    }

    // Feature tiles and how-it-works steps inherit the 1rem body size.
    foreach (['.tile p', '.step p'] as $selector) {
        preg_match('/'.preg_quote($selector, '/').'\s*\{[^}]*\}/', $css, $rule);

        expect($rule[0] ?? '')->not->toBeEmpty()->not->toContain('font-size');
    }
});

it('gives the hero call-to-action buttons a 44px+ touch target', function () {
    $css = file_get_contents(resource_path('css/homepage.css'));

    preg_match('/\.btn\s*\{[^}]*\}/', $css, $btn);

    expect($btn[0] ?? '')->toMatch('/min-height:\s*4[4-9]px|min-height:\s*[5-9][0-9]px/');
});

it('stacks the hero buttons full-width on small phones and inline from 480px up', function () {
    $css = file_get_contents(resource_path('css/homepage.css'));

    preg_match('/\.hero__cta\s*\{[^}]*\}/', $css, $cta);
    preg_match('/@media\s*\(max-width:\s*479px\)\s*\{(?:[^{}]|\{[^{}]*\})*\}/', $css, $smallPhones);

    expect($cta[0] ?? '')->toContain('display: flex')->toContain('flex-wrap: wrap');
    expect($smallPhones[0] ?? '')->toContain('.hero__cta .btn')->toContain('width: 100%');
});

it('sizes the hero to the dynamic viewport height so mobile chrome cannot clip it', function () {
    $css = file_get_contents(resource_path('css/homepage.css'));

    preg_match('/\.hero\s*\{[^}]*\}/', $css, $hero);
    $rule = $hero[0] ?? '';

    expect($rule)
        ->toContain('min-height: 100vh')
        ->toContain('min-height: 100dvh')
        ->toContain('overflow: hidden')
        ->toContain('display: flex');

    // 100vh comes first so browsers without dvh support still get a full-height hero.
    expect(strpos($rule, 'min-height: 100vh'))->toBeLessThan(strpos($rule, 'min-height: 100dvh'));
});

it('keeps the hero free of template decoration: no glow blob, grid, or floating mockups', function () {
    $html = get('/')->getContent();
    $css = file_get_contents(resource_path('css/homepage.css'));

    preg_match('/<section class="hero".*?<\/section>/s', $html, $hero);

    expect($hero[0] ?? '')
        ->not->toContain('hero__blob')
        ->not->toContain('hero__float')
        ->not->toContain('class="preview"')
        ->not->toContain('scroll-cue');

    expect($css)
        ->not->toContain('.hero__blob')
        ->not->toContain('@keyframes float')
        ->not->toContain('@keyframes drift');

    // The only decoration is the board's chalk ledge, kept behind the content.
    preg_match('/\.hero\s*\{[^}]*\}/', $css, $hero);
    preg_match('/\.hero::after\s*\{[^}]*\}/', $css, $ledge);

    expect($hero[0] ?? '')->toContain('isolation: isolate');
    expect($ledge[0] ?? '')->toContain('z-index: -1')->toContain('background: var(--navy-5);');
});

it('links from the hero board to the features section', function () {
    $html = get('/')->getContent();

    preg_match('/<p class="board__hint"[^>]*>.*?<\/p>/s', $html, $hint);

    expect($hint[0] ?? '')
        ->toContain('Math AI Assistant')
        ->toContain('<a href="#features">See how</a>');

    expect($html)->toMatch('/<section[^>]+id="features"/');
});

it('offsets in-page anchors so the fixed header cannot cover their headings', function () {
    $css = file_get_contents(resource_path('css/homepage.css'));

    preg_match('/(?<![\w.-])html\s*\{[^}]*\}/', $css, $html);
    preg_match('/\.nav\s*\{[^}]*\}/', $css, $nav);

    expect($html[0] ?? '')->toContain('scroll-padding-top')->toContain('var(--nav-h)');
    expect($nav[0] ?? '')->toContain('position: fixed')->toContain('height: var(--nav-h)');
});

it('enables smooth anchor scrolling only when motion is not reduced', function () {
    $css = file_get_contents(resource_path('css/homepage.css'));

    expect($css)->toMatch(
        '/@media \(prefers-reduced-motion: no-preference\)\s*\{\s*html\s*\{\s*scroll-behavior: smooth;/'
    );
});

it('inlines the homepage stylesheet instead of a render-blocking link', function () {
    $html = get('/')->getContent();

    // The inline <style> carries the CSP nonce so it is allowed to apply.
    expect($html)
        ->toMatch('/<style nonce="[^"]+">/')
        ->toContain('font-display:optional')
        ->not->toMatch('/<link[^>]+rel="stylesheet"[^>]+homepage-[^"]+\.css/');
});
