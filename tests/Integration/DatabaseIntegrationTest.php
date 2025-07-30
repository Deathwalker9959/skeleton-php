<?php

namespace Skeleton\Tests\Integration;

use Skeleton\Tests\BaseTestCase;
use Skeleton\Database\QueryBuilder;
use Skeleton\Database\Transaction;
use PDO;
use PDOStatement;
use Mockery;
use Mockery\MockInterface;

class DatabaseIntegrationTest extends BaseTestCase
{
    private MockInterface&PDO $mockPdo;

    private QueryBuilder $queryBuilder;

    private Transaction $transaction;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock PDO instance
        /** @var MockInterface&PDO $mockPdo */
        $this->mockPdo = Mockery::mock('PDO');
        $this->queryBuilder = new QueryBuilder($this->mockPdo);
        $this->transaction = new Transaction($this->mockPdo);
    }

    /**
     * Test query builder basic functionality
     */
    public function testQueryBuilderBasicMethods(): void
    {
        // Test table method
        $result = $this->queryBuilder->table('users');
        $this->assertInstanceOf(QueryBuilder::class, $result);

        // Test select method
        $result = $this->queryBuilder->select(['name', 'email']);
        $this->assertInstanceOf(QueryBuilder::class, $result);

        // Test from method
        $result = $this->queryBuilder->from('users');
        $this->assertInstanceOf(QueryBuilder::class, $result);

        // Test where method
        $result = $this->queryBuilder->where('id', '=', 1);
        $this->assertInstanceOf(QueryBuilder::class, $result);

        // Test limit method
        $result = $this->queryBuilder->limit(10);
        $this->assertInstanceOf(QueryBuilder::class, $result);

        // Test orderBy method
        $result = $this->queryBuilder->orderBy('name', 'ASC');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test query builder SQL generation
     */
    public function testQueryBuilderSqlGeneration(): void
    {
        // Test simple SELECT
        $this->queryBuilder->reset();
        $this->queryBuilder->select()->from('users');
        $sql = $this->queryBuilder->toSql();
        $this->assertEquals('SELECT * FROM users', $sql);

        // Test SELECT with WHERE
        $this->queryBuilder->reset();
        $this->queryBuilder->select()->from('users')->where('status', '=', 'active');
        $sql = $this->queryBuilder->toSql();
        $this->assertStringContainsString('SELECT * FROM users', $sql);
        $this->assertStringContainsString('WHERE status = ?', $sql);

        // Test SELECT with LIMIT
        $this->queryBuilder->reset();
        $this->queryBuilder->select()->from('users')->limit(5);
        $sql = $this->queryBuilder->toSql();
        $this->assertStringContainsString('LIMIT 5', $sql);
    }

    /**
     * Test transaction methods
     */
    public function testTransactionMethods(): void
    {
        // Test begin transaction
        $this->mockPdo->shouldReceive('beginTransaction')
            ->once()
            ->andReturn(true);

        $result = $this->transaction->begin();
        $this->assertTrue($result);

        // Test commit (this will make the transaction inactive)
        $this->mockPdo->shouldReceive('commit')
            ->once()
            ->andReturn(true);

        $result = $this->transaction->commit();
        $this->assertTrue($result);
    }

    /**
     * Test transaction rollback
     */
    public function testTransactionRollback(): void
    {
        // Mock begin transaction first
        $this->mockPdo->shouldReceive('beginTransaction')
            ->once()
            ->andReturn(true);

        $result = $this->transaction->begin();
        $this->assertTrue($result);

        // Mock rollback
        $this->mockPdo->shouldReceive('rollBack')
            ->once()
            ->andReturn(true);

        $result = $this->transaction->rollBack();
        $this->assertTrue($result);
    }

    /**
     * Test query builder insert functionality
     */
    public function testQueryBuilderInsert(): void
    {
        $mockStatement = Mockery::mock('PDOStatement');

        $this->mockPdo->shouldReceive('prepare')
            ->once()
            ->andReturn($mockStatement);

        $mockStatement->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $mockStatement->shouldReceive('rowCount')
            ->once()
            ->andReturn(1);

        $result = $this->queryBuilder
            ->insert(['name' => 'John', 'email' => 'john@example.com'])
            ->into('users')
            ->execute();

        $this->assertEquals(1, $result);
    }

    /**
     * Test query builder update functionality
     */
    public function testQueryBuilderUpdate(): void
    {
        $mockStatement = Mockery::mock('PDOStatement');

        $this->mockPdo->shouldReceive('prepare')
            ->once()
            ->andReturn($mockStatement);

        $mockStatement->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $mockStatement->shouldReceive('rowCount')
            ->once()
            ->andReturn(1);

        $result = $this->queryBuilder
            ->update(['name' => 'Jane'])
            ->table('users')
            ->where('id', '=', 1)
            ->execute();

        $this->assertEquals(1, $result);
    }

    /**
     * Test query builder delete functionality
     */
    public function testQueryBuilderDelete(): void
    {
        $mockStatement = Mockery::mock('PDOStatement');

        $this->mockPdo->shouldReceive('prepare')
            ->once()
            ->andReturn($mockStatement);

        $mockStatement->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $mockStatement->shouldReceive('rowCount')
            ->once()
            ->andReturn(1);

        $result = $this->queryBuilder
            ->delete()
            ->from('users')
            ->where('id', '=', 1)
            ->execute();

        $this->assertEquals(1, $result);
    }

    /**
     * Test query builder bind method
     */
    public function testQueryBuilderBind(): void
    {
        $result = $this->queryBuilder->bind('test_value');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test query builder whereIn method
     */
    public function testQueryBuilderWhereIn(): void
    {
        $result = $this->queryBuilder->whereIn('id', [1, 2, 3]);
        $this->assertInstanceOf(QueryBuilder::class, $result);

        $this->queryBuilder->select()->from('users');
        $sql = $this->queryBuilder->toSql();
        $this->assertStringContainsString('WHERE id IN (?, ?, ?)', $sql);
    }

    /**
     * Test query builder groupBy method
     */
    public function testQueryBuilderGroupBy(): void
    {
        $result = $this->queryBuilder->groupBy('category');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test query builder having method
     */
    public function testQueryBuilderHaving(): void
    {
        $result = $this->queryBuilder->having('count', '>', 5);
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test query builder join method
     */
    public function testQueryBuilderJoin(): void
    {
        $result = $this->queryBuilder->join('posts', 'users.id', '=', 'posts.user_id');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test query builder aggregate methods
     */
    public function testQueryBuilderAggregates(): void
    {
        // Test count
        $result = $this->queryBuilder->count();
        $this->assertInstanceOf(QueryBuilder::class, $result);

        // Test min
        $result = $this->queryBuilder->min('age');
        $this->assertInstanceOf(QueryBuilder::class, $result);

        // Test max
        $result = $this->queryBuilder->max('age');
        $this->assertInstanceOf(QueryBuilder::class, $result);

        // Test avg
        $result = $this->queryBuilder->avg('score');
        $this->assertInstanceOf(QueryBuilder::class, $result);

        // Test sum
        $result = $this->queryBuilder->sum('total');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }
}
