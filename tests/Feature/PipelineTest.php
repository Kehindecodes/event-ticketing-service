<?php

namespace Tests\Feature;

use Tests\TestCase;

class PipelineTest extends TestCase
{
    public function test_pipeline_is_working(): void
    {
        $this->get('/')
            ->assertStatus(200);
    }
}
