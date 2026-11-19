<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_tables_have_the_exact_required_columns(): void
    {
        $tables = [
            'districts' => ['id', 'name', 'created_at', 'updated_at'],
            'eateries' => ['id', 'district_id', 'name', 'slug', 'address', 'price_range', 'specialty', 'is_open', 'created_at', 'updated_at'],
            'permits' => ['id', 'eatery_id', 'permit_no', 'expires_on', 'created_at', 'updated_at'],
            'dishes' => ['id', 'eatery_id', 'name', 'price', 'is_bestseller', 'created_at', 'updated_at'],
            'features' => ['id', 'name', 'created_at', 'updated_at'],
            'eatery_feature' => ['eatery_id', 'feature_id'],
            'reviews' => ['id', 'eatery_id', 'user_id', 'rating', 'body', 'created_at', 'updated_at'],
        ];

        foreach ($tables as $table => $columns) {
            $this->assertSame($columns, Schema::getColumnListing($table), $table);
        }

        $this->assertTrue(Schema::hasTable('users'));
    }

    public function test_foreign_keys_reference_the_correct_parent_tables(): void
    {
        $tables = [
            'eateries' => ['district_id' => 'districts'],
            'permits' => ['eatery_id' => 'eateries'],
            'dishes' => ['eatery_id' => 'eateries'],
            'eatery_feature' => ['eatery_id' => 'eateries', 'feature_id' => 'features'],
            'reviews' => ['eatery_id' => 'eateries', 'user_id' => 'users'],
        ];

        foreach ($tables as $table => $parents) {
            $keys = Schema::getForeignKeys($table);
            $this->assertCount(count($parents), $keys, $table);

            foreach ($keys as $key) {
                $this->assertSame($parents[$key['columns'][0]], $key['foreign_table']);
                $this->assertSame(['id'], $key['foreign_columns']);
            }
        }
    }

    public function test_unique_indexes_and_the_composite_pivot_primary_key_exist(): void
    {
        $uniqueColumns = [
            'districts' => ['name'],
            'features' => ['name'],
            'eateries' => ['name', 'slug'],
            'permits' => ['eatery_id', 'permit_no'],
        ];

        foreach ($uniqueColumns as $table => $columns) {
            foreach ($columns as $column) {
                $this->assertTrue(Schema::hasIndex($table, [$column], 'unique'), $table.'.'.$column);
            }
        }

        $primary = collect(Schema::getIndexes('eatery_feature'))->firstWhere('primary', true);
        $this->assertNotNull($primary);
        $this->assertSame(['eatery_id', 'feature_id'], $primary['columns']);
    }

    public function test_boolean_defaults_and_nullable_specialty_work(): void
    {
        $eateryId = $this->createEatery();
        $dishId = DB::table('dishes')->insertGetId([
            'eatery_id' => $eateryId,
            'name' => 'Tinolang Manok',
            'price' => 85.00,
        ]);

        $this->assertDatabaseHas('eateries', ['id' => $eateryId, 'is_open' => 1, 'specialty' => null]);
        $this->assertDatabaseHas('dishes', ['id' => $dishId, 'is_bestseller' => 0]);
    }

    public function test_an_eatery_cannot_have_two_permits(): void
    {
        $eateryId = $this->createEatery();
        DB::table('permits')->insert([
            'eatery_id' => $eateryId,
            'permit_no' => 'DVO-2026-1001',
            'expires_on' => '2027-04-20',
        ]);

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('UNIQUE constraint failed: permits.eatery_id');

        DB::table('permits')->insert([
            'eatery_id' => $eateryId,
            'permit_no' => 'DVO-2026-1002',
            'expires_on' => '2027-04-20',
        ]);
    }

    public function test_foreign_keys_reject_an_eatery_without_a_district(): void
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('FOREIGN KEY constraint failed');

        DB::table('eateries')->insert([
            'district_id' => 999,
            'name' => 'Orphan Eatery',
            'slug' => 'orphan-eatery',
            'address' => 'Davao City',
            'price_range' => 'budget',
        ]);
    }

    public function test_a_feature_cannot_be_attached_to_the_same_eatery_twice(): void
    {
        $eateryId = $this->createEatery();
        $featureId = DB::table('features')->insertGetId(['name' => 'Budget Meal']);
        $pivot = ['eatery_id' => $eateryId, 'feature_id' => $featureId];
        DB::table('eatery_feature')->insert($pivot);

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('UNIQUE constraint failed: eatery_feature.eatery_id, eatery_feature.feature_id');

        DB::table('eatery_feature')->insert($pivot);
    }

    private function createEatery(): int
    {
        $districtId = DB::table('districts')->insertGetId(['name' => 'Poblacion']);

        return DB::table('eateries')->insertGetId([
            'district_id' => $districtId,
            'name' => 'Schema Test Eatery',
            'slug' => 'schema-test-eatery',
            'address' => 'Davao City',
            'price_range' => 'budget',
        ]);
    }
}
