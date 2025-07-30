<?php

namespace Skeleton\Tests\Unit;

use Skeleton\Tests\BaseTestCase;
use Skeleton\Database\QueryBuilder;
use PDO;
use PDOStatement;
use Mockery;

class QueryBuilderTest extends BaseTestCase
{
    private $mockPdo;

    private QueryBuilder $queryBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockPdo = new PDO('sqlite::memory:');
        $this->mockPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->mockPdo->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT);");
        $this->queryBuilder = new QueryBuilder($this->mockPdo);
    }

    /**
     * Test constructor
     */
    public function testConstructor(): void
    {
        $queryBuilder = new QueryBuilder($this->mockPdo);
        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);
    }

    /**
     * Test reset method
     */
    public function testReset(): void
    {
        $this->queryBuilder->select(['name'])->from('users')->where('id', '=', 1);
        $this->queryBuilder->reset();

        // After reset, a simple query should work
        $this->queryBuilder->select()->from('posts');
        $sql = $this->queryBuilder->toSql();
        $this->assertEquals('SELECT * FROM posts', $sql);
    }

    /**
     * Test table method
     */
    public function testTable(): void
    {
        $result = $this->queryBuilder->table('users');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test select method with default columns
     */
    public function testSelectDefault(): void
    {
        $result = $this->queryBuilder->select();
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test select method with specific columns
     */
    public function testSelectSpecific(): void
    {
        $result = $this->queryBuilder->select(['name', 'email']);
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test insert method
     */
    public function testInsert(): void
    {
        $result = $this->queryBuilder->insert(['name' => 'John', 'email' => 'john@example.com']);
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test update method
     */
    public function testUpdate(): void
    {
        $result = $this->queryBuilder->update(['name' => 'Jane']);
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test where method
     */
    public function testWhere(): void
    {
        $result = $this->queryBuilder->where('id', '=', 1);
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test whereIn method
     */
    public function testWhereIn(): void
    {
        $result = $this->queryBuilder->whereIn('id', [1, 2, 3]);
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test limit method
     */
    public function testLimit(): void
    {
        $result = $this->queryBuilder->limit(10);
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test orderBy method
     */
    public function testOrderBy(): void
    {
        $result = $this->queryBuilder->orderBy('name', 'ASC');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test groupBy method
     */
    public function testGroupBy(): void
    {
        $result = $this->queryBuilder->groupBy('category');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test having method
     */
    public function testHaving(): void
    {
        $result = $this->queryBuilder->having('count', '>', 5);
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test join method
     */
    public function testJoin(): void
    {
        $result = $this->queryBuilder->join('posts', 'users.id', '=', 'posts.user_id');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test bind method
     */
    public function testBind(): void
    {
        $result = $this->queryBuilder->bind('test_value');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test bindArray method
     */
    public function testBindArray(): void
    {
        $result = $this->queryBuilder->bindArray(['values' => [1, 2, 3]]);
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test count method
     */
    public function testCount(): void
    {
        $result = $this->queryBuilder->count();
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test min method
     */
    public function testMin(): void
    {
        $result = $this->queryBuilder->min('age');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test max method
     */
    public function testMax(): void
    {
        $result = $this->queryBuilder->max('age');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test avg method
     */
    public function testAvg(): void
    {
        $result = $this->queryBuilder->avg('score');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test sum method
     */
    public function testSum(): void
    {
        $result = $this->queryBuilder->sum('total');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test fetchColumn method
     */
    public function testFetchColumn(): void
    {
        $stmt = $this->mockPdo->prepare('INSERT INTO users (name) VALUES (:name)');
        $stmt->execute([':name' => 'John']);

        $result = $this->queryBuilder
            ->count()
            ->from('users')
            ->fetchColumn(0);

        $this->assertEquals(1, $result);
    }

    /**
     * Test execute method with SELECT
     */
    public function testExecuteSelect(): void
    {
        $mockStatement = Mockery::mock(PDOStatement::class);

        $this->mockPdo->shouldReceive('prepare')
            ->with('SELECT * FROM users')
            ->once()
            ->andReturn($mockStatement);

        $mockStatement->shouldReceive('execute')
            ->with([])
            ->once()
            ->andReturn(true);

        $mockStatement->shouldReceive('fetchAll')
            ->once()
            ->andReturn([['id' => 1, 'name' => 'John']]);

        $result = $this->queryBuilder
            ->select()
            ->from('users')
            ->execute();

        $this->assertIsArray($result);
        $this->assertEquals([['id' => 1, 'name' => 'John']], $result);
    }

    /**
     * Test execute method with INSERT
     */
    public function testExecuteInsert(): void
    {
        $mockStatement = Mockery::mock(PDOStatement::class);

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
            ->insert(['name' => 'John'])
            ->into('users')
            ->execute();

        $this->assertEquals(1, $result);
    }

    /**
     * Test execute method with UPDATE
     */
    public function testExecuteUpdate(): void
    {
        $mockStatement = Mockery::mock(PDOStatement::class);

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
     * Test execute method with DELETE
     */
    public function testExecuteDelete(): void
    {
        $mockStatement = Mockery::mock(PDOStatement::class);

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
     * Test first method
     */
    public function testFirst(): void
    {
        $mockStatement = Mockery::mock(PDOStatement::class);

        $this->mockPdo->shouldReceive('prepare')
            ->once()
            ->andReturn($mockStatement);

        $mockStatement->shouldReceive('execute')
            ->once()
            ->andReturn(true);

        $mockStatement->shouldReceive('fetch')
            ->once()
            ->andReturn(['id' => 1, 'name' => 'John']);

        $result = $this->queryBuilder
            ->select()
            ->from('users')
            ->first();

        $this->assertEquals(['id' => 1, 'name' => 'John'], $result);
    }

    /**
     * Test from method
     */
    public function testFrom(): void
    {
        $result = $this->queryBuilder->from('users');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test into method
     */
    public function testInto(): void
    {
        $result = $this->queryBuilder->into('users');
        $this->assertInstanceOf(QueryBuilder::class, $result);
    }

    /**
     * Test toSql method with simple SELECT
     */
    public function testToSqlSimpleSelect(): void
    {
        $sql = $this->queryBuilder->select()->from('users')->toSql();
        $this->assertEquals('SELECT * FROM users', $sql);
    }

    /**
     * Test toSql method with WHERE clause
     */
    public function testToSqlWithWhere(): void
    {
        $sql = $this->queryBuilder->select()->from('users')->where('status', '=', 'active')->toSql();
        $this->assertStringContainsString('SELECT * FROM users', $sql);
        $this->assertStringContainsString('WHERE status = ?', $sql);
    }

    /**
     * Test toSql method with multiple WHERE clauses
     */
    public function testToSqlWithMultipleWhere(): void
    {
        $sql = $this->queryBuilder
            ->select()
            ->from('users')
            ->where('status', '=', 'active')
            ->where('age', '>', 18, 'AND')
            ->toSql();

        $this->assertStringContainsString('WHERE status = ?', $sql);
        $this->assertStringContainsString('AND age > ?', $sql);
    }

    /**
     * Test toSql method with ORDER BY
     */
    public function testToSqlWithOrderBy(): void
    {
        $sql = $this->queryBuilder->select()->from('users')->orderBy('name', 'ASC')->toSql();
        $this->assertStringContainsString('ORDER BY name ASC', $sql);
    }

    /**
     * Test toSql method with LIMIT
     */
    public function testToSqlWithLimit(): void
    {
        $sql = $this->queryBuilder->select()->from('users')->limit(10)->toSql();
        $this->assertStringContainsString('LIMIT 10', $sql);
    }

    /**
     * Test postProcessWhere method
     */
    public function testPostProcessWhere(): void
    {
        $this->queryBuilder->where('status', '=', 'active');
        $this->queryBuilder->where('age', '>', 18);

        // This should trigger postProcessWhere
        $sql = $this->queryBuilder->select()->from('users')->toSql();

        $this->assertStringContainsString('WHERE', $sql);
    }

    /**
     * Test getPDOStatement method
     */
    public function testGetPDOStatement(): void
    {
        $mockStatement = Mockery::mock(PDOStatement::class);

        $this->mockPdo->shouldReceive('prepare')
            ->once()
            ->andReturn($mockStatement);

        $stmt = $this->queryBuilder->select()->from('users')->getPDOStatement();
        $this->assertSame($mockStatement, $stmt);
    }

    /**
     * Test that the users table is initially empty
     */
    public function testUsersTableIsEmpty(): void
    {
        $result = $this->queryBuilder->select()->from('users')->get();
        $this->assertCount(0, $result);
    }

    /**
     * Test inserting and retrieving a user
     */
    public function testInsertAndRetrieveUser(): void
    {
        $stmt = $this->mockPdo->prepare('INSERT INTO users (name) VALUES (:name)');
        $stmt->execute([':name' => 'Doe']);

        $result = $this->queryBuilder
            ->select(['name'])
            ->from('users')
            ->get();

        $this->assertEquals('Doe', $result[0]['name']);
    }
}
