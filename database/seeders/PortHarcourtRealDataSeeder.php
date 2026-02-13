<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Department;
use App\Models\DepartmentCategory;
use App\Models\Item;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Stock;
use App\Models\UnitOfMeasure;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use Spatie\Permission\Models\Role;

class PortHarcourtRealDataSeeder extends Seeder
{
    private const BRANCH_CODE = 'PHC-002';
    private const BRANCH_NAME = 'SweetTooth Port Harcourt';

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataDir = base_path('real data');

        $paths = [
            'hr' => $dataDir.'/HR.xlsx',
            'hot_kitchen_recipes' => $dataDir.'/HOT KITCHEN RECIPES EXCEL.xlsx',
            'product_prices' => $dataDir.'/SWEETTOOTH PRODUCT PRICES.xlsx',
            'production_analysis' => [
                $dataDir.'/PRODUCTION_ANALYSIS_DECEMBER_2025.xlsx',
                $dataDir.'/PRODUCTION_ANALYSIS_DECEMBER_2025 (1).xlsx',
            ],
        ];

        $this->command->info('Seeding Port Harcourt real-data workflow from Excel files...');

        $hrUsers = $this->parseHrUsers($paths['hr']);
        $hotKitchenData = $this->parseHotKitchenRecipes($paths['hot_kitchen_recipes']);
        $priceProducts = $this->parsePriceWorkbookProducts($paths['product_prices']);

        $analysisProducts = [];
        foreach ($paths['production_analysis'] as $analysisPath) {
            $analysisProducts = array_merge($analysisProducts, $this->parseProductionAnalysisProducts($analysisPath));
        }

        $compiledProducts = $this->compileProducts(
            $priceProducts,
            $analysisProducts,
            $hotKitchenData['products']
        );

        DB::transaction(function () use ($hrUsers, $hotKitchenData, $compiledProducts): void {
            $branch = $this->seedBranch();
            $categories = $this->seedCategories();
            $departments = $this->seedDepartments($branch, $categories);
            $uomMap = $this->buildUomMap();
            $productTypes = $this->seedProductTypes($departments);

            $users = $this->seedUsers($hrUsers, $branch, $departments);

            $items = $this->seedItems($hotKitchenData['items'], $branch, $uomMap);
            $products = $this->seedProducts($compiledProducts, $branch, $productTypes, $uomMap);
            $this->attachProductsToSalesDepartments($compiledProducts, $products, $departments);
            $this->seedRecipes($hotKitchenData['recipes'], $products, $items, $branch, $departments, $uomMap, $users);
            $this->ensureSuperAdminUser($branch, $departments);
        });

