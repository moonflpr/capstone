<?php

use App\Models\Section;
use App\Models\User;

beforeEach(function () {
    $this->student = User::factory()->create(['section_id' => Section::factory()->create()->id]);
});

it('auto-submits a module quiz when the tab is hidden or the window loses focus to a floating window', function () {
    $modulePage = $this->actingAs($this->student)->get(route('student.modules'))->assertOk()->getContent();

    expect($modulePage)
        ->toContain("document.addEventListener('visibilitychange', mqHandleVisibilityChange);")
        ->toContain("window.addEventListener('blur', mqHandleWindowBlur);")
        ->toContain("window.addEventListener('focus', mqShowAutoSubmitNotice);")
        ->toMatch("/function mqHandleWindowBlur\(\) \{.*?document\.hasFocus\(\).*?mqAutoSubmitOnLeave\(\);/s")
        ->toContain('using a floating window during a');
});

it('ignores focus moving into an iframe on the same page', function () {
    $modulePage = $this->actingAs($this->student)->get(route('student.modules'))->assertOk()->getContent();

    expect($modulePage)->toContain("document.activeElement?.tagName === 'IFRAME'");
});

it('auto-submits the summative test when the window loses focus', function () {
    $script = file_get_contents(resource_path('js/dashboard/student_dashboard.js'));

    expect($script)
        ->toContain("window.addEventListener('blur', () => {")
        ->toMatch("/window\.addEventListener\('blur'.*?document\.hasFocus\(\).*?autoSubmitSummativeOnLeave\(\);/s")
        ->toMatch("/if \(document\.hidden\) \{\s*autoSubmitSummativeOnLeave\(\);/");
});
