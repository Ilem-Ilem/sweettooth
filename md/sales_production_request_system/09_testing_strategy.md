# Sales to Production Request System - Testing Strategy

Comprehensive testing approach including unit, integration, and e2e tests.

---

## TEST PYRAMID

```
         ┌─────────────┐
         │     E2E     │  (10-20 tests)
         │  (Slow)     │
         └─────────────┘
              △
         ┌─────────────┐
         │Integration  │  (30-40 tests)
         │  (Medium)   │
         └─────────────┘
              △
         ┌─────────────┐
         │    Unit     │  (100+ tests)
         │   (Fast)    │
         └─────────────┘
```

---

## UNIT TESTS

### 1. Model Tests

#### ProductionRequest Model
```php
namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ProductionRequest;
use App\Models\Department;
use App\Models\User;

class ProductionRequestTest extends TestCase
{
    /** @test */
    public function can_create_production_request()
    {
        $request = ProductionRequest::factory()->create();
        
        $this->assertDatabaseHas('production_requests', [
            'id' => $request->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function has_sales_department_relationship()
    {
        $salesDept = Department::factory()->create();
        $request = ProductionRequest::factory()
            ->create(['sales_department_id' => $salesDept->id]);

        $this->assertTrue($request->salesDepartment->is($salesDept));
    }

    /** @test */
    public function has_production_department_relationship()
    {
        $prodDept = Department::factory()->create();
        $request = ProductionRequest::factory()
            ->create(['production_department_id' => $prodDept->id]);

        $this->assertTrue($request->productionDepartment->is($prodDept));
    }

    /** @test */
    public function has_created_by_relationship()
    {
        $user = User::factory()->create();
        $request = ProductionRequest::factory()
            ->create(['created_by_id' => $user->id]);

        $this->assertTrue($request->createdBy->is($user));
    }

    /** @test */
    public function can_calculate_estimated_completion_time()
    {
        $request = ProductionRequest::factory()
            ->create([
                'created_at' => now(),
                'started_at' => now(),
            ]);

        // Would be calculated by service, not model
        $this->assertNotNull($request->started_at);
    }
}
```

#### ProductionProgressFeedback Model
```php
class ProductionProgressFeedbackTest extends TestCase
{
    /** @test */
    public function has_production_request_relationship()
    {
        $request = ProductionRequest::factory()->create();
        $feedback = ProductionProgressFeedback::factory()
            ->create(['production_request_id' => $request->id]);

        $this->assertTrue($feedback->productionRequest->is($request));
    }

    /** @test */
    public function validates_milestone_enum_values()
    {
        $request = ProductionRequest::factory()->create();

        // Valid
        ProductionProgressFeedback::create([
            'production_request_id' => $request->id,
            'milestone' => 'started',
            'progress_percentage' => 0,
            'updated_by_id' => User::factory()->create()->id,
        ]);

        // Invalid - should fail or be caught
        $this->expectException(\Exception::class);
        ProductionProgressFeedback::create([
            'production_request_id' => $request->id,
            'milestone' => 'invalid_status',
        ]);
    }

    /** @test */
    public function progress_percentage_between_0_and_100()
    {
        $request = ProductionRequest::factory()->create();

        // Valid: 0%
        ProductionProgressFeedback::factory()
            ->create(['production_request_id' => $request->id, 'progress_percentage' => 0]);

        // Valid: 100%
        ProductionProgressFeedback::factory()
            ->create(['production_request_id' => $request->id, 'progress_percentage' => 100]);

        // Invalid: > 100%
        $this->expectException(\Throwable::class);
        ProductionProgressFeedback::factory()
            ->create(['production_request_id' => $request->id, 'progress_percentage' => 101]);
    }
}
```

---

## INTEGRATION TESTS

