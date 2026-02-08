<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\WebSearch;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Testing\TextResponseFake;
use Tests\TestCase;

class WebSearchTest extends TestCase
{
    public function test_returns_search_results(): void
    {
        Prism::fake([
            TextResponseFake::make()->withText('Search results here'),
        ]);

        $action = new WebSearch;
        $result = $action->handle('test query');

        $this->assertSame('Search results here', $result);
    }
}
