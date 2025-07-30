<?php

declare(strict_types=1);

namespace Skeleton\Tests\Unit\Database;

use Skeleton\Tests\BaseTestCase;
use Skeleton\Database\Transaction;
use PDO;
use Mockery;

/**
 * Test case for the Transaction class
 */
class TransactionTest extends BaseTestCase
{
    private $mockPdo;

    private Transaction $transaction;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var MockInterface&PDO $mockPdo */
        $this->mockPdo = Mockery::mock('PDO');
        $this->transaction = new Transaction($this->mockPdo);
    }

    /**
     * Test transaction constructor
     */
    public function testConstructor(): void
    {
        $transaction = new Transaction($this->mockPdo);

        $this->assertInstanceOf(Transaction::class, $transaction);
    }

    /**
     * Test beginning a transaction successfully
     */
    public function testBeginTransactionSuccessfully(): void
    {
        $this->mockPdo->shouldReceive('beginTransaction')
            ->once()
            ->andReturn(true);

        $result = $this->transaction->begin();

        $this->assertTrue($result);
    }

    /**
     * Test beginning a transaction when one is already active
     */
    public function testBeginTransactionWhenAlreadyActive(): void
    {
        // First begin should succeed
        $this->mockPdo->shouldReceive('beginTransaction')
            ->once()
            ->andReturn(true);

        $this->assertTrue($this->transaction->begin());

        // Second begin should fail because transaction is already active
        $result = $this->transaction->begin();
        $this->assertFalse($result);
    }

    /**
     * Test beginning a transaction when PDO beginTransaction fails
     */
    public function testBeginTransactionWhenPdoFails(): void
    {
        $this->mockPdo->shouldReceive('beginTransaction')
            ->once()
            ->andReturn(false);

        $result = $this->transaction->begin();

        $this->assertFalse($result);
    }

    /**
     * Test committing a transaction successfully
     */
    public function testCommitTransactionSuccessfully(): void
    {
        // First begin a transaction
        $this->mockPdo->shouldReceive('beginTransaction')
            ->once()
            ->andReturn(true);

        $this->mockPdo->shouldReceive('commit')
            ->once()
            ->andReturn(true);

        $this->transaction->begin();
        $result = $this->transaction->commit();

        $this->assertTrue($result);
    }

    /**
     * Test committing when no transaction is active
     */
    public function testCommitWhenNoTransactionActive(): void
    {
        $result = $this->transaction->commit();

        $this->assertFalse($result);
    }

    /**
     * Test committing when PDO commit fails
     */
    public function testCommitWhenPdoFails(): void
    {
        // First begin a transaction
        $this->mockPdo->shouldReceive('beginTransaction')
            ->once()
            ->andReturn(true);

        $this->mockPdo->shouldReceive('commit')
            ->once()
            ->andReturn(false);

        $this->transaction->begin();
        $result = $this->transaction->commit();

        $this->assertFalse($result);
    }

    /**
     * Test rolling back a transaction successfully
     */
    public function testRollBackTransactionSuccessfully(): void
    {
        // First begin a transaction
        $this->mockPdo->shouldReceive('beginTransaction')
            ->once()
            ->andReturn(true);

        $this->mockPdo->shouldReceive('rollBack')
            ->once()
            ->andReturn(true);

        $this->transaction->begin();
        $result = $this->transaction->rollBack();

        $this->assertTrue($result);
    }

    /**
     * Test rolling back when no transaction is active
     */
    public function testRollBackWhenNoTransactionActive(): void
    {
        $result = $this->transaction->rollBack();

        $this->assertFalse($result);
    }

    /**
     * Test rolling back when PDO rollBack fails
     */
    public function testRollBackWhenPdoFails(): void
    {
        // First begin a transaction
        $this->mockPdo->shouldReceive('beginTransaction')
            ->once()
            ->andReturn(true);

        $this->mockPdo->shouldReceive('rollBack')
            ->once()
            ->andReturn(false);

        $this->transaction->begin();
        $result = $this->transaction->rollBack();

        $this->assertFalse($result);
    }

    /**
     * Test complete transaction workflow: begin -> commit
     */
    public function testCompleteTransactionWorkflowCommit(): void
    {
        $this->mockPdo->shouldReceive('beginTransaction')
            ->once()
            ->andReturn(true);

        $this->mockPdo->shouldReceive('commit')
            ->once()
            ->andReturn(true);

        // Begin transaction
        $beginResult = $this->transaction->begin();
        $this->assertTrue($beginResult);

        // Commit transaction
        $commitResult = $this->transaction->commit();
        $this->assertTrue($commitResult);

        // Should be able to begin a new transaction after commit
        $this->mockPdo->shouldReceive('beginTransaction')
            ->once()
            ->andReturn(true);

        $newBeginResult = $this->transaction->begin();
        $this->assertTrue($newBeginResult);
    }

    /**
     * Test complete transaction workflow: begin -> rollback
     */
    public function testCompleteTransactionWorkflowRollback(): void
    {
        $this->mockPdo->shouldReceive('beginTransaction')
            ->once()
            ->andReturn(true);

        $this->mockPdo->shouldReceive('rollBack')
            ->once()
            ->andReturn(true);

        // Begin transaction
        $beginResult = $this->transaction->begin();
        $this->assertTrue($beginResult);

        // Rollback transaction
        $rollbackResult = $this->transaction->rollBack();
        $this->assertTrue($rollbackResult);

        // Should be able to begin a new transaction after rollback
        $this->mockPdo->shouldReceive('beginTransaction')
            ->once()
            ->andReturn(true);

        $newBeginResult = $this->transaction->begin();
        $this->assertTrue($newBeginResult);
    }

    /**
     * Test that multiple commits/rollbacks on same transaction fail
     */
    public function testMultipleCommitsOnSameTransaction(): void
    {
        $this->mockPdo->shouldReceive('beginTransaction')
            ->once()
            ->andReturn(true);

        $this->mockPdo->shouldReceive('commit')
            ->once()
            ->andReturn(true);

        // Begin and commit
        $this->transaction->begin();
        $firstCommit = $this->transaction->commit();
        $this->assertTrue($firstCommit);

        // Second commit should fail
        $secondCommit = $this->transaction->commit();
        $this->assertFalse($secondCommit);
    }

    /**
     * Test that multiple rollbacks on same transaction fail
     */
    public function testMultipleRollbacksOnSameTransaction(): void
    {
        $this->mockPdo->shouldReceive('beginTransaction')
            ->once()
            ->andReturn(true);

        $this->mockPdo->shouldReceive('rollBack')
            ->once()
            ->andReturn(true);

        // Begin and rollback
        $this->transaction->begin();
        $firstRollback = $this->transaction->rollBack();
        $this->assertTrue($firstRollback);

        // Second rollback should fail
        $secondRollback = $this->transaction->rollBack();
        $this->assertFalse($secondRollback);
    }
}