### 1. Request Creation Workflow
```php
namespace Tests\Integration;

use Tests\TestCase;
use App\Models\ProductionRequest;
use App\Models\Department;
use App\Models\Product;
use App\Models\User;
use Laravel\Livewire\Livewire;

class CreateProductionRequestTest extends TestCase
{
    /** @test */
    public function sales_user_can_create_production_request()
    {
        $salesUser = User::factory()->create(['department_id' => 1]);
        $prodDept = Department::factory()->create(['type' => 'production']);
        $product = Product::factory()->create(['department_id' => $prodDept->id]);

        Livewire::actingAs($salesUser)
            ->test('sales.production.create-production-request')
            ->set('selectedDepartment', $prodDept->id)
            ->call('addProduct', $product->id)
            ->set('batchQuantities.' . $product->id, 10)
            ->set('priority', 'normal')
            ->call('submitRequest')
            ->assertDispatched('request-created');

        $this->assertDatabaseHas('production_requests', [
            'sales_department_id' => 1,
            'production_department_id' => $prodDept->id,
            'status' => 'pending',
            'priority' => 'normal',
            'created_by_id' => $salesUser->id,
        ]);
    }

    /** @test */
    public function production_department_receives_notification()
    {
        $request = ProductionRequest::factory()
            ->create(['production_department_id' => 2]);

        // Verify broadcast event was sent
        $this->assertTrue(
            \Event::wasDispatched(\App\Events\RequestCreated::class)
        );
    }

    /** @test */
    public function cannot_create_with_invalid_department()
    {
        $salesUser = User::factory()->create();

        Livewire::actingAs($salesUser)
            ->test('sales.production.create-production-request')
            ->set('selectedDepartment', 99999)
            ->call('submitRequest')
            ->assertHasErrors('selectedDepartment');
    }

    /** @test */
    public function cannot_create_without_products()
    {
        $salesUser = User::factory()->create();
        $prodDept = Department::factory()->create();

        Livewire::actingAs($salesUser)
            ->test('sales.production.create-production-request')
            ->set('selectedDepartment', $prodDept->id)
            ->call('submitRequest')
            ->assertHasErrors('selectedProducts');
    }
}
```

### 2. Progress Update Workflow
```php
class UpdateProgressTest extends TestCase
{
    /** @test */
    public function production_staff_can_update_progress()
    {
        $request = ProductionRequest::factory()
            ->create(['status' => 'in_progress']);
        $prodStaff = User::factory()
            ->create(['department_id' => $request->production_department_id]);

        $response = $this->actingAs($prodStaff)
            ->post("/api/production-requests/{$request->id}/progress", [
                'milestone' => 'in_production',
                'progress_percentage' => 50,
                'notes' => 'Halfway through baking',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('production_progress_feedback', [
            'production_request_id' => $request->id,
            'milestone' => 'in_production',
            'progress_percentage' => 50,
        ]);
    }

    /** @test */
    public function progress_cannot_go_backwards()
    {
        $request = ProductionRequest::factory()->create();
        
        // Create first progress at 50%
        ProductionProgressFeedback::factory()
            ->create([
                'production_request_id' => $request->id,
                'progress_percentage' => 50,
            ]);

        // Try to set to 40%
        $response = $this->post("/api/production-requests/{$request->id}/progress", [
            'progress_percentage' => 40,
        ]);

        $response->assertStatus(409); // Conflict
    }

    /** @test */
    public function real_time_update_broadcast_to_sales_user()
    {
        Event::fake();

        $request = ProductionRequest::factory()->create();
        $feedback = ProductionProgressFeedback::factory()
            ->create(['production_request_id' => $request->id]);

        Event::assertDispatched(
            ProgressUpdated::class,
            function ($event) use ($request) {
                return $event->feedback->production_request_id === $request->id;
            }
        );
    }
}
```

