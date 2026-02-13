<?php

use App\Models\Branch;
use App\Models\Item;
use App\Models\UnitOfMeasure;
use App\Services\UomConversionService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Schema::dropIfExists('uom_conversions');
    Schema::dropIfExists('items');
    Schema::dropIfExists('branches');
    Schema::dropIfExists('units_of_measure');

    Schema::create('units_of_measure', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();
        $table->string('name');
        $table->string('symbol');
        $table->string('category');
        $table->integer('sort_order')->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    Schema::create('branches', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('name')->unique();
        $table->string('code')->unique();
        $table->string('location');
        $table->string('email')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    Schema::create('items', function (Blueprint $table) {
        $table->id();
        $table->uuid('branch_id');
        $table->string('name');
        $table->string('sku')->unique();
        $table->string('category');
        $table->unsignedBigInteger('uom_id')->nullable();
        $table->string('status')->default('active');
        $table->timestamps();
    });

    Schema::create('uom_conversions', function (Blueprint $table) {
        $table->id();
        $table->uuid('branch_id')->nullable();
        $table->unsignedBigInteger('item_id')->nullable();
        $table->uuid('product_id')->nullable();
        $table->unsignedBigInteger('from_uom_id');
        $table->unsignedBigInteger('to_uom_id');
        $table->decimal('factor', 24, 12);
        $table->boolean('is_active')->default(true);
        $table->boolean('is_system')->default(false);
        $table->text('notes')->nullable();
        $table->uuid('created_by_id')->nullable();
        $table->string('created_by_type')->nullable();
        $table->timestamps();
    });
});

it('converts quantities using direct and inverse conversions', function () {
    $grams = UnitOfMeasure::query()->create([
        'code' => 'g',
        'name' => 'Grams',
        'symbol' => 'g',
        'category' => 'weight',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $kilograms = UnitOfMeasure::query()->create([
        'code' => 'kg',
        'name' => 'Kilograms',
        'symbol' => 'kg',
        'category' => 'weight',
        'sort_order' => 2,
        'is_active' => true,
    ]);

    $service = app(UomConversionService::class);
    $service->setConversion($grams->id, $kilograms->id, 0.001);

    expect($service->convert(5000, $grams->id, $kilograms->id))->toEqualWithDelta(5.0, 0.000000000001);
    expect($service->convert(2, $kilograms->id, $grams->id))->toEqualWithDelta(2000.0, 0.000000000001);
});

it('resolves chained conversions when direct conversion does not exist', function () {
    $grams = UnitOfMeasure::query()->create([
        'code' => 'g',
        'name' => 'Grams',
        'symbol' => 'g',
        'category' => 'weight',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $kilograms = UnitOfMeasure::query()->create([
        'code' => 'kg',
        'name' => 'Kilograms',
        'symbol' => 'kg',
        'category' => 'weight',
        'sort_order' => 2,
        'is_active' => true,
    ]);

    $pounds = UnitOfMeasure::query()->create([
        'code' => 'lb',
        'name' => 'Pounds',
        'symbol' => 'lb',
        'category' => 'weight',
        'sort_order' => 3,
        'is_active' => true,
    ]);

    $service = app(UomConversionService::class);
    $service->setConversion($grams->id, $kilograms->id, 0.001);
    $service->setConversion($kilograms->id, $pounds->id, 2.20462262185);

    expect($service->convert(500, $grams->id, $pounds->id))->toEqualWithDelta(1.102311310925, 0.0000000001);
});

it('prefers item scoped conversions over global conversions when context matches', function () {
    $grams = UnitOfMeasure::query()->create([
        'code' => 'g',
        'name' => 'Grams',
        'symbol' => 'g',
        'category' => 'weight',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $cup = UnitOfMeasure::query()->create([
        'code' => 'cup',
        'name' => 'Cup',
        'symbol' => 'cup',
        'category' => 'volume',
        'sort_order' => 2,
        'is_active' => true,
    ]);

    $branch = Branch::query()->create([
        'id' => (string) Str::uuid(),
        'name' => 'Branch '.Str::upper(Str::random(6)),
        'code' => 'BR'.Str::upper(Str::random(4)),
        'location' => 'Main location',
        'email' => Str::lower(Str::random(8)).'@example.com',
        'is_active' => true,
    ]);

    $item = Item::query()->create([
        'branch_id' => $branch->id,
        'name' => 'Milk Mix',
        'sku' => 'ITM-'.Str::upper(Str::random(8)),
        'category' => 'raw_material',
        'uom_id' => $grams->id,
        'status' => 'active',
    ]);

    $service = app(UomConversionService::class);
    $service->setConversion($grams->id, $cup->id, 0.0042267528, [], true, ['is_system' => true]);
    $service->setConversion(
        $grams->id,
        $cup->id,
        0.01,
        ['branch_id' => $branch->id, 'item_id' => $item->id],
        true,
        ['is_system' => false]
    );

    $global = $service->convert(100, $grams->id, $cup->id);
    $itemScoped = $service->convert(100, $grams->id, $cup->id, [
        'branch_id' => $branch->id,
        'item_id' => $item->id,
    ]);

    expect($global)->toEqualWithDelta(0.42267528, 0.0000000001);
    expect($itemScoped)->toEqualWithDelta(1.0, 0.0000000001);
});

it('returns null from tryConvert when no conversion exists', function () {
    $grams = UnitOfMeasure::query()->create([
        'code' => 'g',
        'name' => 'Grams',
        'symbol' => 'g',
        'category' => 'weight',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $cup = UnitOfMeasure::query()->create([
        'code' => 'cup',
        'name' => 'Cup',
        'symbol' => 'cup',
        'category' => 'volume',
        'sort_order' => 2,
        'is_active' => true,
    ]);

    $service = app(UomConversionService::class);

    expect($service->tryConvert(12, $grams->id, $cup->id))->toBeNull();
});
