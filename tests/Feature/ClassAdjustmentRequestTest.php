<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Filament\Student\Resources\ClassAdjustmentRequestResource;
use Filament\Forms\Form;

class ClassAdjustmentRequestTest extends TestCase
{
    /**
     * Test that ClassAdjustmentRequest form can be instantiated without filtering by registered classes.
     */
    public function test_class_adjustment_form_instantiation(): void
    {
        // Test that the form schema can be created without errors
        $form = new Form();
        $resource = new ClassAdjustmentRequestResource();
        
        // This should not throw any errors after removing the registered class filtering logic
        $formSchema = ClassAdjustmentRequestResource::form($form);
        
        $this->assertInstanceOf(Form::class, $formSchema);
        
        // Verify that the form contains the expected fields
        $schema = $formSchema->getSchema();
        $this->assertNotEmpty($schema);
    }
}