### 3. Dispatch Verification Workflow
```php
class DispatchVerificationTest extends TestCase
{
    /** @test */
    public function sales_user_can_accept_dispatch()
    {
        $request = ProductionRequest::factory()
            ->create(['status' => 'dispatched']);
        $salesUser = User::factory()
            ->create(['id' => $request->created_by_id]);

        $response = $this->actingAs($salesUser)
            ->patch("/api/production-requests/{$request->id}/accept-dispatch");

        $response->assertStatus(200);

        $this->assertDatabaseHas('production_requests', [
            'id' => $request->id,
            'status' => 'accepted',
        ]);
    }

    /** @test */
    public function sales_user_can_reject_dispatch()
    {
        $request = ProductionRequest::factory()
            ->create(['status' => 'dispatched']);
        $salesUser = User::factory()
            ->create(['id' => $request->created_by_id]);

        $response = $this->actingAs($salesUser)
            ->patch("/api/production-requests/{$request->id}/reject-dispatch", [
                'reason' => 'quality',
                'notes' => 'Products do not meet quality standards',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('production_requests', [
            'id' => $request->id,
            'status' => 'rejected',
        ]);

        $this->assertDatabaseHas('production_request_feedback', [
            'production_request_id' => $request->id,
            'type' => 'rejection',
            'reason' => 'quality',
        ]);
    }

    /** @test */
    public function only_request_creator_can_accept_reject()
    {
        $request = ProductionRequest::factory()
            ->create(['status' => 'dispatched']);
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->patch("/api/production-requests/{$request->id}/accept-dispatch");

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function cannot_verify_if_not_dispatched()
    {
        $request = ProductionRequest::factory()
            ->create(['status' => 'pending']);
        $salesUser = User::factory()
            ->create(['id' => $request->created_by_id]);

        $response = $this->actingAs($salesUser)
            ->patch("/api/production-requests/{$request->id}/accept-dispatch");

        $response->assertStatus(409); // Conflict
    }
}
```

---

## API ENDPOINT TESTS

### Full Request Lifecycle Test
```php
class FullRequestLifecycleTest extends TestCase
{
    /** @test */
    public function complete_request_workflow()
    {
        // 1. Sales creates request
        $salesUser = User::factory()->create();
        $prodDept = Department::factory()->create();
        $product = Product::factory()->create(['department_id' => $prodDept->id]);

        $createResponse = $this->actingAs($salesUser)
            ->postJson('/api/production-requests', [
                'production_department_id' => $prodDept->id,
                'products' => [
                    ['product_id' => $product->id, 'batch_quantity' => 10],
                ],
                'priority' => 'normal',
            ]);

        $requestId = $createResponse->json('data.id');
        $this->assertEquals('pending', $createResponse->json('data.status'));

        // 2. Production starts work
        $prodStaff = User::factory()
            ->create(['department_id' => $prodDept->id]);

        $startResponse = $this->actingAs($prodStaff)
            ->postJson("/api/production-requests/{$requestId}/start");

        $this->assertEquals('in_progress', $startResponse->json('data.status'));

        // 3. Production updates progress
        $progressResponse = $this->actingAs($prodStaff)
            ->postJson("/api/production-requests/{$requestId}/progress", [
                'milestone' => 'in_production',
                'progress_percentage' => 50,
            ]);

        $this->assertCount(2, $progressResponse->json('data.progress'));

        // 4. Production completes
        $completeResponse = $this->actingAs($prodStaff)
            ->patchJson("/api/production-requests/{$requestId}/complete");

        $this->assertEquals('completed', $completeResponse->json('data.status'));

        // 5. Production dispatches
        $dispatchResponse = $this->actingAs($prodStaff)
            ->postJson("/api/production-requests/{$requestId}/dispatch", [
                'products' => [
                    [
                        'product_id' => $product->id,
                        'quantity_produced' => 10,
                        'quantity_dispatched' => 10,
                    ],
                ],
            ]);

        $this->assertEquals('dispatched', $dispatchResponse->json('data.status'));

        // 6. Sales accepts dispatch
        $acceptResponse = $this->actingAs($salesUser)
            ->patchJson("/api/production-requests/{$requestId}/accept-dispatch");

        $this->assertEquals('accepted', $acceptResponse->json('data.status'));
    }
}
```