        $this->command->info('Port Harcourt real-data seeding completed.');
    }

    /**
     * @return array<string, DepartmentCategory>
     */
    private function seedCategories(): array
    {
        $categories = [
            'sales' => DepartmentCategory::updateOrCreate(
                ['name' => 'Sales'],
                ['description' => 'Departments responsible for sales operations.']
            ),
            'production' => DepartmentCategory::updateOrCreate(
                ['name' => 'Production'],
                ['description' => 'Departments responsible for preparing products.']
            ),
            'support' => DepartmentCategory::updateOrCreate(
                ['name' => 'Support'],
                ['description' => 'Support departments for shared operations.']
            ),
        ];

        return $categories;
    }

    private function seedBranch(): Branch
    {
        return Branch::updateOrCreate(
            ['code' => self::BRANCH_CODE],
            [
                'name' => self::BRANCH_NAME,
                'location' => '78 Trans Amadi Industrial Layout',
                'phone' => '+234-803-456-7890',
                'email' => 'portharcourt@sweettooth.com',
                'description' => 'Port Harcourt main branch',
                'country' => 'Nigeria',
                'state' => 'Rivers',
                'city' => 'Port Harcourt',
                'postal_code' => '500001',
                'timezone' => 'Africa/Lagos',
                'is_active' => true,
            ]
        );
    }

    /**
     * @param  array<string, DepartmentCategory>  $categories
     * @return array<string, Department>
     */
    private function seedDepartments(Branch $branch, array $categories): array
    {
        $definitions = [
            'hot_kitchen' => [
                'name' => 'Hot Kitchen Production',
                'category_id' => $categories['production']->id,
                'description' => 'Handles hot-kitchen production workflows.',
            ],
            'pastry' => [
                'name' => 'Pastry Production',
                'category_id' => $categories['production']->id,
                'description' => 'Handles pastry production workflows.',
            ],
            'gelato' => [
                'name' => 'Gelato Production',
                'category_id' => $categories['production']->id,
                'description' => 'Handles gelato production workflows.',
            ],
            'cornerstone' => [
                'name' => 'Cornerstone Production',
                'category_id' => $categories['production']->id,
                'description' => 'Production-only corner-store preparation line.',
            ],
            'till_concession' => [
                'name' => 'Till/Concession',
                'category_id' => $categories['sales']->id,
                'description' => 'Sales till and concession operations.',
            ],
            'corner_store' => [
                'name' => 'Corner Store',
                'category_id' => $categories['sales']->id,
                'description' => 'Corner store sales operations.',
            ],
            'inventory' => [
                'name' => 'Inventory/Store',
                'category_id' => $categories['support']->id,
                'description' => 'Inventory and stock control.',
            ],
            'hr' => [
                'name' => 'HR',
                'category_id' => $categories['support']->id,
                'description' => 'Human resources support.',
            ],
        ];

        $departments = [];
        foreach ($definitions as $key => $definition) {
            $slug = Str::slug($definition['name']);
            $departments[$key] = Department::updateOrCreate(
                [
                    'branch_id' => $branch->id,
                    'slug' => $slug,
                ],
                [
                    'category_id' => $definition['category_id'],
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                ]
            );
        }

        return $departments;
    }

    /**
     * @param  array<string, Department>  $departments
     * @return array<string, ProductType>
     */
    private function seedProductTypes(array $departments): array
    {
        $definitions = [
            'hot_kitchen' => [
                'name' => 'Hot Kitchen Items',
                'code' => 'HK',
                'department_id' => $departments['hot_kitchen']->id,
                'description' => 'Products produced by Hot Kitchen.',
            ],
            'pastry' => [
                'name' => 'Pastry Items',
                'code' => 'PS',
                'department_id' => $departments['pastry']->id,
                'description' => 'Products produced by Pastry.',
            ],
            'gelato' => [
                'name' => 'Gelato Items',
                'code' => 'GL',
                'department_id' => $departments['gelato']->id,
                'description' => 'Products produced by Gelato.',
            ],
            'cornerstone' => [
                'name' => 'Cornerstone Items',
                'code' => 'CS',
                'department_id' => $departments['cornerstone']->id,
                'description' => 'Products produced by Cornerstone.',
            ],
        ];

        $productTypes = [];
        foreach ($definitions as $key => $definition) {
            $productType = ProductType::withTrashed()->firstOrNew(['code' => $definition['code']]);
            if ($productType->exists && $productType->trashed()) {
                $productType->restore();
            }

            $productType->fill([
                'department_id' => $definition['department_id'],
                'name' => $definition['name'],
                'description' => $definition['description'],
                'status' => 'active',
                'sort_order' => 1,
            ])->save();

            $productTypes[$key] = $productType;
        }

        return $productTypes;
    }

    /**
     * @return array<string, int>
     */
    private function buildUomMap(): array
    {
        return UnitOfMeasure::query()
            ->pluck('id', 'code')
            ->mapWithKeys(static fn ($id, $code) => [strtolower((string) $code) => (int) $id])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parseHrUsers(string $filePath): array
    {
        if (! file_exists($filePath)) {
            $this->command->warn("HR workbook not found: {$filePath}");

            return [];
        }

        $sheet = IOFactory::load($filePath)->getSheet(0);
        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = Coordinate::columnIndexFromString($sheet->getHighestDataColumn());

        $headerRow = [];
        for ($column = 1; $column <= $highestColumn; $column++) {
            $headerRow[$column] = $this->normalizeHeaderName((string) $sheet->getCellByColumnAndRow($column, 1)->getFormattedValue());
        }

        $nameColumn = $this->findColumn($headerRow, ['name']) ?? 2;
        $resumptionColumn = $this->findColumn($headerRow, ['resumption_date']) ?? 3;
        $jobColumn = $this->findColumn($headerRow, ['job_description']) ?? 4;
        $dobColumn = $this->findColumn($headerRow, ['dob']) ?? 5;
        $emailColumn = $this->findColumn($headerRow, ['email']) ?? 6;
        $addressColumn = $this->findColumn($headerRow, ['home_address']) ?? 8;
        $phoneColumn = $this->findColumn($headerRow, ['phone_number']) ?? 9;
        $emergencyColumn = $this->findColumn($headerRow, ['contact_in_case_of_emergency']) ?? 10;

        $records = [];
        for ($row = 2; $row <= $highestRow; $row++) {
            $name = trim((string) $sheet->getCellByColumnAndRow($nameColumn, $row)->getFormattedValue());
            if ($name === '') {
                continue;
            }

            $records[] = [
                'name' => $name,
                'job_description' => trim((string) $sheet->getCellByColumnAndRow($jobColumn, $row)->getFormattedValue()),
                'email' => trim((string) $sheet->getCellByColumnAndRow($emailColumn, $row)->getFormattedValue()),
                'phone' => trim((string) $sheet->getCellByColumnAndRow($phoneColumn, $row)->getFormattedValue()),
                'address' => trim((string) $sheet->getCellByColumnAndRow($addressColumn, $row)->getFormattedValue()),
                'dob' => trim((string) $sheet->getCellByColumnAndRow($dobColumn, $row)->getFormattedValue()),
                'resumption_date' => trim((string) $sheet->getCellByColumnAndRow($resumptionColumn, $row)->getFormattedValue()),
                'emergency_contact' => trim((string) $sheet->getCellByColumnAndRow($emergencyColumn, $row)->getFormattedValue()),
            ];
        }

        return $records;
    }

    /**
     * @return array{products: array<int, array<string, mixed>>, recipes: array<int, array<string, mixed>>, items: array<int, array<string, mixed>>}
     */
    private function parseHotKitchenRecipes(string $filePath): array
    {
        if (! file_exists($filePath)) {
            $this->command->warn("Hot Kitchen recipe workbook not found: {$filePath}");

            return [
                'products' => [],
                'recipes' => [],
                'items' => [],
            ];
        }

        $sheet = IOFactory::load($filePath)->getSheet(0);
        $highestRow = $sheet->getHighestDataRow();

        $products = [];
        $recipes = [];
        $itemsMap = [];
        $currentRecipe = null;

        for ($row = 1; $row <= $highestRow; $row++) {
            $first = trim((string) $sheet->getCellByColumnAndRow(1, $row)->getFormattedValue());
            $second = trim((string) $sheet->getCellByColumnAndRow(2, $row)->getFormattedValue());
            $third = trim((string) $sheet->getCellByColumnAndRow(3, $row)->getFormattedValue());

            if ($first === '') {
                continue;
            }

            $upperFirst = strtoupper($first);
            if ($upperFirst === 'INGREDIENT' || Str::startsWith($upperFirst, 'WASTAGE')) {
                continue;
            }

            if (Str::startsWith($upperFirst, 'TOTAL OUTPUT QUANTITY:')) {
                if ($currentRecipe !== null) {
                    [$yieldQuantity, $yieldUom] = $this->extractQuantityAndUom($first);
                    if ($yieldQuantity !== null) {
                        $currentRecipe['yield_quantity'] = $yieldQuantity;
                    }
                    if ($yieldUom !== null) {
                        $currentRecipe['yield_uom'] = $yieldUom;
                    }

                    $priceText = $second !== '' ? $second : $third;
                    $currentRecipe['price'] = $this->toDecimal($priceText) ?? $currentRecipe['price'];
                    $recipes[] = $currentRecipe;

                    $products[] = [
                        'name' => $currentRecipe['name'],
                        'source' => $currentRecipe['source'],
                        'price' => $currentRecipe['price'],
                        'unit_weight' => null,
                        'uom_code' => $this->toUomCode($currentRecipe['yield_uom']),
                    ];
                }

                $currentRecipe = null;

                continue;
            }

            // Product title rows
            if ($second === '' && $third === '') {
                $name = $this->cleanName($first);
                if ($name === '') {
                    continue;
                }

                $currentRecipe = [
                    'name' => $name,
                    'source' => $this->inferSourceFromName($name),
                    'price' => 0.0,
                    'yield_quantity' => 1.0,
                    'yield_uom' => 'PCS',
                    'ingredients' => [],
                ];

                continue;
            }

            if ($currentRecipe === null) {
                continue;
            }

            $ingredientName = $this->cleanName($first);
            if ($ingredientName === '') {
                continue;
            }

            [$quantity, $uom] = $this->extractQuantityAndUom($second);
            $wastage = $this->toPercentage($third) ?? 0.0;

            $currentRecipe['ingredients'][] = [
                'name' => $ingredientName,
                'quantity' => $quantity ?? 0.0,
                'uom' => $uom ?? 'G',
                'wastage_percent' => $wastage,
            ];

            $itemKey = $this->normalizeNameKey($ingredientName);
            if (! isset($itemsMap[$itemKey])) {
                $itemsMap[$itemKey] = [
                    'name' => $ingredientName,
                    'uom_code' => $this->toUomCode($uom),
                ];
            }
        }

        return [
            'products' => $products,
            'recipes' => $recipes,
            'items' => array_values($itemsMap),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parsePriceWorkbookProducts(string $filePath): array
    {
        if (! file_exists($filePath)) {
            $this->command->warn("Product prices workbook not found: {$filePath}");

            return [];
        }

        $sheet = IOFactory::load($filePath)->getSheet(0);
        $highestRow = $sheet->getHighestDataRow();

        $products = [];
        for ($row = 4; $row <= $highestRow; $row++) {
            $gelatoName = trim((string) $sheet->getCellByColumnAndRow(2, $row)->getFormattedValue());
            $gelatoWeight = trim((string) $sheet->getCellByColumnAndRow(3, $row)->getFormattedValue());
            $gelatoPrice = trim((string) $sheet->getCellByColumnAndRow(4, $row)->getFormattedValue());
            if ($gelatoName !== '') {
                $products[] = [
                    'name' => $this->cleanName($gelatoName),
                    'source' => 'gelato',
                    'price' => $this->toDecimal($gelatoPrice),
                    'unit_weight' => $this->toDecimal($gelatoWeight),
                    'uom_code' => 'g',
                ];
            }

            $pastryName = trim((string) $sheet->getCellByColumnAndRow(7, $row)->getFormattedValue());
            $pastryPrice = trim((string) $sheet->getCellByColumnAndRow(8, $row)->getFormattedValue());
            if ($pastryName !== '') {
                $products[] = [
                    'name' => $this->cleanName($pastryName),
                    'source' => 'pastry',
                    'price' => $this->toDecimal($pastryPrice),
                    'unit_weight' => null,
                    'uom_code' => 'pcs',
                ];
            }

            $tillName = trim((string) $sheet->getCellByColumnAndRow(11, $row)->getFormattedValue());
            $tillPrice = trim((string) $sheet->getCellByColumnAndRow(12, $row)->getFormattedValue());
            if ($tillName !== '') {
                $products[] = [
                    'name' => $this->cleanName($tillName),
                    'source' => 'till_concession',
                    'price' => $this->toDecimal($tillPrice),
                    'unit_weight' => null,
                    'uom_code' => 'pcs',
                ];
            }
        }

        return array_values(array_filter($products, static fn (array $entry) => $entry['name'] !== ''));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parseProductionAnalysisProducts(string $filePath): array
    {
        if (! file_exists($filePath)) {
            return [];
        }

        $sheetSourceMap = [
            'GELATO' => 'gelato',
            'PASTRY' => 'pastry',
            'CORNERSTORE.' => 'cornerstone',
            'CONCESSION' => 'till_concession',
            'HOT KITCHEN' => 'hot_kitchen',
        ];

        $products = [];
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $reader->setReadEmptyCells(false);
        $reader->setReadFilter(new class implements IReadFilter
        {
            public function readCell($columnAddress, $row, $worksheetName = ''): bool
            {
                return $row <= 260 && Coordinate::columnIndexFromString($columnAddress) <= 7;
            }
        });

        foreach ($sheetSourceMap as $sheetName => $source) {
            $reader->setLoadSheetsOnly([$sheetName]);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();

            for ($row = 3; $row <= 260; $row++) {
                $name = trim((string) $sheet->getCellByColumnAndRow(1, $row)->getFormattedValue());
                if ($name === '') {
                    continue;
                }

                if (in_array(strtoupper($name), ['PRODUCTS', 'RAW MATERIALS', 'COFFEE'], true)) {
                    continue;
                }

                $products[] = [
                    'name' => $this->cleanName($name),
                    'source' => $source,
                    'price' => null,
                    'cost_per_unit' => $this->toDecimal((string) $sheet->getCellByColumnAndRow(4, $row)->getFormattedValue()),
                    'unit_weight' => null,
                    'uom_code' => $this->toUomCode((string) $sheet->getCellByColumnAndRow(5, $row)->getFormattedValue()),
                ];
            }

            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }

        return array_values(array_filter($products, static fn (array $entry) => $entry['name'] !== ''));
    }

    /**
     * @param  array<int, array<string, mixed>>  $priceProducts
     * @param  array<int, array<string, mixed>>  $analysisProducts
     * @param  array<int, array<string, mixed>>  $recipeProducts
     * @return array<string, array<string, mixed>>
     */
    private function compileProducts(array $priceProducts, array $analysisProducts, array $recipeProducts): array
    {
        $compiled = [];

        foreach (array_merge($priceProducts, $analysisProducts, $recipeProducts) as $entry) {
            $name = (string) Arr::get($entry, 'name', '');
            if ($name === '') {
                continue;
            }

            $key = $this->normalizeNameKey($name);
            $source = (string) Arr::get($entry, 'source', 'hot_kitchen');
            $departmentKey = $this->sourceToDepartmentKey($source, $name);
            $salesDepartmentKeys = $this->sourceToSalesDepartmentKeys($source, $name);
            $sourcePriority = $this->sourcePriority($source);

            if (! isset($compiled[$key])) {
                $compiled[$key] = [
                    'name' => $name,
                    'source' => $source,
                    'source_priority' => $sourcePriority,
                    'department_key' => $departmentKey,
                    'sales_department_keys' => $salesDepartmentKeys,
                    'price' => Arr::get($entry, 'price'),
                    'cost_per_unit' => Arr::get($entry, 'cost_per_unit'),
                    'unit_weight' => Arr::get($entry, 'unit_weight'),
                    'uom_code' => Arr::get($entry, 'uom_code', 'pcs'),
                ];

                continue;
            }

            if ($sourcePriority > $compiled[$key]['source_priority']) {
                $compiled[$key]['source'] = $source;
                $compiled[$key]['source_priority'] = $sourcePriority;
                $compiled[$key]['department_key'] = $departmentKey;
            }

            $compiled[$key]['sales_department_keys'] = array_values(array_unique(array_merge(
                $compiled[$key]['sales_department_keys'],
                $salesDepartmentKeys
            )));

            if (($compiled[$key]['price'] === null || (float) $compiled[$key]['price'] <= 0) && Arr::get($entry, 'price') !== null) {
                $compiled[$key]['price'] = Arr::get($entry, 'price');
            }

            if (($compiled[$key]['cost_per_unit'] === null || (float) $compiled[$key]['cost_per_unit'] <= 0) && Arr::get($entry, 'cost_per_unit') !== null) {
                $compiled[$key]['cost_per_unit'] = Arr::get($entry, 'cost_per_unit');
            }

            if (($compiled[$key]['unit_weight'] === null || (float) $compiled[$key]['unit_weight'] <= 0) && Arr::get($entry, 'unit_weight') !== null) {
                $compiled[$key]['unit_weight'] = Arr::get($entry, 'unit_weight');
            }

            if (($compiled[$key]['uom_code'] === null || $compiled[$key]['uom_code'] === 'pcs') && Arr::get($entry, 'uom_code') !== null) {
                $compiled[$key]['uom_code'] = Arr::get($entry, 'uom_code');
            }
        }

        foreach ($compiled as $key => $entry) {
            unset($compiled[$key]['source_priority']);
            $compiled[$key]['price'] = (float) ($compiled[$key]['price'] ?? 0);
            $compiled[$key]['cost_per_unit'] = (float) ($compiled[$key]['cost_per_unit'] ?? 0);
        }

        return $compiled;
    }

    /**
     * @param  array<int, array<string, mixed>>  $records
     * @param  array<string, Department>  $departments
     * @return array<int, User>
     */
    private function seedUsers(array $records, Branch $branch, array $departments): array
    {
        $createdUsers = [];
        $defaultPasswordHash = Hash::make('password');
        $counter = 1;

        foreach ($records as $record) {
            $name = trim((string) Arr::get($record, 'name', ''));
            if ($name === '') {
                continue;
            }

            $jobDescription = (string) Arr::get($record, 'job_description', '');
            $department = $this->resolveDepartmentFromJob($jobDescription, $departments);
            $email = $this->sanitizeEmail((string) Arr::get($record, 'email', ''), $name, $counter);

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => $defaultPasswordHash,
                    'branch_id' => $branch->id,
                    'last_accessed_branch_id' => $branch->id,
                    'is_active' => true,
                    'employee_number' => sprintf('PHC-EMP-%04d', $counter),
                    'department_id' => $department?->id,
                    'phone' => $this->extractPhone((string) Arr::get($record, 'phone', '')),
                    'hire_date' => $this->normalizeDate((string) Arr::get($record, 'resumption_date', '')),
                    'date_of_birth' => $this->normalizeDate((string) Arr::get($record, 'dob', ''), 'Y-m-d'),
                    'address' => (string) Arr::get($record, 'address', ''),
                    'emergency_contact_name' => Str::limit((string) Arr::get($record, 'emergency_contact', ''), 250, ''),
                    'employment_status' => 'active',
                    'user_type' => 'employee',
                    'email_verified_at' => now(),
                ]
            );

            $roleName = $this->resolveRoleFromJob($jobDescription);
            if ($roleName !== null) {
                $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
                if ($role !== null) {
                    $user->syncRoles([$role]);
                }
            }

            $createdUsers[] = $user;
            $counter++;
        }

        return $createdUsers;
    }

    /**
     * @param  array<int, array<string, mixed>>  $itemRecords
     * @param  array<string, int>  $uomMap
     * @return array<string, Item>
     */
    private function seedItems(array $itemRecords, Branch $branch, array $uomMap): array
    {
        $items = [];

        foreach ($itemRecords as $record) {
            $name = trim((string) Arr::get($record, 'name', ''));
            if ($name === '') {
                continue;
            }

            $normalizedKey = $this->normalizeNameKey($name);
            $sku = 'PHC-ITM-'.strtoupper(substr(sha1($normalizedKey), 0, 8));
            $uomCode = strtolower((string) Arr::get($record, 'uom_code', 'unit'));
            $uomId = $uomMap[$uomCode] ?? $uomMap['unit'] ?? $uomMap['pcs'] ?? null;

            $item = Item::updateOrCreate(
                ['sku' => $sku],
                [
                    'branch_id' => $branch->id,
                    'name' => $name,
                    'category' => $this->inferItemCategory($name),
                    'uom_id' => $uomId,
                    'reorder_level' => 10,
                    'max_stock_level' => 500,
                    'status' => 'active',
                ]
            );

            Stock::updateOrCreate(
                [
                    'branch_id' => $branch->id,
                    'item_id' => $item->id,
                ],
                [
                    'quantity_available' => 500,
                    'quantity_reserved' => 0,
                    'quantity_damaged' => 0,
                    'average_cost' => 0,
                    'health_status' => 'good',
                ]
            );

            $items[$normalizedKey] = $item;
        }

        return $items;
    }

    /**
     * @param  array<string, array<string, mixed>>  $compiledProducts
     * @param  array<string, ProductType>  $productTypes
     * @param  array<string, int>  $uomMap
     * @return array<string, Product>
     */
    private function seedProducts(array $compiledProducts, Branch $branch, array $productTypes, array $uomMap): array
    {
        $products = [];

        foreach ($compiledProducts as $key => $entry) {
            $name = (string) Arr::get($entry, 'name', '');
            if ($name === '') {
                continue;
            }

            $departmentKey = (string) Arr::get($entry, 'department_key', 'hot_kitchen');
            $productType = $productTypes[$departmentKey] ?? $productTypes['hot_kitchen'];

            $uomCode = strtolower((string) Arr::get($entry, 'uom_code', 'pcs'));
            $uomId = $uomMap[$uomCode] ?? $uomMap['pcs'] ?? $uomMap['unit'] ?? null;

            $sku = 'PHC-PRD-'.strtoupper(substr(sha1($key), 0, 8));

            $product = Product::withTrashed()->firstOrNew(['sku' => $sku]);
            if ($product->exists && $product->trashed()) {
                $product->restore();
            }

            $price = (float) Arr::get($entry, 'price', 0);
            $cost = (float) Arr::get($entry, 'cost_per_unit', 0);
            if ($price <= 0 && $cost > 0) {
                $price = round($cost * 1.2, 2);
            }

            $product->fill([
                'branch_id' => $branch->id,
                'name' => $name,
                'product_type_id' => $productType->id,
                'description' => 'Seeded from real-data workbook source: '.Arr::get($entry, 'source', 'unknown'),
                'price' => $price,
                'cost' => $cost > 0 ? $cost : null,
                'shelf_life_days' => 3,
                'uom_id' => $uomId,
                'unit_weight' => Arr::get($entry, 'unit_weight'),
                'is_active' => true,
                'is_available' => true,
            ])->save();

            $products[$key] = $product;
        }

        return $products;
    }

    /**
     * @param  array<string, array<string, mixed>>  $compiledProducts
     * @param  array<string, Product>  $products
     * @param  array<string, Department>  $departments
     */
    private function attachProductsToSalesDepartments(array $compiledProducts, array $products, array $departments): void
    {
        $sortOrder = [
            'till_concession' => 1,
            'corner_store' => 1,
        ];

        foreach ($compiledProducts as $key => $entry) {
            $product = $products[$key] ?? null;
            if ($product === null) {
                continue;
            }

            foreach ((array) Arr::get($entry, 'sales_department_keys', []) as $departmentKey) {
                $department = $departments[$departmentKey] ?? null;
                if ($department === null) {
                    continue;
                }

                DB::table('department_product')->updateOrInsert(
                    [
                        'department_id' => $department->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'is_available' => true,
                        'department_price' => $product->price,
                        'sort_order' => $sortOrder[$departmentKey] ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $sortOrder[$departmentKey] = ($sortOrder[$departmentKey] ?? 1) + 1;
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $recipes
     * @param  array<string, Product>  $products
     * @param  array<string, Item>  $items
     * @param  array<string, Department>  $departments
     * @param  array<string, int>  $uomMap
     * @param  array<int, User>  $users
     */
    private function seedRecipes(
        array $recipes,
        array $products,
        array $items,
        Branch $branch,
        array $departments,
        array $uomMap,
        array $users
    ): void {
        $creator = $users[0] ?? User::query()->where('branch_id', $branch->id)->first();
        if ($creator === null) {
            return;
        }

        foreach ($recipes as $recipeData) {
            $name = (string) Arr::get($recipeData, 'name', '');
            if ($name === '') {
                continue;
            }

            $productKey = $this->normalizeNameKey($name);
            $product = $products[$productKey] ?? null;
            if ($product === null) {
                continue;
            }

            $source = (string) Arr::get($recipeData, 'source', 'hot_kitchen');
            $departmentKey = $this->sourceToDepartmentKey($source, $name);
            $department = $departments[$departmentKey] ?? $departments['hot_kitchen'];

            $recipeSku = 'PHC-RCP-'.strtoupper(substr(sha1($product->sku.'-recipe'), 0, 10));
            $yieldUom = $this->toUomCode((string) Arr::get($recipeData, 'yield_uom', 'pcs'));

            $recipe = Recipe::updateOrCreate(
                ['sku' => $recipeSku],
                [
                    'branch_id' => $branch->id,
                    'department_id' => $department->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_type_id' => $product->product_type_id,
                    'cost_per_unit' => (float) ($product->cost ?? 0),
                    'uom_id' => $uomMap[$yieldUom] ?? $uomMap['pcs'] ?? $uomMap['unit'] ?? null,
                    'yield_quantity' => (float) Arr::get($recipeData, 'yield_quantity', 1),
                    'preparation_time' => null,
                    'instructions' => null,
                    'status' => 'active',
                    'created_by_id' => $creator->id,
                    'created_by_type' => User::class,
                ]
            );

            $ingredientSortOrder = 1;
            foreach ((array) Arr::get($recipeData, 'ingredients', []) as $ingredientData) {
                $ingredientName = (string) Arr::get($ingredientData, 'name', '');
                if ($ingredientName === '') {
                    continue;
                }

                $itemKey = $this->normalizeNameKey($ingredientName);
                $item = $items[$itemKey] ?? null;
                if ($item === null) {
                    continue;
                }

                $uomCode = $this->toUomCode((string) Arr::get($ingredientData, 'uom', 'g'));
                $uomId = $uomMap[$uomCode] ?? $item->uom_id ?? $uomMap['unit'] ?? null;

                RecipeIngredient::updateOrCreate(
                    [
                        'recipe_id' => $recipe->id,
                        'item_id' => $item->id,
                    ],
                    [
                        'quantity' => (float) Arr::get($ingredientData, 'quantity', 0),
                        'uom_id' => $uomId,
                        'cost_per_unit' => 0,
                        'waste_percentage' => (float) Arr::get($ingredientData, 'wastage_percent', 0),
                        'sort_order' => $ingredientSortOrder++,
                        'notes' => null,
                        'preparation_notes' => null,
                    ]
                );
            }
        }
    }

    /**
     * @param  array<string, Department>  $departments
     */
    private function ensureSuperAdminUser(Branch $branch, array $departments): void
    {
        $department = $departments['hr'] ?? null;

        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@sweettooth.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
                'branch_id' => $branch->id,
                'last_accessed_branch_id' => $branch->id,
                'department_id' => $department?->id,
                'employment_status' => 'active',
                'user_type' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $role = Role::where('name', 'Super Admin')->where('guard_name', 'web')->first();
        if ($role !== null) {
            $superAdmin->syncRoles([$role]);
        }
    }

    private function resolveDepartmentFromJob(string $jobDescription, array $departments): ?Department
    {
        $job = Str::lower($jobDescription);

        if (Str::contains($job, ['inventory', 'store'])) {
            return $departments['inventory'] ?? null;
        }

        if (Str::contains($job, ['hr', 'human resource'])) {
            return $departments['hr'] ?? null;
        }

        if (Str::contains($job, ['gelato', 'ice cream'])) {
            return $departments['gelato'] ?? null;
        }

        if (Str::contains($job, ['pastry', 'baker', 'cake'])) {
            return $departments['pastry'] ?? null;
        }

        if (Str::contains($job, ['corner', 'barista', 'coffee'])) {
            return $departments['corner_store'] ?? null;
        }

        if (Str::contains($job, ['chef', 'kitchen', 'cook', 'production'])) {
            return $departments['hot_kitchen'] ?? null;
        }

        return $departments['till_concession'] ?? null;
    }

    private function resolveRoleFromJob(string $jobDescription): ?string
    {
        $job = Str::lower($jobDescription);

        if (Str::contains($job, ['manager', 'floor manager']) && Str::contains($job, ['sales', 'floor'])) {
            return 'Sales Manager';
        }

        if (Str::contains($job, ['manager', 'head']) && Str::contains($job, ['production', 'kitchen', 'pastry', 'gelato'])) {
            return 'Head of Production';
        }

        if (Str::contains($job, ['inventory', 'store'])) {
            return 'Inventory Staff';
        }

        if (Str::contains($job, ['hr', 'human resource'])) {
            return 'HR Officer';
        }

        if (Str::contains($job, ['chef', 'kitchen', 'pastry', 'gelato', 'production'])) {
            return 'Production Staff';
        }

        if (Str::contains($job, ['sales', 'cashier', 'floor'])) {
            return 'Sales Staff';
        }

        return 'Sales Staff';
    }

    private function sanitizeEmail(string $rawEmail, string $name, int $counter): string
    {
        $email = Str::lower(trim($rawEmail));
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        if ($email !== '' && ! Str::contains($email, '@')) {
            $candidate = $email.'@sweettooth.local';
            if (filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
                return $candidate;
            }
        }

        $slug = Str::slug($name, '.');

        return $slug.'.'.$counter.'@sweettooth.local';
    }

    private function extractPhone(string $rawPhone): ?string
    {
        $digits = preg_replace('/[^0-9+]/', '', $rawPhone);

        return $digits !== '' ? Str::limit($digits, 25, '') : null;
    }

    private function normalizeDate(string $rawDate, string $format = 'Y-m-d H:i:s'): ?string
    {
        $rawDate = trim($rawDate);
        if ($rawDate === '') {
            return null;
        }

        $timestamp = strtotime($rawDate);
        if ($timestamp === false) {
            return null;
        }

        return date($format, $timestamp);
    }

    private function inferItemCategory(string $name): string
    {
        $upper = Str::upper($name);

        if (Str::contains($upper, ['BOX', 'CUP', 'CONTAINER', 'WRAP', 'BAG', 'PACKAGING'])) {
            return 'packaging';
        }

        if (Str::contains($upper, ['DETERGENT', 'TOWEL', 'SOAP', 'TISSUE', 'CLEANER'])) {
            return 'consumable';
        }

        if (Str::contains($upper, ['MIXER', 'OVEN', 'FREEZER', 'BOWL', 'KNIFE', 'MACHINE'])) {
            return 'equipment';
        }

        return 'raw_material';
    }

    private function sourceToDepartmentKey(string $source, string $name): string
    {
        $upperName = Str::upper($name);
        if (Str::contains($upperName, ['CORNERSTORE', 'CORNER STORE'])) {
            return 'cornerstone';
        }

        return match ($source) {
            'gelato' => 'gelato',
            'pastry' => 'pastry',
            'cornerstone' => 'cornerstone',
            'hot_kitchen' => 'hot_kitchen',
            'till_concession' => 'hot_kitchen',
            default => 'hot_kitchen',
        };
    }

    /**
     * @return array<int, string>
     */
    private function sourceToSalesDepartmentKeys(string $source, string $name): array
    {
        $upperName = Str::upper($name);
        if ($source === 'cornerstone' || Str::contains($upperName, ['CORNERSTORE', 'CORNER STORE'])) {
            return ['corner_store'];
        }

        return ['till_concession'];
    }

    private function sourcePriority(string $source): int
    {
        return match ($source) {
            'cornerstone' => 5,
            'hot_kitchen', 'gelato', 'pastry' => 4,
            'till_concession' => 3,
            default => 1,
        };
    }

    private function inferSourceFromName(string $name): string
    {
        $upperName = Str::upper($name);

        if (Str::contains($upperName, ['CORNERSTORE', 'CORNER STORE'])) {
            return 'cornerstone';
        }

        return 'hot_kitchen';
    }

    private function normalizeHeaderName(string $value): string
    {
        return Str::snake(Str::of($value)->trim()->replaceMatches('/\s+/', ' ')->toString());
    }

    /**
     * @param  array<int, string>  $headers
     * @param  array<int, string>  $candidates
     */
    private function findColumn(array $headers, array $candidates): ?int
    {
        foreach ($headers as $column => $header) {
            if (in_array($header, $candidates, true)) {
                return $column;
            }
        }

        return null;
    }

    private function cleanName(string $name): string
    {
        $name = str_replace("\xc2\xa0", ' ', $name);
        $name = preg_replace('/\([^)]*\)/', '', $name);
        $name = preg_replace('/\s+/', ' ', (string) $name);

        return trim((string) $name);
    }

    private function normalizeNameKey(string $name): string
    {
        $normalized = Str::upper($this->cleanName($name));
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        return trim((string) $normalized);
    }

    /**
     * @return array{0: float|null, 1: string|null}
     */
    private function extractQuantityAndUom(string $value): array
    {
        $value = str_replace("\xc2\xa0", ' ', strtoupper(trim($value)));
        if ($value === '') {
            return [null, null];
        }

        if (preg_match('/([0-9.,]+)\s*([A-Z]+)/', $value, $matches) !== 1) {
            return [$this->toDecimal($value), null];
        }

        return [
            $this->toDecimal($matches[1]),
            $matches[2] ?? null,
        ];
    }

    private function toDecimal(?string $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $cleaned = trim($value);
        $cleaned = str_ireplace(['PRICE', ':', 'N', '#'], '', $cleaned);
        $cleaned = str_replace([',', ' '], '', $cleaned);
        if ($cleaned === '' || ! is_numeric($cleaned)) {
            return null;
        }

        return (float) $cleaned;
    }

    private function toPercentage(?string $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $cleaned = str_replace('%', '', trim($value));
        if ($cleaned === '' || ! is_numeric($cleaned)) {
            return null;
        }

        return (float) $cleaned;
    }

    private function toUomCode(?string $rawUom): string
    {
        $uom = Str::upper(trim((string) $rawUom));

        return match ($uom) {
            'G', 'GRAM', 'GRAMS' => 'g',
            'KG', 'KG.' => 'kg',
            'ML' => 'ml',
            'L', 'LTR', 'LITRE', 'LITER', 'LITERS' => 'l',
            'PCS', 'PIECE', 'PIECES', 'PORTION', 'PACK', 'CUP', 'CU' => 'pcs',
            'UNIT', 'UNITS' => 'unit',
            default => 'unit',
        };
    }
}