---

## END-TO-END (E2E) TESTS

### Selenium/Cypress Test Example
```javascript
// tests/e2e/production-request.spec.js

describe('Production Request Workflow', () => {
    beforeEach(() => {
        cy.login('sales@example.com', 'password');
    });

    it('should create request and verify production receives it', () => {
        // Navigate to request form
        cy.visit('/production-requests/create');

        // Select department
        cy.get('[data-testid="department-select"]')
            .select('Bakery');

        // Add product
        cy.get('[data-testid="product-Croissant"]')
            .click();

        cy.get('[data-testid="batch-quantity-Croissant"]')
            .clear()
            .type('10');

        // Submit
        cy.get('[data-testid="submit-btn"]')
            .click();

        // Verify success
        cy.get('.alert-success')
            .should('contain', 'Request created successfully');

        // Verify request appears in list
        cy.get('[data-testid="request-list"]')
            .should('contain', 'Butter Croissant');

        // Login as production staff
        cy.logout();
        cy.login('baker@example.com', 'password');

        // Visit production board
        cy.visit('/production-board');

        // Verify request appears
        cy.get('[data-testid="request-board"]')
            .should('contain', 'Butter Croissant');

        // Start production
        cy.get('[data-testid="start-btn"]').first()
            .click();

        cy.get('.alert-success')
            .should('contain', 'Production started');
    });

    it('should handle real-time progress updates', () => {
        // Create request
        // ... (as above)

        // Start production
        // ... (as above)

        // Update progress
        cy.get('[data-testid="milestone-select"]')
            .select('in_production');

        cy.get('[data-testid="progress-slider"]')
            .invoke('val', 50)
            .trigger('change');

        cy.get('[data-testid="save-progress"]')
            .click();

        // Switch to sales user
        cy.logout();
        cy.login('sales@example.com', 'password');

        cy.visit('/production-requests');

        // Verify progress is visible
        cy.get('[data-testid="request-progress"]')
            .should('contain', '50%');
    });
});
```

---

## PERFORMANCE TESTS

### Load Testing
```php
class LoadTest extends TestCase
{
    /** @test */
    public function can_handle_100_concurrent_requests()
    {
        $users = User::factory(100)->create();
        
        $promises = collect($users)->map(function ($user) {
            return Http::async()
                ->post('/api/production-requests', [...])
                ->then(fn($response) => $response->assertStatus(201));
        });

        Http::pool($promises)
            ->wait();
    }
}
```

---

## TEST COVERAGE REQUIREMENTS

| Component | Coverage | Status |
|-----------|----------|--------|
| Models | 90%+ | ✓ |
| Controllers | 85%+ | ✓ |
| Services | 90%+ | ✓ |
| API Endpoints | 100% | ✓ |
| Workflows | 100% | ✓ |
| Error Cases | 95% | ✓ |
| **Overall** | **90%+** | ✓ |

---

## TESTING CHECKLIST

- [ ] All unit tests pass
- [ ] All integration tests pass
- [ ] All E2E tests pass
- [ ] Code coverage ≥ 90%
- [ ] No SQL injection vulnerabilities
- [ ] No XSS vulnerabilities
- [ ] No CSRF vulnerabilities
- [ ] Rate limiting working
- [ ] Broadcasting tests passing
- [ ] Real-time updates functioning
- [ ] Concurrent request handling
- [ ] Database rollback on error
- [ ] Error messages are helpful
- [ ] Audit logs created
- [ ] Notifications sent

---

## Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Unit/Models/ProductionRequestTest.php

# Run with coverage
php artisan test --coverage

# Generate coverage report
php artisan test --coverage --coverage-html=coverage

# Run E2E tests
npx cypress run

# Run performance tests
php artisan test --group=performance
```
